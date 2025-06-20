<?php
include 'db.php';

// Update diary entry
$stmt = $pdo->prepare("
  UPDATE diary_entries SET
    name = ?, tehsil = ?, amount = ?, nec = ?, affidavit = ?, deed = ?, 
    b_c = ?, stamp = ?, expenses = ?, advance = ?, remaining = ?, 
    came_thru = ?, request = ?, village = ?
  WHERE web_entry_no = ? AND year = ?
");

$stmt->execute([
  $_POST['name'], $_POST['tehsil'], $_POST['amount'], $_POST['nec'], $_POST['affidavit'],
  $_POST['deed'], $_POST['b_c'], $_POST['stamp'], $_POST['expenses'], $_POST['advance'],
  $_POST['remaining'], $_POST['came_thru'], $_POST['request'], $_POST['village'],
  $_POST['web_entry_no'], $_POST['year']
]);

// Delete old land details
$pdo->prepare("DELETE FROM land_details WHERE web_entry_no = ? AND year = ?")
    ->execute([$_POST['web_entry_no'], $_POST['year']]);

// Re-insert updated land details
$insertLand = $pdo->prepare("
  INSERT INTO land_details (
    web_entry_no, year, village, pargana_tehsil_district, khata_no, khasra_no, area_in_hectare, share
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$village_names = $_POST['village_name'];
$parganas = $_POST['pargana_tehsil_district'];
$khata_nos = $_POST['khata_no'];
$khasra_nos = $_POST['khasra_no'];
$areas = $_POST['area_in_hectare'];
$shares = $_POST['share'];

for ($i = 0; $i < count($khata_nos); $i++) {
  $insertLand->execute([
    $_POST['web_entry_no'], $_POST['year'],
    $village_names[$i], $parganas[$i],
    $khata_nos[$i], $khasra_nos[$i],
    $areas[$i], $shares[$i]
  ]);
}

header("Location: view_entry.php?web_entry_no=" . $_POST['web_entry_no'] . "&year=" . $_POST['year']);
exit;
?>
