<?php
include 'db.php';

$stmt = $pdo->prepare("INSERT INTO diary_entries 
(serial_no, year, entry_date, name, tehsil, amount, nec, affidavit, deed, b_c, stamp, expenses, advance, remaining, came_thru, request, village) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
  $_POST['serial_no'], $_POST['year'], $_POST['entry_date'], $_POST['name'], $_POST['tesil'], $_POST['amount'],
  $_POST['nec'], $_POST['affidavit'], $_POST['deed'], $_POST['b_c'], $_POST['stamp'],
  $_POST['expenses'], $_POST['advance'], $_POST['remaining'], $_POST['came_thru'], $_POST['request'], $_POST['village']
]);

header("Location: index.php");
exit;
?>
