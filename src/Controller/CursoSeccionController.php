<?php

namespace App\Controller;

use App\Entity\CursoSeccion;
use App\Form\CursoSeccionType;
use App\Repository\CursoSeccionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/curso/seccion')]
class CursoSeccionController extends AbstractController
{
    #[Route('/', name: 'app_curso_seccion_index', methods: ['GET'])]
    public function index(CursoSeccionRepository $cursoSeccionRepository): Response
    {
        return $this->render('curso_seccion/index.html.twig', [
            'curso_seccions' => $cursoSeccionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_curso_seccion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cursoSeccion = new CursoSeccion();
        $form = $this->createForm(CursoSeccionType::class, $cursoSeccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cursoSeccion);
            $entityManager->flush();

            $this->addFlash('success', 'Sección habilitada para el curso correctamente.');

            return $this->redirectToRoute('app_curso_seccion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('curso_seccion/new.html.twig', [
            'curso_seccion' => $cursoSeccion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_curso_seccion_show', methods: ['GET'])]
    public function show(CursoSeccion $cursoSeccion): Response
    {
        return $this->render('curso_seccion/show.html.twig', [
            'curso_seccion' => $cursoSeccion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_curso_seccion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CursoSeccion $cursoSeccion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CursoSeccionType::class, $cursoSeccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Sección habilitada actualizada correctamente.');

            return $this->redirectToRoute('app_curso_seccion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('curso_seccion/edit.html.twig', [
            'curso_seccion' => $cursoSeccion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_curso_seccion_delete', methods: ['POST'])]
    public function delete(Request $request, CursoSeccion $cursoSeccion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cursoSeccion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cursoSeccion);
            $entityManager->flush();

            $this->addFlash('success', 'Sección habilitada eliminada correctamente.');
        }

        return $this->redirectToRoute('app_asignacion_secciones_curso');
    }
}