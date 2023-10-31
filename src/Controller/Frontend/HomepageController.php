<?php

namespace App\Controller\Frontend;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    public function __construct(
        private ProductRepository $repo,

    ) {
    }
    #[Route('', name: 'app.homepage', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Frontend/Home/index.html.twig', [
            'products' => $this->repo->findAll(),
        ]);
    }
}
