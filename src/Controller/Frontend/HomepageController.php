<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('', name: 'app.homepage')]
    public function index(): Response
    {
        return $this->render('Frontend/Home/index.html.twig');
    }
}
