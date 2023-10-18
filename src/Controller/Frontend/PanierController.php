<?php

namespace App\Controller\Frontend;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/panier', name: 'app.panier')]
class PanierController extends AbstractController
{
    public function __construct(
        private ProductRepository $repo,
    ) {
    }
    #[Route('', name: '.index', methods: ['GET', 'POST'])]
    public function addToPanier(Request $request, $id)
    {
        return $this->render('Frontend/Panier/index.html.twig', [
            'products' => $this->repo->findAll(),
        ]);

        // // 获取产品信息，例如从数据库中查询
        // $product = $this->$request->getRepository(Product::class)->find($id);

        // // 创建或获取用户的购物篮，你可以使用Symfony的授权系统来获取当前用户
        // $user = $this->getUser(); // 假设你有用户身份认证

        // // 将产品添加到购物篮
        // $panier = $user->$this->getPaniers(); // 假设有与用户关联的购物篮实体
        // $panier->addProduct($product);

        // // 保存购物篮到数据库
        // $entityManager = $this->$request->getManager();
        // $entityManager->persist($panier);
        // $entityManager->flush();

        // // 返回响应，例如JSON响应，以便在前端更新购物篮数量
        // return new JsonResponse([
        //     'message' => 'Product added to cart',
        //     'cartItemCount' => $product->getItemCount(), // 获取购物篮中的产品数量
        // ]);
    }
}
