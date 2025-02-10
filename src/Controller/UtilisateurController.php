<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
    #[Route('/ajouter-utilisateur', name: 'ajouter_utilisateur')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
 
        // Traiter la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'utilisateur dans la base de données
            $em->persist($utilisateur);
            $em->flush();
            $this->addFlash('success',"l'utilisateur a bien été ajouter");
            // Redirection ou message de succès
            return $this->redirectToRoute('ajouter_utilisateur');
        }

        return $this->render('utilisateur/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/liste-utilisateurs', name: 'liste_utilisateurs')]
    public function list(EntityManagerInterface $em): Response
    {
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAll();
       
        return $this->render('utilisateur/list.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
        #[Route('/modifier-utilisateur/{id}', name: 'modifier_utilisateur')]
    public function edit(Request $request, EntityManagerInterface $em, $id): Response
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('liste_utilisateurs');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer-utilisateur/{id}', name: 'supprimer_utilisateur')]
    public function delete(EntityManagerInterface $em, $id): Response
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);
        if ($utilisateur) {
            $em->remove($utilisateur);
            $em->flush();
        }

        return $this->redirectToRoute('liste_utilisateurs');
    }

    }
