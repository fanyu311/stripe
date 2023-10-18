<?php

namespace App\Controller\Backend;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/products', name: 'admin.products')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/Product/index.html.twig', [
            'products' => $this->productRepo->findAll(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On attribut l'utilsateur connecté à l'product
            $product->setUser($this->getUser());

            $this->productRepo->save($product);

            $this->addFlash('success', 'product créé avec succès');

            return $this->redirectToRoute('admin.products.index');
        }

        return $this->render('Backend/Product/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(?Product $product, Request $request): Response
    {
        // On vérifie que l'product est bien trouvé
        if (!$product instanceof Product) {
            $this->addFlash('error', 'product non trouvé');

            return $this->redirectToRoute('admin.products.index');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepo->save($product);

            $this->addFlash('success', 'product mis à jour avec succès');

            return $this->redirectToRoute('admin.products.index');
        }

        return $this->render('Backend/Product/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: '.delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $product = $this->productRepo->find($request->get('id', 0));

        if (!$product instanceof Product) {
            $this->addFlash('error', 'product non trouvé');

            return $this->redirectToRoute('admin.products.index', [], 404);
        }

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->get('token'))) {
            $this->productRepo->remove($product);

            $this->addFlash('success', 'product supprimé avec succès');

            return $this->redirectToRoute('admin.products.index');
        }

        $this->addFlash('error', 'Token invalid');

        return $this->redirectToRoute('admin.products.index');
    }
}
