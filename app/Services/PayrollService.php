<?php

namespace App\Services;

use Codedge\Fpdf\Fpdf\Fpdf;

class PayrollPdf extends Fpdf
{
    public $month;
    public $year;
    public $colWidths = [];
    public $renderTableHeader = true;

    function Header()
    {
        // Use the widths passed from the service
        $w = $this->colWidths;
        if (empty($w)) {
            // Fallback default widths just in case
            $w = [
                 8, 42, 25, 14, 15, 10, 15, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 8, 20, 20, 25
            ];
        }

        // --- Header Section ---
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, 'GENERAL PAYROLL', 0, 1, 'C');
        
        $this->SetFont('Arial', 'I', 9);
        $m = $this->month ? strtoupper($this->month) : 'JUNE';
        $y = $this->year ?? '2025';
        $this->Cell(0, 5, "For the month of $m, $y", 0, 1, 'C');
        
        $this->Ln(2);

        $this->SetFont('Arial', 'B', 9);
        // Header Info Row 1
        $this->Cell(10, 5, '', 0, 0); 
        $this->Cell(150, 5, 'Entity Name: EASTERN SAMAR STATE UNIVERSITY', 0, 0, 'L');
        $this->Cell(0, 5, 'Payroll No.: _________________', 0, 1, 'R');
        
        // Header Info Row 2
        $this->Cell(10, 5, '', 0, 0); 
        $this->Cell(150, 5, 'Fund Cluster: _________________', 0, 0, 'L');
        $this->Cell(0, 5, 'Sheet ' . $this->PageNo() . ' of {nb} Sheets', 0, 1, 'R');
        
        $this->Ln(2);

        // --- Table Header ---
        if ($this->renderTableHeader) {
            $this->SetFont('Arial', 'B', 6);
            $h = 14; 
            $yPos = $this->GetY();
            $xPos = $this->GetX(); 

            // Draw Row 1 Boxes
            // Num (0)
            $this->Rect($xPos, $yPos, $w[0], $h); 
            $this->SetXY($xPos, $yPos+4); $this->Cell($w[0], 3, 'Num', 0, 0, 'C'); 
            $this->SetXY($xPos, $yPos+8); $this->Cell($w[0], 3, 'ber', 0, 0, 'C'); 
            
            $currentX = $xPos + $w[0];
            
            // Name (1)
            $this->SetXY($currentX, $yPos);
            $this->Cell($w[1], $h, 'Name', 1, 0, 'C');
            $currentX += $w[1];

            // Position (2)
            $this->SetXY($currentX, $yPos);
            $this->Cell($w[2], $h, 'Position', 1, 0, 'C');
            $currentX += $w[2];

            // Employee No (3)
            $this->SetXY($currentX, $yPos);
            $this->MultiCell($w[3], $h/2, "EMPLOYEE\nNO.", 1, 'C');
            $currentX += $w[3];

            // COMPENSATIONS Group (4,5,6)
            $this->SetXY($currentX, $yPos);
            $compWidth = $w[4] + $w[5] + $w[6];
            $this->Cell($compWidth, 5, 'COMPENSATIONS', 1, 0, 'C');
            
            // DEDUCTIONS Group (7 to 21)
            $dedWidth = 0; 
            for($i=7; $i<=21; $i++) $dedWidth += $w[$i];
            $this->SetXY($currentX + $compWidth, $yPos);
            $this->Cell($dedWidth, 5, 'DEDUCTIONS', 1, 0, 'C');

            // End Group
            $endX = $currentX + $compWidth + $dedWidth;
            
            // Num (22)
            $this->SetXY($endX, $yPos);
            $this->Rect($endX, $yPos, $w[22], $h);
            $this->SetXY($endX, $yPos+4); $this->Cell($w[22], 3, 'Num', 0, 0, 'C');
            $this->SetXY($endX, $yPos+8); $this->Cell($w[22], 3, 'ber', 0, 0, 'C');

            // Total Ded (23)
            $this->SetXY($endX + $w[22], $yPos);
            $this->Rect($endX + $w[22], $yPos, $w[23], $h);
            $this->SetXY($endX + $w[22], $yPos+2);
            $this->MultiCell($w[23], 4, "Total\nDeductions", 0, 'C');

            // Net Amount (24)
            $this->Rect($endX + $w[22] + $w[23], $yPos, $w[24], $h);
            $this->SetXY($endX + $w[22] + $w[23], $yPos+2);
            $this->MultiCell($w[24], 4, "Net\nAmount Due", 0, 'C');

            // Account Number (25)
            $this->Rect($endX + $w[22] + $w[23] + $w[24], $yPos, $w[25], $h);
            $this->SetXY($endX + $w[22] + $w[23] + $w[24], $yPos+5); 
            $this->Cell($w[25], 4, "Account Number", 0, 1, 'C'); 


            // --- Sub-Headers (Row 2 Start) ---
            $subHeaderY = $yPos + 5;
            
            // Rate (4)
            $this->SetXY($xPos + $w[0]+$w[1]+$w[2]+$w[3], $subHeaderY);
            $this->Rect($this->GetX(), $subHeaderY, $w[4], 9);
            $this->MultiCell($w[4], 3, "Rate per\nmonth", 0, 'C');

            // PERA (5)
            $this->SetXY($xPos + $w[0]+$w[1]+$w[2]+$w[3]+$w[4], $subHeaderY);
            $this->Rect($this->GetX(), $subHeaderY, $w[5], 9);
            $this->MultiCell($w[5], 3, "\nPERA", 0, 'C'); 

            // Earned (6)
            $this->SetXY($xPos + $w[0]+$w[1]+$w[2]+$w[3]+$w[4]+$w[5], $subHeaderY);
            $this->Rect($this->GetX(), $subHeaderY, $w[6], 9);
            $this->MultiCell($w[6], 3, "Earned for\nthe period", 0, 'C');

            // Deductions Sub-Columns
            $dedStartX = $xPos + $w[0]+$w[1]+$w[2]+$w[3]+$w[4]+$w[5]+$w[6];
            $curDedX = $dedStartX;

            // GSIS MPL (7)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[7], 9);
            $this->MultiCell($w[7], 3, "GSIS\nMPL", 0, 'C');
            $curDedX += $w[7];

            // Phil Health (8)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[8], 9);
            $this->MultiCell($w[8], 3, "Phil.\nHealth", 0, 'C');
            $curDedX += $w[8];

            // Local PAVE (9)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[9], 9);
            $this->MultiCell($w[9], 3, "Local\nPAVE", 0, 'C');
            $curDedX += $w[9];

            // Life & Ret (10)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[10], 9);
            $this->MultiCell($w[10], 3, "Life &\nRetire", 0, 'C');
            $curDedX += $w[10];

            // PAG-IBIG GROUP (11,12,13)
            $pagibigW = $w[11] + $w[12] + $w[13];
            $this->SetXY($curDedX, $subHeaderY);
            $this->Cell($pagibigW, 4, 'PAG-IBIG', 1, 0, 'C');
            
            $pagibigSubY = $subHeaderY + 4;
            
            // Pagibig Prem (11)
            $this->SetXY($curDedX, $pagibigSubY);
            $this->Cell($w[11], 5, 'Premium', 1, 0, 'C');
            $curDedX += $w[11];
            
            // Pagibig MP3 (12)
            $this->SetXY($curDedX, $pagibigSubY);
            $this->Cell($w[12], 5, "MP3/Loc", 1, 0, 'C'); 
            $curDedX += $w[12];
            
            // Pagibig Cal (13)
            $this->SetXY($curDedX, $pagibigSubY);
            $this->Cell($w[13], 5, "Calamity", 1, 0, 'C'); 
            $curDedX += $w[13];

            // Back to main Deductions line (height 9)
            // City Sav (14)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[14], 9);
            $this->MultiCell($w[14], 3, "City Savings\nBank", 0, 'C');
            $curDedX += $w[14];

            // Withholding (15)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[15], 9);
            $this->MultiCell($w[15], 3, "Withhold\n-ing tax", 0, 'C');
            $curDedX += $w[15];

            // Absence (16)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[16], 9);
            $this->MultiCell($w[16], 3, "Absence\nw/out pay", 0, 'C');
            $curDedX += $w[16];

            // IGP (17)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[17], 9);
            $this->MultiCell($w[17], 3, "IGP\nCottage", 0, 'C'); 
            $curDedX += $w[17];

            // ESSU FFA (18)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[18], 9);
            $this->MultiCell($w[18], 3, "ESSU\nFFA", 0, 'C');
            $curDedX += $w[18];

            // Retiree (19)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[19], 9);
            $this->MultiCell($w[19], 3, "Retiree's\nFin. Asst", 0, 'C');
            $curDedX += $w[19];

            // Union (20)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[20], 9);
            $this->MultiCell($w[20], 3, "ESSU\nUnion", 0, 'C');
            $curDedX += $w[20];

            // CFI (21)
            $this->SetXY($curDedX, $subHeaderY);
            $this->Rect($curDedX, $subHeaderY, $w[21], 9);
            $this->MultiCell($w[21], 3, "\nCFI", 0, 'C');
            
            // Move to Data start position
            $this->SetXY($xPos, $yPos + $h);
        }
    }
}

class PayrollService
{
    protected $fpdf;

    public function __construct(Fpdf $fpdf)
    {
        $this->fpdf = $fpdf;
    }

    public function generatePayrollPdf(array $data)
    {
        // Use custom PayrollPdf class instead of injected Fpdf
        $pdf = new PayrollPdf('L', 'mm', 'Legal');
        
        $pdf->month = isset($data['month']) ? strtoupper($data['month']) : 'JUNE';
        $pdf->year = $data['year'] ?? '2025';

        // Columns configuration
        $w = [
             8, // 0: Num
            42, // 1: Name       (Increased for full name)
            25, // 2: Position   (Increased)
            14, // 3: Emp No     (Increased)
            15, // 4: Rate
            10, // 5: PERA
            15, // 6: Earned
             9, // 7: GSIS MPL
             9, // 8: PhilHealth
             9, // 9: Local PAVE
             9, // 10: Life & Ret
             9, // 11: Pagibig Prem
             9, // 12: Pagibig MP3
             9, // 13: Pagibig Cal
             9, // 14: City Sav
             9, // 15: Withholding
             9, // 16: Absence
             9, // 17: IGP Cottage
             9, // 18: ESSU FFA
             9, // 19: Retiree Fin
             9, // 20: ESSU Union
             9, // 21: CFI
             8, // 22: Num
            20, // 23: Total Ded
            20, // 24: Net Amount
            25  // 25: Account No
        ];
        
        $pdf->colWidths = $w;
        $totalTableWidth = array_sum($w);

        $pdf->AliasNbPages(); 
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage('L', 'Legal');

        // --- Table Body ---
        $pdf->SetFont('Arial', '', 7); 
        $employees = $data['employees'] ?? [];
        
        // Fill empty rows to make it look like a full sheet
        if (count($employees) < 20) {
             for ($i = count($employees); $i < 40; $i++) {
                 $employees[] = [];
             }
        }

        foreach ($employees as $index => $emp) {
            $rowH = 5.5; // Row height
            
            // 0: Num
            $pdf->Cell($w[0], $rowH, $index + 1, 1, 0, 'C');
            // 1: Name
            $pdf->Cell($w[1], $rowH, isset($emp['name']) ? strtoupper($emp['name']) : '', 1, 0, 'L');
            // 2: Position
            $pdf->Cell($w[2], $rowH, $emp['position'] ?? '', 1, 0, 'L');
            // 3: Emp No
            $pdf->Cell($w[3], $rowH, $emp['employee_no'] ?? '', 1, 0, 'C');
            // 4: Rate
            $pdf->Cell($w[4], $rowH, isset($emp['rate']) ? number_format($emp['rate'], 2) : '', 1, 0, 'R');
            // 5: PERA
            $pdf->Cell($w[5], $rowH, isset($emp['pera']) ? number_format($emp['pera'], 2) : '', 1, 0, 'R');
            // 6: Earned
            $pdf->Cell($w[6], $rowH, isset($emp['earned']) ? number_format($emp['earned'], 2) : '', 1, 0, 'R');
            
            // Deductions 7-21
            // In a real app, map these keys correctly. simulating empty/formatted
            for($j=7; $j<=21; $j++) {
                $pdf->Cell($w[$j], $rowH, '', 1, 0, 'R'); 
            }
            
            // 22: Num
            $pdf->Cell($w[22], $rowH, $index + 1, 1, 0, 'C');
            
            // 23: Total Ded
            $pdf->Cell($w[23], $rowH, isset($emp['total_deductions']) ? number_format($emp['total_deductions'], 2) : '', 1, 0, 'R');
            
            // 24: Net
            $pdf->Cell($w[24], $rowH, isset($emp['net_amount']) ? number_format($emp['net_amount'], 2) : '', 1, 0, 'R');
            
            // 25: Account No
            $pdf->Cell($w[25], $rowH, $emp['account_number'] ?? '', 1, 1, 'R');
        }
        
        // --- TOTALS ROW ---
        $pdf->SetFont('Arial', 'B', 7);
        $totalLeftW = $w[0] + $w[1] + $w[2] + $w[3];
        $pdf->Cell($totalLeftW, 5.5, 'TOTALS', 1, 0, 'C');
        
        // Compensations Totals
        $pdf->Cell($w[4], 5.5, '0.00', 1, 0, 'R');
        $pdf->Cell($w[5], 5.5, '0.00', 1, 0, 'R');
        $pdf->Cell($w[6], 5.5, '0.00', 1, 0, 'R');
        
        // Deductions Totals
        for($j=7; $j<=21; $j++) {
            $pdf->Cell($w[$j], 5.5, '0.00', 1, 0, 'R');
        }
        
        // End Totals
        $pdf->Cell($w[22], 5.5, '', 1, 0, 'C'); // Num
        $pdf->Cell($w[23], 5.5, '0.00', 1, 0, 'R'); // Total Ded
        $pdf->Cell($w[24], 5.5, '0.00', 1, 0, 'R'); // Net
        $pdf->Cell($w[25], 5.5, '', 1, 1, 'R'); // Account
        
        $pdf->Ln(5);

        // Check if there is enough space for the certification block (~70mm)
        if ($pdf->GetY() + 70 > $pdf->GetPageHeight() - 10) {
            $pdf->renderTableHeader = false;
            $pdf->AddPage();
        }

        // --- Certification Block ---
        // 4 Columns? Or 2 big columns? 
        // Based on the image: A and C are top, B and D are bottom.
        // Each block takes half the width.
        $pdf->SetFont('Arial', '', 8);
        
        $halfW = $totalTableWidth / 2;
        
        // Block A & C Headers
        $pdf->Cell(10, 5, 'A', 1, 0, 'C');
        $pdf->Cell($halfW - 10, 5, 'CERTIFIED: Services duly rendered as stated.', 'TBR', 0, 'L');
        
        $pdf->Cell(10, 5, 'C', 1, 0, 'C');
        $pdf->Cell($halfW - 10, 5, 'Approved for Payment:', 'TBR', 1, 'L');
        
        // Names Space
        // Use SetX to align properly if Cell flow is tricky
        $currY = $pdf->GetY();
        $pdf->SetXY($pdf->GetX(), $currY);
        
        // Left Box Body (A)
        $pdf->Cell($halfW, 18, '', 'LR', 0); 
        // Right Box Body (C)
        $pdf->Cell($halfW, 18, '', 'LR', 1);
        
        // Names
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($halfW, 5, 'DR. VICENTE A. AGDA, JR', 'LR', 0, 'C');
        $pdf->Cell($halfW, 5, 'DR. ANDRES C. PAGATPATAN, JR.', 'LR', 1, 'C');
        
        // Positions
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($halfW, 5, 'Vice President for Academic Affairs', 'LRB', 0, 'C');
        $pdf->Cell($halfW, 5, 'SUC President III', 'LRB', 1, 'C');
        
        // Block B & D Headers
        $pdf->Cell(10, 5, 'B', 1, 0, 'C');
        $pdf->Cell($halfW - 10, 5, 'CERTIFIED: Supporting documents complete...', 'TBR', 0, 'L');
        
        $pdf->Cell(10, 5, 'D', 1, 0, 'C');
        $pdf->Cell($halfW - 10, 5, 'CERTIFIED: Each employee whose name appears...', 'TBR', 1, 'L');
        
        // Signatures Space
        $pdf->Cell($halfW, 18, '', 'LR', 0);
        $pdf->Cell($halfW, 18, '', 'LR', 1);
        
        // Names
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($halfW, 5, 'BRENDA M. DAGANDAN, CPA', 'LR', 0, 'C');
        $pdf->Cell($halfW, 5, 'CHARLES VINCENT D. LIM', 'LR', 1, 'C');
        
        // Positions
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($halfW, 5, 'SAO (Accountant IV)', 'LRB', 0, 'C');
        $pdf->Cell($halfW, 5, 'Administrative Officer V', 'LRB', 1, 'C');
        
        
        $pdf->Output('I', 'GeneralPayroll.pdf');
        exit;
    }
}
