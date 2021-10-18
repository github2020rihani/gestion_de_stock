<?php


namespace App\Controller\superAdmin;


use App\Entity\Achat;
use App\Entity\AchatArticle;
use App\Repository\AchatArticleRepository;
use App\Repository\AchatRepository;
use App\Repository\ArticleRepository;
use App\Repository\FournisseurRepository;
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

    public function __construct(EntityManagerInterface $em,
                                AchatArticleRepository $achatArticleRepository,
                                AchatRepository $achatRepository,
                                FournisseurRepository $fournisseurRepository, ArticleRepository $articleRepository)
    {
        $this->em = $em;
        $this->fournisseurRepository = $fournisseurRepository;
        $this->articleRepository = $articleRepository;
        $this->achatArticleRepository = $achatArticleRepository;
        $this->achatRepository = $achatRepository;
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
                $this->addFlash('error', 'il ya une achat avec enregistrer avec le numero' . $numero_achat);
                exit;
            }
            //input achat
            $fournisseur = $request->get('fournisseur');
            $date_creation = $request->get('date_creation');
            $fodec = $request->get('fodec');
            //calculer total

            //save achat
            $achat->setFournisseur($this->fournisseurRepository->find($fournisseur));
            $achat->setNumero($numero_achat);
            $achat->setAddedBy($this->getUser());
            $achat->setCreatedAt(new \DateTime($date_creation));
            $achat->setFodec($fodec);
            $this->em->persist($achat);
            $this->em->flush();
            //input aricleachat
            $ref = $request->get('article');
            $puhtnet = $request->get('puhtnet');
            $qte = $request->get('qte');
            $pventettc = $request->get('pventettc');
            foreach ($ref as $key => $value) {
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
            }
            $this->addFlash('success', 'Ajout effectué avec succés');
            return $this->redirectToRoute('index_achat');
        }


        return $this->render('superAdmin/Achat/new.html.twig', [
            'article' => '',
            'fournisseurs' => $fournisseurs,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/", name="index_achat")
     */
    public function index(): Response
    {
        $achats = $this->achatRepository->findAllAchat();
        return $this->render('superAdmin/Achat/index.html.twig', [
            'achats' => $achats
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_achat")
     */
    public function edit(Achat $achat, Request $request): Response
    {
        $fournisseurs = $this->fournisseurRepository->findAll();
        $articles = $this->articleRepository->findAll();
        $achatObjet = $this->achatRepository->getDetailAchat($achat);
        if ($request->isMethod('POST')) {
            $numero_achat = $request->get('numero_achat');
            $old_numero = $achat->getNumero();
            $articlesToDelete =explode(",", $request->get('articleToDelete')[0]);
            if ($old_numero != $numero_achat) {
                $achatArticleExiste = $this->achatRepository->findBy(array('numero' => $numero_achat));
                if ($achatArticleExiste) {
                    $this->addFlash('error', 'il ya une achat avec enregistrer avec le numero' . $numero_achat);
                    exit;
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
            }else{
                $this->addFlash('error', 'Aucun Achat trouvee');
                exit;
            }


            //input aricleachat
            $ref = $request->get('article');
            $puhtnet = $request->get('puhtnet');
            $qte = $request->get('qte');
            $pventettc = $request->get('pventettc');
            //delete Articles
            if ($articlesToDelete) {
                foreach ($articlesToDelete as $key => $value) {
                    $achatArticledelete = $this->achatArticleRepository->findAchatArticleByIdAricleAndIdAchat($value, $achat);
                    $this->em->remove( $achatArticledelete);
                    $this->em->flush();
                }

            }
            //insert new Article
            foreach ($ref as $key => $value) {
                $achatArticledeleteByref = $this->achatArticleRepository->findAchatArticleByIdAricleAndIdAchat($value, $achat);
                if(!$achatArticledeleteByref) {
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
                }else{
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
     * @param Request $request
     * @Route("/get/articles", name="get_articles", options={"expose" = true})
     */
    public function getArticles()
    {
        $articles = $this->articleRepository->findArticles();
        return $this->json($articles);

    }

    /**
     * @param Request $request
     * @Route("/get/articlesById", name="get_articles_byId", options={"expose" = true})
     */
    public function getArticleById(Request $request)
    {
        $article = $this->articleRepository->getArticleById($request->request->get('id_article'));
        return $this->json($article);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/get/articles/achat", name="get_articles_achat", options={"expose" = true})
     */
    public function getArticleByAchat(Request $request)
    {
        $idAchat = $request->get('id_achat');
        $articles_achat = $this->achatRepository->getDetailAchat($idAchat);
        $articles = [];
        foreach ($articles_achat[0]['achatArticles'] as $key => $value) {
            $articles[] = $value['article']['id'];
        }
        return $this->json($articles);

    }
}