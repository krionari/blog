<?php
// src/Controller/BlogController.php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleSearchType;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/category", name="category_")
 */

class CategoryController extends AbstractController
{

    /**
     * @Route("", name="index")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', ['categories' => $categoryRepository->findAll()]);
    }

    /**
     * @Route("/add", name="new")
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

            $this->addFlash('success', 'Catégorie ajoutée avec un succés certain');

            return $this->redirectToRoute('category_index');

        }

        return $this->render('category/add.html.twig', ['formCategory' => $formCategory->createView()]);
    }

    /**
     * @Route ("/{id}", name="delete", methods={"DELETE"})
     *
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        $this->addFlash('danger' , 'Catégorie supprimée, quel dommage, Mme Chambier.');

        return $this->redirectToRoute('category_index');
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit (Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $category->setName($category->getName());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Catégorie modifié avec un succés exellent, un grand bravo');

            return $this->redirectToRoute('category_index');

        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);

    }
}