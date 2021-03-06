<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllWithCategoryAndTags(),
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugify $slugify, \Swift_Mailer $mailer): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);
            $author = $this->getUser();
            $article->setAuthor($author);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $message = (new \Swift_Message('Un nouvel article vient d\'être publié !'))
                ->setFrom('zboubilarge@gmail.com')
                ->setTo($this->getParameter('mailer_from')) //recupere l'email defini dans env.local MAILER_FROM_ADDRESS
                ->setBody($this->renderView('article/email/notification.html.twig', [
                    'article' => $article
                ]), 'text/html');


            $mailer->send($message);

            $this->addFlash('success', 'Article ajouté avec un succés certain');
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article, Slugify $slugify): Response
    {

        if($this->getUser() === $article->getAuthor() || $this->isGranted('ROLE_ADMIN')){

            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $slug = $slugify->generate($article->getTitle());
                $article->setTitle($slug);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Article modifié avec un succés certain');

                return $this->redirectToRoute('article_index', [
                    'id' => $article->getId(),
                ]);
            }

            return $this->render('article/edit.html.twig', [
                'article' => $article,
                'form' => $form->createView(),
            ]);
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        $this->addFlash('danger', 'Article supprimé. C\'est triste mais c\'est la vie.');

        return $this->redirectToRoute('article_index');
    }

    /**
     * @Route("/{id}/favorite", name="article_favorite", methods={"GET", "POST"})
     */
    public function favorite(Article $article, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if($this->getUser()->getFavorites()->contains($article)){
            $user->removeFavorite($article);
        }else{
            $user->addFavorite($article);
        }

        $em->flush();

        return $this->json(['isFavorite' => $this->getUser()->isFavorite($article)]);

    }
}
