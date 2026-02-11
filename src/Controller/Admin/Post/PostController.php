<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Form\Admin\PostFormType;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\VarDumper\Cloner\Data;

#[Route('admin')]
final class PostController extends AbstractController
{
    #[Route('/post/index', name: 'app_admin_post_index', methods: ['GET'])]
    public function index(PostRepository $PR): Response
    {
        $posts = $PR->findAll();

        return $this->render('pages/admin/post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/create', name: 'app_admin_post_create', methods: ['GET', 'POST'])]
    public function create(Request $req, EntityManagerInterface $em): Response
    {
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var User
             */
            $admin = $this->getUser();
            $post->setUser($admin);

            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Bravo vous avez ajouté un casse-tête');

            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render('pages/admin/post/create.html.twig', [
            'postForm' => $form->createView(),
        ]);
    }
    #[Route('/post/{id<\d+>}/edit', name: 'app_admin_post_edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $req, EntityManagerInterface $em): Response {

        $form = $this->createForm(PostFormType::class, $post);
        
        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid()){
        
            $post->setUpdatedAt(new DateTimeImmutable());

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', "L'article a été modifié, bien joué");
           
            return $this->redirectToRoute('app_admin_post_index');
        }
        
        return $this->render("pages/admin/post/edit.html.twig", [
            "postForm" => $form->createView(),
            "post" => $post
        ]);
    }
    #[Route('/post/{id<\d+>}/delete', name: 'app_admin_post_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $em, Post $post, Request $req ): Response{
        if($this->isCsrfTokenValid("token_post{$post->getId()}", $req->request->get('csrf_token'))){
            $em->remove($post);
            $em->flush();

            $this->addFlash("success","l'article a été supprimé");
        }
        return $this->redirectToRoute("app_admin_post_index");
    }
    #[Route('/post/{id<\d+>}/show', name: 'app_admin_post_show', methods: ['GET'])]
    public function show(Post $post, PostRepository $PR): Response {

        return $this->render('pages/admin/post/show.html.twig',[
            "post" => $post
        ]);
    }
}
