<?php

namespace App\Controller\Visitor\Welcome;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WelcomeController extends AbstractController
{
    #[Route('/', name: 'app_visitor_welcome', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/visitor/welcome/index.html.twig');
    }

    #[Route('/liste-des-articles', name: 'app_visitor_blog_index', methods: ['GET'])]
    public function blog(): Response
    {
        return $this->render('pages/visitor/blog/index.html.twig');
    }
}
