<?php
// src/Controller/BlogController.php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use App\Form\ArticleSearchType;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
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
        $form = $this->createForm(ArticleSearchType::class, null, ['method' => Request::METHOD_GET]);
        $category = new Category();
        $formCategory = $this->createForm(CategoryType::class, $category);

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles){
            throw $this->createNotFoundException('No article found in article\'s table.');
        }

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
            'form' => $form->createView(),
            'formCategory' => $formCategory->createView()
        ]);
    }

    /**
     * @param string $slug
     * @Route("/show/{slug}", requirements={"slug"="[a-z A-Z0-9-]+"}, name="show")
     * @return Response A response instance
     */
    public function show(?string $slug = 'article sans titre'): Response
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

        $tags = $article->getTags();


        if (!$article) {
            throw $this->createNotFoundException(
                'No article with '.$slug.' title, found in article\'s table.'
            );
        }

        return $this->render('blog/show.html.twig', [
            'slug' => ucwords(str_replace('-', ' ',($slug))),
            'article' => $article,
            'tags' => $tags,
            'category' => $article->getCategory()
        ]);
    }

    /**
     * @param object $category
     * @Route("/category/{name}", requirements={"name"="[a-z A-Z\.0-9-_+]+"}, name="category")
     * @return Response A response instance
     */
    public function showByCategory(Category $category): Response
    {


      /* $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]); */

        $articles = $category->getArticles();
        /* -------------------------------------------
          $articles = $this->getDoctrine()
              ->getRepository(Article::class)
              ->findBy(
                  ['category' => $category],
                  ['id' => 'Desc'],
                  3
                  );

  /*
          if (!$category) {
              throw $this->createNotFoundException(
                  'No category with ' . $category . ' title, found in article\'s table.'
              );
          }

          if (!$articles) {
              throw $this->createNotFoundException(
                  'No category with ' . $category . ' title, found in article\'s table.'
              );
          }  */

        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'articles' => $articles
        ]);
    }

    /**
     * @param Tag $tag
     * @return Response
     * @Route("/tag/{name}", requirements={"name"="[a-zA-Z0-9-+]+"}, name="tag")
     */
    public function showByTag(Tag $tag): Response
    {
        $articles = $tag->getArticles();

        return $this->render('blog/tag.html.twig', ['articles' => $articles , 'tag' => $tag]);

    }
}