<?php
$sql = "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql .= "DELETE FROM `sales_realizations`;\n";
$sql .= "DELETE FROM `sales_targets`;\n";
$sql .= "DELETE FROM `sales_members`;\n";
$sql .= "DELETE FROM `entities`;\n";
$sql .= "DELETE FROM `teams`;\n\n";

$sql .= "INSERT INTO `teams` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES\n";
$sql .= "(1, 'Team Alpha', 'TA', NOW(), NOW()),\n";
$sql .= "(2, 'Team Beta', 'TB', NOW(), NOW());\n\n";

$sql .= "INSERT INTO `entities` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES\n";
$sql .= "(1, 'Buana', 'B1', NOW(), NOW()),\n";
$sql .= "(2, 'Bentala', 'B2', NOW(), NOW()),\n";
$sql .= "(3, 'Tritunas', 'T1', NOW(), NOW()),\n";
$sql .= "(4, 'Bahana', 'B3', NOW(), NOW()),\n";
$sql .= "(5, 'Suwung', 'S1', NOW(), NOW()),\n";
$sql .= "(6, 'Instafactory', 'I1', NOW(), NOW());\n\n";

$sql .= "INSERT INTO `sales_members` (`id`, `team_id`, `name`, `code`, `created_at`, `updated_at`) VALUES\n";
$sql .= "(1, 1, 'Andi Suryono', 'SM01', NOW(), NOW()),\n";
$sql .= "(2, 1, 'Budi Santoso', 'SM02', NOW(), NOW()),\n";
$sql .= "(3, 2, 'Citra Lestari', 'SM03', NOW(), NOW()),\n";
$sql .= "(4, 2, 'Dodi Pratama', 'SM04', NOW(), NOW());\n\n";

$targets = [];
$realizations = [];

$entities = [
    1 => ['base_target' => 3000000000, 'growth' => 1.1],
    2 => ['base_target' => 5000000000, 'growth' => 1.05],
    3 => ['base_target' => 1000000000, 'growth' => 1.2],
    4 => ['base_target' => 2000000000, 'growth' => 1.0],
    5 => ['base_target' => 50000000, 'growth' => 1.15],
    6 => ['base_target' => 150000000, 'growth' => 1.05]
];

$salesMembers = [1, 2, 3, 4];
$year = 2026;

// Generating logic similar to the images provided
for ($month = 1; $month <= 12; $month++) {
    foreach ($entities as $entityId => $config) {
        $monthlyTargetTotal = $config['base_target'] * pow($config['growth'], $month - 1);
        
        // Split target across 4 sales members
        foreach ($salesMembers as $smId) {
            $memberTarget = $monthlyTargetTotal / 4;
            $targets[] = "($year, $month, $smId, $entityId, " . round($memberTarget) . ", NOW(), NOW())";
            
            // Generate Realization (only up to May for some to mimic image, or varying)
            // Let's generate up to May (month 5) for realization
            if ($month <= 5) {
                // achievement varies between 70% and 130%
                $randAch = rand(70, 130) / 100;
                // Make Bentala very high
                if ($entityId == 2) $randAch = rand(100, 150) / 100;
                // Make Tritunas very low
                if ($entityId == 3) $randAch = rand(10, 50) / 100;

                $memberReal = $memberTarget * $randAch;
                $realizations[] = "($year, $month, $smId, $entityId, " . round($memberReal) . ", NOW(), NOW())";
            }
        }
    }
}

if (!empty($targets)) {
    $sql .= "INSERT INTO `sales_targets` (`year`, `month`, `sales_member_id`, `entity_id`, `target_amount`, `created_at`, `updated_at`) VALUES\n";
    $sql .= implode(",\n", $targets) . ";\n\n";
}

if (!empty($realizations)) {
    $sql .= "INSERT INTO `sales_realizations` (`year`, `month`, `sales_member_id`, `entity_id`, `realization_amount`, `created_at`, `updated_at`) VALUES\n";
    $sql .= implode(",\n", $realizations) . ";\n\n";
}

$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

file_put_contents('dummy_data.sql', $sql);
echo "dummy_data.sql generated successfully!\n";
