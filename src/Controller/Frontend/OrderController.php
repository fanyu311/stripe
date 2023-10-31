<?php

namespace App\Controller\Frontend;

use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Panier;
use App\Entity\Product;
use Stripe\PaymentIntent;
use App\Entity\PanierItem;
use App\Form\PanierItemType;
use App\Manager\ProductManager;
use App\Services\StripeService;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'app.orders')]
class OrderController extends AbstractController
{

    public function __construct(
        private OrderRepository $repo,
        private ProductRepository $productRepo,
    ) {
    }

    // la route d'acceuille de panier-> pour pouvoir accéder panier 
    #[Route('', name: '.index')]
    public function index(SessionInterface $session, ProductRepository $productRepo)
    {
        $panier = $session->get('panier', []);

        // initialize des variables
        // 初始化变量
        $data = [];
        $total = 0;

        // boucler le panier -> pour chercher les noms de produit ou quantity etc dans le tableau data 
        // 其中$panier是一个关联数组-> $id代表产品的ID，值$quantity代表购物车种该产品的数量，其中foreach循环用于遍历购物车中的每个产品->添加到订单项中
        foreach ($panier as $id => $quantity) {
            $product = $productRepo->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quantity,
            ];


            $total += $product->getPrice() * $quantity;
        };


        // compact是一种将变量传递给twig模板的简洁方式，创建一个关联数组，其中键是变量名，值是对应变量的值
        return $this->render('Frontend/Panier/index.html.twig', compact('data', 'total'));
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
        ProductManager $productManager,

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


    //Ajouter une fonction IndexOrders au controller OrderController / Cette fonction permet d'afficher la liste de mes commandes
    #[Route('/payment', name: '.commands', methods: ["GET", "POST"])]
    public function indexOrders(ProductManager $productManager): Response
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

    //Ajouter route et fonction  AddProductTo Order au controller OrderController / Cette fonction permet d'ajouter un produit à la commande
    #[Route('/add/{id}', name: '.add')]
    // si le produit n'existe pas = aucun id 
    public function addProductToOrder(Product $product, SessionInterface $session)
    {
        //récuperer l'id du product
        $id = $product->getId();

        // récuperer le panier existe
        $panier = $session->get('panier', []);
        // $panier[3] = 1;

        // ajout le produit dans le panier , si n'y est pas ecore 
        // sinon on incrément sa quantité
        if (empty($panier[$id])) {
            // si vide on mise 1 quntité
            $panier[$id] = 1;
        } else {
            // incrémente
            $panier[$id]++;
        }
        // on sauvgarde 
        $session->set('panier', $panier);
        // rediréger le page du panier 
        return $this->redirectToRoute('app.orders.index');
    }

    //Ajouter fonction RemoveProductFromOrder au controller OrderController / Cette Fonction permet de remove un produit de la commande
    #[Route('/remove/{id}', name: '.remove')]
    public function  RemoveProductFromOrder(Product $product, SessionInterface $session)
    {
        //récuperer l'id du product
        $id = $product->getId();

        // récuperer le panier existe
        $panier = $session->get('panier', []);
        // $panier[3] = 1;

        // retire le produit du panier , si n'y a que 1 exemplaire
        if (!empty($panier[$id])) {
            // si $panier >1 décrement , sinon envlève $panier[$id]
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }
        // on sauvgarde 
        $session->set('panier', $panier);
        // rediréger le page du panier 
        return $this->redirectToRoute('app.orders.index');
    }



    // Ajouter fonction DeleteProductFromOrder au controller OrderController / Cette Fonction permet de supprimer un produit de la commande
    #[Route('/delete/{id}', name: '.delete')]
    public function  DeleteProductFromOrder(Product $product, SessionInterface $session)
    {
        //récuperer l'id du product
        $id = $product->getId();

        // récuperer le panier existe
        $panier = $session->get('panier', []);

        // si n'est pas vide ; on envlève ce id du product
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        // on sauvgarde 
        $session->set('panier', $panier);
        // rediréger le page du panier 
        return $this->redirectToRoute('app.orders.index');
    }

    // vider touts les commandes si l'utilisateur veux pas -> après je peut utiliser js pour demander l'utilisateur vraiment envier supprimer le command
    #[Route('/empty', name: '.empty')]
    public function  emptyProductFromOrder(SessionInterface $session)
    {
        // just supprimer panier
        $session->remove('panier');
        return $this->redirectToRoute('app.orders.index');
    }

    //Ajouter une fonction PayOrder au controller OrderController / Cette fonction permet  la gestion du paiment via Stripe. (attention à bien revérifier que le montant correspond à la commande, sinon rediriger vers page de paiement avec un message d'erreur)

    // 注入StripeService

    #[Route('/payorder', name: '.payorder')]
    public function PayOrder(
        SessionInterface $session,
        ProductRepository $productRepo,
        EntityManagerInterface $em,
        ProductManager $productManager,
        StripeService $stripeService, // 注入StripeService,

    ): Response {

        // vérifier l'utilisateur de  connecté ou pas 
        $this->denyAccessUnlessGranted('ROLE_USER');

        // récupérer le panier , si j'ai pas le panier ;j'ai mis un tableau vide 
        $panier = $session->get('panier', []);

        // si le panier vide envoyer un message et rediriger le page de hommepage
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('app.homepage');
        }

        // si le panier n'est pas vide , on crée la commande 
        $order = new Panier();
        // on remplit la commande
        $order->setUser($this->getUser());
        $order->setReference(uniqid());

        // 初始化一个关联数组，用于存储stripe支付intent相关的参数信息
        //先把参数初设值为null-> 在支付intent创建后，他将被设置为实际的stripe支付intent的id跟实际状态
        $stripeParameters = [
            'stripeIntentId' => null, // 根据需要设置Stripe参数
            'stripeIntentStatus' => null,
        ];

        $totalAmount = 0; // 用于存储总金额

        // on parcourt le panier pour créer les détails de commande
        foreach ($panier as $itemId => $quantity) {
            // on va chercher le produit 
            $product = $productRepo->find($itemId);

            if (!$product) {
                $this->addFlash('error', 'Certains produits dans votre panier ne sont plus disponibles.');
                continue;
            }

            $price = $product->getPrice();

            // 累加总金额
            $totalAmount += $price * $quantity;

            //on crée le détail de commande 
            $panierItem = new PanierItem();
            $panierItem->setProduct($product);
            $panierItem->setPrice($price);
            $panierItem->setQuantity($quantity);
            $panierItem->setPanier($order);

            // 添加购物车项到购物车 // ajoute le détail de commande dans le exterieur de command
            $order->addPanierItem($panierItem);
        }
        // 设置订单的总金额
        $order->setTotalAmount($totalAmount);

        // 使用StripeService创建付款Intent
        try {
            $paymentIntent = $stripeService->paymentIntent($product);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // 处理Stripe支付错误
            $this->addFlash('error', 'Erreur de paiement Stripe.');
            return $this->redirectToRoute('app.cart');
        }

        $stripeParameters['stripeIntentId'] = $paymentIntent->id; // 设置Stripe参数
        $stripeParameters['stripeIntentStatus'] = $paymentIntent->status; // 设置Stripe参数

        // 在循环中进行支付
        $stripeService->paiement($totalAmount * 100, Order::DEVISE, $product->getName(), $stripeParameters);

        // dd($totalAmount);
        if ($order->getPanierItems()->isEmpty()) {
            $this->addFlash('error', 'Aucun produit valide dans le panier.');
            return $this->redirectToRoute('app.homepage');
        }

        // on persiste et on flush -> on créer et on excute mes requete
        $em->persist($order);
        $em->flush();
        // dd($order);
        // quand tout fini le ajouter recharge le page vont remove les articles 
        $session->remove('panier');
        // $this->addFlash('success', 'Paiement de votre commande');

        return $this->render('Frontend/Order/payment.html.twig', [
            'totalAmount' => $totalAmount,
            'product' => $product,
            //dans le productmanager -> intentSecret
            'intentSecret' => $productManager->intentSecret($product),
            'user' => $this->getUser(),
            'order' => $order,
        ]);
    }

    // Ajouter une fonction CancelOrder au controller OrderController / Cette fonction permet de supprimer une commande 

    #[Route('/cancelorder', name: '.cancelorder')]
    public function CancelOrder(Request $request)
    {
        $order = $this->repo->find($request->get('id', 0));


        if (!$order) {
            throw $this->createNotFoundException('La commande n\'existe pas.');
        }


        // Supprimez la commande
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->get('token'))) {
            $this->repo->remove($order);

            $this->addFlash('success', 'Article supprimé avec succès');

            return $this->redirectToRoute('app.orders.commands');
        }

        // Ajoutez un message error si le CSRF invalide
        $this->addFlash('error', 'Token invalid');

        // Redirigez l'utilisateur vers une page  d'accueil
        return $this->redirectToRoute('app.homepage');
    }

    // Ajouter une fonction ShowOrder au controller OrderController / Cette fonction permet d'afficher une commande 
    #[Route('/showorder/{id}', name: '.showorder')]
    public function ShowOrder(Request $request): Response
    {
        $order = $this->repo->find($request->get('id', 0));

        if (!$order) {
            throw $this->createNotFoundException('La commande n\'existe pas.');
        }

        return $this->render('Frontend/Order/showOrder.html.twig', [
            'order' => $order,
            'product' => $order->getProduct(),
        ]);
    }
}
