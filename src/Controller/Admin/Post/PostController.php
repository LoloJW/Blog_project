<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Form\Admin\PostFormType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin')]
final class PostController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PostRepository $PR,
        private readonly CategoryRepository $CR,
    ) {
    }

    #[Route('/post/index', name: 'app_admin_post_index', methods: ['GET'])]
    public function index(): Response
    {
        $posts = $this->PR->findAll();

        return $this->render('pages/admin/post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/create', name: 'app_admin_post_create', methods: ['GET', 'POST'])]
    public function create(Request $req): Response
    {
        if (0 == $this->CR->count()) {
            $this->addFlash('warning', 'pour rédiger des articles, vous devez avoir une catégorie.');

            return $this->redirectToRoute('app_admin_category_index');
        }

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

            $this->em->persist($post);
            $this->em->flush();

            $this->addFlash('success', 'Bravo vous avez ajouté un casse-tête');

            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render('pages/admin/post/create.html.twig', [
            'postForm' => $form->createView(),
        ]);
    }

    #[Route('/post/{id<\d+>}/edit', name: 'app_admin_post_edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $req): Response
    {
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($post);
            $this->em->flush();

            $this->addFlash('success', "L'article a été modifié, bien joué");

            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render('pages/admin/post/edit.html.twig', [
            'postForm' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/post/{id<\d+>}/delete', name: 'app_admin_post_delete', methods: ['POST'])]
    public function delete(Post $post, Request $req): Response
    {
        if ($this->isCsrfTokenValid("token_post_delete{$post->getId()}", $req->request->get('csrf_token'))) {
            $this->em->remove($post);
            $this->em->flush();

            $this->addFlash('success', "l'article a été supprimé");
        }

        return $this->redirectToRoute('app_admin_post_index');
    }

    #[Route('/post/{id<\d+>}/show', name: 'app_admin_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('pages/admin/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/post/{id<\d+>}/publish', name: 'app_admin_post_publish', methods: ['POST'])]
    public function publish(Post $post, Request $req): Response
    {
        if (!$this->isCsrfTokenValid("publish-post-{$post->getId()}", $req->request->get('csrf_token'))) {
            return $this->redirectToRoute('app_admin_post_index');
        }

        if (!$post->isPublished()) {
            $post->setPublishedAt(new \DateTimeImmutable());
            $post->setIsPublished(true);
            $this->addFlash('success', "l'article a été publié.");
        } else {
            $post->setIsPublished(false);
            $post->setPublishedAt(null);
            $this->addFlash('success', "l'article a été retiré de la liste des publications.");
        }

        $this->em->persist($post);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_post_index');
    }
}
