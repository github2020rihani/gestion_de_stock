<?php


namespace App\Controller\Commercial;

use App\Entity\Devis;
use App\Entity\DevisArticle;
use App\Repository\ArticleRepository;
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

    /**
     * DevisController constructor.
     * @param EntityManagerInterface $em
     * @param DevisRepository $devisRepository
     * @param ClientRepository $clientRepository
     */
    public function __construct(EntityManagerInterface $em, DevisRepository $devisRepository,
                                PrixRepository $prixRepository, ArticleRepository $articleRepository,
                                DevisArticleRepository $devisArticleRepository,
                                ClientRepository $clientRepository)
    {
        $this->em = $em;
        $this->devisRepository = $devisRepository;
        $this->clientRepository = $clientRepository;
        $this->prixRepository = $prixRepository;
        $this->articleRepository = $articleRepository;
        $this->devisArticleRepository = $devisArticleRepository;
    }

    /**
     * @Route("/", name="perso_index_devis")
     */
    public function index()
    {
        $devis = $this->devisRepository->findAll();
        return $this->render('commercial/devis/index.html.twig', array('devis' => $devis));

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
            $lastDevis = $this->devisRepository->getLastDevis();
            if ($lastDevis) {
                $lastId = 000 + $lastDevis->getId() + 1;
                $numero_devis = $_ENV['PERFIX_DEVIS'] . '000' . $lastId;
            } else {
                $numero_devis = $_ENV['PERFIX_DEVIS'] . '0001';
            }
            //chek devis exite
            $devis = $this->devisRepository->findBy(array('numero' => $numero_devis));
            if ($devis) {
                $this->addFlash('error', 'Un devis existe  déja avec ce numero ');
                return $this->redirectToRoute('perso_index_devis');
            } else {
                $devis = new Devis();

                $devis->setNumero($numero_devis);
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
        return $this->render('commercial/devis/detail.html.twig', array('devi' => $devi[0]));
    }


    /**
     * @param Request $request
     * @Route("/edit/{id}", name="perso_edit_devis")
     */
    public function edit(Request $request, Devis $idDevis)
    {
        $customers = $this->clientRepository->findAll();
        $articles = $this->articleRepository->findAll();
        $devi = $this->devisRepository->findDetailDeviAndStock($idDevis);
        //request method post edit devis
        if ($request->isMethod('post')) {
            $client = $this->clientRepository->find($request->request->get('customers'));
            $qte = $request->request->get('qte');
            $articlesNew = $request->request->get('article');
            //delete Articles
            $articlesToDelete = explode(",", $request->get('articleToDelete')[0]);
            if ($devi[0]) {
                $devisExiste = $this->devisRepository->find($devi[0]['id']);
                //update devis existe
                $devisExiste->setCreadetBy($this->getUser());
                $devisExiste->setClient($client);
                $totalTTc = 0;

                foreach ($articlesNew as $key => $value) {
                    $articleExiste = $this->devisArticleRepository->findBy(array('article' => $value, 'devi' => $idDevis));
                    $prixArticle = $this->prixRepository->getArticleById($value);
                    if ($articleExiste && $articleExiste[0]) {
                        //update last article devis
                        //check qte
                        if ($qte[$key] > $prixArticle[0]['qte']) {
                            $this->addFlash('error', 'La quatité est depasser le sock ');
                            return $this->redirectToRoute('perso_index_devis');
                        }
                        $articleExiste[0]->setQte($qte[$key]);
                        $articleExiste[0]->setPventettc($prixArticle[0]['puVenteTTC']);
                        $articleExiste[0]->setTotal((float)$prixArticle[0]['puVenteTTC'] * (int)$qte[$key]);
                        $articleExiste[0]->setRemise($prixArticle[0]['article']['remise']);
                        $articleExiste[0]->setArticle($this->articleRepository->find($value));
                        $articleExiste[0]->setDevi($devisExiste);
                        $this->em->persist($articleExiste[0]);
                        $totalTTc = $totalTTc + (float)$articleExiste[0]->getTotal();

                    } else {
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

                }
                $devisExiste->setTotalTTC($totalTTc);
                $this->em->persist($devisExiste);
                $this->em->flush();
                //delete article devis

                if ($articlesToDelete && $articlesToDelete[0]) {
                    foreach ($articlesToDelete as $key => $item) {
                        $articleExisteTodelete = $this->devisArticleRepository->findBy(array('article' => $item, 'devi' => $idDevis));
                        $this->em->remove($articleExisteTodelete[0]);
                        $this->em->flush();

                    }
                }


            }
            return $this->redirectToRoute('perso_index_devis');


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