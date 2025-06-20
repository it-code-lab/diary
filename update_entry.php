<?php
require_once("db.php");

$web_entry_no = $_POST['web_entry_no'] ?? null;
$year = $_POST['year'] ?? null;

if (!$web_entry_no || !$year) {
    die("Missing web_entry_no or year");
}

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

    // Update main entry
    $sql = "UPDATE diary_entries SET 
        entry_date = ?, name = ?, tehsil = ?, amount = ?, nec = ?, affidavit = ?, deed = ?, b_c = ?, stamp = ?, 
        came_thru = ?, expenses = ?, advance = ?, remaining = ?, village = ?, request = ?
        WHERE web_entry_no = ? AND year = ?";

    $params = [
        $_POST['entry_date'], $_POST['name'], $_POST['tehsil'], $_POST['amount'],
        $_POST['nec'], $_POST['affidavit'], $_POST['deed'], $_POST['b_c'], $_POST['stamp'],
        $_POST['came_thru'], $_POST['expenses'], $_POST['advance'], $_POST['remaining'],
        $_POST['village'], $_POST['request'], $web_entry_no, $year
    ];

    // Debug log: show full SQL with values
    $debug_sql = $sql;
    foreach ($params as $param) {
        $param_escaped = $pdo->quote($param); // safe SQL literal
        $debug_sql = preg_replace('/\?/', $param_escaped, $debug_sql, 1);
    }
    // error_log("📝 Executing SQL: $debug_sql");

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Delete existing land details
    $pdo->prepare("DELETE FROM land_details WHERE web_entry_no = ? AND year = ?")->execute([$web_entry_no, $year]);


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
    // error_log("❌ Update failed: " . $e->getMessage());
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
