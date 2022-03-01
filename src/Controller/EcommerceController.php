<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    
}
