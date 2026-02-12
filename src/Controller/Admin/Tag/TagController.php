<?php

namespace App\Controller\Admin\Tag;

use App\Entity\Tag;
use App\Form\Admin\TagFormType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin')]
final class TagController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TagRepository $TR,
    ) {
    }

    #[Route('tag/index', name: 'app_admin_tag_index', methods: ['GET'])]
    public function index(): Response
    {
        $tags = $this->TR->findAll();

        return $this->render('pages/admin/tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('tag/create', name: 'app_admin_tag_create', methods: ['GET', 'POST'])]
    public function create(Request $req): Response
    {
        $tag = new Tag();

        $form = $this->createForm(TagFormType::class, $tag);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->setCreatedAt(new \DateTimeImmutable());
            $tag->setupdatedAt(new \DateTimeImmutable());

            $this->em->persist($tag);
            $this->em->flush();

            $this->addFlash('success', 'Le tag a été ajouté avec succès');

            return $this->redirectToRoute('app_admin_tag_index');
        }

        return $this->render('pages/admin/tag/create.html.twig', [
            'tagForm' => $form->createView(),
        ]);
    }

    #[Route('tag/{id<\d+>}/edit', name: 'app_admin_tag_edit', methods: ['GET', 'POST'])]
    public function edit(Tag $tag, Request $req): Response
    {
        $form = $this->createForm(TagFormType::class, $tag);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($tag);
            $this->em->flush();

            return $this->redirectToRoute('app_admin_tag_index');
        }

        return $this->render('pages/admin/tag/edit.html.twig', [
            'tagForm' => $form->createView(),
        ]);
    }

    #[Route('tag/{id<\d+>}/delete', name: 'app_admin_tag_delete', methods: ['GET', 'POST'])]
    public function delete(Request $req, Tag $tag, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid("tag-{$tag->getId()}", $req->request->get('csrf_token'))) {
            $this->em->remove($tag);
            $this->em->flush();
            $this->addFlash('success', 'Le tag a bien été supprimé');
        }

        return $this->redirectToRoute('app_admin_tag_index');
    }
}
