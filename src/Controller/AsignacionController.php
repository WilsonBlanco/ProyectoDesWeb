<?php

namespace App\Controller;

use App\Entity\CursoCarreraSemestre;
use App\Entity\CursoSeccion;
use App\Form\CursoCarreraSemestreType;
use App\Form\CursoSeccionType;
use App\Repository\CarreraRepository;
use App\Repository\CursoRepository;
use App\Repository\SeccionRepository;
use App\Repository\SemestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/asignacion')]
class AsignacionController extends AbstractController
{
    #[Route('/', name: 'app_asignacion_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('asignacion/index.html.twig');
    }

    #[Route('/cursos-carrera-semestre', name: 'app_asignacion_cursos_carrera_semestre', methods: ['GET', 'POST'])]
    public function cursosCarreraSemestre(
        Request $request,
        EntityManagerInterface $entityManager,
        CarreraRepository $carreraRepository,
        CursoRepository $cursoRepository,
        SemestreRepository $semestreRepository
    ): Response {
        $cursoCarreraSemestre = new CursoCarreraSemestre();
        $form = $this->createForm(CursoCarreraSemestreType::class, $cursoCarreraSemestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cursoCarreraSemestre);
            $entityManager->flush();

            $this->addFlash('success', 'Cursos asignados a la carrera y semestre correctamente.');
            return $this->redirectToRoute('app_asignacion_cursos_carrera_semestre');
        }

        $asignaciones = $entityManager->getRepository(CursoCarreraSemestre::class)->findAll();

        return $this->render('asignacion/cursos_carrera_semestre.html.twig', [
            'form' => $form->createView(),
            'asignaciones' => $asignaciones,
            'carreras' => $carreraRepository->findAll(),
            'cursos' => $cursoRepository->findAll(),
            'semestres' => $semestreRepository->findAll(),
        ]);
    }

    #[Route('/secciones-curso', name: 'app_asignacion_secciones_curso', methods: ['GET', 'POST'])]
    public function seccionesCurso(
        Request $request,
        EntityManagerInterface $entityManager,
        SeccionRepository $seccionRepository
    ): Response {
        $cursoSeccion = new CursoSeccion();
        $form = $this->createForm(CursoSeccionType::class, $cursoSeccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cursoSeccion);
            $entityManager->flush();

            $this->addFlash('success', 'Secciones habilitadas para el curso correctamente.');
            return $this->redirectToRoute('app_asignacion_secciones_curso');
        }

        $seccionesHabilitadas = $entityManager->getRepository(CursoSeccion::class)->findAll();

        return $this->render('asignacion/secciones_curso.html.twig', [
            'form' => $form->createView(),
            'seccionesHabilitadas' => $seccionesHabilitadas,
            'secciones' => $seccionRepository->findAll(),
        ]);
    }
}