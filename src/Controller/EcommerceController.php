<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Produit;
use App\Form\ContactType;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Notification\ContactNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EcommerceController extends AbstractController
{
    #[Route('/produit', name: 'produit')]
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        return $this->render('ecommerce/index.html.twig',[
            'produits' => $produits
        ]);
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
    #[Route('/produit/{id}', name:'show')]
    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Produit::class);

        $produit = $repo->find($id);

        return $this->render('ecommerce/show.html.twig',[
            'produit' => $produit 
        ]);
    }
    #[Route('/ecommerce/new_produit', name:'new_produit')]
    #[Route('/ecommerce/edit/{id}', name:'edit_produit')]
    public function form(Request $request, EntityManagerInterface $manager, Produit $produit = null)
    {
        if(!$produit)
        {
        $produit = new Produit;
        $produit->setAuteur($this->getUser());
        }
   
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
        dd($request);
        $manager->persist($produit);
        $manager->flush();
        return $this->redirectToRoute('produit', [
            'id' => $produit->getId()
        ]);
    }
    return $this->render("ecommerce/creation.html.twig", [
        'formProduit' => $form->createView(),
        'editMode' => $produit->getId() !== NULL 
    ]);
    }
}