<?php
include 'db.php';

$web_entry_no = $_GET['web_entry_no'] ?? '';

// Fetch diary entry
$stmt = $pdo->prepare("SELECT * FROM diary_entries WHERE web_entry_no = ?");
$stmt->execute([$web_entry_no]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch land details
$land_stmt = $pdo->prepare("SELECT * FROM land_details WHERE web_entry_no = ? ORDER BY id ASC");
$land_stmt->execute([$web_entry_no]);
$land_details = $land_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group land details by village/pargana pair
$grouped_land = [];
foreach ($land_details as $land) {
  $key = $land['village'] . '||' . $land['pargana_tehsil_district'];
  $grouped_land[$key][] = $land;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Entry</title>
  <link rel="stylesheet" href="css/styles.css">
  <script>
    function canRemoveSection(container) {
      const inputs = container.querySelectorAll('input[type="text"], textarea, select');
      for (let input of inputs) {
        if (input.value.trim() !== '') {
          alert("Please clear all fields before cancelling.");
          return false;
        }
      }
      return true;
    }

    function addCancelButton(container) {
      const cancelBtn = document.createElement('button');
      cancelBtn.type = 'button';
      cancelBtn.textContent = 'Cancel';
      cancelBtn.className = 'btn btn-danger';
      cancelBtn.style.marginLeft = '10px';
      cancelBtn.onclick = function () {
        if (canRemoveSection(container)) {
          container.remove();
        }
      };
      container.appendChild(cancelBtn);
    }

    function addLandRow(groupId) {
      const container = document.getElementById(groupId);
      const row = document.createElement('div');
      row.style.border = '1px solid #ccc';
      row.style.padding = '10px';
      row.style.marginTop = '10px';
      row.style.position = 'relative';

      row.innerHTML = `
        <label>Khata No: <input type="text" name="khata_no[]"></label>
        <label>Khasra/Plot No: <input type="text" name="khasra_no[]"></label>
        <label>Area (Hectare): <input type="text" name="area_in_hectare[]"></label>
        <label>Share: <input type="text" name="share[]"></label>
        <input type="hidden" name="village_group[]" value="${groupId}">
      `;
      addCancelButton(row);
      container.appendChild(row);
    }

    function addVillageGroup() {
      const groupsContainer = document.getElementById('village-groups');
      const groupId = `group_${Date.now()}`;
      const div = document.createElement('div');
      div.id = groupId;
      div.style.background = '#e4e4e4';
      div.style.padding = '15px';
      div.style.borderRadius = '15px';
      div.style.marginBottom = '15px';

      div.innerHTML = `
        <label>Village: <input type="text" name="village_name[]"></label><br>
        <label>Pargana/Tehsil/District: <input type="text" name="pargana_tehsil_district[]"></label><br>
        <div id="${groupId}_rows"></div>
        <button type="button" onclick="addLandRow('${groupId}_rows')">+ Add Khata, Khasra</button>
      `;
      addCancelButton(div);
      groupsContainer.appendChild(div);
    }
  </script>
</head>
<body>
<h2>Edit Diary Entry</h2>
<form method="post" action="update_entry.php">
  <input type="hidden" name="web_entry_no" value="<?= $web_entry_no ?>">
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

  <hr>
  <h3>Land Details</h3>
  <div id="village-groups">
    <?php foreach ($grouped_land as $key => $lands): 
      list($village, $pargana) = explode('||', $key);
      $groupId = 'group_' . md5($key);
    ?>
    <div id="<?= $groupId ?>" style="background:#e4e4e4; padding:15px; border-radius:15px; margin-bottom:15px;">
      <label>Village: <input type="text" name="village_name[]" value="<?= $village ?>"></label><br>
      <label>Pargana/Tehsil/District: <input type="text" name="pargana_tehsil_district[]" value="<?= $pargana ?>"></label><br>
      <div id="<?= $groupId ?>_rows">
        <?php foreach ($lands as $land): ?>
          <div style="border:1px solid #ccc; padding:10px; margin-top:10px;">
            <label>Khata No: <input type="text" name="khata_no[]" value="<?= $land['khata_no'] ?>"></label>
            <label>Khasra/Plot No: <input type="text" name="khasra_no[]" value="<?= $land['khasra_no'] ?>"></label>
            <label>Area (Hectare): <input type="text" name="area_in_hectare[]" value="<?= $land['area_in_hectare'] ?>"></label>
            <label>Share: <input type="text" name="share[]" value="<?= $land['share'] ?>"></label>
            <input type="hidden" name="village_group[]" value="<?= $groupId ?>">
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" onclick="addLandRow('<?= $groupId ?>_rows')">+ Add Khata, Khasra</button>
      <script>addCancelButton(document.getElementById("<?= $groupId ?>"));</script>
    </div>
    <?php endforeach; ?>
  </div>
  <button type="button" onclick="addVillageGroup()">+ Add Village / Pargana Group</button><br><br>
  <button type="submit">Update Entry</button>
</form>
</body>
</html>
