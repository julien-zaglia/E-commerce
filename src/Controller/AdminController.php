<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Entity\Commentaire;
use App\Form\FormCategorieType;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Form\CommentaireAdminFormType;
use App\Form\AdminRegistrationFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }


//************************************************************************************************************************************************ ADMIN PRODUIT *************************************************************************************************************************************************/
        
    #[Route("/admin/produit", name:"admin_produit")]
    public function adminProduit(ProduitRepository $repo) 
    {
        // On appel getManager afin de récupérer le noms des champs et des colonnes

        $em = $this->getDoctrine()->getManager();

        // récupération des champs
        $colonnes = $em->getClassMetadata(Produit::class)->getFieldNames(); 
        
        dump($colonnes);

        $produit = $repo->findAll();

        dump($produit);

        return $this->render('admin/admin_produit.html.twig', [ 'produits' => $produit,
        'colonnes' => $colonnes
        ]); 
    }


    
    #[Route("/admin/produit/new", name:"admin_new_produit")]
    #[Route("/admin/{id}/edit-produit", name:"admin_edit_produit")]
    public function formProduit(EntityManagerInterface $manager,Produit $produit = null, Request $request)
    {
        if(!$produit)
        {
            $produit = new Produit;
        }
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($produit);
            $manager->flush();

            if ($request->attributes->get('route') == 'admin_new_produit')
            {
                //si je me trouve sur la route d'ajout d'un produit
                $this->addFlash("success", "Votre produit'" . $produit->getNom() ."' a bien été crée !");
            }
            else
            {
                //sinon, je me trouve dans la route d'édition d'un produit
                $this->addFlash("success", "Votre produit'" . $produit->getNom() ."'  a bien été modifié !");
            }
            return $this->redirectToRoute("admin_produit");
        }
        return $this->render("admin/Form_produit.html.twig", [
            "formProduit"=>$form->createView(),
            "editMode"=>$produit->getId()!==NULL]);
    }

    #[Route('/admin/delete/produit/{id}', name:'delete_produit')]

        public function deleteProduit(EntityManagerInterface $manager, Produit $produit)
        {
            $nom = $produit->getNom();
            $manager->remove($produit);
            //remove() est une méthode qui permet de préparer la suppressions
            $manager->flush();
            $this->addFlash('success', "Le produit '" . $nom . "' à bien été supprimé !");
            return $this->redirectToRoute('admin_produit');

        //la méthode deleteProduit() s'occupe seulement de supprimer un produit. Nous n'avons pas besoin d'afficher un template. 
    }


//************************************************************************************************************************************************ ADMIN CATEGORIE *************************************************************************************************************************************************/


    #[Route("/admin/categorie", name:"admin_categorie")]
        public function adminCategorie(CategorieRepository $repo) 
        {
            // On appel getManager afin de récupérer le noms des champs et des colonnes

            $em = $this->getDoctrine()->getManager();

            // récupération des champs
            $colonnes = $em->getClassMetadata(Categorie::class)->getFieldNames(); 
            
            dump($colonnes);

            $categorie = $repo->findAll();

            dump($categorie);

            return $this->render('admin/admin_categorie.html.twig', [ 'categorie' => $categorie,
            'colonnes' => $colonnes
            ]); 
        }


    
    #[Route("/admin/categorie/new", name:"admin_new_categorie")]
    #[Route("/admin/{id}/edit-categorie", name:"admin_edit_categorie")]

    public function formCategorie(EntityManagerInterface $manager,Categorie $categorie = null, Request $request)
    {
        if(!$categorie)
        {
            $categorie = new Categorie;
        }
        $form = $this->createForm(FormCategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($categorie);
            $manager->flush();

            if ($request->attributes->get('route') == 'admin_new_categorie')
            {
                //si je me trouve sur la route d'ajout d'un categorie
                $this->addFlash("success", "Votre categorie'" . $categorie->getTitre() ."' a bien été crée !");
            }
            else
            {
                //sinon, je me trouve dans la route d'édition d'un categorie
                $this->addFlash("success", "Votre categorie'" . $categorie->getTitre() ."'  a bien été modifié !");
            }
            return $this->redirectToRoute("admin_categorie");
        }
        return $this->render("admin/Form_categorie.html.twig", [
            "formCategorie"=>$form->createView(),
            "editMode"=>$categorie->getId()!==NULL]);
    }

    #[Route('/admin/delete/categorie/{id}', name:'delete_categorie')]

        public function deleteCategorie(EntityManagerInterface $manager, Categorie $categorie)
        {
            $nom = $categorie->getTitre();
            $manager->remove($categorie);
            //remove() est une méthode qui permet de préparer la suppressions
            $manager->flush();
            $this->addFlash('success', "Le categorie '" . $nom . "' à bien été supprimé !");
            return $this->redirectToRoute('admin_categorie');

        //la méthode delete Categorie() s'occupe seulement de supprimer un categorie. Nous n'avons pas besoin d'afficher un template. 
    }


//********************************************************************************************************************************************** USER *************************************************************************************************************************************************/

    
    #[Route("admin/user", name:"admin_users")]
              
        public function adminUser(EntityManagerInterface $manager, UserRepository $repo)
        {
            $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();

            $users =$repo->findAll();
            return $this->render("admin/admin_user.html.twig", [
            'users' => $users,
            'colonnes'=>$colonnes
            ]);
        }

    #[Route("admin/users/new", name:"admin_new_user")]
    #[Route("admin/users/edit/{id}", name:"admin_edit_user")]
    

    public function formUser(EntityManagerInterface $manager, User $user = null, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        if(!$user)
        {
            $user = new User;
        }

        $form = $this->createForm(AdminRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
        //    dd($form->get('plainPassword')->getData());

            if($form->get('plainPassword')->getData() !== Null)
            {
                      //$form->get('plainPassword')->getData() !== Null Si le password est différent de nul
                $user->setPassword(
                $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()));
            }
                      
            if($form->get('plainPassword')->getData() == Null &&  $request->attributes->get('_route')== 'admin_new_user')
            {
            //Si le password est vide et qu'on vient de la route 'admin_new_user
                $this->addFlash("danger", "Veuillez renseigner un mot de passe");
                return $this->redirectToRoute("admin_new_user");
            }
            else
            {
                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute("admin_users");
            }
                      
        }    

        return $this->render("admin/form_user.html.twig", [
            'formUser'=>$form->createView(),
            'editMode' => $user->getId() !== Null]);
    }
             
    #[Route("/admin/user/delete/{id}", name:"delete_user")]
              
    public function deleteUser(EntityManagerInterface $manager, User $user = null)
    {
        if (!$user)
        {
            $this->addFlash('danger', 'Il n y a pas d\'utilisateur à supprimer');
            return $this->redirectToRoute('admin_users');
        }

        $nom = $user->getEmail();
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', "L'utilisateur '" . $nom . "' a bien été supprimé!");
        return $this->redirectToRoute('admin_users');
    }
 
//********************************************************************************************************************************************** COMMENTAIRE *************************************************************************************************************************************************/

    
    #[Route("/admin/commentaire", name:"admin_commentaire")]
    
    public function adminCommentaire(EntityManagerInterface $manager, CommentaireRepository $repo)
    {
        $colonnes = $manager->getClassMetadata(Commentaire::class)->getFieldNames();
          
        $commentaire = $repo->findAll();

        return $this->render("admin/admin_commentaire.html.twig", [
            'commentaire' => $commentaire,
            'colonnes' => $colonnes
        ]);
    }

     
    #[Route("/admin/commentaire/new", name:"admin_new_commentaire")]
    #[Route("/admin/commentaire/edit/{id}", name:"admin_edit_commentaire")]
    
    public function formCommentaire(EntityManagerInterface $manager, Commentaire $commentaire = null, Request $request)
    {
        if (!$commentaire)
        {
        $commentaire = new Commentaire;
        $commentaire->setCreatedAt(new \DateTime());
        }
        $form = $this->createForm(CommentaireAdminFormType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($commentaire);
            $manager->flush();

            if ($request->attributes->get('_route')== 'admin_new_commentaire'){
                // si je me trouve sur la route d'ajout d'un article
                $this->addFlash("success", "Votre commentaire dans l'article '" . $commentaire->getProduit()->getNom() ."' a bien été crée !");
            }
            else
            {
                // sinon, je me trouve dans la route d'édition d'un article
                $this->addFlash("success", "Votre commentaire dans l'article '" . $commentaire->getProduit()->getNom() ."' a bien été modifié !");
            }
            return $this->redirectToRoute(("admin_commentaire"));
        }

        return $this->render("admin/form_commentaire.html.twig", [
            "formCommentaire" => $form->createView(),
            "editMode" => $commentaire->getId() !== Null
        ]);

    }
          
        #[Route("/admin/commentaire/delete/{id}", name:"delete_commentaire")]
           
          
        public function deleteCommentaire(EntityManagerInterface $manager, Commentaire $commentaire = null){

            if (!$commentaire)
            {
                $this->addFlash('danger', 'Il n y a pas de commentaire à supprimer');
                return $this->redirectToRoute('admin_articles');
            }
      
            $nom = $commentaire->getProduit()->getNom();
            $manager ->remove($commentaire);
            // remove() est une méthode qui permet de préparer la suppression
            $manager->flush();
      
            $this->addFlash('success',"Le commentaire de l'article '" . $nom . "' a bien été supprimé !");
            return $this->redirectToRoute('admin_commentaire');
      
            // la méthode deletearticle sert a supprimé a un article, pas besoin de template
        }
}

