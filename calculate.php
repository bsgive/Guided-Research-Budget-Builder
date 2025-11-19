<?php
ob_clean();
ob_start();
require 'vendor/autoload.php';
require 'database.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$budgetId = $_GET['budget_id'] ?? null;
if (!$budgetId) die("Missing budget_id");



//get values for calcs
function fetchAll($conn, $sql, $params = [])
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    if (!empty($params)) {
        $types = str_repeat("i", count($params)); 
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $rows;
}

//get budget
$budget = fetchAll($conn, "SELECT * FROM budgets WHERE id = ?", [$budgetId])[0];

$startYear = $budget['start_year'];
$duration  = $budget['duration_years'];

// salery info
$personnel = fetchAll(
    $conn,
    "SELECT bp.*, fs.name, fs.base_salary
     FROM budget_personnel bp
     JOIN faculty_staff fs ON fs.id = bp.pi_id
     WHERE bp.budget_id = ?",
    [$budgetId]
);

// fringe rates
$fringeRows = fetchAll($conn, "SELECT year, rate_percent FROM fringe_rates");
$fringeRates = [];
foreach ($fringeRows as $r) {
    $fringeRates[$r['year']] = $r['rate_percent'] / 100;
}

$personnelCalc = [];

foreach ($personnel as $p) {

    for ($y = 1; $y <= $duration; $y++) {
        $year = $startYear + $y - 1;

        $effortField = "effort_y$y";
        $effort = ($p[$effortField] ?? 0) / 100;

        $salary = $p['base_salary'] * $effort;
        $fringeRate = $fringeRates[$year] ?? 0;
        $fringe = $salary * $fringeRate;

        $personnelCalc[] = [
            'name'   => $p['name'],
            'year'   => $year,
            'salary' => $salary,
            'fringe' => $fringe
        ];
    }
}


// student info
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

foreach ($students as $s) {
    foreach ($tuitionRows as $t) {
        if ($s['residency_status'] == $t['residency_status']) {

            $totalTuition = ($t['tuition_amount'] + $t['fees']) * $s['fte'];

            $tuitionCalc[] = [
                'name' => $s['name'],
                'year' => $t['year'],
                'tuition' => $totalTuition
            ];
        }
    }
}


// travel info
$travel = fetchAll(
    $conn,
    "SELECT bt.*, tp.type, tp.per_diem, tp.airfare_estimate, tp.lodging_cap
     FROM budget_travel bt
     JOIN travel_profiles tp ON tp.id = bt.travel_type_id
     WHERE bt.budget_id = ?",
    [$budgetId]
);

$travelCalc = [];

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
}


// F&A rates
$faRows = fetchAll($conn, "SELECT * FROM f_and_a_rates");

$faRates = [];
foreach ($faRows as $row) {
    $faRates[$row['year']] = $row['rate_percent'] / 100;
}

$faCalc = [];

foreach ($personnelCalc as $pc) {
    $year = $pc['year'];
    $subtotal = $pc['salary'] + $pc['fringe'];

    $faRate = $faRates[$year] ?? 0;

    $faCalc[] = [
        'year' => $year,
        'fa'   => $subtotal * $faRate
    ];
}


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Budget Preview");

$sheet->fromArray(['Category', 'Name/Type', 'Year', 'Amount'], null, 'A1');

$row = 2;

// Personnel rows
foreach ($personnelCalc as $p) {
    $sheet->fromArray(['Personnel Salary', $p['name'], $p['year'], $p['salary']], null, "A$row");
    $row++;
    $sheet->fromArray(['Personnel Fringe', $p['name'], $p['year'], $p['fringe']], null, "A$row");
    $row++;
}

// Student tuition
foreach ($tuitionCalc as $t) {
    $sheet->fromArray(['Student Tuition', $t['name'], $t['year'], $t['tuition']], null, "A$row");
    $row++;
}

// Travel
foreach ($travelCalc as $t) {
    $sheet->fromArray(['Travel', $t['type'], '-', $t['cost']], null, "A$row");
    $row++;
}

// F&A
foreach ($faCalc as $f) {
    $sheet->fromArray(['F&A', 'Indirect Costs', $f['year'], $f['fa']], null, "A$row");
    $row++;
}

$filename = "budget_export_{$budgetId}.xlsx";
ob_end_clean();

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;

?>

