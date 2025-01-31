<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\teacherService;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherReportController extends Controller
{
    public function __construct(protected teacherService $teacherService)
    {}
    public function exportPDF()
    {
        $teacherReport = $this->teacherService->reportData();
        $pdf = Pdf::loadView('dashboard.reports.pdf-content', ['teacher_report' => $teacherReport]);
        return $pdf->download('teacher_report.pdf');
    }
}
