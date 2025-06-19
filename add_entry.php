<?php
include 'db.php';

// Fetch next serial number
$stmt = $pdo->query("SELECT MAX(serial_no) AS max_serial FROM diary_entries");
$max = $stmt->fetch(PDO::FETCH_ASSOC)['max_serial'] ?? 0;
$next_serial = $max + 1;

$today = date('Y-m-d');
$current_year = date('Y');

$request_options = [
  "PNB Kisan Credit Card",
  "Sarv UP Kisan Credit Card",
  "Tractor",
  "Oriental Bank Credit Card"
];

$village_options = [
  "Ambiapur", "Beheta Gusain", "Bilsi", "Gudhni", "Islamnagar", "Junavai",
  "Kachla", "Kariamai", "Khitaura", "Khitaura Kundan", "Kolihai", "Kotha",
  "Madkawali", "Noorpur Pinoni", "Orchi", "Ranet", "Rudain", "Sahaswan",
  "Saraibarolia", "Shekhupur", "Ugheti", "Ujhani", "Pidol", "Mujaria"
];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Entry with Land Details</title>
  <link rel="stylesheet" href="css/styles.css">
  <script>
    function updateRemaining() {
      const expense = parseFloat(document.getElementById('expenses').value) || 0;
      const advance = parseFloat(document.getElementById('advance').value) || 0;
      document.getElementById('remaining').value = (expense - advance).toFixed(2);
    }

    function addLandRow(groupId) {
      const container = document.getElementById(groupId);
      const newRow = document.createElement('div');
      newRow.innerHTML = `
        <div style="margin-bottom:10px; padding:10px; border:1px solid #ccc;">
          <label>Khata No: <input type="text" name="khata_no[]"></label>
          <label>Khasra/Plot No: <input type="text" name="khasra_no[]"></label>
          <label>Area (Hectare): <input type="text" name="area_in_hectare[]"></label>
          <label>Share: <input type="text" name="share[]"></label>
          <input type="hidden" name="village_group[]" value="${groupId}">
        </div>
      `;
      container.appendChild(newRow);
    }

    function addVillageGroup() {
      const groupsContainer = document.getElementById('village-groups');
      const groupId = `group_${Date.now()}`;
      const div = document.createElement('div');
      div.id = groupId;
      div.innerHTML = `
        <h4>New Village/Pargana Group</h4>
        <label>Village: <input type="text" name="village_name[]"></label>
        <label>Pargana/Tehsil/District: <input type="text" name="pargana_tehsil_district[]"></label>
        <div id="${groupId}"></div>
        <button type="button" onclick="addLandRow('${groupId}')">+ Add Khata, Khasra</button>
        <hr>
      `;
      groupsContainer.appendChild(div);
    }
  </script>
</head>
<body>
  <h2>Add Diary Entry + Land Details</h2>
  <form action="save_entry.php" method="post">
    <label>Serial No: <input type="number" name="serial_no" value="<?= $next_serial ?>" required></label><br>
    <label>Year: <input type="text" name="year" value="<?= $current_year ?>" required></label><br>
    <label>Date: <input type="date" name="entry_date" value="<?= $today ?>"></label><br>
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Tehsil: <input type="text" name="tehsil"></label><br>
    <label>Amount: <input type="text" name="amount"></label><br>
    <label>NEC: <input type="text" name="nec" maxlength="1"></label><br>
    <label>Affidavit: <input type="text" name="affidavit" maxlength="1"></label><br>
    <label>Deed: <input type="text" name="deed" maxlength="1"></label><br>
    <label>B-C: <input type="text" name="b_c"></label><br>
    <label>Stamp: <input type="text" name="stamp"></label><br>
    <label>Came Through: <input type="text" name="came_thru"></label><br>
    <label>Expenses: <input type="text" name="expenses" id="expenses" oninput="updateRemaining()"></label><br>
    <label>Advance: <input type="text" name="advance" id="advance" oninput="updateRemaining()"></label><br>
    <label>Remaining: <input type="text" name="remaining" id="remaining" readonly></label><br>

    <label>Village:</label>
    <select name="village">
      <option value="">--Select--</option>
      <?php foreach ($village_options as $val): ?>
        <option value="<?= $val ?>"><?= $val ?></option>
      <?php endforeach; ?>
    </select><br>

    <label>Request:</label>
    <select name="request">
      <option value="">--Select--</option>
      <?php foreach ($request_options as $val): ?>
        <option value="<?= $val ?>"><?= $val ?></option>
      <?php endforeach; ?>
    </select><br>

    <hr>
    <h3>Land Details</h3>
    <div id="village-groups"></div>
    <button type="button" onclick="addVillageGroup()">+ Add Village / Pargana Group</button><br><br>

    <button type="submit">Save Entry & Land</button>
  </form>
</body>
</html>
