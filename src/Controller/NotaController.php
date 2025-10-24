<?php

namespace App\Controller;

use App\Entity\Nota;
use App\Entity\Inscripcion;
use App\Form\RegistroNotasType;
use App\Repository\CarreraRepository;
use App\Repository\CursoRepository;
use App\Repository\SeccionRepository;
use App\Repository\SemestreRepository;
use App\Repository\InscripcionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nota')]
class NotaController extends AbstractController
{
    #[Route('/', name: 'app_nota_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('nota/index.html.twig');
    }

    #[Route('/registro', name: 'app_nota_registro', methods: ['GET', 'POST'])]
    public function registro(
        Request $request,
        EntityManagerInterface $entityManager,
        CarreraRepository $carreraRepository,
        CursoRepository $cursoRepository,
        SeccionRepository $seccionRepository,
        SemestreRepository $semestreRepository,
        InscripcionRepository $inscripcionRepository
    ): Response {
        // Inicializar variables
        $alumnosConNotas = [];
        $mostrarFormularioNotas = false;
        $datosSeleccionados = null;

        // Crear formulario de selección
        $formSeleccion = $this->createForm(RegistroNotasType::class);
        $formSeleccion->handleRequest($request);

        // Procesar el formulario de selección (GET o POST)
        if ($formSeleccion->isSubmitted() && $formSeleccion->isValid()) {
            $data = $formSeleccion->getData();
            $carrera = $data['carrera'];
            $curso = $data['curso'];
            $seccion = $data['seccion'];
            $semestre = $data['semestre'];

            $datosSeleccionados = [
                'carrera' => $carrera,
                'curso' => $curso,
                'seccion' => $seccion,
                'semestre' => $semestre
            ];

            $mostrarFormularioNotas = true;

            // Obtener alumnos inscritos para mostrar en el grid
            $inscripciones = $inscripcionRepository->findByCarreraCursoSeccionSemestre(
                $carrera,
                $curso,
                $seccion,
                $semestre
            );

            foreach ($inscripciones as $inscripcion) {
                $alumno = $inscripcion->getAlumno();
                
                // Obtener nota existente si existe
                $notaExistente = $entityManager->getRepository(Nota::class)->findOneBy([
                    'alumno' => $alumno,
                    'curso' => $curso,
                    'seccion' => $seccion,
                    'semestre' => $semestre
                ]);

                $alumnosConNotas[] = [
                    'alumno' => $alumno,
                    'nota' => $notaExistente,
                    'inscripcion' => $inscripcion
                ];
            }

            // Ordenar por apellidos
            usort($alumnosConNotas, function($a, $b) {
                return strcmp($a['alumno']->getApellidos(), $b['alumno']->getApellidos());
            });
        }

        // Procesar el envío del formulario de notas (solo POST con datos específicos)
        if ($request->isMethod('POST') && $request->request->has('guardar_notas')) {
            $carreraId = $request->request->get('carrera_id');
            $cursoId = $request->request->get('curso_id');
            $seccionId = $request->request->get('seccion_id');
            $semestreId = $request->request->get('semestre_id');
            $notasData = $request->request->all('notas');

            if ($carreraId && $cursoId && $seccionId && $semestreId) {
                $carrera = $carreraRepository->find($carreraId);
                $curso = $cursoRepository->find($cursoId);
                $seccion = $seccionRepository->find($seccionId);
                $semestre = $semestreRepository->find($semestreId);

                if ($carrera && $curso && $seccion && $semestre) {
                    $notasRegistradas = 0;
                    $notasActualizadas = 0;

                    foreach ($notasData as $alumnoId => $calificacion) {
                        if ($calificacion !== '') {
                            $alumno = $entityManager->getRepository(\App\Entity\Alumno::class)->find($alumnoId);
                            $calificacion = (float) $calificacion;
                            
                            if ($alumno) {
                                // Verificar si ya existe una nota para este alumno en este curso y semestre
                                $notaExistente = $entityManager->getRepository(Nota::class)->findOneBy([
                                    'alumno' => $alumno,
                                    'curso' => $curso,
                                    'seccion' => $seccion,
                                    'semestre' => $semestre
                                ]);

                                if ($notaExistente) {
                                    // Actualizar nota existente
                                    $notaExistente->setCalificacion($calificacion);
                                    $notasActualizadas++;
                                } else {
                                    // Crear nueva nota
                                    $nota = new Nota();
                                    $nota->setAlumno($alumno);
                                    $nota->setCurso($curso);
                                    $nota->setSeccion($seccion);
                                    $nota->setSemestre($semestre);
                                    $nota->setCalificacion($calificacion);
                                    
                                    $entityManager->persist($nota);
                                    $notasRegistradas++;
                                }
                            }
                        }
                    }

                    $entityManager->flush();

                    if ($notasRegistradas > 0 || $notasActualizadas > 0) {
                        $mensaje = '';
                        if ($notasRegistradas > 0) {
                            $mensaje .= "{$notasRegistradas} nuevas notas registradas. ";
                        }
                        if ($notasActualizadas > 0) {
                            $mensaje .= "{$notasActualizadas} notas actualizadas. ";
                        }
                        $this->addFlash('success', trim($mensaje));
                    } else {
                        $this->addFlash('warning', 'No se registraron nuevas notas.');
                    }

                    return $this->redirectToRoute('app_nota_registro');
                }
            }
        }

        return $this->render('nota/registro.html.twig', [
            'form' => $formSeleccion->createView(),
            'alumnosConNotas' => $alumnosConNotas,
            'mostrarFormularioNotas' => $mostrarFormularioNotas,
            'datosSeleccionados' => $datosSeleccionados,
            'carreras' => $carreraRepository->findAll(),
            'cursos' => $cursoRepository->findAll(),
            'secciones' => $seccionRepository->findAll(),
            'semestres' => $semestreRepository->findBy(['activo' => true]),
        ]);
    }

    #[Route('/historial', name: 'app_nota_historial', methods: ['GET'])]
    public function historial(
        InscripcionRepository $inscripcionRepository,
        Request $request
    ): Response {
        $alumnoId = $request->query->get('alumno_id');
        $notas = [];

        if ($alumnoId) {
            $notas = $inscripcionRepository->findNotasByAlumno($alumnoId);
        }

        return $this->render('nota/historial.html.twig', [
            'notas' => $notas,
            'alumnoId' => $alumnoId,
        ]);
    }
}