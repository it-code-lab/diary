<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
?>

<?php
require_once("db.php");
$stmt = $pdo->query("SELECT * FROM diary_entries ORDER BY web_entry_no DESC");
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diary Entries</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            background: #f4f4f4;
        }

        .container {
            max-width: 960px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .add-entry {
            display: block;
            margin-bottom: 15px;
            color: #007BFF;
            text-decoration: none;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
        }

        @media screen and (max-width: 600px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead tr {
                display: none;
            }

            tr {
                margin-bottom: 15px;
                border-bottom: 1px solid #ccc;
                background-color: antiquewhite;
            }

            td {
                padding: 8px 10px;
                text-align: right;
                position: relative;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                text-align: left;
                font-weight: bold;
            }

            td:last-child {
                text-align: left;
            }

            td[data-label="Action"]::before {
                content: "Action";
                font-weight: bold;
                display: block;
                margin-bottom: 4px;
            }

            td[data-label="Action"] {
                text-align: right;
            }

            .action-links {
                justify-content: flex-start;
                display: inline !important;
            }
        }

        .action-links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 5px;
        }

        .btn-link {
            padding: 4px 8px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
        }

        .btn-link:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <p style="text-align:right;"><a href="logout.php">Logout (<?= $_SESSION['user'] ?>)</a></p>

    <div class="container">
        <h2>Web Diary Entries</h2>
        <a href="add_entry.php" class="add-entry">+ Add New Entry</a>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Web Entry No</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Tehsil</th>
                        <th>Village</th>
                        <th>Request</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $row): ?>
                        <tr>
                            <td data-label="Web Entry No"><?= $row['web_entry_no'] ?></td>
                            <td data-label="Date"><?= $row['entry_date'] ?></td>
                            <td data-label="Name"><?= htmlspecialchars($row['name']) ?></td>
                            <td data-label="Tehsil"><?= htmlspecialchars($row['tehsil']) ?></td>
                            <td data-label="Village"><?= htmlspecialchars($row['village']) ?></td>
                            <td data-label="Request"><?= htmlspecialchars($row['request']) ?></td>
                            <td data-label="Action">
                                <div class="action-links">
                                    <a href="view_entry.php?web_entry_no=<?= $row['web_entry_no'] ?>&year=<?= $row['year'] ?>"
                                        class="btn-link">View</a>
                                    <a href="edit_entry.php?web_entry_no=<?= $row['web_entry_no'] ?>&year=<?= $row['year'] ?>"
                                        class="btn-link">Edit</a>
                                </div>
                            </td>


                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>