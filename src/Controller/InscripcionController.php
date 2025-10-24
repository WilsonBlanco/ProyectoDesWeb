<?php

namespace App\Controller;

use App\Entity\Inscripcion;
use App\Entity\Alumno;
use App\Entity\CursoSeccion;
use App\Form\InscripcionType;
use App\Form\InscripcionMultipleType;
use App\Repository\AlumnoRepository;
use App\Repository\CarreraRepository;
use App\Repository\CursoCarreraSemestreRepository;
use App\Repository\CursoSeccionRepository;
use App\Repository\SemestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/inscripcion')]
class InscripcionController extends AbstractController
{
    #[Route('/', name: 'app_inscripcion_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('inscripcion/index.html.twig');
    }

    #[Route('/individual', name: 'app_inscripcion_individual', methods: ['GET', 'POST'])]
    public function individual(
        Request $request,
        EntityManagerInterface $entityManager,
        AlumnoRepository $alumnoRepository,
        CursoSeccionRepository $cursoSeccionRepository
    ): Response {
        $inscripcion = new Inscripcion();
        $form = $this->createForm(InscripcionType::class, $inscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verificar si ya existe una inscripción para este alumno en esta sección
            $existeInscripcion = $entityManager->getRepository(Inscripcion::class)
                ->findOneBy([
                    'alumno' => $inscripcion->getAlumno(),
                    'cursoSeccion' => $inscripcion->getCursoSeccion()
                ]);

            if ($existeInscripcion) {
                $this->addFlash('error', 'El alumno ya está inscrito en esta sección del curso.');
            } else {
                // Asignar automáticamente el cursoCarreraSemestre desde cursoSeccion
                $cursoSeccion = $inscripcion->getCursoSeccion();
                $inscripcion->setCursoCarreraSemestre($cursoSeccion->getCursoCarreraSemestre());

                $entityManager->persist($inscripcion);
                $entityManager->flush();

                $this->addFlash('success', 'Alumno inscrito correctamente al curso.');
            }

            return $this->redirectToRoute('app_inscripcion_individual');
        }

        $inscripciones = $entityManager->getRepository(Inscripcion::class)->findAll();

        return $this->render('inscripcion/individual.html.twig', [
            'form' => $form->createView(),
            'inscripciones' => $inscripciones,
            'alumnos' => $alumnoRepository->findAll(),
            'cursoSecciones' => $cursoSeccionRepository->findAll(),
        ]);
    }

    #[Route('/multiple', name: 'app_inscripcion_multiple', methods: ['GET', 'POST'])]
    public function multiple(
        Request $request,
        EntityManagerInterface $entityManager,
        AlumnoRepository $alumnoRepository,
        CarreraRepository $carreraRepository,
        SemestreRepository $semestreRepository,
        CursoSeccionRepository $cursoSeccionRepository
    ): Response {
        $form = $this->createForm(InscripcionMultipleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $alumnos = $data['alumnos'] ?? [];
            $cursoSeccion = $data['cursoSeccion'] ?? null;

            if (empty($alumnos)) {
                $this->addFlash('error', 'Debe seleccionar al menos un alumno.');
            } elseif (!$cursoSeccion) {
                $this->addFlash('error', 'Debe seleccionar un curso y sección.');
            } else {
                $inscritos = 0;
                $yaInscritos = 0;

                foreach ($alumnos as $alumno) {
                    // Verificar si ya está inscrito
                    $existeInscripcion = $entityManager->getRepository(Inscripcion::class)
                        ->findOneBy([
                            'alumno' => $alumno,
                            'cursoSeccion' => $cursoSeccion
                        ]);

                    if (!$existeInscripcion) {
                        $inscripcion = new Inscripcion();
                        $inscripcion->setAlumno($alumno);
                        $inscripcion->setCursoSeccion($cursoSeccion);
                        $inscripcion->setCursoCarreraSemestre($cursoSeccion->getCursoCarreraSemestre());
                        
                        $entityManager->persist($inscripcion);
                        $inscritos++;
                    } else {
                        $yaInscritos++;
                    }
                }

                $entityManager->flush();

                $mensaje = "Se han inscrito {$inscritos} alumnos al curso.";
                if ($yaInscritos > 0) {
                    $mensaje .= " {$yaInscritos} alumnos ya estaban inscritos.";
                }

                $this->addFlash('success', $mensaje);
            }

            return $this->redirectToRoute('app_inscripcion_multiple');
        }

        return $this->render('inscripcion/multiple.html.twig', [
            'form' => $form->createView(),
            'carreras' => $carreraRepository->findAll(),
            'semestres' => $semestreRepository->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_inscripcion_delete', methods: ['POST'])]
    public function delete(Request $request, Inscripcion $inscripcion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscripcion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscripcion);
            $entityManager->flush();
            
            $this->addFlash('success', 'Inscripción eliminada correctamente.');
        }

        return $this->redirectToRoute('app_inscripcion_individual');
    }
}