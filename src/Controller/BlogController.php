<?php
// src/Controller/BlogController.php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/blog", name="blog_")
 */

class BlogController extends AbstractController
{
    /**
     * @Route("", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles){
            throw $this->createNotFoundException('No article found in article\'s table.');
        }

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @param string $slug
     * @Route("/show/{slug}", requirements={"slug"="[a-z0-9-]+"}, name="show")
     * @return Response A response instance
     */
    public function show(?string $slug = 'article sans titre')
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);


        if (!$article) {
            throw $this->createNotFoundException(
                'No article with '.$slug.' title, found in article\'s table.'
            );
        }

        return $this->render('blog/show.html.twig', [
            'slug' => ucwords(str_replace('-', ' ',($slug))),
            'article' => $article,
        ]);
    }

    /**
     * @param string $scategoryName
     * @Route("/category/{categoryName}", requirements={"categoryName"="[a-zA-Z0-9-]+"}, name="_category")
     * @return Response A response instance
     */
    public function showByCategory(string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'Desc'],
                3
                );

        if (!$category) {
            throw $this->createNotFoundException(
                'No category with ' . $category . ' title, found in article\'s table.'
            );
        }

        if (!$articles) {
            throw $this->createNotFoundException(
                'No category with ' . $category . ' title, found in article\'s table.'
            );
        }

        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'articles' => $articles
        ]);
    }
}