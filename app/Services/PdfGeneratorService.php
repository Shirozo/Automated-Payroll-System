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

    /**
     * Generate a Daily Time Record (DTR) PDF.
     *
     * @param array $data Data to be written into the PDF.
     * @return string Path or standard PDF output.
     */
    public function generateDtrPdf(array $data)
    {
        $this->fpdf->AddPage();
        
        // Example: Title
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->Cell(0, 10, 'Daily Time Record', 0, 1, 'C');
        
        // Example: Subtitle or Employee Name
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, 'Employee: ' . ($data['employee_name'] ?? 'N/A'), 0, 1, 'C');
        $this->fpdf->Ln(5);

        // Example: Table Header
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(30, 10, 'Date', 1, 0, 'C');
        $this->fpdf->Cell(40, 10, 'Morning In', 1, 0, 'C');
        $this->fpdf->Cell(40, 10, 'Morning Out', 1, 0, 'C');
        $this->fpdf->Cell(40, 10, 'Afternoon In', 1, 0, 'C');
        $this->fpdf->Cell(40, 10, 'Afternoon Out', 1, 1, 'C');

        // Example: Table Data
        $this->fpdf->SetFont('Arial', '', 10);
        if (isset($data['attendance']) && is_array($data['attendance'])) {
            foreach ($data['attendance'] as $record) {
                $this->fpdf->Cell(30, 10, $record['date'] ?? '', 1, 0, 'C');
                $this->fpdf->Cell(40, 10, $record['am_in'] ?? '', 1, 0, 'C');
                $this->fpdf->Cell(40, 10, $record['am_out'] ?? '', 1, 0, 'C');
                $this->fpdf->Cell(40, 10, $record['pm_in'] ?? '', 1, 0, 'C');
                $this->fpdf->Cell(40, 10, $record['pm_out'] ?? '', 1, 1, 'C');
            }
        }

        // Output the PDF to browser directly
        // I for inline rendering, D for download, F to save to local file
        $this->fpdf->Output('I', 'attendance_dtr.pdf');
        exit;
    }
}
