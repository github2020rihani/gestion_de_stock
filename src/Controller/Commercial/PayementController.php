<?php


namespace App\Controller\Commercial;


use App\Entity\ArticlesVendue;
use App\Entity\BondLivraison;
use App\Entity\Invoice;
use App\Entity\Payemet;
use App\Repository\ArticleRepository;
use App\Repository\ArticlesVendueRepository;
use App\Repository\BondLivraisonRepository;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use App\Repository\PayemetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PayementController
 * @package App\Controller\Commercial
 * @Route("/personelle/paiement")
 */
class PayementController extends AbstractController
{
    private $em;
    private $invoiceRepository;
    private $articleRepository;
    private $articlesVendueRepository;
    private $bondLivraisonRepository;
    private $devisRepository;
    private $payemetRepository;

    public function __construct(EntityManagerInterface $em, InvoiceRepository $invoiceRepository,
                                ArticleRepository $articleRepository,
                                DevisRepository $devisRepository,
                                PayemetRepository $payemetRepository,
                                BondLivraisonRepository $bondLivraisonRepository,
                                ArticlesVendueRepository $articlesVendueRepository)
    {
        $this->em = $em;
        $this->invoiceRepository = $invoiceRepository;
        $this->articleRepository = $articleRepository;
        $this->articlesVendueRepository = $articlesVendueRepository;
        $this->bondLivraisonRepository = $bondLivraisonRepository;
        $this->devisRepository = $devisRepository;
        $this->payemetRepository = $payemetRepository;

    }

    /**
     * @Route("/paiement/facture/{id}", name="perso_paiement_invoice")
     */
    public function formPayement(Invoice $id, Request $request)
    {
        $invoice = $this->invoiceRepository->find($id);
        if ($invoice) {
            if ($invoice->getExistBl()) {
                $dataInvoice = $this->invoiceRepository->getDataInvoice($id);
            } else {
                $dataInvoice = $this->invoiceRepository->getDataInvoiceSeul($id);
            }
        }
        if ($request->isMethod('post')) {

            $date = new \DateTime();
            //save Article Vendue
            self::saveArticleVendue($dataInvoice[0], $date);
            //save payement
            self::savePayement($dataInvoice[0], $request, $date);
            //generate recus payement
            self::generateRecupayement();


        }
        return $this->render('commercial/payement/form_payemnt.html.twig', array('invoice' => $dataInvoice[0],
                'PREFIX_FACT' => $_ENV['PREFIX_FACT'],
                'PREFIX_CUSTOMER' => $_ENV['PREFIX_CUSTOMER'])
        );
    }

    /**
     * @Route("/paiement2/facture/{id}", name="perso_paiement_invoice_part2")
     */
    public function checkPayement(Invoice $invoice, Request $request)
    {

        $payement = $this->payemetRepository->findPayementByIdInvoice($invoice->getId());
        $payementObject = $this->payemetRepository->find($payement['id']);
        $invoiceObject = $this->invoiceRepository->find($payement['invoice']['id']);
        if ($request->isMethod('post')) {

            //update payement
            self::savePayement2($payementObject[0], $invoiceObject[0], $request);

            //generate recus payement
            self::generateRecupayement();


        }
        dump($payement);
        return $this->render('commercial\payement2\form_payemnt.html.twig', array('payement' => $payement, 'PREFIX_FACT' => $_ENV['PREFIX_FACT']));

    }


    public function saveArticleVendue($dataInvoice, $date)
    {
        if ($dataInvoice['existBl']) {
            foreach ($dataInvoice['bonLivraison']['bonlivraisonArticles'] as $key => $article) {
                $article_vendue = new ArticlesVendue();
                $article_vendue->setCreatedAt($date);
                $article_vendue->setAddedBy($this->getUser());
                $article_vendue->setBl($this->bondLivraisonRepository->find($dataInvoice['bonLivraison']['id']));
                $this->em->persist($article_vendue);
                $this->em->flush();
            }
        } else {
            foreach ($dataInvoice['invoiceArticles'] as $key => $article) {
                $article_vendue = new ArticlesVendue();
                $article_vendue->setCreatedAt($date);
                $article_vendue->setAddedBy($this->getUser());
                $article_vendue->setInvoice($this->invoiceRepository->find($dataInvoice['id']));
                $this->em->persist($article_vendue);
                $this->em->flush();
            }

        }

    }

    public function savePayement($dataInvoice, $request, $date)
    {
        $invoiceObj = $this->invoiceRepository->find($dataInvoice['id']);
        $nameFloder = 'Facture_' . $dataInvoice['numero'] . '_' . $dataInvoice['year'];
        $uploadDir = $this->getParameter('uploads_directory');
        $mypath = $uploadDir . 'caisse/' . $nameFloder;
        $file_invoice = $request->files->get('file_invoice');
        $file_bl = $request->files->get('file_bl');
        $file_devi = $request->files->get('file_devi');
        $file_cheque = $request->files->get('file_cheque');
        $montant = $request->get('montant');
        $rest = $request->get('reste');
        $retenue = $request->get('retenue');
        $num_cheque = $request->get('num_cheque');
        $totalTTc = $dataInvoice['totalTTC'];
        $res = 0.000;
        $re = 0.000;
        $payement = new Payemet();
        $payement->setAddedBy($this->getUser());
        $payement->setCreatedAt(new \DateTime());
        $payement->setType('Facture');
        $payement->setTotalttc($totalTTc);
        $payement->setInvoice($invoiceObj);
        $filesCheque = [];
        if ($file_invoice) {
            $newFilenameInvoice = 'Facture_signer' . $dataInvoice['numero'] . '_' . $dataInvoice['year'] . $file_invoice->guessExtension();
            if (!is_dir($mypath)) {
                mkdir($mypath, 0777, TRUE);
            }
            $baseurlInvoice = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/caisse/' . $nameFloder . '/' . $newFilenameInvoice;
            $payement->setFileInvoice($baseurlInvoice);
            $file_invoice->move(
                $mypath,
                $newFilenameInvoice
            );
        }

        if ($dataInvoice['existBl']) {
            //change status devis et bl and upload file
            if ($file_bl) {
                $blObject = $this->bondLivraisonRepository->find($dataInvoice['bonLivraison']['id']);
                $blObject->setStatus(2);
                $this->em->persist($blObject);
                $this->em->flush();
                $newFilenameBl = 'bl_signer' . $dataInvoice['bonLivraison']['numero'] . '_' . $dataInvoice['bonLivraison']['year'] . $file_bl->guessExtension();
                if (!is_dir($mypath)) {
                    mkdir($mypath, 0777, TRUE);
                }
                $baseurlBl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/caisse/' . $nameFloder . '/' . $newFilenameBl;
                $payement->setFileBl($baseurlBl);
                $file_bl->move(
                    $mypath,
                    $newFilenameBl
                );
            }
            //file devis
            if ($file_devi) {
                $deviObject = $this->devisRepository->find($dataInvoice['bonLivraison']['devi']['id']);
                $deviObject->setStatus(2);
                $this->em->persist($deviObject);
                $this->em->flush();
                $newFilenameDevis = 'devi_signer' . $dataInvoice['bonLivraison']['devi']['numero'] . '_' . $dataInvoice['bonLivraison']['year'] . $file_devi->guessExtension();
                if (!is_dir($mypath)) {
                    mkdir($mypath, 0777, TRUE);
                }
                $baseurlDevis = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/caisse/' . $nameFloder . '/' . $newFilenameDevis;
                $payement->setFileBl($baseurlDevis);
                $file_devi->move(
                    $mypath,
                    $newFilenameDevis
                );
            }

            if ($dataInvoice['bonLivraison']['typePayement'] == 1) //espece
            {
                $payement->setTypePayement(1);
                if ((float)$montant > (float)$totalTTc) {
                    $re = (float)$totalTTc - (float)$montant;
                    $res = 0.000;
                } elseif ((float)$montant < (float)$totalTTc) {
                    $res = (float)$totalTTc - (float)$montant;
                    $re = 0.000;
                } else {
                    $res = 0.000;
                    $re = 0.000;
                }
                $payement->setReste((float)(number_format((float)$res, 3)));
                $payement->setRetenu((float)(number_format($re, 3)));
                $payement->setMontant((float)(number_format($montant, 3)));
                if ($res != 0.000) {
                    $invoiceObj->setStatus(3);

                } else {
                    $invoiceObj->setStatus(2);

                }
                $this->em->persist($invoiceObj);
                $this->em->flush();

            } else {
                //cheque
                $payement->setTypePayement(2);
                $payement->setNumeroCheque($num_cheque);
                if ($file_cheque) {
                    $newFilenameCheque = 'cheque_' . $dataInvoice['numero'] . '_' . $dataInvoice['year'] . $file_cheque->guessExtension();
                    if (!is_dir($mypath)) {
                        mkdir($mypath, 0777, TRUE);
                    }
                    $baseurlCheque = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/caisse/' . $nameFloder . '/' . $newFilenameCheque;
                    $file_cheque->move(
                        $mypath,
                        $newFilenameCheque
                    );
                    $filesCheque[] = $baseurlCheque;
                }
                $payement->setFileCheque($filesCheque);


                if ((float)$montant > (float)$totalTTc) {
                    $re = (float)$totalTTc - (float)$montant;
                    $res = 0.000;
                } elseif ((float)$montant < (float)$totalTTc) {
                    $res = (float)$totalTTc - (float)$montant;
                    $re = 0.000;
                } else {
                    $res = 0.000;
                    $re = 0.000;
                }
                $payement->setReste((float)(number_format((float)$res, 3)));
                $payement->setRetenu((float)(number_format($re, 3)));
                $payement->setMontant((float)(number_format($montant, 3)));
                if ($res != 0.000) {
                    $invoiceObj->setStatus(3);

                } else {
                    $invoiceObj->setStatus(2);

                }
                $this->em->persist($invoiceObj);
                $this->em->flush();


            }
        } else {

            //invoice seul
            if ($dataInvoice['typePayement'] == 1) //espece
            {
                $payement->setTypePayement(1);
                if ((float)$montant > (float)$totalTTc) {
                    $re = (float)$totalTTc - (float)$montant;
                    $res = 0.000;
                } elseif ((float)$montant < (float)$totalTTc) {
                    $res = (float)$totalTTc - (float)$montant;
                    $re = 0.000;
                } else {
                    $res = 0.000;
                    $re = 0.000;
                }
                $payement->setReste((float)(number_format((float)$res, 3)));
                $payement->setRetenu((float)(number_format($re, 3)));
                $payement->setMontant((float)(number_format($montant, 3)));

                if ($res != 0.000) {
                    $invoiceObj->setStatus(3);

                } else {
                    $invoiceObj->setStatus(2);

                }
                $this->em->persist($invoiceObj);
                $this->em->flush();

            } else {
                //cheque
                $payement->setTypePayement(2);
                $payement->setNumeroCheque($num_cheque);
                if ($file_cheque) {

                    $newFilenameCheque = 'cheque_' . $dataInvoice['numero'] . '_' . $dataInvoice['year'] . $file_cheque->guessExtension();
                    if (!is_dir($mypath)) {
                        mkdir($mypath, 0777, TRUE);
                    }
                    $baseurlCheque = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/caisse/' . $nameFloder . '/' . $newFilenameCheque;
                    $file_cheque->move(
                        $mypath,
                        $newFilenameCheque
                    );
                    $filesCheque[] = $baseurlCheque;

                }
                $payement->setFileCheque($filesCheque);

                if ((float)$montant > (float)$totalTTc) {
                    $re = (float)$totalTTc - (float)$montant;
                    $res = 0.000;
                } elseif ((float)$montant < (float)$totalTTc) {
                    $res = (float)$totalTTc - (float)$montant;
                    $re = 0.000;
                } else {
                    $res = 0.000;
                    $re = 0.000;
                }
                $payement->setReste((float)(number_format((float)$res, 3)));
                $payement->setRetenu((float)(number_format($re, 3)));
                $payement->setMontant((float)(number_format($montant, 3)));
                if ($res != 0.000) {
                    $invoiceObj->setStatus(3);

                } else {
                    $invoiceObj->setStatus(2);

                }
                $this->em->persist($invoiceObj);
                $this->em->flush();


            }
        }
        //save file facture


        $this->em->persist($payement);
        $this->em->flush();


    }

    public function savePayement2($payementObject, $invoiceObject, $request)
    {
        $file_cheque = $request->files->get('file_cheque');
        $num_cheque = $request->get('num_cheque');
        $nameFloder = 'Facture_' . $invoiceObject['numero'] . '_' . $invoiceObject['year'];
        $uploadDir = $this->getParameter('uploads_directory');
        $mypath = $uploadDir . 'caisse/' . $nameFloder;
        $montant = $request->get('montant');
        $rest = $request->get('reste');
        $retenue = $request->get('retenue');
        $totalTTc = $payementObject['totalTTC'];
        $res = 0.000;
        $re = 0.000;
        $filesCheque = [];
        $typesPayement = [];
        $date = new \DateTime();
        $fc = $payementObject->getFileCheque();
        foreach ($fc as $key => $value) {
            $filesCheque[] = $value;

        }
        $types_apayements = $payementObject->getTypesPayements();
        foreach ($types_apayements as $key => $value) {
            $typesPayement[] = $value;

        }


        if ($filesCheque) {
            $typesPayement[] = 'Cheque';

        }else{
            $typesPayement[] = 'Espece';

        }
        $payementObject->setTypesPayements($typesPayement);
        $payementObject->setAddedBy($this->getUser());
        $payementObject->setCreatedAt(new \DateTime());

        if ((float)$montant + (float)$payementObject->getMontant() > (float)$totalTTc) {
            $re = (float)$totalTTc - (float)$montant + (float)$payementObject->getMontant();
            $res = 0.000;
        } elseif ((float)$montant + (float)$payementObject->getMontant() < (float)$totalTTc) {
            $res = (float)$totalTTc - (float)$montant + (float)$payementObject->getMontant();
            $re = 0.000;
        } else {
            $res = 0.000;
            $re = 0.000;
        }
        $payementObject->setReste((float)(number_format((float)$res, 3)));
        $payementObject->setRetenu((float)(number_format($re, 3)));
        $payementObject->setMontant((float)$payementObject->getMontant() + (float)(number_format($montant, 3)));
        if ($res != 0.000) {
            $invoiceObject->setStatus(3);

        } else {
            $invoiceObject->setStatus(2);

        }
        $this->em->persist($invoiceObject);
        $this->em->flush();

        if ($file_cheque) {
            $newFilenameCheque = 'cheque_'.$date->getTimestamp().'_'. $invoiceObject['numero'] . '_' . $invoiceObject['year'] . $file_cheque->guessExtension();

            $baseurlCheque = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/caisse/' . $nameFloder . '/' . $newFilenameCheque;
            $file_cheque->move(
                $mypath,
                $newFilenameCheque
            );
            $filesCheque[] = $baseurlCheque;
        }

        $payementObject->setFileCheque($filesCheque);

        if ($num_cheque) {
            $payementObject->setNumeroCheque($num_cheque);
        }


        $this->em->persist($payementObject);
        $this->em->flush();


    }

    public function generateRecupayement()
    {
    }


}