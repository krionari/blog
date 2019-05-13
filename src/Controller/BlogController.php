<?php
// src/Controller/BlogController.php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/blog", name="blog_")
 */

class BlogController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'owner' => 'Pascalito',
        ]);
    }

    /**
     * @Route("/show/{slug}", requirements={"slug"="[a-z0-9-]+"}, name="show")
     */
    public function show($slug = 'article sans titre')
    {
        return $this->render('blog/show.html.twig', [
            'slug' => ucwords(str_replace('-', ' ',($slug))),
        ]);
    }
}