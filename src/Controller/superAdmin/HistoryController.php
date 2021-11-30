<?php


namespace App\Controller\superAdmin;


use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super_admin/historique")
 */
class HistoryController extends AbstractController
{

    private $em;
    private $historyRepository;

    public function __construct(EntityManagerInterface $em, HistoryRepository $historyRepository)
    {
        $this->em = $em;
        $this->historyRepository = $historyRepository;

    }

    /**
     * @Route("/bl", name="sa_history_bl")
     */
    public function index()
    {
        $data = [];
        $prefixFile = [];
        $history = $this->historyRepository->findAllFiles();
        if ($history) {
            foreach ($history as $key => $value) {
                if ($value->getBl()) {
                    if (!in_array($value->getBl()->getId(), $prefixFile)) {
                        $prefixFile[$value->getBl()->getId()] = $value->getBl()->getId();

                    }
                    $data[$prefixFile[$value->getBl()->getId()]][] = $value;
                }

            }

        }
//        dd($prefixFile);
        dd($data);

    }


    /**
     * @Route("/invoice", name="sa_history_invoice")
     */
    public function indexInvoice()
    {
        $data = [];
        $prefixFile = [];
        $history = $this->historyRepository->findAllFiles();
        if ($history) {
            foreach ($history as $key => $value) {
                if ($value->getInvoice()) {
                    if (!in_array($value->getInvoice()->getId(), $prefixFile)) {
                        $prefixFile[$value->getInvoice()->getId()] = $value->getInvoice()->getId();

                    }
                    $data[$prefixFile[$value->getInvoice()->getId()]][] = $value;
                }

            }

        }
//        dd($prefixFile);
        dd($data);

    }

}