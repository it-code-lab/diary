<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
?>

<?php
include 'db.php';

$web_entry_no = $_GET['web_entry_no'];
$year = $_GET['year'];

// Fetch diary entry
$stmt = $pdo->prepare("SELECT * FROM diary_entries WHERE web_entry_no = ? AND year = ?");
$stmt->execute([$web_entry_no, $year]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch land details
$land_stmt = $pdo->prepare("
  SELECT * FROM land_details 
  WHERE web_entry_no = ? AND year = ?
  ORDER BY village, pargana_tehsil_district, khata_no, khasra_no
");
$land_stmt->execute([$web_entry_no, $year]);
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 15px;
      background-color: #f8f8f8;
    }

    .entry-container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      font-size: 22px;
      text-align: center;
      margin-bottom: 20px;
    }

    .nav-links {
      margin-bottom: 10px;
      font-size: 14px;
    }

    .nav-links a {
      color: #007BFF;
      text-decoration: none;
      margin-right: 8px;
    }

    .nav-links a:hover {
      text-decoration: underline;
    }

    .field {
      margin: 5px 0;
      font-size: 15px;
    }

    .field strong {
      font-weight: 600;
      display: inline-block;
      min-width: 110px;
    }

    .land-section {
      margin-top: 25px;
    }

    .land-header {
      background-color: #e0e0e0;
      padding: 8px;
      font-weight: bold;
      font-size: 15px;
      border-radius: 5px 5px 0 0;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      margin-bottom: 20px;
    }

    th,
    td {
      padding: 8px;
      text-align: left;
      border: 1px solid #ccc;
    }

    th {
      background-color: #f5f5f5;
      font-weight: bold;
    }

    @media screen and (max-width: 600px) {
      .field {
        font-size: 14px;
      }

      .land-header {
        font-size: 14px;
      }

      table {
        font-size: 13px;
      }
    }
  </style>

</head>

<body>
  <div class="entry-container">
    <h2>Web Entry <?= $entry['web_entry_no'] ?>/<?= $entry['year'] ?></h2>

    <div class="nav-links">
      <a href="index.php">← Back to Diary</a> |
      <a href="edit_entry.php?web_entry_no=<?= $entry['web_entry_no'] ?>&year=<?= $entry['year'] ?>">✏️ Edit This
        Entry</a>
    </div>

    <div class="field"><strong>Name:</strong> <?= htmlspecialchars($entry['name']) ?></div>
    <div class="field"><strong>Date:</strong> <?= $entry['entry_date'] ?></div>
    <div class="field"><strong>Tehsil:</strong> <?= htmlspecialchars($entry['tehsil']) ?></div>
    <div class="field"><strong>Request:</strong> <?= htmlspecialchars($entry['request']) ?></div>
    <div class="field"><strong>Village:</strong> <?= htmlspecialchars($entry['village']) ?></div>
    <div class="field"><strong>Came Through:</strong> <?= htmlspecialchars($entry['came_thru']) ?></div>
    <div class="field"><strong>Amount:</strong> <?= $entry['amount'] ?></div>
    <div class="field"><strong>NEC:</strong> <?= $entry['nec'] ?></div>
    <div class="field"><strong>Affidavit:</strong> <?= $entry['affidavit'] ?></div>
    <div class="field"><strong>Deed:</strong> <?= $entry['deed'] ?></div>
    <div class="field"><strong>B-C:</strong> <?= $entry['b_c'] ?></div>
    <div class="field"><strong>Stamp:</strong> <?= $entry['stamp'] ?></div> 
    <div class="field"><strong>Expenses:</strong> <?= $entry['expenses'] ?></div>
    <div class="field"><strong>Advance:</strong> <?= $entry['advance'] ?></div>
    <div class="field"><strong>Remaining:</strong> <?= $entry['remaining'] ?>
    </div>

    <hr>

    <h3>Land Details</h3>

    <?php foreach ($grouped_land as $group => $rows): ?>
      <div class="land-section">
        <div class="land-header">Village / Pargana: <?= str_replace('|', ' | ', $key) ?></div>
        <div class="table-wrapper">
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
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= $row['khata_no'] ?></td>
                  <td><?= $row['khasra_no'] ?></td>
                  <td><?= $row['area_in_hectare'] ?></td>
                  <td><?= $row['share'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</body>

</html>