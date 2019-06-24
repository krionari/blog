<?php
// src/Controller/BlogController.php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleSearchType;
use App\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/category", name="blog_")
 */

class CategoryController extends AbstractController
{
    /**
     * @Route("/add", name="_category")
     * @return Response A response instance
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request): Response
    {
        $category = new Category();
        $formCategory = $this->createForm(CategoryType::class, $category);

        $formCategory->handleRequest($request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()){

            $nameCategory = $formCategory['name']->getData();
            $category->setName($nameCategory);

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('blog__category');

        }

        return $this->render('category/add.html.twig', ['formCategory' => $formCategory->createView()]);
    }
}