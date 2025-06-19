<?php
// db.php - assumed to already exist and included
include 'db.php';

// Fetch entry to edit
$serial_no = $_GET['serial_no'] ?? '';
$year = $_GET['year'] ?? '';

// Fetch diary entry
$stmt = $pdo->prepare("SELECT * FROM diary_entries WHERE serial_no = ? AND year = ?");
$stmt->execute([$serial_no, $year]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch land details
$land_stmt = $pdo->prepare("SELECT * FROM land_details WHERE serial_no = ? AND year = ? ORDER BY id ASC");
$land_stmt->execute([$serial_no, $year]);
$land_details = $land_stmt->fetchAll(PDO::FETCH_ASSOC);

$request_options = ["PNB Kisan Credit Card", "Sarv UP Kisan Credit Card", "Tractor", "Oriental Bank Credit Card"];
$village_options = ["Ambiapur", "Beheta Gusain", "Bilsi", "Gudhni", "Islamnagar", "Junavai", "Kachla", "Kariamai", "Khitaura", "Khitaura Kundan", "Kolihai", "Kotha", "Madkawali", "Noorpur Pinoni", "Orchi", "Ranet", "Rudain", "Sahaswan", "Saraibarolia", "Shekhupur", "Ugheti", "Ujhani", "Pidol", "Mujaria"];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Entry</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h2>Edit Diary Entry #<?= htmlspecialchars($serial_no) ?> (<?= htmlspecialchars($year) ?>)</h2>
<form method="post" action="update_entry.php">
  <input type="hidden" name="serial_no" value="<?= $serial_no ?>">
  <input type="hidden" name="year" value="<?= $year ?>">

  <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($entry['name']) ?>"></label><br>
  <label>Tehsil: <input type="text" name="tehsil" value="<?= htmlspecialchars($entry['tehsil']) ?>"></label><br>
  <label>Amount: <input type="text" name="amount" value="<?= $entry['amount'] ?>"></label><br>
  <label>NEC: <input type="text" name="nec" value="<?= $entry['nec'] ?>"></label><br>
  <label>Affidavit: <input type="text" name="affidavit" value="<?= $entry['affidavit'] ?>"></label><br>
  <label>Deed: <input type="text" name="deed" value="<?= $entry['deed'] ?>"></label><br>
  <label>B-C: <input type="text" name="b_c" value="<?= $entry['b_c'] ?>"></label><br>
  <label>Stamp: <input type="text" name="stamp" value="<?= $entry['stamp'] ?>"></label><br>
  <label>Came Through: <input type="text" name="came_thru" value="<?= $entry['came_thru'] ?>"></label><br>
  <label>Expenses: <input type="text" name="expenses" value="<?= $entry['expenses'] ?>"></label><br>
  <label>Advance: <input type="text" name="advance" value="<?= $entry['advance'] ?>"></label><br>
  <label>Remaining: <input type="text" name="remaining" value="<?= $entry['remaining'] ?>"></label><br>
  <label>Village: 
    <select name="village">
      <option value="">--Select--</option>
      <?php foreach ($village_options as $val): ?>
        <option value="<?= $val ?>" <?= $entry['village'] == $val ? 'selected' : '' ?>><?= $val ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Request:</label>
  <select name="request">
    <option value="">--Select--</option>
    <?php foreach ($request_options as $val): ?>
      <option value="<?= $val ?>" <?= $entry['request'] == $val ? 'selected' : '' ?>><?= $val ?></option>
    <?php endforeach; ?>
  </select><br>

  <hr>
  <h3>Edit Land Details</h3>
  <?php foreach ($land_details as $index => $land): ?>
    <fieldset>
      <legend>Entry #<?= $index + 1 ?></legend>
      <input type="hidden" name="land_id[]" value="<?= $land['id'] ?>">
      <label>Village: <input type="text" name="village_name[]" value="<?= $land['village'] ?>"></label><br>
      <label>Pargana/Tehsil/District: <input type="text" name="pargana_tehsil_district[]" value="<?= $land['pargana_tehsil_district'] ?>"></label><br>
      <label>Khata No: <input type="text" name="khata_no[]" value="<?= $land['khata_no'] ?>"></label><br>
      <label>Khasra No: <input type="text" name="khasra_no[]" value="<?= $land['khasra_no'] ?>"></label><br>
      <label>Area (Hectare): <input type="text" name="area_in_hectare[]" value="<?= $land['area_in_hectare'] ?>"></label><br>
      <label>Share: <input type="text" name="share[]" value="<?= $land['share'] ?>"></label><br>
    </fieldset>
  <?php endforeach; ?>

  <button type="submit">Update Entry</button>
</form>
</body>
</html>
