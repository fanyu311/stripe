<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var StripeService
     */
    protected $stripeService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StripeService $stripeService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StripeService $stripeService
    ) {
        $this->em = $entityManager;
        $this->stripeService = $stripeService;
    }

    public function getProducts()
    {
        return $this->em->getRepository(Product::class)
            ->findAll();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function countSoldeOrder(User $user)
    {
        return $this->em->getRepository(Order::class)
            ->countSoldeOrder($user);
    }

    public function getOrders(User $user)
    {
        return $this->em->getRepository(Order::class)
            ->findByUser($user);
    }

    // relié à controller , on va pas utilise notre service directement dans les controller 
    public function intentSecret(Product $product)
    {
        // provenir de notre service
        $intent = $this->stripeService->paymentIntent($product);

        // quand bon ;il va return un client c'est crée
        return $intent['client_secret'] ?? null;
    }

    /**
     * @param array $stripeParameter
     * @param Product $product
     * @return array|null
     */
    public function stripe(array $stripeParameter, Product $product)
    {
        // une fois quand on lencer le payment ,on a le table de resource de desous de le tableau
        $resource = null;
        $data = $this->stripeService->stripe($stripeParameter, $product);

        if ($data) {
            $resource = [
                // charge-> première charge de payment
                // data 0 -> dans le data y a clé 0 => après de ça on peut récupérer ce qu'on a besoin 
                // id -> on va récupérer l'id de stripe
                // brand -> visa
                'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
                'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
                'stripeId' => $data['charges']['data'][0]['id'],
                'stripeStatus' => $data['charges']['data'][0]['status'],
                'stripeToken' => $data['client_secret']
            ];
        }

        return $resource;
    }

    /**
     * @param array $resource
     * @param Product $product
     * @param User $user
     */
    // method -> créer un order / commend => besoin de resource de au dessus de method 
    public function create_subscription(array $resource, Product $product, User $user)
    {

        $order = new Order();
        $order->setUser($user);
        $order->setProduct($product);
        // pour récupréer y a sold ou promotion;vont changer 
        $order->setPrice($product->getPrice());
        // 参考代码-> un id chaque fois pour j'utiliser chaque référence
        $order->setReference(uniqid('', false));
        // place dans le entity ordre
        $order->setBrandStripe($resource['stripeBrand']);
        $order->setLast4Stripe($resource['stripeLast4']);
        $order->setIdChargeStripe($resource['stripeId']);
        $order->setStripeToken($resource['stripeToken']);
        $order->setStatusStripe($resource['stripeStatus']);
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($order);
        $this->em->flush();
    }
}
