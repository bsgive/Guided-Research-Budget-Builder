<?php
ob_clean();
ob_start();
require 'vendor/autoload.php';
require 'database.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$budgetId = isset($_GET['budget_id']) ? $_GET['budget_id'] : null;
if (!$budgetId) {
    die("Missing budget_id");
}

function fetchAll($conn, $sql, $params = [])
{
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat("i", count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

//budget
$budget = fetchAll($conn, "SELECT * FROM budgets WHERE id = ?", [$budgetId])[0];
$startYear = $budget['start_year'];
$duration  = $budget['duration_years'];

//personnel
$personnel = fetchAll(
    $conn,
    "SELECT bp.*, fs.name, fs.base_salary
     FROM budget_personnel bp
     JOIN faculty_staff fs ON fs.id = bp.pi_id
     WHERE bp.budget_id = ?",
    [$budgetId]
);

//fringe rates
$fringeRows = fetchAll($conn, "SELECT year, rate_percent FROM fringe_rates");
$fringeRates = [];
foreach ($fringeRows as $r) {
    $fringeRates[$r['year']] = $r['rate_percent'] / 100;
}

$personnelCalc = [];
$personnelTotals = [];

foreach ($personnel as $p) {
    for ($y = 1; $y <= $duration; $y++) {
        $year = $startYear + $y - 1;
        $effort = (isset($p["effort_y$y"]) ? $p["effort_y$y"] : 0) / 100;

        $salary = $p['base_salary'] * $effort;
        $fringe = $salary * (isset($fringeRates[$year]) ? $fringeRates[$year] : 0);

        $personnelCalc[] = [
            'name'   => $p['name'],
            'year'   => $year,
            'salary' => $salary,
            'fringe' => $fringe
        ];

        $personnelTotals[$year] = (isset($personnelTotals[$year]) ? $personnelTotals[$year] : 0) + $salary + $fringe;
    }
}

//students
$students = fetchAll(
    $conn,
    "SELECT bs.*, s.name, s.residency_status, bs.fte
     FROM budget_students bs
     JOIN students s ON s.sid = bs.student_id
     WHERE bs.budget_id = ?",
    [$budgetId]
);

$tuitionRows = fetchAll($conn, "SELECT * FROM tuition_fees");

$tuitionCalc = [];
$tuitionTotals = [];

foreach ($students as $s) {
    foreach ($tuitionRows as $t) {
        if ($s['residency_status'] == $t['residency_status']) {
            $totalTuition = (($t['tuition_amount'] + $t['fees']) * $s['fte']) / 100;

            $tuitionCalc[] = [
                'name' => $s['name'],
                'year' => $t['year'],
                'tuition' => $totalTuition
            ];
            $tuitionTotals[$t['year']] = (isset($tuitionTotals[$t['year']]) ? $tuitionTotals[$t['year']] : 0) + $totalTuition;
        }
    }
}

//travel
$travel = fetchAll(
    $conn,
    "SELECT bt.*, tp.type, tp.per_diem, tp.airfare_estimate, tp.lodging_cap
     FROM budget_travel bt
     JOIN travel_profiles tp ON tp.id = bt.travel_type_id
     WHERE bt.budget_id = ?",
    [$budgetId]
);

$travelCalc = [];
$travelTotal = 0;

foreach ($travel as $t) {
    $singleTripCost =
        ($t['per_diem'] + $t['lodging_cap']) * $t['days'] +
        $t['airfare_estimate'];

    $total = $singleTripCost * $t['trips'];

    $travelCalc[] = [
        'type'  => $t['type'],
        'trips' => $t['trips'],
        'cost'  => $total
    ];

    $travelTotal += $total;
}

//F and A
$faRows = fetchAll($conn, "SELECT * FROM f_and_a_rates");
$faRates = [];
foreach ($faRows as $row) $faRates[$row['year']] = $row['rate_percent'] / 100;

$faCalc = [];
$faTotals = [];

foreach ($personnelCalc as $pc) {
    $year = $pc['year'];
    $subtotal = $pc['salary'] + $pc['fringe'];
    $fa = $subtotal * (isset($faRates[$year]) ? $faRates[$year] : 0);

    $faCalc[] = [
        'year' => $year,
        'fa'   => $fa
    ];

    $faTotals[$year] = (isset($faTotals[$year]) ? $faTotals[$year] : 0) + $fa;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Budget Export");

$sheet->fromArray(['Category', 'Name/Type', 'Year', 'Amount'], null, 'A1');
$row = 2;

//PERSONNEL
foreach ($personnelCalc as $p) {
    $sheet->fromArray(['Personnel Salary', $p['name'], $p['year'], $p['salary']], null, "A$row"); $row++;
    $sheet->fromArray(['Personnel Fringe', $p['name'], $p['year'], $p['fringe']], null, "A$row"); $row++;
}

//PERSONNEL TOTALS
foreach ($personnelTotals as $yr => $total) {
    $sheet->fromArray(['TOTAL Personnel', '', $yr, $total], null, "A$row");
    $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
    $row++;
}

//STUDENT TUITION
foreach ($tuitionCalc as $t) {
    $sheet->fromArray(['Student Tuition', $t['name'], $t['year'], $t['tuition']], null, "A$row");
    $row++;
}

//TUITION TOTALS
foreach ($tuitionTotals as $yr => $total) {
    $sheet->fromArray(['TOTAL Tuition', '', $yr, $total], null, "A$row");
    $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
    $row++;
}

//TRAVEL
foreach ($travelCalc as $t) {
    $sheet->fromArray(['Travel', $t['type'], '-', $t['cost']], null, "A$row");
    $row++;
}
$sheet->fromArray(['TOTAL Travel', '', '-', $travelTotal], null, "A$row");
$sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
$row++;

//F and A
foreach ($faCalc as $f) {
    $sheet->fromArray(['F&A', 'Indirect Costs', $f['year'], $f['fa']], null, "A$row");
    $row++;
}

//F and A TOTALS
foreach ($faTotals as $yr => $total) {
    $sheet->fromArray(['TOTAL F&A', '', $yr, $total], null, "A$row");
    $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
    $row++;
}

//GRAND TOTAL
$totalSum =
    array_sum($personnelTotals) +
    array_sum($tuitionTotals) +
    $travelTotal +
    array_sum($faTotals);

$row++;
$sheet->fromArray(['GRAND TOTAL', '', '', $totalSum], null, "A$row");
$sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
$sheet->getStyle("D$row")->getFont()->setSize(14);

foreach (['A','B','C','D'] as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$sheet->getStyle("D2:D$row")->getNumberFormat()->setFormatCode('#,##0.00');

$filename = "budget_export_{$budgetId}.xlsx";
ob_end_clean();
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
