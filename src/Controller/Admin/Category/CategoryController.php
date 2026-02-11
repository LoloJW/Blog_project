<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\Admin\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//  function __construct() {
//     EntityManagerInterface();
// }
#[Route('/admin')]
final class CategoryController extends AbstractController
{
    #[Route('/category/index', name: 'app_admin_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findall();

        // dd($category);

        return $this->render('pages/admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/create', name: 'app_admin_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('pages/admin/category/create.html.twig', ['categoryForm' => $form->createView()]);
    }

    #[Route('/category/{id<\d+>}/edit', name: 'app_admin_category_edit', methods: ['POST', 'GET'])]
    public function edit(Category $category, Request $req, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie a été modifiée');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('pages/admin/category/edit.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    #[Route('/category/{id<\d+>}/delete', name: 'app_admin_category_delete', methods: ['POST'])]
    public function delete(Category $category, Request $req, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid("category-{$category->getId()}", $req->request->get('csrf_token'))) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie a été supprimé');
        }

        return $this->redirectToRoute('app_admin_category_index');
    }
}
