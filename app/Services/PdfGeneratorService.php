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

        $this->fpdf->SetFont('Arial', '', 14);
        $this->fpdf->Cell(0, 10, 'Daily Time Record', 0, 1, 'C');
        $this->fpdf->Ln(-3);
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(20, 10, '', 0, 0, 'C');
        $this->fpdf->Cell(20, 10, 'EMPLOYEE: ', 0, 0, 'C');
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(40, 10, ($data['employee_name'] ?? 'N/A'), 0, 0, 'L');
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(15, 10, 'MONTH: ', 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(40, 10, ($data['month'] ?? 'N/A'), 0, 0, 'L');
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(13, 10, 'YEAR:', 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(50, 10, ($data['year'] ?? 'N/A'), 0, 1, 'L');
        $this->fpdf->Ln(5);

        $this->fpdf->SetFont('Arial', 'B', 8);
        $this->fpdf->Cell(20, 7, '', 0, 0, 'C');
        $this->fpdf->Cell(20, 7, 'DAY', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'AM', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'PM', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'HOURS', 1, 1, 'C');

        $this->fpdf->Cell(20, 7, '', 0, 0, 'C');
        $this->fpdf->Cell(7, 7, '', 1, 0, 'C');
        $this->fpdf->Cell(13, 7, '', 1, 0, 'C');
        $this->fpdf->Cell(20, 7, 'IN', 1, 0, 'C');
        $this->fpdf->Cell(20, 7, 'OUT', 1, 0, 'C');
        $this->fpdf->Cell(20, 7, 'IN', 1, 0, 'C');
        $this->fpdf->Cell(20, 7, 'OUT', 1, 0, 'C');
        $this->fpdf->Cell(40, 7, 'HOURS', 1, 1, 'C');

        $this->fpdf->SetFont('Arial', '', 8);
        if (isset($data['attendance']) && is_array($data['attendance'])) {
            foreach ($data['attendance'] as $record) {
                $this->fpdf->Cell(20, 5, '', 0, 0, 'C');
                $this->fpdf->Cell(7, 5, $record['date'], 1, 0, 'C');
                $this->fpdf->Cell(13, 5, $record['day'], 1, 0, 'C');
                $this->fpdf->Cell(20, 5, !empty($record['am_in']) ? date('h:i A', strtotime($record['am_in'])) : '', 1, 0, 'C');
                $this->fpdf->Cell(20, 5, !empty($record['am_out']) ? date('h:i A', strtotime($record['am_out'])) : '', 1, 0, 'C');
                $this->fpdf->Cell(20, 5, !empty($record['pm_in']) ? date('h:i A', strtotime($record['pm_in'])) : '', 1, 0, 'C');
                $this->fpdf->Cell(20, 5, !empty($record['pm_out']) ? date('h:i A', strtotime($record['pm_out'])) : '', 1, 0, 'C');
                $this->fpdf->Cell(40, 5, $record['hours_rendered'] > 0 ? $record['hours_rendered'] : "", 1, 1, 'C');
            }
        }

        $this->fpdf->Ln(5);
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(40, 7, "I CERTIFY", 0, 0, "R");
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(0, 7, "on my honor that the above is true and correct report of hours of work ", 0, 1, "L");
        $this->fpdf->Cell(17, 7, "", 0, 0, "L");
        $this->fpdf->Cell(0, 7, "performed, record of which was made daily at the time of arrival and departure from the office.", 0, 1, "L");
        $this->fpdf->Ln(10);
        $this->fpdf->Cell(0, 7, "_______________________", 0, 1, "C");
        $this->fpdf->Cell(0, 7, "Signature", 0, 1, "C");
        $this->fpdf->Ln(10);
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(85, 7, "VERIFIED", 0, 0, "R");
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(85, 7, "as to the prescribed office hours:", 0, 1, "L");
        $this->fpdf->Ln(10);
        $this->fpdf->Cell(0, 7, "_______________________", 0, 1, "C");
        $this->fpdf->Cell(0, 7, "In-Charge", 0, 1, "C");

        $this->fpdf->Output('I', $data["employee_name"] . "-" . $data['month'] . "-" . $data['year'] . ' DTR.pdf');
        exit;
    }
}
