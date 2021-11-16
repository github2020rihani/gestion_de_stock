<?php


namespace App\Controller\Commercial;

use App\Entity\Devis;
use App\Entity\DevisArticle;
use App\Repository\ArticleRepository;
use App\Repository\BondLivraisonRepository;
use App\Repository\ClientRepository;
use App\Repository\DevisArticleRepository;
use App\Repository\DevisRepository;
use App\Repository\PrixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("personelle/devis")
 */
class DevisController extends AbstractController
{

    private $em;
    private $devisRepository;
    private $clientRepository;
    private $prixRepository;
    private $articleRepository;
    private $devisArticleRepository;
    private $bondLivraisonRepository;

    /**
     * DevisController constructor.
     * @param EntityManagerInterface $em
     * @param DevisRepository $devisRepository
     * @param ClientRepository $clientRepository
     */
    public function __construct(EntityManagerInterface $em, DevisRepository $devisRepository,
                                PrixRepository $prixRepository, ArticleRepository $articleRepository,
                                BondLivraisonRepository $bondLivraisonRepository,
                                DevisArticleRepository $devisArticleRepository,
                                ClientRepository $clientRepository)
    {
        $this->em = $em;
        $this->devisRepository = $devisRepository;
        $this->clientRepository = $clientRepository;
        $this->prixRepository = $prixRepository;
        $this->articleRepository = $articleRepository;
        $this->devisArticleRepository = $devisArticleRepository;
        $this->bondLivraisonRepository = $bondLivraisonRepository;
    }

    /**
     * @Route("/", name="perso_index_devis")
     */
    public function index()
    {
        $devis = $this->devisRepository->findAll();
        return $this->render('commercial/devis/index.html.twig', array('devis' => $devis, 'perfex_devis' => $_ENV['PREFIX_DEVI']));

    }

    /**
     * @Route("/add", name="perso_add_devis")
     */
    public function add(Request $request)
    {

        $customers = $this->clientRepository->findAll();
        if ($request->isMethod('post')) {
            $client = $this->clientRepository->find($request->request->get('customers'));
            $qte = $request->request->get('qte');
            $articles = $request->request->get('article');
            $year =date('Y');
            $lastDevis = $this->devisRepository->getLastDevisWithCurrentYear($year);

            if ($lastDevis) {
                $lastId = 000 + $lastDevis->getId() + 1;
                $numero_devis =  '000' . $lastId;
            } else {
                $numero_devis =  '0001';
            }
            //chek devis exite
            $devis = $this->devisRepository->findBy(array('numero' => $numero_devis));
            if ($devis) {
                $this->addFlash('error', 'Un devis existe  déja avec ce numero ');
                return $this->redirectToRoute('perso_index_devis');
            } else {
                $devis = new Devis();

                $devis->setNumero($numero_devis);
                $devis->setYear($year);
                $devis->setCreadetBy($this->getUser());
                $devis->setClient($client);
                $totalTTc = 0;
                foreach ($articles as $key => $id_art) {
                    $article = $this->prixRepository->getArticleById($id_art);
                    if ($article[0]) {
                        if ($qte[$key] > $article[0]['qte']) {
                            $this->addFlash('error', 'La quatité est depasser le sock ');
                            return $this->redirectToRoute('perso_index_devis');
                        }
                        $devisArticle = new DevisArticle();
                        $devisArticle->setQte($qte[$key]);
                        $devisArticle->setPventettc($article[0]['puVenteTTC']);
                        $devisArticle->setTotal((float)$article[0]['puVenteTTC'] * (int)$qte[$key]);
                        $devisArticle->setRemise($article[0]['article']['remise']);
                        $devisArticle->setArticle($this->articleRepository->find($id_art));
                        $devisArticle->setDevi($devis);
                        $this->em->persist($devisArticle);
                        $totalTTc = $totalTTc + (float)$devisArticle->getTotal();
                    }
                }
                $devis->setTotalTTC($totalTTc);
                $this->em->persist($devis);
                $this->em->flush();

                $this->addFlash('success', 'Ajoute a été effectuer avec succès');
                return $this->redirectToRoute('perso_index_devis');
            }


        }


        return $this->render('commercial/devis/add.html.twig', array('customers' => $customers));


    }

    /**
     * @Route("/detail/{id}", name="perso_detail_devis")
     */
    public function detail(Devis $id_devis)
    {

        $devi = $this->devisRepository->findDetailDevi($id_devis);
        return $this->render('commercial/devis/detail.html.twig', array('devi' => $devi[0], 'perfex_devis' => $_ENV['PREFIX_DEVI']));
    }


    /**
     * @param Request $request
     * @Route("/edit/{id}", name="perso_edit_devis")
     */
    public function edit(Request $request, Devis $idDevis)
    {
        $customers = $this->clientRepository->findAll();
        $articles = $this->articleRepository->findBy(array('stocked' => true));
        $devi = $this->devisRepository->findDetailDeviAndStock($idDevis);
        //request method post edit devis
        if ($request->isMethod('post')) {
            $client = $this->clientRepository->find($request->request->get('customers'));
            $qte = $request->request->get('qte');
            $articlesNew = $request->request->get('article');
            //delete Articles
            if ($devi[0]) {
                $devisExiste = $this->devisRepository->find($devi[0]['id']);
                //update devis existe
                $devisExiste->setCreadetBy($this->getUser());
                $devisExiste->setClient($client);
                $totalTTc = 0;
                //delete old article
                $old_articles = $this->devisArticleRepository->findBy(array('devi' =>$devi[0]['id']));
                if ($old_articles) {
                    foreach ($old_articles as $key => $value) {
                        $this->em->remove($value);
                        $this->em->flush();
                    }
                }


                foreach ($articlesNew as $key => $value) {
                    $prixArticle = $this->prixRepository->getArticleById($value);

                    //save new article devis
                    //check qte
                    if ($qte[$key] > $prixArticle[0]['qte']) {
                        $this->addFlash('error', 'La quatité est depasser le sock ');
                        return $this->redirectToRoute('perso_index_devis');
                    }
                    $devisArticle = new DevisArticle();
                    $devisArticle->setQte($qte[$key]);
                    $devisArticle->setPventettc($prixArticle[0]['puVenteTTC']);
                    $devisArticle->setTotal((float)$prixArticle[0]['puVenteTTC'] * (int)$qte[$key]);
                    $devisArticle->setRemise($prixArticle[0]['article']['remise']);
                    $devisArticle->setArticle($this->articleRepository->find($value));
                    $devisArticle->setDevi($devisExiste);
                    $this->em->persist($devisArticle);
                    $totalTTc = $totalTTc + (float)$devisArticle->getTotal();
                }

                $devisExiste->setTotalTTC($totalTTc);
                if ($devisExiste->getStatusMaj() == true) {
                    $devisExiste->setStatusMaj(false);
                }

                if ($devisExiste->getStatus() == 1) {
                    $devisExiste->setStatus(0);
                    //delete old bl and invoice
                    $bl = $this->bondLivraisonRepository->findBy(array('devi' => $devisExiste->getId()));
                    if ($bl && $bl[0]) {
                        $blExiste = $this->bondLivraisonRepository->find($bl[0]->getId());
                        $this->em->remove($blExiste);
                        $this->em->flush();
                    }

                }
                $this->em->persist($devisExiste);
                $this->em->flush();


                return $this->redirectToRoute('perso_index_devis');

            }
        }


        return $this->render('commercial/devis/edit.html.twig', array(
            'customers' => $customers,
            'articles' => $articles,
            'devi' => $devi[0]));

    }

    /**
     * @param Request $request
     * @Route("api/print/devis", name="perso_print_devis" , options={"expose" = true})
     */
    public function printDevis(Request $request)
    {
        $id_devis = $request->get('id_devis');
        $devis = $this->devisRepository->findDetailDeviAndStock($id_devis);
        $uploadDir = $this->getParameter('uploads_directory');
        $date = new \DateTime();
        $devObj = $this->devisRepository->find($devis[0]['id']);

        if ($devObj->getStatusMaj() == false) {
            $success = true;
            $message = '';
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            $pdfOptions->set('isPhpEnabled', 'true');
            $dompdf = new Dompdf($pdfOptions);
            $html = $this->renderView('commercial/devis/print_devis.html.twig', [
                'devis' => $devis[0]
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
            $mypath = $uploadDir. 'coffreFort/customer_'.$devis[0]['client']['id'].'/dossier_'.$devis[0]['id'];
            $pdfFilepath = $uploadDir. 'coffreFort/customer_'.$devis[0]['client']['id'].'/dossier_'.$devis[0]['id'].'devis.pdf';
            if (!is_dir($mypath)){
                mkdir($mypath,0777,TRUE);

            }
            file_put_contents($pdfFilepath, $output);


            //save file inventaire
            $devObj = $this->devisRepository->find($devis[0]['id']);
            $devObj->setFile($pdfFilepath);
            $this->em->persist($devObj);
            $this->em->flush();
            $file_with_path = $pdfFilepath ;
            $response = new BinaryFileResponse ($file_with_path);
            $response->headers->set('Content-Type', 'application/pdf; charset=utf-8', 'application/force-download');
            $response->headers->set('Content-Disposition', 'attachment; filename=devis.pdf');
        } else {
            $mssage = 'Cette devis est modifiable , veuillez modifier cette devis ';
            $success = false;
            $response = null;
        }
        return $response ;

    }

    /**
     * @param Request $request
     * @Route("api/print/d", name="perso_print_devis2" )
     */
    public function printDevis2(Request $request)
    {
        $devis = $this->devisRepository->findDetailDeviAndStock(10);
        return $this->render('commercial/devis/print_devis.html.twig', [
            'devis' => $devis[0]
        ]);


    }



}