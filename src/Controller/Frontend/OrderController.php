<?php

namespace App\Controller\Frontend;

use App\Entity\Product;
use App\Manager\ProductManager;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'app.orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $repo,

    ) {
    }
    #[Route('', name: '.index', methods: ["GET"])]
    public function index(): Response
    {
        return $this->render('Frontend/Order/index.html.twig', [
            'orders' => $this->repo->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: '.payment', methods: ['GET', 'POST'])]
    public function payment(Product $product, ProductManager $productManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('Frontend/Order/payment.html.twig', [
            'user' => $this->getUser(),
            //dans le productmanager -> intentSecret
            'intentSecret' => $productManager->intentSecret($product),
            'product' => $product,

        ]);
    }


    #[Route('/{id}/subscription', name: '.subscription', methods: ["GET", "POST"])]
    public function subscription(
        Product $product,
        Request $request,
        ProductManager $productManager
    ) {
        $user = $this->getUser();

        if ($request->getMethod() === "POST") {
            //retourne resource 
            $resource = $productManager->stripe($_POST, $product);


            if (null !== $resource) {
                $productManager->create_subscription($resource, $product, $user);

                // la route pour dire que le paiement valider 
                return $this->render('Frontend/Order/reponse.html.twig', [
                    'product' => $product,
                ]);
            }
        }

        return $this->redirectToRoute('app.orders.payment', ['id' => $product->getId()]);
    }


    #[Route('/payment', name: 'app.payment.orders', methods: ["GET", "POST"])]
    public function payment_orders(ProductManager $productManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('Frontend/Order/paymentStory.html.twig', [
            'user' => $this->getUser(),
            'orders' => $productManager->getOrders($this->getUser()),
            'sumOrder' => $productManager->countSoldeOrder($this->getUser()),
        ]);
    }
}
