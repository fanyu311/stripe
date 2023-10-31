<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\PanierItem;
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

        // quand bon ;il va return un clientsecret
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
            // echo '<pre>';
            // var_dump($data['last4']);
            // die;
            // echo '</pre>';
            $resource = [
                // charge-> première charge de payment
                // data 0 -> dans le data y a clé 0 => après de ça on peut récupérer ce qu'on a besoin 
                // id -> on va récupérer l'id de stripe
                // brand -> visa
                //quand une fois passe le paiement,stripe va retourne tout ca 
                'stripeBrand' => $data["payment_method"],
                'stripeLast4' => $data['last4'],
                'stripeId' => $data['id'],
                'stripeStatus' => $data['status'],
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
        // $order->setLast4Stripe($resource['stripeLast4']);
        $order->setIdChargeStripe($resource['stripeId']);
        $order->setStripeToken($resource['stripeToken']);
        $order->setStatusStripe($resource['stripeStatus']);
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($order);
        $this->em->flush();
    }
}
