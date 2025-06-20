<?php
require_once("db.php");

// Collect main diary fields
$year = $_POST['year'];
$entry_date = $_POST['entry_date'];
$name = $_POST['name'];
$tehsil = $_POST['tehsil'];
$amount = $_POST['amount'];
$nec = $_POST['nec'];
$affidavit = $_POST['affidavit'];
$deed = $_POST['deed'];
$bc = $_POST['b_c'];
$stamp = $_POST['stamp'];
$came_thru = $_POST['came_thru'];
$expenses = $_POST['expenses'];
$advance = $_POST['advance'];
$remaining = $_POST['remaining'];
$village = $_POST['village'] ?? null;
$request = $_POST['request'] ?? null;

try {
    $pdo->beginTransaction();

    // Step 1: Calculate new web_entry_no for this year
    $stmt = $pdo->prepare("SELECT MAX(web_entry_no) FROM diary_entries WHERE year = ?");
    $stmt->execute([$year]);
    $maxWebEntryNo = $stmt->fetchColumn();
    $web_entry_no = $maxWebEntryNo ? $maxWebEntryNo + 1 : 1;
    //error_log("📌 New web_entry_no: $web_entry_no");

    // Insert main entry
    $sql = "INSERT INTO diary_entries (
        web_entry_no, entry_date, name, tehsil, amount, nec, affidavit, deed, b_c, stamp, 
        came_thru, expenses, advance, remaining, village, request, year
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [
        $web_entry_no, $entry_date, $name, $tehsil, $amount, $nec, $affidavit, $deed, $bc, $stamp,
        $came_thru, $expenses, $advance, $remaining, $village, $request, $year
    ];

    // error_log("Diary Entry SQL: " . formatQuery($sql, $params));

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    
    // error_log("✅ Inserted diary entry $web_entry_no");

    // Capture all land detail related fields
    $village_names = $_POST['village_name'] ?? [];
    $pargana_names = $_POST['pargana_tehsil_district'] ?? [];
    $khata_nos = $_POST['khata_no'] ?? [];
    $khasra_nos = $_POST['khasra_no'] ?? [];
    $areas = $_POST['area_in_hectare'] ?? [];
    $shares = $_POST['share'] ?? [];
    $village_groups = $_POST['village_group'] ?? [];

    // Log data to verify inputs
    // error_log("📦 village_names: " . print_r($village_names, true));
    // error_log("📦 village_groups: " . print_r($village_groups, true));
    // error_log("📦 khata_nos: " . print_r($khata_nos, true));

    // Step 1: Map group_id to list of khata/khasra indexes
    $group_to_indexes = [];
    foreach ($village_groups as $index => $group_id) {
        $group_to_indexes[$group_id][] = $index;
    }

    // Step 2: For each village/pargana group, find its ID and insert its khata details
    $i = 0;
    foreach ($village_names as $village_name) {
        $pargana = $pargana_names[$i] ?? '';
        $group_id = array_keys($group_to_indexes)[$i] ?? null;

        if (!$group_id) {
            // error_log("⚠️ Missing group ID for village $village_name");
            $i++;
            continue;
        }

        foreach ($group_to_indexes[$group_id] as $j) {

            $landSql = "INSERT INTO land_details 
            (web_entry_no, year, village, pargana_tehsil_district, khata_no, khasra_no, area_in_hectare, share) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $landParams = [
                $web_entry_no, $year,
                $village_name,
                $pargana,
                $khata_nos[$j],
                $khasra_nos[$j],
                $areas[$j],
                $shares[$j]
            ];

            // error_log("Land SQL: " . formatQuery($landSql, $landParams));

            $stmt = $pdo->prepare($landSql);
            $stmt->execute($landParams);

            // error_log("✅ Inserted land details for $village_name/$pargana - khata: {$khata_nos[$j]}");
        }

        $i++;
    }

    $pdo->commit();
    header("Location: view_entry.php?web_entry_no=$web_entry_no&year=$year");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    // error_log("❌ Error saving entry: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}


function formatQuery($query, $params) {
    foreach ($params as $param) {
        $param = is_null($param) ? 'NULL' : $param;
        $param = is_numeric($param) ? $param : "'".str_replace("'", "''", $param)."'";
        $query = preg_replace('/\?/', $param, $query, 1);
    }
    return $query;
}
