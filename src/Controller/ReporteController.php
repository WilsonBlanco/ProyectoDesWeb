<?php

namespace App\Controller;

use App\Repository\AlumnoRepository;
use App\Repository\CarreraRepository;
use App\Repository\CursoRepository;
use App\Repository\NotaRepository;
use App\Repository\SeccionRepository;
use App\Repository\SemestreRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reporte')]
class ReporteController extends AbstractController
{
    #[Route('/', name: 'app_reporte_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('reporte/index.html.twig');
    }

    #[Route('/alumnos-por-carrera', name: 'app_reporte_alumnos_carrera', methods: ['GET', 'POST'])]
    public function alumnosPorCarrera(
        Request $request,
        CarreraRepository $carreraRepository,
        AlumnoRepository $alumnoRepository
    ): Response {
        $carreraId = $request->query->get('carrera_id');
        $formato = $request->query->get('formato');
        
        $carrera = null;
        $alumnos = [];

        if ($carreraId) {
            $carrera = $carreraRepository->find($carreraId);
            if ($carrera) {
                $alumnos = $alumnoRepository->findBy(['carrera' => $carrera], ['apellidos' => 'ASC', 'nombres' => 'ASC']);
            }
        }

        // Exportar a PDF
        if ($formato === 'pdf' && $carrera) {
            return $this->generarPdfAlumnosCarrera($alumnos, $carrera);
        }

        // Exportar a Excel
        if ($formato === 'excel' && $carrera) {
            return $this->generarExcelAlumnosCarrera($alumnos, $carrera);
        }

        return $this->render('reporte/alumnos_carrera.html.twig', [
            'carreras' => $carreraRepository->findAll(),
            'carreraSeleccionada' => $carrera,
            'alumnos' => $alumnos,
        ]);
    }

    #[Route('/notas-por-curso', name: 'app_reporte_notas_curso', methods: ['GET', 'POST'])]
    public function notasPorCurso(
        Request $request,
        CarreraRepository $carreraRepository,
        CursoRepository $cursoRepository,
        SeccionRepository $seccionRepository,
        SemestreRepository $semestreRepository,
        NotaRepository $notaRepository
    ): Response {
        $carreraId = $request->query->get('carrera_id');
        $cursoId = $request->query->get('curso_id');
        $seccionId = $request->query->get('seccion_id');
        $semestreId = $request->query->get('semestre_id');
        $formato = $request->query->get('formato');

        $carrera = null;
        $curso = null;
        $seccion = null;
        $semestre = null;
        $notas = [];

        if ($carreraId && $cursoId && $seccionId && $semestreId) {
            $carrera = $carreraRepository->find($carreraId);
            $curso = $cursoRepository->find($cursoId);
            $seccion = $seccionRepository->find($seccionId);
            $semestre = $semestreRepository->find($semestreId);

            if ($carrera && $curso && $seccion && $semestre) {
                $notas = $notaRepository->findByCursoCarreraSeccionSemestre($curso, $carrera, $seccion, $semestre);
            }
        }

        // Exportar a PDF
        if ($formato === 'pdf' && $carrera && $curso && $seccion && $semestre) {
            return $this->generarPdfNotasCurso($notas, $curso, $carrera, $seccion, $semestre);
        }

        // Exportar a Excel
        if ($formato === 'excel' && $carrera && $curso && $seccion && $semestre) {
            return $this->generarExcelNotasCurso($notas, $curso, $carrera, $seccion, $semestre);
        }

        return $this->render('reporte/notas_curso.html.twig', [
            'carreras' => $carreraRepository->findAll(),
            'cursos' => $cursoRepository->findAll(),
            'secciones' => $seccionRepository->findAll(),
            'semestres' => $semestreRepository->findBy([], ['fechaInicio' => 'DESC']),
            'carreraSeleccionada' => $carrera,
            'cursoSeleccionado' => $curso,
            'seccionSeleccionada' => $seccion,
            'semestreSeleccionado' => $semestre,
            'notas' => $notas,
        ]);
    }

    #[Route('/consulta-alumno', name: 'app_reporte_consulta_alumno', methods: ['GET'])]
    public function consultaAlumno(
        Request $request,
        AlumnoRepository $alumnoRepository,
        NotaRepository $notaRepository
    ): Response {
        $alumnoId = $request->query->get('alumno_id');
        $formato = $request->query->get('formato');

        $alumno = null;
        $notas = [];

        if ($alumnoId) {
            $alumno = $alumnoRepository->find($alumnoId);
            if ($alumno) {
                $notas = $notaRepository->findBy(['alumno' => $alumno], ['semestre' => 'DESC', 'curso' => 'ASC']);
            }
        }

        // Exportar a PDF
        if ($formato === 'pdf' && $alumno) {
            return $this->generarPdfConsultaAlumno($alumno, $notas);
        }

        // Exportar a Excel
        if ($formato === 'excel' && $alumno) {
            return $this->generarExcelConsultaAlumno($alumno, $notas);
        }

        return $this->render('reporte/consulta_alumno.html.twig', [
            'alumnos' => $alumnoRepository->findAll(),
            'alumnoSeleccionado' => $alumno,
            'notas' => $notas,
        ]);
    }

    // Métodos para generar PDFs
    private function generarPdfAlumnosCarrera(array $alumnos, $carrera): Response
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $html = $this->renderView('reporte/pdf/alumnos_carrera.html.twig', [
            'alumnos' => $alumnos,
            'carrera' => $carrera,
            'fecha' => new \DateTime(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="alumnos_' . $carrera->getNombre() . '.pdf"'
            ]
        );
    }

    private function generarPdfNotasCurso(array $notas, $curso, $carrera, $seccion, $semestre): Response
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $html = $this->renderView('reporte/pdf/notas_curso.html.twig', [
            'notas' => $notas,
            'curso' => $curso,
            'carrera' => $carrera,
            'seccion' => $seccion,
            'semestre' => $semestre,
            'fecha' => new \DateTime(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="notas_' . $curso->getNombre() . '_' . $seccion->getNombre() . '.pdf"'
            ]
        );
    }

private function generarPdfConsultaAlumno($alumno, array $notas): Response
{
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true); // Habilitar carga remota
    $options->set('chroot', $this->getParameter('kernel.project_dir') . '/public');
    
    $dompdf = new Dompdf($options);

    // Preparar datos para la foto
    $fotoBase64 = null;
    if ($alumno->getFotografia()) {
        $fotoPath = $this->getParameter('kernel.project_dir') . '/public/uploads/fotos_alumnos/' . $alumno->getFotografia();
        if (file_exists($fotoPath)) {
            $fotoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($fotoPath));
        }
    }

    $html = $this->renderView('reporte/pdf/consulta_alumno.html.twig', [
        'alumno' => $alumno,
        'notas' => $notas,
        'fecha' => new \DateTime(),
        'foto_base64' => $fotoBase64, // Pasar la foto en base64
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="historial_' . $alumno->getApellidos() . '_' . $alumno->getNombres() . '.pdf"'
        ]
    );
}

    // Métodos para generar Excel
    private function generarExcelAlumnosCarrera(array $alumnos, $carrera): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Reporte de Alumnos por Carrera');
        $sheet->setCellValue('A2', 'Carrera: ' . $carrera->getNombre());
        $sheet->setCellValue('A3', 'Fecha: ' . (new \DateTime())->format('d/m/Y'));

        $sheet->setCellValue('A5', 'ID');
        $sheet->setCellValue('B5', 'Nombres');
        $sheet->setCellValue('C5', 'Apellidos');
        $sheet->setCellValue('D5', 'Fecha Nacimiento');
        $sheet->setCellValue('E5', 'Carrera');

        // Datos
        $row = 6;
        foreach ($alumnos as $alumno) {
            $sheet->setCellValue('A' . $row, $alumno->getId());
            $sheet->setCellValue('B' . $row, $alumno->getNombres());
            $sheet->setCellValue('C' . $row, $alumno->getApellidos());
            $sheet->setCellValue('D' . $row, $alumno->getFechaNacimiento()->format('d/m/Y'));
            $sheet->setCellValue('E' . $row, $alumno->getCarrera()->getNombre());
            $row++;
        }

        // Autoajustar columnas
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="alumnos_' . $carrera->getNombre() . '.xlsx"'
            ]
        );
    }

    private function generarExcelNotasCurso(array $notas, $curso, $carrera, $seccion, $semestre): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Reporte de Notas por Curso');
        $sheet->setCellValue('A2', 'Curso: ' . $curso->getNombre());
        $sheet->setCellValue('A3', 'Carrera: ' . $carrera->getNombre());
        $sheet->setCellValue('A4', 'Sección: ' . $seccion->getNombre());
        $sheet->setCellValue('A5', 'Semestre: ' . $semestre->getNombre());
        $sheet->setCellValue('A6', 'Fecha: ' . (new \DateTime())->format('d/m/Y'));

        $sheet->setCellValue('A8', 'ID Alumno');
        $sheet->setCellValue('B8', 'Nombres');
        $sheet->setCellValue('C8', 'Apellidos');
        $sheet->setCellValue('D8', 'Nota');
        $sheet->setCellValue('E8', 'Estado');
        $sheet->setCellValue('F8', 'Fecha Registro');

        // Datos
        $row = 9;
        foreach ($notas as $nota) {
            $sheet->setCellValue('A' . $row, $nota->getAlumno()->getId());
            $sheet->setCellValue('B' . $row, $nota->getAlumno()->getNombres());
            $sheet->setCellValue('C' . $row, $nota->getAlumno()->getApellidos());
            $sheet->setCellValue('D' . $row, $nota->getCalificacion());
            $sheet->setCellValue('E' . $row, $nota->isAprobado() ? 'Aprobado' : 'Reprobado');
            $sheet->setCellValue('F' . $row, $nota->getFechaRegistro()->format('d/m/Y H:i'));
            $row++;
        }

        // Autoajustar columnas
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="notas_' . $curso->getNombre() . '_' . $seccion->getNombre() . '.xlsx"'
            ]
        );
    }

    private function generarExcelConsultaAlumno($alumno, array $notas): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Historial Académico - ' . $alumno->getNombreCompleto());
        $sheet->setCellValue('A2', 'Carrera: ' . $alumno->getCarrera()->getNombre());
        $sheet->setCellValue('A3', 'Fecha Nacimiento: ' . $alumno->getFechaNacimiento()->format('d/m/Y'));
        $sheet->setCellValue('A4', 'Fecha Consulta: ' . (new \DateTime())->format('d/m/Y'));

        $sheet->setCellValue('A6', 'Semestre');
        $sheet->setCellValue('B6', 'Curso');
        $sheet->setCellValue('C6', 'Sección');
        $sheet->setCellValue('D6', 'Nota');
        $sheet->setCellValue('E6', 'Estado');
        $sheet->setCellValue('F6', 'Fecha Registro');

        // Datos
        $row = 7;
        foreach ($notas as $nota) {
            $sheet->setCellValue('A' . $row, $nota->getSemestre()->getNombre());
            $sheet->setCellValue('B' . $row, $nota->getCurso()->getNombre());
            $sheet->setCellValue('C' . $row, $nota->getSeccion()->getNombre());
            $sheet->setCellValue('D' . $row, $nota->getCalificacion());
            $sheet->setCellValue('E' . $row, $nota->isAprobado() ? 'Aprobado' : 'Reprobado');
            $sheet->setCellValue('F' . $row, $nota->getFechaRegistro()->format('d/m/Y H:i'));
            $row++;
        }

        // Autoajustar columnas
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="historial_' . $alumno->getApellidos() . '_' . $alumno->getNombres() . '.xlsx"'
            ]
        );
    }
}