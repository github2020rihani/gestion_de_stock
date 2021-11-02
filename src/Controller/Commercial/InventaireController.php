<?php


namespace App\Controller\Commercial;


use App\Repository\InventaireArticleRepository;
use App\Repository\InventaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personelle/inventaires")
 */
class InventaireController extends AbstractController
{

    private $em;
    private $inventaireRepository;

    public function __construct(EntityManagerInterface $em, InventaireRepository $inventaireRepository)
    {
        $this->em = $em;
        $this->inventaireRepository = $inventaireRepository;
    }

    /**
     * @Route("/", name="perso_index_inventaire")
     */
    public function index(PaginatorInterface $paginator, Request $request,
                          InventaireArticleRepository $inventaireArticleRepository)
    {

        $invtaires = $this->inventaireRepository->findAll();
        if ($invtaires) {
            $lastInventaire = $this->inventaireRepository->findLastInventaire();
            if ($lastInventaire) {
                $query = $inventaireArticleRepository->getDataInventaire($lastInventaire[0]->getId());
                $dataInv = $paginator->paginate(
                    $query, /* query NOT result */
                    $request->query->getInt('page', 1), /*page number*/
                    10 /*limit per page*/
                );
            }

            return $this->render('commercial/inventaire/index.html.twig',
                array('inventaires' => $invtaires, 'inv' => $lastInventaire[0],
                    'lastInv' => $dataInv));
        } else {

            return $this->redirectToRoute('dashboard_personelle');
        }

    }

    /**
     * @param Request $request
     * @Route("/select/inventaire", name="get_inventaire", options={"expose" =true})
     */
    public function getInventaire(Request $request, InventaireArticleRepository $inventaireArticleRepository,
                                  PaginatorInterface $paginator)
    {
        $id_inv = $request->get('id_inv');
        //verif Exist inventaire
        $inventaire = $this->inventaireRepository->find($id_inv);
        if ($inventaire) {
            $query = $this->inventaireRepository->getDataInventaire($inventaire->getId());

            $dataInv = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
            $status = 'true';
            $content = $this->render('commercial/inventaire/_contentInventaire.html.twig', array(
                'inv' => $dataInv

            ))->getContent();
        } else {
            $status = 'false';
            $message = 'Aucun Inventaire existe';
            $content = '';
        }
        return $this->json(array('content' => $content, 'status' => $status));

    }


    /**
     * @Route("/imprime/inventaire", name="perso_imprime_inventaire", options={"expose" =true})
     */
    public function ImprimerInvetaire()
    {
        $uploadDir = $this->getParameter('uploads_directory');
        $lastInventaire = $this->inventaireRepository->findLastInventaire();
        $dataInv = [];
        if ($lastInventaire){
            $dataInv = $this->inventaireRepository->getDataInventaire($lastInventaire[0]->getId());
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            $pdfOptions->set('isPhpEnabled', 'true');
            $dompdf = new Dompdf($pdfOptions);
            $html = $this->renderView('commercial/inventaire/print_inventaire.html.twig', [
                'inv' => $dataInv[0]
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
            $pdfFilepath = $uploadDir. 'inventaire/'.$dataInv[0]['numero'].'.pdf';
            file_put_contents($pdfFilepath, $output);

            //save file inventaire
            $inv = $this->inventaireRepository->find($dataInv[0]['id']);
            $inv->setFilePdf($dataInv[0]['numero'].'.pdf');
            $this->em->persist($inv);
            $this->em->flush();


            $file_with_path = $uploadDir .'inventaire/'.$dataInv[0]['numero'].'.pdf';
            $response = new BinaryFileResponse ($file_with_path);
            $response->headers->set('Content-Type', 'application/pdf; charset=utf-8', 'application/force-download');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$dataInv[0]['numero'].'.pdf');
        }else{
            $response = null ;
        }

        return $response ;
    }

    /**
     * @Route("/excel/inventaire", name="ownload_excel_inventaire", options={"expose" =true})
     */
    public function downloadFileExcel() {
        $uploadDir = $this->getParameter('uploads_directory');
        $lastInventaire = $this->inventaireRepository->findLastInventaire();
        $dataInv = [];
        if ($lastInventaire){
            $dataInv = $this->inventaireRepository->getDataInventaire($lastInventaire[0]->getId());
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($dataInv[0]['numero']);
            $this->setCellHeader($sheet);
            $this->setCellBody($dataInv[0], $sheet);
            $sheet->getStyle('A1:F1')->getFont()->setBold(true);
            $sheet->getStyle('A1:F1')->getBorders()->setDiagonalDirection(true);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $fileName = $dataInv[0]['numero'].'.xlsx';
            $path = $uploadDir . 'inventaire';
            $excelFilepath = $path . '/' . $fileName;
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($excelFilepath);
            $writer->save($temp_file);


            //save file inventaire
            $inv = $this->inventaireRepository->find($dataInv[0]['id']);

            $inv->setFileExel($dataInv[0]['numero'].'.xlsx');

            $this->em->persist($inv);
            $this->em->flush();


            $file_with_path = $uploadDir .'inventaire/'.$dataInv[0]['numero'].'.xlsx';
            $response = new BinaryFileResponse ($file_with_path);
            $response->headers->set('Content-Type', 'application/octetstream; charset=utf-8', 'application/force-download');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$dataInv[0]['numero'].'.xlsx');
        }else{
            $response = null ;
        }

        return $response ;
    }



    function setCellHeader($sheet)
    {
        $sheet->setCellValue('A1', 'Ref article');
        $sheet->setCellValue('B1', 'Description');
        $sheet->setCellValue('C1', 'PU ACHAT NET');
        $sheet->setCellValue('D1', 'PU ACHAT TTC');
        $sheet->setCellValue('E1', 'QTE');
        $sheet->setCellValue('F1', 'TOTAL TTC');
    }

    function setCellBody($data, $sheet)
    {
        $startRow = 2;
        $row = $startRow;
        $count_invArt = 0 ;
        foreach ($data['inventaireArticles'] as $key => $value) {
            $i = $row++;
            $count_invArt = $row++ ;
            $sheet->setCellValue("A{$i}", $value['article']['ref']);
            $sheet->setCellValue("B{$i}", $value['article']['description']);
            $sheet->setCellValue("C{$i}", $value['prAchatHT']);
            $sheet->setCellValue("D{$i}", $value['prAchatTTC']);
            $sheet->setCellValue("E{$i}", $value['qte']);
            $sheet->setCellValue("F{$i}", $value['totalTTC']);

        }
//        $sheet->setCellValue("A{$count_invArt}:E{$count_invArt}", $value['totalTTC']);
//        $sheet->getStyle("A{$count_invArt}:F{$count_invArt}")->getFont()->setBold(true);
//
//        $sheet->getStyle("A{$count_invArt}:F{$count_invArt}")->getBorders()->setDiagonalDirection(true);
    }




}