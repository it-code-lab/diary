<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Loan Diary Dashboard</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <h1>Diary Entries</h1>
  <a href="add_entry.php">+ Add New Entry</a>
  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>Serial No</th><th>Year</th><th>Name</th><th>Bank</th><th>Amount</th><th>Village</th><th>Action</th>
    </tr>
    <?php
    $stmt = $pdo->query("SELECT * FROM diary_entries ORDER BY year DESC, serial_no ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
          <td>{$row['serial_no']}</td>
          <td>{$row['year']}</td>
          <td>{$row['name']}</td>
          <td>{$row['tehsil']}</td>
          <td>{$row['amount']}</td>
          <td>{$row['village']}</td>
          <td><a href='view_entry.php?serial_no={$row['serial_no']}&year={$row['year']}'>View</a> |
          <a href='edit_entry.php?serial_no={$row['serial_no']}&year={$row['year']}'>Edit</a>
          </td>
          </tr>";
    }
    ?>
  </table>
</body>
</html>
