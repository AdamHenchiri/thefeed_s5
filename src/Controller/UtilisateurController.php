<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\PublicationRepository;
use App\Repository\UtilisateurRepository;
use App\Service\FlashMessageHelperInterface;
use App\Service\UtilisateurManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UtilisateurController extends AbstractController
{

    #[Route('/inscription', name: 'inscription',methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UtilisateurManager $manager, FlashMessageHelperInterface $flashMessageHelper): Response
    {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('feed');
        }

        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class,
            $user,
            [
                'method'=> "POST",
                'action'=>$this->generateUrl('inscription')
            ]);

        //Traitement du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // À ce stade, le formulaire et ses données sont valides
            // L'objet "Exemple" a été mis à jour avec les données, il ne reste plus qu'à le sauvegarder
            $plainPassword = $form['plainPassword']->getData();
            $fichierPhotoProfil = $form['fichierPhotoProfil']->getData();
            echo $fichierPhotoProfil;
            $manager->proccessNewUtilisateur($user,$plainPassword,$fichierPhotoProfil);
            $entityManager->persist($user);
            $entityManager->flush();
            //On redirige vers la page suivante
            $this->addFlash('succes','Inscription réussie');
            return $this->redirectToRoute('feed');
        }else{
            $flashMessageHelper->addFormErrorsAsFlash($form);
        }


        return $this->render('utilisateur/inscription.html.twig', [
            "inscriForm" => $form
        ]);
    }

    #[Route('/connexion', name: 'connexion', methods: ['GET', 'POST'])]
    public function connexion(AuthenticationUtils $authenticationUtils) : Response {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('feed');
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('utilisateur/connexion.html.twig',[
            "lastUsername"=>$lastUsername
        ]);
    }

    #[Route('/deconnexion', name: 'deconnexion', methods: ['POST'])]
    public function routeDeconnexion(): never
    {
        //Ne sera jamais appelée
        throw new \Exception("Cette route n'est pas censée être appelée. Vérifiez security.yaml");
    }

    #[Route('/utilisateurs/{login}/feed', name: 'pagePerso', methods: ["GET"])]
    public function pagePerso(#[MapEntity] ?Utilisateur $user, PublicationRepository $repository): Response
    {
        if($user == null) {
            $this->addFlash('error','Utilisateur inexistant');
            return $this->redirectToRoute('feed');
        }

        return $this->render('utilisateur/page_perso.html.twig',[
            'user'=>$user
        ]);
    }
}
