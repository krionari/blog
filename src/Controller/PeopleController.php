<?php
// src/Controller/PeopleController.php

namespace App\Controller;

use App\Entity\Hobbies;
use App\Entity\People;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/people", name="Person_")
 */
class PeopleController extends AbstractController
{
    /**
     * @Route("", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $peoples = $this->getDoctrine()
            ->getRepository(People::class)
            ->findAll();

        $hobbies = $this->getDoctrine()
            ->getRepository(Hobbies::class)
            ->findAll();

        return $this->render('people/index.html.twig', ['peoples' => $peoples , 'hobbies' => $hobbies]);
    }
}