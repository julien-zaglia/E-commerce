<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Produit;
use App\Form\ContactType;
use App\Form\ProduitType;
use App\Entity\Commentaire;
use App\Form\RechercheType;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManager;
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
    public function index(ProduitRepository $repo, Request $request): Response
    {
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);
        $produits = $repo->getAllByOrder();
        
        if($form->isSubmitted() && $form->isValid())        // si le user fait une recherche
        {
            $data = $form->get('recherche')->getData();     // je récupère la saisie de l'user
            $tabArticles = $repo->getProduitByName($data); 
            // dd($request); la recherche passe bien 
            if(!$tabArticles)
            {
                $this->addFlash('danger',"Recherche infructueuse"); // si la recherche est ko mettre un message 'recherche ko'
            }
        }
        else
        {
            $tabArticles = $repo->findAll();           
        }
        return $this->render('ecommerce/index.html.twig',[
            'produits' => $produits,
            'RechercheForm' => $form->createView()
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
    public function show(Produit $produit, Request $request, EntityManagerInterface $manager)
    {
        $commentaire = new Commentaire;

        $form = $this->createForm(CommentFormType::class, $commentaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
    {
        $commentaire->setCreatedAt(new \DateTime);
        $commentaire->setProduit($produit);
        $commentaire->setAuteur($this->getUser());
        $manager->persist($commentaire);
        $manager->flush();
        return $this->redirectToRoute('produit', [
            'id' => $produit->getId()
        ]);
    }

    return $this->render("ecommerce/show.html.twig", [
        'formCommentaire' => $form->createView(),
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

    #[Route('/ecommerce/profil', name:'profil')]
    public function profil(EntityManagerInterface $manager, ProduitRepository $repo)
    {
        $colonnes = $manager->getClassMetadata(Produit::class)->getFieldNames();
        $produit = $repo->getProduitsByUser($this->getUser());
        return $this->render('ecommerce/profil.html.twig',[
            'produit' =>$produit,
            'colonnes' => $colonnes
        ]);
    }
}