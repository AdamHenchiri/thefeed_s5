<?php

namespace App\Controller;

use App\Service\FlashMessageHelper;
use App\Service\FlashMessageHelperInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PublicationType;
use Symfony\Component\HttpFoundation\JsonResponse;


class PublicationController extends AbstractController
{
    #[Route('/delete/{id}', name: 'deletePublication', options: ["expose" => true], methods: ['DELETE'])]
    public function delete(#[MapEntity] ?Publication $post, EntityManagerInterface $entityManager){
        if($this->isGranted('ROLE_USER')) {
            if ($post != null) {
                if ($post->getAuteur() === $this->getUser()) {
                    $entityManager->remove($post);
                    $entityManager->flush();
                    return JsonResponse("", 200);
                } else {
                    return JsonResponse("opération interdite", 403);
                }
            } else {
                return JsonResponse("ressource non trouvée", 404);
            }
        }
    }

    #[Route('/', name: 'feed', options: ["expose" => true], methods: ["GET", "POST"])]
    public function index(Request $request, EntityManagerInterface $entityManager, PublicationRepository $repository, FlashMessageHelperInterface $flashMessageHelper): Response
    {
        $post = new Publication();
        $utilisateur = $this->getUser();

        $form = $this->createForm(PublicationType::class,
                                    $post,
                                    [
                                        'method'=> "POST",
                                        'action'=>$this->generateUrl('feed')
                                    ]);
        //Traitement du formulaire
        $form->handleRequest($request);

        if($request->isMethod('POST')) {
            $this->denyAccessUnlessGranted('ROLE_USER');
            if ($form->isSubmitted() && $form->isValid()) {
                // À ce stade, le formulaire et ses données sont valides
                // L'objet "Exemple" a été mis à jour avec les données, il ne reste plus qu'à le sauvegarder
                $post->setAuteur($utilisateur);
                $entityManager->persist($post);
                $entityManager->flush();

                //On redirige vers la page suivante
                return $this->redirectToRoute('feed');
            } else {
                $flashMessageHelper->addFormErrorsAsFlash($form);
            }
        }

        $posts = $repository->findAllOrderedByDate();
        return $this->render('publication/feed.html.twig', [
            'publications' =>  $posts,
            "postForm" => $form,
        ]);
    }
}
