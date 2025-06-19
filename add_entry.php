<!DOCTYPE html>
<html>
<head>
  <title>Add Loan Entry</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <h2>Add New Diary Entry</h2>
  <form action="save_entry.php" method="post">
    <label>Serial No: <input type="number" name="serial_no" required></label><br>
    <label>Year: <input type="text" name="year" required></label><br>
    <label>Date: <input type="date" name="entry_date"></label><br>
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Tehsil: <input type="text" name="tehsil"></label><br>
    <label>Amount: <input type="text" name="amount"></label><br>
    <label>NEC: <input type="text" name="nec"></label><br>
    <label>Affidavit: <input type="text" name="affidavit"></label><br>
    <label>Deed: <input type="text" name="deed"></label><br>
    <label>B.C.: <input type="text" name="b_c"></label><br>
    <label>Stamp: <input type="text" name="stamp"></label><br>
    <label>Expenses: <input type="text" name="expenses"></label><br>
    <label>Advance: <input type="text" name="advance"></label><br>
    <label>Remaining: <input type="text" name="remaining"></label><br>
    <label>Came Through: <input type="text" name="came_thru"></label><br>
    <label>Request: <input type="text" name="request"></label><br>
    <label>Village: <input type="text" name="village"></label><br>
    <button type="submit">Save Entry</button>
  </form>
</body>
</html>
