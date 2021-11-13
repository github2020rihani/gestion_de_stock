<?php


namespace App\Controller\superAdmin;


use App\Entity\Achat;
use App\Entity\AchatArticle;
use App\Entity\Prix;
use App\Entity\Stock;
use App\Repository\AchatArticleRepository;
use App\Repository\AchatRepository;
use App\Repository\ArticleRepository;
use App\Repository\FournisseurRepository;
use App\Repository\PrixRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super_admin/achat")
 */
class AchatController extends AbstractController
{

    private $em;
    private $fournisseurRepository;
    private $achatArticleRepository;
    private $articleRepository;
    private $achatRepository;
    private $stockRepository;
    private $prixRepository;

    public function __construct(EntityManagerInterface $em,
                                AchatArticleRepository $achatArticleRepository,
                                AchatRepository $achatRepository,
                                StockRepository $stockRepository,
                                PrixRepository $prixRepository,
                                FournisseurRepository $fournisseurRepository, ArticleRepository $articleRepository)
    {
        $this->em = $em;
        $this->fournisseurRepository = $fournisseurRepository;
        $this->articleRepository = $articleRepository;
        $this->achatArticleRepository = $achatArticleRepository;
        $this->achatRepository = $achatRepository;
        $this->stockRepository = $stockRepository;
        $this->prixRepository = $prixRepository;
    }

    /**
     * @param Request $request
     * @Route("/add", name="add_achat")
     */
    public function new(Request $request)
    {
        $achat = new Achat();
        $fournisseurs = $this->fournisseurRepository->findAll();
        $articles = $this->articleRepository->findAll();
        if ($request->isMethod('POST')) {

            $numero_achat = $request->get('numero_achat');

            $achatArticleExiste = $this->achatRepository->findBy(array('numero' => $numero_achat));
            if ($achatArticleExiste) {
                $this->addFlash('error', 'Il ya une enregistré avec le numéro' . $numero_achat);
                return $this->redirectToRoute('index_achat');
            }
            //input achat
            $fournisseur = $request->get('fournisseur');
            $fodec = $request->get('fodecChecked');
            $remise = $request->get('remise');
            $transport = $request->get('transport');
            $ref = $request->get('article');
            if ($fodec == "true") {
                $achat->setFodec(true);
            }else{
                $achat->setFodec(false);
            }
            //input aricleachat
            $puhtnet = $request->get('puhtnet');
            $qte = $request->get('qte');
            $pventettc = $request->get('pventettc');
            $totalHt = 0 ;
            $totalTva = 0 ;
            $totalTTC = 0 ;
            foreach ($ref as $key => $value) {
                $totalHt= $totalHt+ ((float)$puhtnet[$key] * $qte[$key]);

            }
            if ($fodec == "true") {
                $achat->setFodec(true);
                $totalHt = $totalHt *  $_ENV['FODEC'];
            }else{
                $achat->setFodec(false);
            }

            $totalTva =   $totalHt * $_ENV['TVA_ARTICLE'];
            $totalTTC=  (float)$totalHt + (float)$totalTva+ (float)$remise + $_ENV['TIMBRE'] + (float)$transport ;
            $achat->setTotalHT((float)number_format($totalHt , 3));
            $achat->setTotalTVA((float)number_format($totalTva , 3));
            $achat->setTotalTTC((float)number_format($totalTTC , 3));
                //save achat
            $achat->setFournisseur($this->fournisseurRepository->find($fournisseur));
            $achat->setNumero($numero_achat);
            $achat->setAddedBy($this->getUser());
            $achat->setCreatedAt(new \DateTime('now'));
            $achat->setRemise( (float)number_format($remise, 3));
            $achat->setTronsport( (float)number_format($transport, 3));
            $achat->setTimbre( (float)number_format($_ENV['TIMBRE'], 3));
            $this->em->persist($achat);
            $this->em->flush();

            //save Article achat
            foreach ($ref as $key => $value) {
                $article = $this->articleRepository->find($value);
                $achatArticle = new AchatArticle();
                //check article exist in other achat
                $articleAchatExisteInOtherAchat = $this->achatArticleRepository->findBy(array('article' => $article->getId()));
                if ($articleAchatExisteInOtherAchat) {
                    foreach ($articleAchatExisteInOtherAchat as $key => $value) {
                        $value->setTypePrix('old');
                        $this->em->persist($value);
                        $this->em->flush();
                    }
                }
                $achatArticle->setTypePrix('new');
                $achatArticle->setCreatedAt($achat->getCreatedAt());
                $achatArticle->setAchat($achat);
                $achatArticle->setAddedBy($this->getUser());
                $achatArticle->setArticle($article);
                $achatArticle->setPuhtnet((float)$puhtnet[$key]);
                $achatArticle->setQte($qte[$key]);
                $achatArticle->setPventettc((float)$pventettc[$key]);
                $achatArticle->setPventeHT((float) number_format(((((float)$pventettc[$key] / 119))*100),3));
                $achatArticle->setTva($_ENV['TVA_ARTICLE_PERCENT']);
                $puttc[$key] = (float)(number_format($puhtnet[$key] * $_ENV['TVA_ARTICLE'], 3));
                $achatArticle->setPuttc($puttc[$key]);
                $achatArticle->setMarge((float)number_format(((($pventettc[$key] - $puttc[$key]) / $puttc[$key]) * 100), 2));
                $this->em->persist($achatArticle);
                $this->em->flush();




            }
            $this->addFlash('success', 'Ajout effectué avec succés');
            return $this->redirectToRoute('index_achat');
        }


        return $this->render('superAdmin/Achat/new.html.twig', ['article' => '',
            'fournisseurs' => $fournisseurs,
            'articles' => $articles,]);
    }

    /**
     * @Route("/", name="index_achat")
     */
    public
    function index(): Response
    {
        $achats = $this->achatRepository->findAllAchat();
        return $this->render('superAdmin/Achat/index.html.twig', [
            'achats' => $achats
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_achat")
     */
    public
    function edit(Achat $achat, Request $request): Response
    {
        $fournisseurs = $this->fournisseurRepository->findAll();
        $articles = $this->articleRepository->findAll();
        $achatObjet = $this->achatRepository->getDetailAchat($achat);
        if ($request->isMethod('POST')) {
            $numero_achat = $request->get('numero_achat');
            $old_numero = $achat->getNumero();
            $articlesToDelete = explode(",", $request->get('articleToDelete')[0]);

            if ($old_numero != $numero_achat) {
                $achatArticleExiste = $this->achatRepository->findBy(array('numero' => $numero_achat));
                if ($achatArticleExiste) {
                    $this->addFlash('error', 'il ya une achat avec enregistrer avec le numero' . $numero_achat);
                   return  $this->redirectToRoute('index_achat');
                }
            }

            //input achat
            $fournisseur = $request->get('fournisseur');
            $date_creation = $request->get('date_creation');
            $fodec = $request->get('fodec');
            //calculer total

            //update achat
            if ($achat) {
                $achat->setFournisseur($this->fournisseurRepository->find($fournisseur));
                $achat->setNumero($numero_achat);
                $achat->setAddedBy($this->getUser());
                $achat->setCreatedAt(new \DateTime($date_creation));
                $achat->setFodec($fodec);
                $this->em->persist($achat);
                $this->em->flush();
            } else {
                $this->addFlash('error', 'Aucun Achat trouvee');
                return  $this->redirectToRoute('index_achat');
            }


            //input aricleachat
            $ref = $request->get('article');
            $puhtnet = $request->get('puhtnet');
            $qte = $request->get('qte');
            $pventettc = $request->get('pventettc');
            //delete Articles
            if (!empty($articlesToDelete[0])) {
                $qte_article_delete = 0;
                //delete from achat article
                foreach ($articlesToDelete as $key => $value) {
                    $achatArticledelete = $this->achatArticleRepository->findAchatArticleByIdAricleAndIdAchat($value, $achat);
                    $qte_article_delete = $achatArticledelete->getQte();
                    $this->em->remove($achatArticledelete);
                    $this->em->flush();

                    $article_stocked = $this->stockRepository->findArticleInStockById($value);
                    if ($article_stocked && $article_stocked[0]) {
                        $article_stocked[0]->setQte((int)$article_stocked[0]->getQte() - $qte_article_delete);
                        $this->em->persist($article_stocked[0]);
                        $this->em->flush();
                    }
                }
            }
            //insert new Article
            foreach ($ref as $key => $value) {
                $achatArticledeleteByref = $this->achatArticleRepository->findAchatArticleByIdAricleAndIdAchat($value, $achat);
                if (!$achatArticledeleteByref) {
                    $article = $this->articleRepository->find($value);
                    $achatArticle = new AchatArticle();
                    $achatArticle->setCreatedAt($achat->getCreatedAt());
                    $achatArticle->setAchat($achat);
                    $achatArticle->setAddedBy($this->getUser());
                    $achatArticle->setArticle($this->articleRepository->find($value));
                    $achatArticle->setPuhtnet((float)$puhtnet[$key]);
                    $achatArticle->setQte($qte[$key]);
                    $achatArticle->setPventettc((float)$pventettc[$key]);
                    $achatArticle->setTva($_ENV['TVA_ARTICLE_PERCENT']);
                    $puttc[$key] = (float)(number_format($puhtnet[$key] * $_ENV['TVA_ARTICLE'], 3));
                    $achatArticle->setPuttc($puttc[$key]);
                    $achatArticle->setMarge((float)number_format(((($pventettc[$key] - $puttc[$key]) / $puttc[$key]) * 100), 2));
                    $this->em->persist($achatArticle);
                    $this->em->flush();



                } else {
                    $achatArticledeleteByref->setCreatedAt($achat->getCreatedAt());
                    $achatArticledeleteByref->setAchat($achat);
                    $achatArticledeleteByref->setAddedBy($this->getUser());
                    $achatArticledeleteByref->setArticle($this->articleRepository->find($value));
                    $achatArticledeleteByref->setPuhtnet((float)$puhtnet[$key]);
                    $achatArticledeleteByref->setQte($qte[$key]);
                    $achatArticledeleteByref->setPventettc((float)$pventettc[$key]);
                    $achatArticledeleteByref->setTva($_ENV['TVA_ARTICLE_PERCENT']);
                    $puttc[$key] = (float)(number_format($puhtnet[$key] * $_ENV['TVA_ARTICLE'], 3));
                    $achatArticledeleteByref->setPuttc($puttc[$key]);
                    $achatArticledeleteByref->setMarge((float)number_format(((($pventettc[$key] - $puttc[$key]) / $puttc[$key]) * 100), 2));
                    $this->em->persist($achatArticledeleteByref);
                    $this->em->flush();


                }

            }
            $this->addFlash('success', 'Modifier effectué avec succés');
            return $this->redirectToRoute('index_achat');
        }

        return $this->render('superAdmin/Achat/edit.html.twig', [
            'fournisseurs' => $fournisseurs,
            'articles' => $articles,
            'achat' => $achatObjet[0],
        ]);
    }


    /**
     * @Route("/detail/{id}", name="detail_achat")
     */
    public
    function detail(Achat $achat, Request $request): Response
    {
        $fournisseurs = $this->fournisseurRepository->findAll();
        $articles = $this->articleRepository->findAll();
        $achatObjet = $this->achatRepository->getDetailAchat($achat);

        return $this->render('superAdmin/Achat/detail.html.twig', [
            'fournisseurs' => $fournisseurs,
            'articles' => $articles,
            'achat' => $achatObjet[0],
        ]);
    }

    /**
     * @param Request $request
     * @Route("/get/articles", name="get_articles", options={"expose" = true})
     */
    public
    function getArticles()
    {
        $articles = $this->articleRepository->findArticles();
        return $this->json($articles);

    }

    /**
     * @param Request $request
     * @Route("/get/articlesById", name="get_articles_byId", options={"expose" = true})
     */
    public
    function getArticleById(Request $request)
    {
        $article = $this->articleRepository->getArticleById($request->request->get('id_article'));
        return $this->json($article);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/get/articles/achat", name="get_articles_achat", options={"expose" = true})
     */
    public
    function getArticleByAchat(Request $request)
    {
        $idAchat = $request->get('id_achat');
        $articles_achat = $this->achatRepository->getDetailAchat($idAchat);
        $articles = [];
        foreach ($articles_achat[0]['achatArticles'] as $key => $value) {
            $articles[] = $value['article']['id'];
        }
        return $this->json($articles);

    }


    /**
     * @param Request $request
     * @Route("/stocker/achat", name="stocker_achat", options={"expose" = true})
     */
    public function stockerAchat(Request $request) {
        $id_achat = $request->get('id_achat');
        $achatexiste = $this->achatRepository->getDetailAchat($id_achat);
        if ($achatexiste) {
            $achat = $this->achatRepository->find($achatexiste[0]['id']);
            //update achat stocked
            $achat->setStocker(true);
            $this->em->persist($achat);
            $this->em->flush();

            foreach ($achatexiste[0]['achatArticles'] as $key=>$value){
                //save in stock
                $articleinStock = $this->stockRepository->findArticleInStockById($value['article']['id']);
                if ($articleinStock && $articleinStock[0]) {
                    $articleinStock[0]->setQte((int)$articleinStock[0]->getQte() + $value['qte']);
                    $this->em->persist($articleinStock[0]);
                    $this->em->flush();
                } else {
                    $stock = new Stock();
                    $stock->setArticle($this->articleRepository->find($value['article']['id']));
                    $stock->setQte($value['qte']);
                    $stock->setDateEntree(new \DateTime('now'));
                    $this->em->persist($stock);
                    $this->em->flush();
                }
                //save in prix
                $articleExisteInprix = $this->prixRepository->findByIdArticle($value['article']['id']);
                if ($articleExisteInprix && $articleExisteInprix[0]) {
                    //verif si exit nexw pri or nn
                    $articleWithNewPriw = $this->achatArticleRepository->findArticleWithNewPrix($value['article']['id'], 'new');

                    //si modifier

                    if ($articleWithNewPriw && $articleWithNewPriw[0]){
                        $articleExisteInprix[0]->setPuAchaHT($value['puhtnet']);
                        $articleExisteInprix[0]->setPuVenteHT($value['pventeHT']);
                        $articleExisteInprix[0]->setPhAchatTTC($value['puttc']);
                        $articleExisteInprix[0]->setPuVenteTTC($value['pventettc']);
                        $articleExisteInprix[0]->setTaux($value['marge']);
                        $this->em->persist($articleExisteInprix[0]);
                        $this->em->flush();
                    }
                }else{
                    //insert article in prix
                    $prix = new Prix();
                    $prix->setAddedBy($this->getUser());
                    $prix->setArticle($this->articleRepository->find($value['article']['id']));
                    $prix->setTva($_ENV['TVA_ARTICLE_PERCENT']);
                    $prix->setPuAchaHT($value['puhtnet']);
                    $prix->setPuVenteHT($value['pventeHT']);
                    $prix->setPhAchatTTC($value['puttc']);
                    $prix->setPuVenteTTC($value['pventettc']);
                    $prix->setTaux($value['marge']);
                    $prix->setQte($value['qte']);
                    $this->em->persist($prix);
                    $this->em->flush();

                }

//stocked Article
                $art = $this->articleRepository->find($value['article']['id']);
                if ($art->getStocked() == false) {
                    $art->setStocked(true);
                    $this->em->persist($art);
                    $this->em->flush();
                }

            }
            $message = 'Achat a été stocker ';
            $success = true;
        }else{
            $message = 'Aucun achat trouver  ';
            $success = false;

        }

        return $this->json(array('message' => $message , 'success' => $success));
    }
}