<?php
require_once("db.php");

$web_entry_no = $_POST['web_entry_no'];
$year = $_POST['year'];
$entry_date = $_POST['entry_date'];
$name = $_POST['name'];
$tehsil = $_POST['tehsil'];
$amount = $_POST['amount'];
$nec = $_POST['nec'];
$affidavit = $_POST['affidavit'];
$deed = $_POST['deed'];
$bc = $_POST['b_c'];
$stamp = $_POST['stamp'];
$came_thru = $_POST['came_thru'];
$expenses = $_POST['expenses'];
$advance = $_POST['advance'];
$remaining = $_POST['remaining'];
$village = $_POST['village'] ;
$request = $_POST['request'] ;

try {
    $pdo->beginTransaction();

    // 1. Update diary entry
    $stmt = $pdo->prepare("UPDATE diary_entries SET 
        entry_date = ?, name = ?, tehsil = ?, amount = ?, nec = ?, affidavit = ?, deed = ?, b_c = ?, stamp = ?, 
        came_thru = ?, expenses = ?, advance = ?, remaining = ?, village = ?, request = ?
        WHERE web_entry_no = ? AND year = ?");
    $stmt->execute([
        $entry_date, $name, $tehsil, $amount, $nec, $affidavit, $deed, $bc, $stamp,
        $came_thru, $expenses, $advance, $remaining, $village, $request,
        $web_entry_no, $year
    ]);

    // 2. Delete existing land details
    $stmt = $pdo->prepare("DELETE FROM land_details WHERE web_entry_no = ? AND year = ?");
    $stmt->execute([$web_entry_no, $year]);

    // 3. Re-insert land details
    $village_names = $_POST['village_name'] ?? [];
    $pargana_names = $_POST['pargana_tehsil_district'] ?? [];
    $khata_nos = $_POST['khata_no'] ?? [];
    $khasra_nos = $_POST['khasra_no'] ?? [];
    $areas = $_POST['area_in_hectare'] ?? [];
    $shares = $_POST['share'] ?? [];
    $village_groups = $_POST['village_group'] ?? [];

    $village_map = [];
    foreach ($village_groups as $index => $group_id) {
        $village_map[$group_id][] = $index;
    }

    foreach ($village_names as $i => $village_name) {
        $group_id = "group_$i";
        $pargana = $pargana_names[$i];

        foreach ($village_map[$group_id] ?? [] as $j) {
            $stmt = $pdo->prepare("INSERT INTO land_details 
                (web_entry_no, year, village_name, pargana_tehsil_district, khata_no, khasra_no, area_in_hectare, share) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $web_entry_no, $year,
                $village_name,
                $pargana,
                $khata_nos[$j],
                $khasra_nos[$j],
                $areas[$j],
                $shares[$j]
            ]);
        }
    }

    $pdo->commit();
    header("Location: view_entry.php?web_entry_no=$web_entry_no&year=$year");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
