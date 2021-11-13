<?php


namespace App\Controller\Commercial;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personelle/article")
 */
class ArticleController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @Route("/add", name="perso_add_article")
     */
    public function add(Request $request, ArticleRepository $articleRepository) {

        $article = new Article();
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $articleexiste = $articleRepository->findBy(array('ref' => $form->get('ref')->getData()));
            if (!$articleexiste){
                $article->setAddedBy($this->getUser());
                $this->em->persist($article);
                $this->em->flush();
                $this->addFlash('success','Ajout effectué avec succés');

            }else{
                $this->addFlash('error','Il y a un article existe avec ce reference ');
                return $this->render('commercial/Article/new.html.twig',[
                    'form' => $form->createView(),
                    'article' => ''
                ]);
            }


            return $this->redirectToRoute('perso_index_article');
        }

        return $this->render('commercial/Article/new.html.twig',[
            'form' => $form->createView(),
            'article' => ''
        ]);
    }

    /**
     * @Route("/", name="perso_index_article")
     */
    public function index( Request $request , EntityManagerInterface $em, ArticleRepository  $articleRepository): Response
    {
        $articles = $articleRepository->findBy(array('stocked' => true));
        return $this->render('commercial/Article/index.html.twig',[
            'articles' =>$articles
        ]);
    }


    /**
     * @param Request $request
     * @Route("/edit/{id}", name="perso_edit_article")
     */
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository) {

        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $oldRef = $article->getRef();
            if ($oldRef != $form->get('ref')->getData()) {
                $articleexiste = $articleRepository->findBy(array('ref' => $form->get('ref')->getData()));
                if (!$articleexiste) {
                    $article->setAddedBy($this->getUser());

                    $this->em->persist($article);
                    $this->em->flush();
                    $this->addFlash('success','Modifier effectué avec succés');

                }else{
                    $this->addFlash('error','Il y a un article existe avec ce reference ');

                    return $this->render('commercial/Article/new.html.twig',[
                        'form' => $form->createView(),
                        'article' => $article
                    ]);
                }
            }else{
                $article->setAddedBy($this->getUser());
                $this->em->persist($article);
                $this->em->flush();
                $this->addFlash('success','Modifier effectué avec succés');
            }

            return $this->redirectToRoute('perso_index_article');
        }

        return $this->render('commercial/Article/new.html.twig',[
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

}