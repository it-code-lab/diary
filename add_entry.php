<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
?>

<?php
include 'db.php';

// Fetch next serial number
$stmt = $pdo->query("SELECT MAX(web_entry_no) AS max_serial FROM diary_entries");
$max = $stmt->fetch(PDO::FETCH_ASSOC)['max_serial'] ?? 0;
$next_web_entry = $max + 1;

$today = date('Y-m-d');
$current_year = date('Y');

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
    <title>Add Entry with Land Details</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="/diary/images/icon.png">
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
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 22px;
            margin-bottom: 20px;
            text-align: center;
        }

        form label {
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

        button {
            padding: 10px 18px;
            font-size: 14px;
            margin-top: 20px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .land-group,
        .land-row {
            border: 1px solid #ddd;
            padding: 12px;
            margin: 10px 0;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .cancel-btn {
            background: #dc3545;
            margin-left: 10px;
        }

        .cancel-btn:hover {
            background: #a71d2a;
        }

        @media (max-width: 600px) {
            h2 {
                font-size: 20px;
            }

            button {
                width: 100%;
            }
        }
    </style>

    <script>
        function updateRemaining() {
            const expense = parseFloat(document.getElementById('expenses').value) || 0;
            const advance = parseFloat(document.getElementById('advance').value) || 0;
            document.getElementById('remaining').value = (expense - advance).toFixed(0);
        }

        function addLandRow(groupId) {
            const container = document.getElementById(groupId);
            const row = document.createElement('div');
            row.style.border = '1px solid #ccc';
            row.style.padding = '10px';
            row.style.marginTop = '10px';
            row.style.position = 'relative';
            row.style.background = '#fff';

            row.innerHTML = `
    <label>Khata No: <input type="text" name="khata_no[]"></label>
    <label>Khasra/Plot No: <input type="text" name="khasra_no[]"></label>
    <label>Area (Hectare): <input type="text" name="area_in_hectare[]"></label>
    <label>Share: <input type="text" name="share[]"></label>
    <input type="hidden" name="village_group[]" value="${groupId}">
  `;

            addCancelButton(row); // <-- attach cancel logic
            container.appendChild(row);
        }


        function addVillageGroup() {
            const groupsContainer = document.getElementById('village-groups');
            const groupId = `group_${Date.now()}`;
            const div = document.createElement('div');
            div.id = groupId;
            div.style.padding = '15px';
            div.style.borderRadius = '15px';
            div.style.marginBottom = '15px';

            div.innerHTML = `
    <h4>New Village/Pargana Group</h4>
    <label>Village: <input type="text" name="village_name[]"></label><br>
    <label>Pargana/Tehsil/District: <input type="text" name="pargana_tehsil_district[]"></label><br>
    <div id="${groupId}_rows"></div>
    <button type="button" onclick="addLandRow('${groupId}_rows')">+ Add Khata, Khasra</button>
  `;

            addCancelButton(div);  // <-- attach cancel logic
            groupsContainer.appendChild(div);
        }

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
            cancelBtn.style.marginLeft = '10px';
            cancelBtn.className = 'btn btn-danger';

            cancelBtn.onclick = function () {
                if (canRemoveSection(container)) {
                    container.remove();
                }
            };

            container.appendChild(cancelBtn);
        }

    </script>
</head>

<body>
    <div class="container">
        <p>
            <a href="index.php">← Back to Diary</a>
        </p>
        <h2>Add Web Entry</h2>
        <form action="save_entry.php" method="post">
            <label>Web Entry No: <input type="number" name="web_entry_no" value="<?= $next_web_entry ?>"
                    required></label><br>
            <label>Year: <input type="text" name="year" value="<?= $current_year ?>" required></label><br>
            <label>Date: <input type="date" name="entry_date" value="<?= $today ?>"></label><br>
            <!-- <label>Name: <input type="text" name="name" required></label><br> -->
            <label>Name:<br>
            <textarea name="name" rows="4" cols="50" required></textarea>
            </label><br>
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

            <button type="submit">Save Entry</button>
        </form>
    </div>
</body>

</html>