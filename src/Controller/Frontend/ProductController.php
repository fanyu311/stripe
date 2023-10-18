<?php

namespace App\Controller\Frontend;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/products', name: 'app.products')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $repo,

    ) {
    }
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Frontend/Product/index.html.twig', [
            'products' => $this->repo->findAll(),
        ]);
    }
}
