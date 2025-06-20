<?php
include 'db.php';

// 1. Insert into diary_entries
$stmt = $pdo->prepare("
  INSERT INTO diary_entries (
    web_entry_no, year, entry_date, name, tehsil, amount, nec, affidavit,
    deed, b_c, stamp, expenses, advance, remaining, came_thru, request, village
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
  $_POST['web_entry_no'], $_POST['year'], $_POST['entry_date'], $_POST['name'], $_POST['tehsil'],
  $_POST['amount'], $_POST['nec'], $_POST['affidavit'], $_POST['deed'], $_POST['b_c'],
  $_POST['stamp'], $_POST['expenses'], $_POST['advance'], $_POST['remaining'],
  $_POST['came_thru'], $_POST['request'], $_POST['village']
]);

// 2. Prepare land_details insert
$landStmt = $pdo->prepare("
  INSERT INTO land_details (
    web_entry_no, year, village, pargana_tehsil_district, khata_no, khasra_no, area_in_hectare, share
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

// Extract the grouping
$village_names = $_POST['village_name'];
$parganas = $_POST['pargana_tehsil_district'];
$khata_nos = $_POST['khata_no'];
$khasra_nos = $_POST['khasra_no'];
$areas = $_POST['area_in_hectare'];
$shares = $_POST['share'];
$village_groups = $_POST['village_group'];  // same length as khata_no[]

$group_map = [];
foreach ($village_groups as $i => $group_id) {
  if (!isset($group_map[$group_id])) {
    $group_map[$group_id] = [
      'village' => $village_names[count($group_map)],
      'pargana' => $parganas[count($group_map)]
    ];
  }

  $group = $group_map[$group_id];

  $landStmt->execute([
    $_POST['web_entry_no'], $_POST['year'],
    $group['village'],
    $group['pargana'],
    $khata_nos[$i],
    $khasra_nos[$i],
    $areas[$i],
    $shares[$i]
  ]);
}

// 3. Redirect back to dashboard or view
header("Location: view_entry.php?web_entry_no=" . $_POST['web_entry_no'] . "&year=" . $_POST['year']);
exit;
?>
