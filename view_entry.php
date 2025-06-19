<?php
include 'db.php';
$serial_no = $_GET['serial_no'];
$year = $_GET['year'];

$stmt = $pdo->prepare("SELECT * FROM diary_entries WHERE serial_no = ? AND year = ?");
$stmt->execute([$serial_no, $year]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Application Preview</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div class="doc">
    <h1>Loan Application Preview</h1>
    <p><strong>Name:</strong> <?= $entry['name'] ?></p>
    <p><strong>Tehsil:</strong> <?= $entry['tehsil'] ?></p>
    <p><strong>Amount:</strong> <?= $entry['amount'] ?></p>
    <p><strong>Village:</strong> <?= $entry['village'] ?></p>
    <p><strong>Affidavit:</strong> <?= $entry['affidavit'] ?></p>
    <p><strong>Deed:</strong> <?= $entry['deed'] ?></p>
    <p><strong>Stamp:</strong> <?= $entry['stamp'] ?></p>
    <!-- You can expand more fields here as needed -->
  </div>
</body>
</html>
