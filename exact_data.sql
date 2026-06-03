SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `sales_realizations`;
DELETE FROM `sales_targets`;
DELETE FROM `sales_members`;
DELETE FROM `entities`;
DELETE FROM `teams`;

INSERT INTO `teams` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'TEAM SALES A', 'TSA', NOW(), NOW()),
(2, 'TEAM SALES B', 'TSB', NOW(), NOW()),
(3, 'TEAM SALES C', 'TSC', NOW(), NOW()),
(4, 'BREE COFFEE', 'BC', NOW(), NOW()),
(5, 'TANABAMBU', 'TB', NOW(), NOW()),
(6, 'RHEINATA SHASI K.', 'RSK', NOW(), NOW()),
(7, 'SABRINA NURHASANAH', 'SN', NOW(), NOW());

INSERT INTO `entities` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'BUANA', 'E1', NOW(), NOW()),
(2, 'BENTALA', 'E2', NOW(), NOW()),
(3, 'TRITUNAS', 'E3', NOW(), NOW()),
(4, 'BAHANA', 'E4', NOW(), NOW()),
(5, 'SUWUNG', 'E5', NOW(), NOW()),
(6, 'INSTAFACTORY', 'E6', NOW(), NOW());

INSERT INTO `sales_members` (`id`, `team_id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, 'REZKY ADIBRATA', 'SM1', NOW(), NOW()),
(2, 1, 'YUDHIE SISWAHYUDIE', 'SM2', NOW(), NOW()),
(3, 1, 'SUYATMAN', 'SM3', NOW(), NOW()),
(4, 2, 'KARPIANTO ARIBOWO', 'SM4', NOW(), NOW()),
(5, 3, 'RIO ANGGRIAWAN', 'SM5', NOW(), NOW()),
(6, 4, 'BREE COFFEE & KITCHEN', 'SM6', NOW(), NOW()),
(7, 5, 'TANABAMBU', 'SM7', NOW(), NOW()),
(8, 6, 'SUWUNG STUDIO', 'SM8', NOW(), NOW()),
(9, 7, 'INSTAFACTORY', 'SM9', NOW(), NOW());

INSERT INTO `sales_targets` (`year`, `month`, `sales_member_id`, `entity_id`, `target_amount`, `created_at`, `updated_at`) VALUES
(2026, 4, 1, 1, 1097701931, NOW(), NOW()),
(2026, 5, 1, 1, 1920978378, NOW(), NOW()),
(2026, 1, 1, 1, 24423867954, NOW(), NOW()),
(2026, 4, 1, 2, 2019709580, NOW(), NOW()),
(2026, 5, 1, 2, 3534491765, NOW(), NOW()),
(2026, 1, 1, 2, 44938538155, NOW(), NOW()),
(2026, 4, 2, 1, 156816030, NOW(), NOW()),
(2026, 5, 2, 1, 274428053, NOW(), NOW()),
(2026, 1, 2, 1, 3489156667, NOW(), NOW()),
(2026, 4, 2, 2, 261206558, NOW(), NOW()),
(2026, 5, 2, 2, 457111476, NOW(), NOW()),
(2026, 1, 2, 2, 5811845904, NOW(), NOW()),
(2026, 4, 3, 1, 318600429, NOW(), NOW()),
(2026, 5, 3, 1, 358425483, NOW(), NOW()),
(2026, 1, 3, 1, 3305479451, NOW(), NOW()),
(2026, 4, 3, 2, 403941916, NOW(), NOW()),
(2026, 5, 3, 2, 454434656, NOW(), NOW()),
(2026, 1, 3, 2, 4190897378, NOW(), NOW()),
(2026, 4, 4, 1, 1633275000, NOW(), NOW()),
(2026, 5, 4, 1, 1633275000, NOW(), NOW()),
(2026, 1, 4, 1, 15948450000, NOW(), NOW()),
(2026, 4, 4, 2, 3213000000, NOW(), NOW()),
(2026, 5, 4, 2, 3213000000, NOW(), NOW()),
(2026, 1, 4, 2, 31374000000, NOW(), NOW()),
(2026, 4, 5, 3, 1653750000, NOW(), NOW()),
(2026, 5, 5, 3, 1653750000, NOW(), NOW()),
(2026, 1, 5, 3, 19787242440, NOW(), NOW()),
(2026, 4, 6, 4, 701345000, NOW(), NOW()),
(2026, 5, 6, 4, 700345000, NOW(), NOW()),
(2026, 1, 6, 4, 6860760000, NOW(), NOW()),
(2026, 4, 7, 4, 1395918000, NOW(), NOW()),
(2026, 5, 7, 4, 1558718000, NOW(), NOW()),
(2026, 1, 7, 4, 13304256000, NOW(), NOW()),
(2026, 4, 8, 5, 53644225, NOW(), NOW()),
(2026, 5, 8, 5, 60354225, NOW(), NOW()),
(2026, 1, 8, 5, 530538800, NOW(), NOW()),
(2026, 4, 9, 6, 156729430, NOW(), NOW()),
(2026, 5, 9, 6, 188437857, NOW(), NOW()),
(2026, 1, 9, 6, 1761669717, NOW(), NOW());

INSERT INTO `sales_realizations` (`year`, `month`, `sales_member_id`, `entity_id`, `realization_amount`, `created_at`, `updated_at`) VALUES
(2026, 4, 1, 1, 1062014621, NOW(), NOW()),
(2026, 5, 1, 1, 33000000, NOW(), NOW()),
(2026, 1, 1, 1, 1768102343, NOW(), NOW()),
(2026, 4, 1, 2, 1741157800, NOW(), NOW()),
(2026, 5, 1, 2, 1941270660, NOW(), NOW()),
(2026, 1, 1, 2, 6275356510, NOW(), NOW()),
(2026, 4, 2, 1, 15840000, NOW(), NOW()),
(2026, 5, 2, 1, 49080000, NOW(), NOW()),
(2026, 1, 2, 1, 303899000, NOW(), NOW()),
(2026, 4, 2, 2, 429890000, NOW(), NOW()),
(2026, 5, 2, 2, 173798000, NOW(), NOW()),
(2026, 1, 2, 2, 1103156000, NOW(), NOW()),
(2026, 4, 3, 1, 296074400, NOW(), NOW()),
(2026, 5, 3, 1, 229999000, NOW(), NOW()),
(2026, 1, 3, 1, 1866409979, NOW(), NOW()),
(2026, 4, 3, 2, 416500000, NOW(), NOW()),
(2026, 5, 3, 2, 76620000, NOW(), NOW()),
(2026, 1, 3, 2, 1211997000, NOW(), NOW()),
(2026, 4, 4, 1, 1024174745, NOW(), NOW()),
(2026, 5, 4, 1, 1301825850, NOW(), NOW()),
(2026, 1, 4, 1, 1299228640, NOW(), NOW()),
(2026, 4, 4, 2, 649264050, NOW(), NOW()),
(2026, 5, 4, 2, 1636424610, NOW(), NOW()),
(2026, 1, 4, 2, 5356995772, NOW(), NOW()),
(2026, 4, 5, 3, 139026600, NOW(), NOW()),
(2026, 5, 5, 3, 92841000, NOW(), NOW()),
(2026, 1, 5, 3, 314375335, NOW(), NOW()),
(2026, 4, 6, 4, 282263650, NOW(), NOW()),
(2026, 5, 6, 4, 323303150, NOW(), NOW()),
(2026, 1, 6, 4, 1019513916, NOW(), NOW()),
(2026, 4, 7, 4, 843464749, NOW(), NOW()),
(2026, 5, 7, 4, 888491486, NOW(), NOW()),
(2026, 1, 7, 4, 2451048344, NOW(), NOW()),
(2026, 4, 8, 5, 30340000, NOW(), NOW()),
(2026, 5, 8, 5, 32777000, NOW(), NOW()),
(2026, 1, 8, 5, 71405000, NOW(), NOW()),
(2026, 4, 9, 6, 155069000, NOW(), NOW()),
(2026, 5, 9, 6, 129801000, NOW(), NOW()),
(2026, 1, 9, 6, 540507938, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
