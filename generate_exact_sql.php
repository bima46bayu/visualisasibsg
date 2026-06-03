<?php
$sql = "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql .= "DELETE FROM `sales_realizations`;\n";
$sql .= "DELETE FROM `sales_targets`;\n";
$sql .= "DELETE FROM `sales_members`;\n";
$sql .= "DELETE FROM `entities`;\n";
$sql .= "DELETE FROM `teams`;\n\n";

$teams = [
    1 => ['TEAM SALES A', 'TSA'],
    2 => ['TEAM SALES B', 'TSB'],
    3 => ['TEAM SALES C', 'TSC'],
    4 => ['BREE COFFEE', 'BC'],
    5 => ['TANABAMBU', 'TB'],
    6 => ['RHEINATA SHASI K.', 'RSK'],
    7 => ['SABRINA NURHASANAH', 'SN']
];
$sql .= "INSERT INTO `teams` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES\n";
$teamValues = [];
foreach($teams as $id => $t) {
    $teamValues[] = "($id, '{$t[0]}', '{$t[1]}', NOW(), NOW())";
}
$sql .= implode(",\n", $teamValues) . ";\n\n";

$entities = [
    1 => ['BUANA', 'E1'],
    2 => ['BENTALA', 'E2'],
    3 => ['TRITUNAS', 'E3'],
    4 => ['BAHANA', 'E4'],
    5 => ['SUWUNG', 'E5'],
    6 => ['INSTAFACTORY', 'E6']
];
$sql .= "INSERT INTO `entities` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES\n";
$entityValues = [];
foreach($entities as $id => $e) {
    $entityValues[] = "($id, '{$e[0]}', '{$e[1]}', NOW(), NOW())";
}
$sql .= implode(",\n", $entityValues) . ";\n\n";

$salesMembers = [
    1 => [1, 'REZKY ADIBRATA', 'SM1'],
    2 => [1, 'YUDHIE SISWAHYUDIE', 'SM2'],
    3 => [1, 'SUYATMAN', 'SM3'],
    4 => [2, 'KARPIANTO ARIBOWO', 'SM4'],
    5 => [3, 'RIO ANGGRIAWAN', 'SM5'],
    6 => [4, 'BREE COFFEE & KITCHEN', 'SM6'],
    7 => [5, 'TANABAMBU', 'SM7'],
    8 => [6, 'SUWUNG STUDIO', 'SM8'],
    9 => [7, 'INSTAFACTORY', 'SM9']
];
$sql .= "INSERT INTO `sales_members` (`id`, `team_id`, `name`, `code`, `created_at`, `updated_at`) VALUES\n";
$smValues = [];
foreach($salesMembers as $id => $sm) {
    $smValues[] = "($id, {$sm[0]}, '{$sm[1]}', '{$sm[2]}', NOW(), NOW())";
}
$sql .= implode(",\n", $smValues) . ";\n\n";

$dataPoints = [
    [1, 1, 1097701931, 1062014621, 1920978378, 33000000, 27442548263, 2863116964],
    [1, 2, 2019709580, 1741157800, 3534491765, 1941270660, 50492739500, 9957784970],
    [2, 1, 156816030, 15840000, 274428053, 49080000, 3920400750, 368819000],
    [2, 2, 261206558, 429890000, 457111476, 173798000, 6530163938, 1706844000],
    [3, 1, 318600429, 296074400, 358425483, 229999000, 3982505363, 2392483379],
    [3, 2, 403941916, 416500000, 454434656, 76620000, 5049273950, 1705117000],
    [4, 1, 1633275000, 1024174745, 1633275000, 1301825850, 19215000000, 3625229235],
    [4, 2, 3213000000, 649264050, 3213000000, 1636424610, 37800000000, 7642684432],
    [5, 3, 1653750000, 139026600, 1653750000, 92841000, 23094742440, 546242935],
    [6, 4, 701345000, 282263650, 700345000, 323303150, 8262450000, 1625080716],
    [7, 4, 1395918000, 843464749, 1558718000, 888491486, 16258892000, 4183004579],
    [8, 5, 53644225, 30340000, 60354225, 32777000, 644537250, 134522000],
    [9, 6, 156729430, 155069000, 188437857, 129801000, 2106837004, 825377938],
];

$targets = [];
$reals = [];

foreach($dataPoints as $row) {
    $sm = $row[0];
    $ent = $row[1];
    $apr_t = $row[2]; $apr_r = $row[3];
    $may_t = $row[4]; $may_r = $row[5];
    $tot_t = $row[6]; $tot_r = $row[7];

    // April
    $targets[] = "(2026, 4, $sm, $ent, $apr_t, NOW(), NOW())";
    $reals[] = "(2026, 4, $sm, $ent, $apr_r, NOW(), NOW())";

    // May
    $targets[] = "(2026, 5, $sm, $ent, $may_t, NOW(), NOW())";
    $reals[] = "(2026, 5, $sm, $ent, $may_r, NOW(), NOW())";

    // January (Rest of the year to match total)
    $rest_t = $tot_t - $apr_t - $may_t;
    $rest_r = $tot_r - $apr_r - $may_r;
    
    // Some values might be negative if they only started tracking later, but let's assume they are fine.
    $targets[] = "(2026, 1, $sm, $ent, $rest_t, NOW(), NOW())";
    $reals[] = "(2026, 1, $sm, $ent, $rest_r, NOW(), NOW())";
}

$sql .= "INSERT INTO `sales_targets` (`year`, `month`, `sales_member_id`, `entity_id`, `target_amount`, `created_at`, `updated_at`) VALUES\n";
$sql .= implode(",\n", $targets) . ";\n\n";

$sql .= "INSERT INTO `sales_realizations` (`year`, `month`, `sales_member_id`, `entity_id`, `realization_amount`, `created_at`, `updated_at`) VALUES\n";
$sql .= implode(",\n", $reals) . ";\n\n";

$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

file_put_contents('exact_data.sql', $sql);
