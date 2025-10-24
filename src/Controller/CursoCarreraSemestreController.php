<?php

namespace App\Controller;

use App\Entity\CursoCarreraSemestre;
use App\Form\CursoCarreraSemestreType;
use App\Repository\CursoCarreraSemestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/curso/carrera/semestre')]
class CursoCarreraSemestreController extends AbstractController
{
    #[Route('/', name: 'app_curso_carrera_semestre_index', methods: ['GET'])]
    public function index(CursoCarreraSemestreRepository $cursoCarreraSemestreRepository): Response
    {
        return $this->render('curso_carrera_semestre/index.html.twig', [
            'curso_carrera_semestres' => $cursoCarreraSemestreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_curso_carrera_semestre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cursoCarreraSemestre = new CursoCarreraSemestre();
        $form = $this->createForm(CursoCarreraSemestreType::class, $cursoCarreraSemestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cursoCarreraSemestre);
            $entityManager->flush();

            $this->addFlash('success', 'Curso asignado a carrera y semestre correctamente.');

            return $this->redirectToRoute('app_curso_carrera_semestre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('curso_carrera_semestre/new.html.twig', [
            'curso_carrera_semestre' => $cursoCarreraSemestre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_curso_carrera_semestre_show', methods: ['GET'])]
    public function show(CursoCarreraSemestre $cursoCarreraSemestre): Response
    {
        return $this->render('curso_carrera_semestre/show.html.twig', [
            'curso_carrera_semestre' => $cursoCarreraSemestre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_curso_carrera_semestre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CursoCarreraSemestre $cursoCarreraSemestre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CursoCarreraSemestreType::class, $cursoCarreraSemestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Asignación actualizada correctamente.');

            return $this->redirectToRoute('app_curso_carrera_semestre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('curso_carrera_semestre/edit.html.twig', [
            'curso_carrera_semestre' => $cursoCarreraSemestre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_curso_carrera_semestre_delete', methods: ['POST'])]
    public function delete(Request $request, CursoCarreraSemestre $cursoCarreraSemestre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cursoCarreraSemestre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cursoCarreraSemestre);
            $entityManager->flush();

            $this->addFlash('success', 'Asignación eliminada correctamente.');
        }

        return $this->redirectToRoute('app_asignacion_cursos_carrera_semestre');
    }
}