<?php

namespace App\Controller;

use App\Entity\Seccion;
use App\Form\SeccionType;
use App\Repository\SeccionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/seccion')]
final class SeccionController extends AbstractController
{
    #[Route(name: 'app_seccion_index', methods: ['GET'])]
    public function index(SeccionRepository $seccionRepository): Response
    {
        return $this->render('seccion/index.html.twig', [
            'seccions' => $seccionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_seccion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seccion = new Seccion();
        $form = $this->createForm(SeccionType::class, $seccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seccion);
            $entityManager->flush();

            return $this->redirectToRoute('app_seccion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seccion/new.html.twig', [
            'seccion' => $seccion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seccion_show', methods: ['GET'])]
    public function show(Seccion $seccion): Response
    {
        return $this->render('seccion/show.html.twig', [
            'seccion' => $seccion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seccion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seccion $seccion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeccionType::class, $seccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_seccion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seccion/edit.html.twig', [
            'seccion' => $seccion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seccion_delete', methods: ['POST'])]
    public function delete(Request $request, Seccion $seccion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seccion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($seccion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_seccion_index', [], Response::HTTP_SEE_OTHER);
    }
}
