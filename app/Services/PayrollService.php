<?php

namespace App\Services;

use Codedge\Fpdf\Fpdf\Fpdf;

class PayrollPdf extends Fpdf
{
    public $month;
    public $year;
    public $deduction;
    public $colWidths = [];
    public $renderTableHeader = true;

    function Header()
    {
        // Use the widths passed from the service
        $w = $this->colWidths;

        if ($this->deduction == "retiree") {
            $deduct_word = "Retiree Fin\nAsst.";
        } else if ($this->deduction == "death_aid") {
            $deduct_word = "Death\nAid.";
        } else {
            $deduct_word = "Health\nCare.";
        }
        if (empty($w)) {
            // Fallback default widths just in case
            $w = [
                5, // 0: Num
                25, // 1: Name       (Increased for full name)
                25, // 2: Position   (Increased)
                20, // 3: Emp No     (Increased)
                14, // 4: Rate
                12, // 5: PERA
                14, // 6: Earned
                10, // 7: GSIS MPL
                10, // 8: PhilHealth
                10, // 9: Local PAVE
                10, // 10: Life & Ret
                11, // 11: Pagibig Prem
                11, // 12: Pagibig MP3
                11, // 13: Pagibig Cal
                13, // 14: City Sav
                13, // 15: Withholding
                14, // 16: Absence
                11, // 17: IGP Cottage
                10, // 18: ESSU FFA
                13, // 19: Retiree Fin
                10, // 20: ESSU Union
                10, // 21: CFI
                5, // 22: Num
                18, // 23: Total Ded
                18, // 24: Net Amount
                18  // 25: Account No
            ];
        }

        // --- Header Section ---
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(260, 5, 'GENERAL PAYROLL', 0, 0, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(20, 5, "Appendix 33", 0, 1, 'C');

        $this->SetFont('Arial', 'I', 9);
        $m = $this->month ? strtoupper($this->month) : 'JUNE';
        $y = $this->year ?? '2025';
        $this->Cell(260, 5, "For the month of $m, $y", 0, 1, 'C');

        $this->Ln(2);

        $this->SetFont('Arial', 'B', 9);
        // Header Info Row 1
        $this->Cell(270, 5, 'Entity Name: EASTERN SAMAR STATE UNIVERSITY', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Payroll No.: _________________', 0, 1, 'L');

        // Header Info Row 2
        $this->Cell(270, 5, 'Fund Cluster: _________________', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Sheet ' . $this->PageNo() . ' of {nb} Sheets', 0, 1, 'L');

        $this->Ln(-1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 5, 'We acknowledge receipt of cash shown opposite our name as full compensation for services rendered for the period covered.', 0, 1, 'L');


        // --- Table Header ---
        if ($this->renderTableHeader) {
            $this->SetFont('Arial', 'B', 6);
            $h = 14;
            $yPos = $this->GetY();
            $xPos = $this->GetX();

            // Draw Row 1 Boxes
            // Num (0)
            $this->Rect($xPos, $yPos, $w[0], $h);
            $this->SetXY($xPos, $yPos + 4);
            $this->Cell($w[0], 3, 'Num', 0, 0, 'C');
            $this->SetXY($xPos, $yPos + 8);
            $this->Cell($w[0], 3, 'ber', 0, 0, 'C');

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
            $this->MultiCell($w[3], $h / 2, "EMPLOYEE\nNO.", 1, 'C');
            $currentX += $w[3];

            // COMPENSATIONS Group (4,5,6)
            $this->SetXY($currentX, $yPos);
            $compWidth = $w[4] + $w[5] + $w[6];
            $this->Cell($compWidth, 5, 'COMPENSATIONS', 1, 0, 'C');

            // DEDUCTIONS Group (7 to 21)
            $dedWidth = 0;
            for ($i = 7; $i <= 21; $i++) $dedWidth += $w[$i];
            $this->SetXY($currentX + $compWidth, $yPos);
            $this->Cell($dedWidth, 5, 'DEDUCTIONS', 1, 0, 'C');

            // End Group
            $endX = $currentX + $compWidth + $dedWidth;

            // Num (22)
            $this->SetXY($endX, $yPos);
            $this->Rect($endX, $yPos, $w[22], $h);
            $this->SetXY($endX, $yPos + 4);
            $this->Cell($w[22], 3, 'Num', 0, 0, 'C');
            $this->SetXY($endX, $yPos + 8);
            $this->Cell($w[22], 3, 'ber', 0, 0, 'C');

            // Total Ded (23)
            $this->SetXY($endX + $w[22], $yPos);
            $this->Rect($endX + $w[22], $yPos, $w[23], $h);
            $this->SetXY($endX + $w[22], $yPos + 2);
            $this->MultiCell($w[23], 4, "Total\nDeductions", 0, 'C');

            // Net Amount (24)
            $this->Rect($endX + $w[22] + $w[23], $yPos, $w[24], $h);
            $this->SetXY($endX + $w[22] + $w[23], $yPos + 2);
            $this->MultiCell($w[24], 4, "Net\nAmount Due", 0, 'C');

            // Account Number (25)
            $this->Rect($endX + $w[22] + $w[23] + $w[24], $yPos, $w[25], $h);
            $this->SetXY($endX + $w[22] + $w[23] + $w[24], $yPos + 5);
            $this->Cell($w[25], 4, "Account Number", 0, 1, 'C');


            // --- Sub-Headers (Row 2 Start) ---
            $subHeaderY = $yPos + 5;

            // Rate (4)
            $this->SetXY($xPos + $w[0] + $w[1] + $w[2] + $w[3], $subHeaderY);
            $this->Rect($this->GetX(), $subHeaderY, $w[4], 9);
            $this->MultiCell($w[4], 3, "Rate per\nmonth", 0, 'C');

            // PERA (5)
            $this->SetXY($xPos + $w[0] + $w[1] + $w[2] + $w[3] + $w[4], $subHeaderY);
            $this->Rect($this->GetX(), $subHeaderY, $w[5], 9);
            $this->MultiCell($w[5], 3, "\nPERA", 0, 'C');

            // Earned (6)
            $this->SetXY($xPos + $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5], $subHeaderY);
            $this->Rect($this->GetX(), $subHeaderY, $w[6], 9);
            $this->MultiCell($w[6], 3, "Earned for\nthe period", 0, 'C');

            // Deductions Sub-Columns
            $dedStartX = $xPos + $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5] + $w[6];
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
            $this->MultiCell($w[10], 3, "Life &\nRetirement", 0, 'C');
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
            $this->MultiCell($w[19], 3, $deduct_word, 0, 'C');
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

    public function generatePayrollPdf($data)
    {
        // Use custom PayrollPdf class instead of injected Fpdf
        $pdf = new PayrollPdf('L', 'mm', 'Legal');

        $pdf->month = $data->month;
        $pdf->year = $data->year;
        $pdf->deduction = $data->deduction;

        $sortedPayrollData = $data->payrollData()
            ->join('employees', 'payroll_data.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('payroll_data.*') // Ensure we only get payroll_data fields back
            ->get();

        // Columns configuration
        $w = [
                5, // 0: Num
                25, // 1: Name       (Increased for full name)
                25, // 2: Position   (Increased)
                20, // 3: Emp No     (Increased)
                14, // 4: Rate
                12, // 5: PERA
                14, // 6: Earned
                10, // 7: GSIS MPL
                10, // 8: PhilHealth
                10, // 9: Local PAVE
                10, // 10: Life & Ret
                11, // 11: Pagibig Prem
                11, // 12: Pagibig MP3
                11, // 13: Pagibig Cal
                13, // 14: City Sav
                13, // 15: Withholding
                14, // 16: Absence
                11, // 17: IGP Cottage
                10, // 18: ESSU FFA
                13, // 19: Retiree Fin
                10, // 20: ESSU Union
                10, // 21: CFI
                5, // 22: Num
                18, // 23: Total Ded
                18, // 24: Net Amount
                18  // 25: Account No
            ];

        $pdf->colWidths = $w;
        $totalTableWidth = array_sum($w);

        $pdf->AliasNbPages();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage('L', 'Legal');

        // --- Table Body ---
        $pdf->SetFont('Arial', '', 7);
        $totals = [
            "rate" => 0,
            "pera" => 0,
            "earned" => 0,
            "gsis_mpl" => 0,
            "philhealth" => 0,
            "local_pave" => 0,
            "life_retirement" => 0,
            "pagibig_premium" => 0,
            "pagibig_mp3" => 0,
            "pagibig_calamity" => 0,
            "city_savings" => 0,
            "withholding" => 0,
            "ab_wo_pay" => 0,
            "cottage" => 0,
            "essu_ffa" => 0,
            "custom_deduction" => 0,
            "essu_union" => 0,
            "cfi" => 0,
            "total_deduction" => 0,
            "total_net" => 0,

        ];

        foreach ($sortedPayrollData as $index => $emp) {
            $rowH = 5.5; // Row height

            $total_deduction_emp = 0;

            // 0: Num
            $pdf->Cell($w[0], $rowH, $index + 1, 1, 0, 'C');
            // 1: Name
            $pdf->Cell($w[1], $rowH, $emp->employee->user->name, 1, 0, 'L');
            // 2: Position
            $pdf->Cell($w[2], $rowH, $emp->employee->position->name, 1, 0, 'C');
            // 3: Emp No
            $pdf->Cell($w[3], $rowH, $emp->employee->employee_number, 1, 0, 'C');
            $pdf->SetFont('Arial', '', 6);
            // 4: Rate
            $pdf->Cell($w[4], $rowH, number_format($emp->rate, 2), 1, 0, 'R');
            $totals["rate"] += $emp->rate;
            // 5: PERA
            $pdf->Cell($w[5], $rowH, number_format($emp->pera, 2), 1, 0, 'R');
            $totals["pera"] += $emp->pera;
            // 6: Earned
            $pdf->Cell($w[6], $rowH, number_format($emp->period_earned, 2), 1, 0, 'R');
            $totals["earned"] += $emp->period_earned;

            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell($w[7], $rowH, number_format($emp->gsis_mpl, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->gsis_mpl;
            $totals["gsis_mpl"] += $emp->gsis_mpl;

            $pdf->Cell($w[8], $rowH, number_format($emp->philhealth, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->philhealth;
            $totals["philhealth"] += $emp->philhealth;

            $pdf->Cell($w[9], $rowH, number_format($emp->local_pave, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->local_pave;
            $totals["local_pave"] += $emp->local_pave;

            $pdf->Cell($w[10], $rowH, number_format($emp->life_retirement, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->life_retirement;
            $totals["life_retirement"] += $emp->life_retirement;

            $pdf->Cell($w[11], $rowH, number_format($emp->pagibig_premium, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->pagibig_premium;
            $totals["pagibig_premium"] += $emp->pagibig_premium;

            $pdf->Cell($w[12], $rowH, number_format($emp->pagibig_mp3, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->pagibig_mp3;
            $totals["pagibig_mp3"] += $emp->pagibig_mp3;

            $pdf->Cell($w[13], $rowH, number_format($emp->pagibig_calamity, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->pagibig_calamity;
            $totals["pagibig_calamity"] += $emp->pagibig_calamity;

            $pdf->Cell($w[14], $rowH, number_format($emp->city_savings, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->city_savings;
            $totals["city_savings"] += $emp->city_savings;

            $pdf->Cell($w[15], $rowH, number_format($emp->withholding_tax, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->withholding_tax;
            $totals["withholding"] += $emp->withholding_tax;

            $pdf->Cell($w[16], $rowH, number_format($emp->absence_wo_pay, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->absence_wo_pay;
            $totals["ab_wo_pay"] += $emp->absence_wo_pay;

            $pdf->Cell($w[17], $rowH, number_format($emp->cottage_rental, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->cottage_rental;
            $totals["cottage"] += $emp->cottage_rental;

            $pdf->Cell($w[18], $rowH, number_format($emp->essu_ffa, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->essu_ffa;
            $totals["essu_ffa"] += $emp->essu_ffa;

            $pdf->Cell($w[19], $rowH, number_format($emp->custom_deduction, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->custom_deduction;
            $totals["custom_deduction"] += $emp->custom_deduction;

            $pdf->Cell($w[20], $rowH, number_format($emp->essu_union, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->essu_union;
            $totals["essu_union"] += $emp->essu_union;

            $pdf->Cell($w[21], $rowH, number_format($emp->cfi, 2), 1, 0, 'R');
            $total_deduction_emp += $emp->cfi;
            $totals["cfi"] += $emp->cfi;

            $pdf->SetFont('Arial', '', 6);
            // 22: Num
            $pdf->Cell($w[22], $rowH, $index + 1, 1, 0, 'C');

            // 23: Total Ded
            $pdf->Cell($w[23], $rowH, number_format($total_deduction_emp, 2), 1, 0, 'R');

            $net = $emp->period_earned - $total_deduction_emp;

            // 24: Net
            $pdf->Cell($w[24], $rowH, number_format($net, 2), 1, 0, 'R');

            // 25: Account No
            $pdf->Cell($w[25], $rowH, $emp['account_number'] ?? '', 1, 1, 'R');

            $totals['total_deduction'] += $total_deduction_emp;
            $totals['total_net'] += $net;
        }

        // --- TOTALS ROW ---
        $pdf->SetFont('Arial', 'B', 7);
        $totalLeftW = $w[0] + $w[1] + $w[2] + $w[3];
        $pdf->Cell($totalLeftW, 5.5, 'TOTALS', 1, 0, 'C');

        // Compensations Totals
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell($w[4], 5.5, number_format($totals["rate"], 2), 1, 0, 'R');
        $pdf->Cell($w[5], 5.5, number_format($totals["pera"], 2), 1, 0, 'R');
        $pdf->Cell($w[6], 5.5, number_format($totals["earned"], 2), 1, 0, 'R');

        // Deductions Totals
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell($w[7], 5.5, number_format($totals["gsis_mpl"], 2), 1, 0, 'R');
        $pdf->Cell($w[8], 5.5, number_format($totals["philhealth"], 2), 1, 0, 'R');
        $pdf->Cell($w[9], 5.5, number_format($totals["local_pave"], 2), 1, 0, 'R');
        $pdf->Cell($w[10], 5.5, number_format($totals["life_retirement"], 2), 1, 0, 'R');
        $pdf->Cell($w[11], 5.5, number_format($totals["pagibig_premium"], 2), 1, 0, 'R');
        $pdf->Cell($w[12], 5.5, number_format($totals["pagibig_mp3"], 2), 1, 0, 'R');
        $pdf->Cell($w[13], 5.5, number_format($totals["pagibig_calamity"], 2), 1, 0, 'R');
        $pdf->Cell($w[14], 5.5, number_format($totals["city_savings"], 2), 1, 0, 'R');
        $pdf->Cell($w[15], 5.5, number_format($totals["withholding"], 2), 1, 0, 'R');
        $pdf->Cell($w[16], 5.5, number_format($totals["ab_wo_pay"], 2), 1, 0, 'R');
        $pdf->Cell($w[17], 5.5, number_format($totals["cottage"], 2), 1, 0, 'R');
        $pdf->Cell($w[18], 5.5, number_format($totals["essu_ffa"], 2), 1, 0, 'R');
        $pdf->Cell($w[19], 5.5, number_format($totals["custom_deduction"], 2), 1, 0, 'R');
        $pdf->Cell($w[20], 5.5, number_format($totals["essu_union"], 2), 1, 0, 'R');
        $pdf->Cell($w[21], 5.5, number_format($totals["cfi"], 2), 1, 0, 'R');

        // End Totals
        $pdf->Cell($w[22], 5.5, '', 1, 0, 'C'); // Num
        $pdf->Cell($w[23], 5.5, '0.00', 1, 0, 'R'); // Total Ded
        $pdf->Cell($w[24], 5.5, '0.00', 1, 0, 'R'); // Net
        $pdf->Cell($w[25], 5.5, '', 1, 1, 'R'); // Account

        $pdf->Ln(5);

        // Check if there is enough space for the certification block (~55mm)
        if ($pdf->GetY() + 55 > $pdf->GetPageHeight() - 10) {
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
        $pdf->Cell($halfW - 10, 5, 'CERTIFIED: Services duly rendered as stated:', "T", 0, 'L');

        $pdf->Cell(10, 5, 'C', 1, 0, 'C');
        $pdf->Cell($halfW - 10, 5, 'Approved for Payment:________', 'TR', 1, 'L');

        // Names Space
        // Use SetX to align properly if Cell flow is tricky
        $currY = $pdf->GetY();
        $pdf->SetXY($pdf->GetX(), $currY);

        // Left Box Body (A)
        $pdf->Cell($halfW, 5, '', 'LR', 0);
        // Right Box Body (C)
        $pdf->Cell($halfW, 5, '', 'LR', 1);

        // Names
        $pdf->SetFont('', 'BU');
        $pdf->Cell(20, 5, '', 'L', 0, 'L');
        $pdf->Cell(30, 5, 'DR. VICENTE A. AGDA, JR', 0, 0, 'L');
        $pdf->Cell(20, 5, '', '', 0, 'L');
        $pdf->Cell(10, 5, '_______', '', 0, 'L');
        $pdf->Cell($halfW - 80, 5, '', 0, 0, 'L');
        $pdf->Cell(20, 5, '', "L", 0, 'L');
        $pdf->Cell(30, 5, 'DR. ANDRES C. PAGATPATAN, JR.', 0, 0, 'L');
        $pdf->Cell(20, 5, '', '', 0, 'L');
        $pdf->Cell(10, 5, '_______', '', 0, 'L');
        $pdf->Cell($halfW - 80, 5, '', "R", 1, 'L');

        // Positions
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 5, '', 'L', 0, 'L');
        $pdf->Cell(50, 5, 'Vice President for Academic Affairs', 'B', 0, 'C');
        $pdf->Cell(25, 5, 'Date', 'B', 0, 'C');
        $pdf->Cell($halfW - 90, 5, '', 0, 0, 'L');
        $pdf->Cell(15, 5, '', 'L', 0, 'L');
        $pdf->Cell(50, 5, 'SUC President III', 'B', 0, 'C');
        $pdf->Cell(25, 5, 'Date', 'B', 0, 'C');
        $pdf->Cell($halfW - 90, 5, '', "R", 1, 'R');

        // Block B & D Headers
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(10, 5, 'B', 1, 0, 'C');
        $pdf->Cell($halfW - 10, 5, 'CERTIFIED: Supporting documents complete and proper, and cash available in the amount of P____________', 'TR', 0, 'L');

        $pdf->Cell(10, 5, 'D', 1, 0, 'C');
        $pdf->Cell($halfW - 45, 5, "CERTIFIED: Each employee whose name appears on the payroll has been paid the amount as indicated opposite his/her name", 'TR', 0, 'L');
        $pdf->Cell(5, 5, 'E', 1, 0, 'L');
        $pdf->Cell(30, 5, '', "TR", 1, 'L');

        // Signatures Space
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($halfW, 5, '', 'LR', 0);
        $pdf->Cell($halfW - 35, 5, '', 'LR', 0);
        $pdf->Cell(15, 5, 'ORS/BURS', 0, 0, 'L');
        $pdf->Cell(20, 5, '____________', 'R', 1, 'R');

        $pdf->Cell($halfW, 5, '', 'LR', 0);
        $pdf->Cell($halfW - 35, 5, '', 'LR', 0);
        $pdf->Cell(15, 5, 'Date:', 0, 0, 'L');
        $pdf->Cell(20, 5, '___________', 'R', 1, 'L');



        // Names
        $pdf->SetFont('Arial', 'UB', 8);
        $pdf->Cell(15, 5, '', 'L', 0, 'L');
        $pdf->Cell(50, 5, 'BRENDA M. DAGANDAN, CPA', '', 0, 'L');
        $pdf->Cell($halfW - 65, 5, '_______', 'R', 0, 'L');
        $pdf->Cell(15, 5, '', 'L', 0, 'L');
        $pdf->Cell(50, 5, 'CHARLES VINCENT D. LIM', '', 0, 'L');
        $pdf->Cell($halfW - 100, 5, '_______', 'R', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 5, 'JEV No.:', '', 0, 'L');
        $pdf->Cell(20, 5, '___________', 'R', 1, 'L');


        // Positions
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 5, '', 'LB', 0, 'L');
        $pdf->Cell(50, 5, 'SAO (Accountant IV)', 'B', 0, 'L');
        $pdf->Cell($halfW - 65, 5, 'Date', 'RB', 0, 'L');
        $pdf->Cell(15, 5, '', 'LB', 0, 'L');
        $pdf->Cell(50, 5, 'Administrative Officer V', 'B', 0, 'L');
        $pdf->Cell($halfW - 100, 5, 'Date', 'RB', 0, 'L');
        $pdf->Cell(15, 5, 'Date:', 'B', 0, 'L');
        $pdf->Cell(20, 5, '___________', 'RB', 1, 'L');


        $pdf->Output('I', 'GeneralPayroll.pdf');
        exit;
    }
}
