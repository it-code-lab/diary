<?php
include 'db.php';

$serial_no = $_GET['serial_no'];
$year = $_GET['year'];

// Fetch diary entry
$stmt = $pdo->prepare("SELECT * FROM diary_entries WHERE serial_no = ? AND year = ?");
$stmt->execute([$serial_no, $year]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch land details
$land_stmt = $pdo->prepare("
  SELECT * FROM land_details 
  WHERE serial_no = ? AND year = ?
  ORDER BY village, pargana_tehsil_district, khata_no, khasra_no
");
$land_stmt->execute([$serial_no, $year]);
$land_rows = $land_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group land details
$grouped_land = [];
foreach ($land_rows as $row) {
    $key = $row['village'] . ' | ' . $row['pargana_tehsil_district'];
    $grouped_land[$key][] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Application Document</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .entry, .group { margin-bottom: 30px; }
    .group-title { background: #eee; padding: 8px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
  </style>
</head>
<body>
  <h1>Loan Application - Serial <?= $entry['serial_no'] ?>/<?= $entry['year'] ?></h1>

  <div class="entry">
    <p><strong>Name:</strong> <?= $entry['name'] ?></p>
    <p><strong>Date:</strong> <?= $entry['entry_date'] ?></p>
    <p><strong>Tehsil:</strong> <?= $entry['tehsil'] ?></p>
    <p><strong>Request:</strong> <?= $entry['request'] ?></p>
    <p><strong>Village:</strong> <?= $entry['village'] ?></p>
    <p><strong>Came Through:</strong> <?= $entry['came_thru'] ?></p>
    <p><strong>Amount:</strong> <?= $entry['amount'] ?></p>
    <p><strong>NEC:</strong> <?= $entry['nec'] ?> | <strong>Affidavit:</strong> <?= $entry['affidavit'] ?> | 
       <strong>Deed:</strong> <?= $entry['deed'] ?> | <strong>B-C.:</strong> <?= $entry['b_c'] ?></p>
    <p><strong>Stamp:</strong> <?= $entry['stamp'] ?> | <strong>Expenses:</strong> <?= $entry['expenses'] ?> | 
       <strong>Advance:</strong> <?= $entry['advance'] ?> | <strong>Remaining:</strong> <?= $entry['remaining'] ?></p>
  </div>

  <hr>

  <h2>Land Details</h2>

  <?php foreach ($grouped_land as $group => $rows): ?>
    <div class="group">
      <div class="group-title">Village / Pargana: <?= $group ?></div>
      <table>
        <thead>
          <tr>
            <th>Khata No</th>
            <th>Khasra No</th>
            <th>Area (Hectare)</th>
            <th>Share</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $land): ?>
            <tr>
              <td><?= $land['khata_no'] ?></td>
              <td><?= $land['khasra_no'] ?></td>
              <td><?= $land['area_in_hectare'] ?></td>
              <td><?= $land['share'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endforeach; ?>

</body>
</html>
