<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
?>

<?php
include 'db.php';

$web_entry_no = $_GET['web_entry_no'] ?? '';
$year = $_GET['year'] ?? '';

// Fetch main diary entry
$stmt = $pdo->prepare("SELECT * FROM diary_entries WHERE web_entry_no = ? AND year = ?");
$stmt->execute([$web_entry_no, $year]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch land details
$stmt = $pdo->prepare("SELECT * FROM land_details WHERE web_entry_no = ? AND year = ?");
$stmt->execute([$web_entry_no, $year]);
$lands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group lands by a virtual group ID for editing
$grouped_lands = [];
foreach ($lands as $index => $land) {
  $key = $land['village'] . '|' . $land['pargana_tehsil_district'];
  $grouped_lands[$key][] = $land;
}

$request_options = [
  "PNB Kisan Credit Card",
  "Sarv UP Kisan Credit Card",
  "Tractor",
  "Oriental Bank Credit Card"
];

$village_options = [
  "Ambiapur",
  "Beheta Gusain",
  "Bilsi",
  "Gudhni",
  "Islamnagar",
  "Junavai",
  "Kachla",
  "Kariamai",
  "Khitaura",
  "Khitaura Kundan",
  "Kolihai",
  "Kotha",
  "Madkawali",
  "Noorpur Pinoni",
  "Orchi",
  "Ranet",
  "Rudain",
  "Sahaswan",
  "Saraibarolia",
  "Shekhupur",
  "Ugheti",
  "Ujhani",
  "Pidol",
  "Mujaria"
];
?>

<!DOCTYPE html>
<html>

<head>
  <title>Edit Entry</title>
  <link rel="stylesheet" href="css/styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 15px;
    }

    .container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 22px;
    }

    label {
      display: block;
      margin-top: 12px;
      font-weight: bold;
      font-size: 14px;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    select {
      width: 100%;
      padding: 8px;
      margin-top: 4px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    .land-group,
    .land-row {
      border: 1px solid #ddd;
      padding: 12px;
      margin-top: 12px;
      background: #f9f9f9;
      border-radius: 5px;
    }

    .land-row {
      margin-top: 10px;
    }

    .cancel-btn {
      margin-left: 8px;
      background: #dc3545;
      color: white;
      padding: 6px 12px;
      font-size: 13px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .cancel-btn:hover {
      background: #a71d2a;
    }

    button {
      margin-top: 20px;
      padding: 10px 18px;
      font-size: 15px;
      border: none;
      border-radius: 4px;
      background: #007bff;
      color: white;
      cursor: pointer;
    }

    button:hover {
      background: #0056b3;
    }

    @media (max-width: 600px) {
      h2 {
        font-size: 20px;
      }

      button,
      .cancel-btn {
        width: 100%;
        margin-top: 10px;
      }
    }
  </style>

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
        <div style="margin-bottom:10px; padding:10px; border:1px solid #ccc; background: #f9f9f9;">
          <label>Khata No: <input type="text" name="khata_no[]"></label>
          <label>Khasra/Plot No: <input type="text" name="khasra_no[]"></label>
          <label>Area (Hectare): <input type="text" name="area_in_hectare[]"></label>
          <label>Share: <input type="text" name="share[]"></label>
          <input type="hidden" name="village_group[]" value="${groupId}">
          <button type="button" onclick="removeIfEmpty(this)">Cancel</button>
        </div>
      `;
      container.appendChild(newRow);
    }

    function removeIfEmpty(btn) {
      const div = btn.parentElement;
      const inputs = div.querySelectorAll('input[type="text"]');
      const allEmpty = Array.from(inputs).every(input => input.value.trim() === '');
      if (allEmpty) div.remove();
      else alert("Clear all fields before removing.");
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
        <button type="button" onclick="document.getElementById('${groupId}').parentElement.remove()">Cancel</button>
        <hr>
      `;
      groupsContainer.appendChild(div);
    }
  </script>
</head>

<body>
  <div class="container">
    <p>
      <a href="index.php">← Back to Diary</a>
    </p>
    <h2>Edit Diary Entry</h2>
    <form action="update_entry.php" method="post">
      <input type="hidden" name="web_entry_no" value="<?= $web_entry_no ?>">
      <label>Year: <input type="text" name="year" value="<?= $entry['year'] ?>" required></label><br>
      <label>Date: <input type="date" name="entry_date" value="<?= $entry['entry_date'] ?>"></label><br>
      <label>Name: <input type="text" name="name" value="<?= $entry['name'] ?>"></label><br>
      <label>Tehsil: <input type="text" name="tehsil" value="<?= $entry['tehsil'] ?>"></label><br>
      <label>Amount: <input type="text" name="amount" value="<?= $entry['amount'] ?>"></label><br>
      <label>NEC: <input type="text" name="nec" value="<?= $entry['nec'] ?>" maxlength="1"></label><br>
      <label>Affidavit: <input type="text" name="affidavit" value="<?= $entry['affidavit'] ?>"
          maxlength="1"></label><br>
      <label>Deed: <input type="text" name="deed" value="<?= $entry['deed'] ?>" maxlength="1"></label><br>
      <label>B-C: <input type="text" name="b_c" value="<?= $entry['b_c'] ?>"></label><br>
      <label>Stamp: <input type="text" name="stamp" value="<?= $entry['stamp'] ?>"></label><br>
      <label>Came Through: <input type="text" name="came_thru" value="<?= $entry['came_thru'] ?>"></label><br>
      <label>Expenses: <input type="text" id="expenses" name="expenses" value="<?= $entry['expenses'] ?>"
          oninput="updateRemaining()"></label><br>
      <label>Advance: <input type="text" id="advance" name="advance" value="<?= $entry['advance'] ?>"
          oninput="updateRemaining()"></label><br>
      <label>Remaining: <input type="text" id="remaining" name="remaining" value="<?= $entry['remaining'] ?>"
          readonly></label><br>

      <label>Village:
        <select name="village">
          <option value="">--Select--</option>
          <?php foreach ($village_options as $val): ?>
            <option value="<?= $val ?>" <?= $entry['village'] == $val ? 'selected' : '' ?>><?= $val ?></option>
          <?php endforeach; ?>
        </select>
      </label><br>

      <label>Request:
        <select name="request">
          <option value="">--Select--</option>
          <?php foreach ($request_options as $val): ?>
            <option value="<?= $val ?>" <?= $entry['request'] == $val ? 'selected' : '' ?>><?= $val ?></option>
          <?php endforeach; ?>
        </select>
      </label><br>

      <hr>
      <h3>Land Details</h3>
      <div id="village-groups">
        <?php foreach ($grouped_lands as $groupKey => $lands): ?>
          <?php list($villageName, $pargana) = explode('|', $groupKey); ?>
          <?php $groupId = uniqid('group_'); ?>
          <div id="<?= $groupId ?>">
            <h4>Village/Pargana Group</h4>
            <label>Village: <input type="text" name="village_name[]" value="<?= $villageName ?>"></label>
            <label>Pargana/Tehsil/District: <input type="text" name="pargana_tehsil_district[]"
                value="<?= $pargana ?>"></label>
            <div id="<?= $groupId ?>">
              <?php foreach ($lands as $land): ?>
                <div style="border:1px solid #ccc; padding:10px; margin-top:10px; background: #fff;">
                  <label>Khata No: <input type="text" name="khata_no[]" value="<?= $land['khata_no'] ?>"></label>
                  <label>Khasra/Plot No: <input type="text" name="khasra_no[]" value="<?= $land['khasra_no'] ?>"></label>
                  <label>Area (Hectare): <input type="text" name="area_in_hectare[]"
                      value="<?= $land['area_in_hectare'] ?>"></label>
                  <label>Share: <input type="text" name="share[]" value="<?= $land['share'] ?>"></label>
                  <input type="hidden" name="village_group[]" value="<?= $groupId ?>">
                  <button type="button" onclick="removeIfEmpty(this)">Cancel</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" onclick="addLandRow('<?= $groupId ?>')">+ Add Khata, Khasra</button>
            <hr>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" onclick="addVillageGroup()">+ Add Village / Pargana Group</button><br><br>
      <button type="submit">Save Changes</button>
    </form>
  </div>
</body>

</html>