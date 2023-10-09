<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExempleController extends AbstractController
{
    #[Route('/hello', name: 'hello_get', methods: ["GET"])]
    public function index(): Response
    {
        return $this->render('exemple/demo/demo1.html.twig', [
        ]);
    }

    #[Route('/hello/{name}', name: 'hello_get2', methods: ["GET"])]
    public function helloget2($name): Response
    {
        return $this->render('exemple/demo/demo2.html.twig', [
            'name'=> $name
        ]);
    }

    #[Route('/courses', name: 'listecourses_get3', methods: ["GET"])]
    public function listecourses(): Response
    {
        $this->addFlash('success', "bienvenue !!");
        return $this->render('exemple/demo/demo3.html.twig', [
            'liste'=> ["lait", "pain", "Livre sur Symfony", "œufs", "pq", "gaufrettes"]
        ]);
    }

}
