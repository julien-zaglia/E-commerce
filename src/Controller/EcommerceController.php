<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EcommerceController extends AbstractController
{
    #[Route('/produit', name: 'produit')]
    public function index(): Response
    {
        return $this->render('ecommerce/index.html.twig');
    }
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('ecommerce/home.html.twig');
    }
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $manager, ContactNotification $notification)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $notification->notify($contact);// utilisation de la class ContactNotification
            $this->addFlash('success', 'Votre Email a bien été envoyé');

            $manager->persist($contact);    // On prépare l'insertion 
            $manager->flush();              // On execute l'insertion 
        }
        return $this->render("ecommerce/contact.html.twig",[

            'formContact'=>$form->createView()
        ]);
    }    
}
