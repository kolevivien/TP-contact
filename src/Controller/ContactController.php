<?php
namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde en base de données
            $entityManager->persist($contact);
            $entityManager->flush();

            // Récupération des données
            $service = $contact->getService();
            $emailContact = $contact->getEmail();
            $nom = $contact->getNom();
            $message = $contact->getMessage();

            // Définition des emails en fonction du service sélectionné
            $destinataires = [
                'comptable' => 'comptable@example.com',
                'pdg' => 'pdg@example.com',
                'coursier' => 'coursier@example.com',
            ];

            // Vérifier si le service a une adresse e-mail définie
            $emailDestinataire = $destinataires[$service] ?? 'default@example.com';

            // Création de l'email
            try{
            $email = (new Email()) 
                ->from($emailContact)
                ->to($emailDestinataire)
                ->subject('Nouveau message de contact')
                ->text("Nom: {$nom}\nEmail: {$emailContact}\nService: {$service}\n\nMessage:\n{$message}");

            // Envoi de l'email
            $mailer->send($email);

            // Message flash et redirection
            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return $this->redirectToRoute('contact');
        } catch (\Exception $e){
             $this->addFlash('danger', 'Impossible envoyer votre email');
        }
    }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
