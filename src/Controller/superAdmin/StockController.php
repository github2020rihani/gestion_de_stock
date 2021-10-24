<?php

namespace App\Controller\superAdmin;

use App\Entity\Inventaire;
use App\Entity\InventaireArticle;
use App\Repository\ArticleRepository;
use App\Repository\InventaireArticleRepository;
use App\Repository\InventaireRepository;
use App\Repository\PrixRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/super_admin/stock")
 */
class StockController extends AbstractController
{

    private $em;
    private $stockRepository;
    private $inventaireRepository;
    private $inventaireArticleRepository;
    private $articleRepository;
    private $prixRepository;

    public function __construct(EntityManagerInterface $em, StockRepository $stockRepository,
                                InventaireArticleRepository $inventaireArticleRepository,
                                ArticleRepository $articleRepository,
                                PrixRepository $prixRepository,
                                InventaireRepository $inventaireRepository)
    {
        $this->em = $em;
        $this->stockRepository = $stockRepository;
        $this->inventaireRepository = $inventaireRepository;
        $this->inventaireArticleRepository = $inventaireArticleRepository;
        $this->articleRepository = $articleRepository;
        $this->prixRepository = $prixRepository;

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="index_stock")
     */
    public function index()
    {
        $stocks = $this->stockRepository->findAll();
        return $this->render('superAdmin/stock/index.html.twig', array('stocks' => $stocks));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/maj/stock", name="maj_stock" , options={"expose" =true})
     */
    public function updateQteArticle(Request $request)
    {
        $newQte = $request->get('qte');
        $type = $request->get('type');
        $article = $request->get('id_article');
        if ((int)$newQte == 0) {
            $message = 'Aucun Modifiation';
            return $this->json(array('message' => $message, 'success' => true));
            exit;
        }

        $articleStocked = $this->stockRepository->findArticleInStockById((int)$article);
        if ($articleStocked && $articleStocked[0]) {

            if ($type == 'add') {
                $articleStocked[0]->setQte((int)$articleStocked[0]->getQte() + (int)$newQte);
                $articleStocked[0]->setInventer(false);

            } else {
                if ((int)$newQte > (int)$articleStocked[0]->getQte()) {
                    $message = 'La quantité est supérieur de la quantité du base ';
                    return $this->json(array('message' => $message, 'success' => false));
                } else {

                    $articleStocked[0]->setQte((int)$articleStocked[0]->getQte() - (int)$newQte);
                    $articleStocked[0]->setInventer(false);
                }
            }

            $this->em->persist($articleStocked[0]);
            $this->em->flush();

            //update qte in table prix
            $prixArticle = $this->prixRepository->findBy(array('article' => $this->articleRepository->find($article)));
            if ($prixArticle[0]) {
                $prixArticle[0]->setQte((int)$prixArticle[0]->getQte() + (int)$newQte);
                $this->em->persist($prixArticle[0]);
                $this->em->flush();

            } else {
                $message = 'Article n\'exste pas ';
                return $this->json(array('message' => $message, 'success' => false));
            }


            $message = 'La quantité a été modifier';
            $success = true;
        } else {
            $message = 'Aucun article trouver dans le stock ';
            $success = false;

        }

        return $this->json(array('message' => $message, 'success' => $success));

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/verifier/stock", name="verifier_stock" , options={"expose" =true})
     */
    public function verifierStock(Request $request)
    {
        $id_article = $request->get('id_article');
        $articleExiste = $this->articleRepository->find($id_article);
        $articleStocked = $this->stockRepository->findArticleInStockById($id_article);
        $numeInv = 'Inv_' . date('m' . '_' . date('Y'));
        $deleteOldTotalTTCQte =0 ;
        $deleteOldTotalTTCPrix = 0 ;
        $deleteOldTotalTTCTotal = 0 ;
        if ($articleExiste) {
            $dataArticle = $this->prixRepository->findByIdArticle($id_article);
            $inventExiste = $this->inventaireRepository->findBy(array('numero' => $numeInv));

            //verif if inventer in current Month
            $articleInventerInCurrentMonth = $this->inventaireArticleRepository->findInvByNum($numeInv, $id_article);
            if ($articleInventerInCurrentMonth && $articleInventerInCurrentMonth[0]) {

                //check qte prix et qte inventaire
                if ((int)$articleInventerInCurrentMonth[0]->getQte() != (int)$dataArticle[0]->getQte()
                    || (float)$dataArticle[0]->getPhAchatTTC() != (float)$articleInventerInCurrentMonth[0]->getPrAchatTTC()) {

                    if ((int)$articleInventerInCurrentMonth[0]->getQte() != (int)$dataArticle[0]->getQte()){
                        $deleteOldTotalTTCQte = (float)($dataArticle[0]->getPhAchatTTC() * (int)$articleInventerInCurrentMonth[0]->getQte());
                    }

                    if ((float)$dataArticle[0]->getPhAchatTTC() != (float)$articleInventerInCurrentMonth[0]->getPrAchatTTC()){
                        $deleteOldTotalTTCPrix = (float)((float)$articleInventerInCurrentMonth[0]->getPrAchatTTC() * (int)$dataArticle[0]->getQte());
                        //setter prix
                        $articleInventerInCurrentMonth[0]->setPrAchatTTC((float)$dataArticle[0]->getPhAchatTTC());
                        $articleInventerInCurrentMonth[0]->setPrAchatHT((float)$dataArticle[0]->getPuAchaHT());
                    }

                    $deleteOldTotalTTCTotal = $deleteOldTotalTTCQte + $deleteOldTotalTTCPrix ;





                    $articleInventerInCurrentMonth[0]->setQte((int)$dataArticle[0]->getQte());
                    $articleInventerInCurrentMonth[0]->setTotalTTC((float)($dataArticle[0]->getPhAchatTTC() * (int)$dataArticle[0]->getQte()));
                    $inventExiste[0]->setTotalTTC(((float)$inventExiste[0]->getTotalTTC() - (float)$deleteOldTotalTTCTotal) + ((float)($dataArticle[0]->getPhAchatTTC() * (int)$dataArticle[0]->getQte())));
                    $this->em->persist($articleInventerInCurrentMonth[0]);
                    $this->em->persist($inventExiste[0]);
                    //set invetaire
                    $articleStocked[0]->setInventer(true);
                    $this->em->persist($articleStocked[0]);
                    $this->em->flush();

                    return $this->json(array('success' => 'true', 'message' => 'Article a été inventé avec succès '));

                } else {
                    return $this->json(array('success' => 'false', 'message' => 'Article déja inventer ce mois'));

                }

            }
            if ($dataArticle) {
                //save in table Inventaire
                //verif si existe inventaire in current month
                $inventaireArticle = new InventaireArticle();
                if (!$inventExiste) {
                    //save new inventaire
                    $inventaire = new Inventaire();
                    $inventaire->setAddedBy($this->getUser());
                    $inventaire->setNumero($numeInv);
                    $inventaire->setTotalTTC((float)($dataArticle[0]->getPhAchatTTC() * (int)$dataArticle[0]->getQte()));
                    $this->em->persist($inventaire);
                    $this->em->flush();
                    $inventaireArticle->setInventaire($inventaire);

                } else {
                    $inventaireArticle->setInventaire($inventExiste[0]);
                    $inventExiste[0]->setTotalTTC((float)$inventExiste[0]->getTotalTTC() + ((float)($dataArticle[0]->getPhAchatTTC() * (int)$dataArticle[0]->getQte())));
                    $this->em->persist($inventExiste[0]);
                    $this->em->flush();

                }
                //save inventaire Article
                $inventaireArticle->setCreatedAt(new \DateTime('now'));
                $inventaireArticle->setArticle($articleExiste);
                $inventaireArticle->setPrAchatHT($dataArticle[0]->getPuAchaHT());
                $inventaireArticle->setPrAchatTTC($dataArticle[0]->getPhAchatTTC());
                $inventaireArticle->setQte($dataArticle[0]->getQte());
                $inventaireArticle->setTotalTTC((float)($dataArticle[0]->getPhAchatTTC() * (int)$dataArticle[0]->getQte()));
                $this->em->persist($inventaireArticle);

                $articleStocked[0]->setInventer(true);
                $this->em->persist($articleStocked[0]);
                $this->em->flush();

                $success = 'true';
                $message = 'Article a été inventé avec succès ';
            } else {
                //dataArticle n'existe pas
                $success = 'false';
                $message = 'Article n\'existe pas ';

            }
        } else {
            //article n'existe pas
            $success = 'false';
            $message = 'Article n\'existe pas ';
        }
        return $this->json(array('success' => $success, 'message' => $message));




    }


}