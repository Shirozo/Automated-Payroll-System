<?php

namespace App\Services;

use Codedge\Fpdf\Fpdf\Fpdf;

class PdfGeneratorService
{
    protected $fpdf;

    public function __construct(Fpdf $fpdf)
    {
        $this->fpdf = $fpdf;
    }

    public function generateDtrPdf(array $data)
    {
        $this->fpdf->AddPage();
        
        $this->fpdf->SetFont('Arial', 'B', 14);
        $this->fpdf->Cell(0, 10, 'Daily Time Record', 0, 1, 'C');
        $this->fpdf->Ln(-3);
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(0, 10, 'Date : ' . $data['month'] . " " . $data['year'], 0, 1, 'C');
        $this->fpdf->Ln(-5);
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(0, 10, 'Employee: ' . ($data['employee_name'] ?? 'N/A'), 0, 1, 'C');
        $this->fpdf->Ln(5);

        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(30, 7, 'Date', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'Morning In', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'Morning Out', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'Afternoon In', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'Afternoon Out', 1, 1, 'C');

        $this->fpdf->SetFont('Arial', '', 10);
        if (isset($data['attendance']) && is_array($data['attendance'])) {
            foreach ($data['attendance'] as $record) {
                $this->fpdf->Cell(30, 6, $record['date'] ?? '', 1, 0, 'C');
                $this->fpdf->Cell(40, 6, !empty($record['am_in']) ? date('h:i A', strtotime($record['am_in'])) : '', 1, 0, 'C');
                $this->fpdf->Cell(40, 6, !empty($record['am_out']) ? date('h:i A', strtotime($record['am_out'])) : '', 1, 0, 'C');
                $this->fpdf->Cell(40, 6, !empty($record['pm_in']) ? date('h:i A', strtotime($record['pm_in'])) : '', 1, 0, 'C');
                $this->fpdf->Cell(40, 6, !empty($record['pm_out']) ? date('h:i A', strtotime($record['pm_out'])) : '', 1, 1, 'C');
            }
        }

        $this->fpdf->Output('I', $data["employee_name"] . "-" . $data['month'] . "-" . $data['year'] . ' DTR.pdf');
        exit;
    }
}
