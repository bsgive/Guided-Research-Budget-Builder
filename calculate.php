<?php
ob_clean();
ob_start();

require 'vendor/autoload.php';
require 'vendor/autoload.php';
require 'database.php';
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$budgetId = $_GET['budget_id'] ?? null;
if (!$budgetId) die("Missing budget_id");

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
foreach ($fringeRows as $r) $fringeRates[$r['year']] = $r['rate_percent'] / 100;

$personnelCalc = [];
$personnelTotals = [];

foreach ($personnel as $p) {
    for ($y = 1; $y <= $duration; $y++) {
        $year = $startYear + $y - 1;
        $effort = ($p["effort_y$y"] ?? 0) / 100;

        $salary = $p['base_salary'] * $effort;
        $fringe = $salary * ($fringeRates[$year] ?? 0);

        $personnelCalc[] = [
            'name'   => $p['name'],
            'year'   => $year,
            'salary' => $salary,
            'fringe' => $fringe
        ];

        $personnelTotals[$year] = ($personnelTotals[$year] ?? 0) + $salary + $fringe;
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
            $totalTuition = ($t['tuition_amount'] + $t['fees']) * ($s['fte'] / 100);

            $tuitionCalc[] = [
                'name' => $s['name'],
                'year' => $t['year'],
                'tuition' => $totalTuition
            ];
            $tuitionTotals[$t['year']] = ($tuitionTotals[$t['year']] ?? 0) + $totalTuition;
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
    $fa = $subtotal * ($faRates[$year] ?? 0);

    $faCalc[] = [
        'year' => $year,
        'fa'   => $fa
    ];

    $faTotals[$year] = ($faTotals[$year] ?? 0) + $fa;
}

$years = [];

for ($i = 0; $i < $duration; $i++) {
    $years[] = $startYear + $i;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Budget Export');

$header = array_merge(['Category', 'Name/Type'], $years);
$sheet->fromArray($header, null, 'A1');

$row = 2;

$yearCols = [];
$colIndex = 3; 

foreach ($years as $yr) {
    $yearCols[$yr] = Coordinate::stringFromColumnIndex($colIndex++);
}

$personnelRows = [];

foreach ($personnelCalc as $p) {

    if (!isset($yearCols[$p['year']])) {
        continue;
    }

    $personnelRows["Personnel Salary|{$p['name']}"][$p['year']] = $p['salary'];
    $personnelRows["Personnel Fringe|{$p['name']}"][$p['year']] = $p['fringe'];
}

foreach ($personnelRows as $key => $values) {
    [$category, $name] = explode('|', $key);

    $sheet->setCellValue("A$row", $category);
    $sheet->setCellValue("B$row", $name);

    foreach ($values as $yr => $amt) {
        $sheet->setCellValue($yearCols[$yr] . $row, $amt);
    }
    $row++;
}

//personnel totals
$sheet->setCellValue("A$row", "TOTAL Personnel");
foreach ($years as $yr) {
    $sheet->setCellValue(
        $yearCols[$yr] . $row,
        $personnelTotals[$yr] ?? 0
    );
}

$sheet->getStyle("A$row:" . end($yearCols) . $row)->getFont()->setBold(true);
$row++;

$tuitionRows = [];

foreach ($tuitionCalc as $t) {

    if (!isset($yearCols[$t['year']])) {
        continue;
    }

    $tuitionRows[$t['name']][$t['year']] = $t['tuition'];
}

foreach ($tuitionRows as $name => $values) {
    $sheet->setCellValue("A$row", "Student Tuition");
    $sheet->setCellValue("B$row", $name);

    foreach ($values as $yr => $amt) {
        $sheet->setCellValue($yearCols[$yr] . $row, $amt);
    }
    $row++;
}

//tuition totals
$sheet->setCellValue("A$row", "TOTAL Tuition");
foreach ($years as $yr) {
    $sheet->setCellValue(
        $yearCols[$yr] . $row,
        $tuitionTotals[$yr] ?? 0
    );
}

$sheet->getStyle("A$row:" . end($yearCols) . $row)->getFont()->setBold(true);
$row++;

//travel
$firstYearCol = reset($yearCols);

foreach ($travelCalc as $t) {
    $sheet->setCellValue("A$row", "Travel");
    $sheet->setCellValue("B$row", $t['type']);
    $sheet->setCellValue($firstYearCol . $row, $t['cost']);
    $row++;
}

$sheet->setCellValue("A$row", "TOTAL Travel");
$sheet->setCellValue($firstYearCol . $row, $travelTotal);
$sheet->getStyle("A$row:" . end($yearCols) . $row)->getFont()->setBold(true);
$row++;

foreach ($faCalc as $f) {

    if (!isset($yearCols[$f['year']])) {
        continue;
    }

    $sheet->setCellValue("A$row", "F&A");
    $sheet->setCellValue("B$row", "Indirect Costs");
    $sheet->setCellValue($yearCols[$f['year']] . $row, $f['fa']);
    $row++;
}

//F and A Totals
$sheet->setCellValue("A$row", "TOTAL F&A");
foreach ($years as $yr) {
    $sheet->setCellValue(
        $yearCols[$yr] . $row,
        $faTotals[$yr] ?? 0
    );
}

$sheet->getStyle("A$row:" . end($yearCols) . $row)->getFont()->setBold(true);
$row++;

//Grand Total
$sheet->setCellValue("A$row", "GRAND TOTAL");

foreach ($years as $yr) {
    $sum =
        ($personnelTotals[$yr] ?? 0) +
        ($tuitionTotals[$yr] ?? 0) +
        ($faTotals[$yr] ?? 0);
    if ($yr === $years[0]) {
        $sum += $travelTotal;
    }

    $sheet->setCellValue($yearCols[$yr] . $row, $sum);
}

$sheet->getStyle("A$row:" . end($yearCols) . $row)->getFont()->setBold(true);
$sheet->getStyle($yearCols[$years[0]] . $row)->getFont()->setSize(14);

foreach (range('A', end($yearCols)) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$sheet->getStyle(
    "C2:" . end($yearCols) . $row
)->getNumberFormat()->setFormatCode('#,##0.00');

$filename = "budget_export_{$budgetId}.xlsx";

ob_end_clean();
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

?>
