<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;

#[Route('/admin/orders', name: 'admin.orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepo,
    ) {
    }
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/Order/index.html.twig', [
            'orders' => $this->orderRepo->findAll(),
        ]);
    }
}
