<?php

/**
 * Secret Web Seeder for Render / Online Production Database
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';

header('Content-Type: text/html; charset=utf-8');

// Security Check: Requires ?key=orlms_secret_seed_2026 or matching SEED_SECRET env var
$secretKey = getenv('SEED_SECRET') ?: 'orlms_secret_seed_2026';
$providedKey = $_GET['key'] ?? '';

if ($providedKey !== $secretKey) {
    http_response_code(403);
    die("<h2 style='color:red;'>403 Forbidden: Invalid or missing secret key.</h2><p>Access to database seeder is restricted.</p>");
}

echo "<h2>ORLMS Online Database Seeder</h2>";


try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color:blue;'>Connected to MySQL Database (" . DB_HOST . ")...</p>";

    // Execute full database schema creation
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $schemaQueries = [
        "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(150) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('super_admin','legislative_staff','committee_member','sp_member') NOT NULL DEFAULT 'legislative_staff',
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_users_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `committees` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `jurisdiction` TEXT NOT NULL,
            `chairperson_id` INT UNSIGNED DEFAULT NULL,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_committees_chairperson` (`chairperson_id`),
            CONSTRAINT `fk_committees_chairperson` FOREIGN KEY (`chairperson_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `ordinances` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ordinance_no` VARCHAR(50) NOT NULL,
            `title` VARCHAR(500) NOT NULL,
            `subject` VARCHAR(500) DEFAULT NULL,
            `content` LONGTEXT DEFAULT NULL,
            `author_id` INT UNSIGNED DEFAULT NULL,
            `committee_id` INT UNSIGNED DEFAULT NULL,
            `status` ENUM('draft','submitted','under_review','endorsed','approved','enacted','rejected','archived','published','certified','signed_lce','vetoed','sp_review_approved','sp_review_disapproved','sp_review_comments') NOT NULL DEFAULT 'draft',
            `ai_summary` TEXT DEFAULT NULL,
            `file_path` VARCHAR(500) DEFAULT NULL,
            `date_filed` DATE DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_ordinances_no` (`ordinance_no`),
            KEY `fk_ordinances_author` (`author_id`),
            KEY `fk_ordinances_committee` (`committee_id`),
            KEY `idx_ordinances_status` (`status`),
            CONSTRAINT `fk_ordinances_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk_ordinances_committee` FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `resolutions` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `resolution_no` VARCHAR(50) NOT NULL,
            `title` VARCHAR(500) NOT NULL,
            `subject` VARCHAR(500) DEFAULT NULL,
            `content` LONGTEXT DEFAULT NULL,
            `author_id` INT UNSIGNED DEFAULT NULL,
            `committee_id` INT UNSIGNED DEFAULT NULL,
            `status` ENUM('draft','submitted','under_review','endorsed','approved','enacted','rejected','archived','published','certified','signed_lce','vetoed','sp_review_approved','sp_review_disapproved','sp_review_comments') NOT NULL DEFAULT 'draft',
            `ai_summary` TEXT DEFAULT NULL,
            `file_path` VARCHAR(500) DEFAULT NULL,
            `date_filed` DATE DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_resolutions_no` (`resolution_no`),
            KEY `fk_resolutions_author` (`author_id`),
            KEY `fk_resolutions_committee` (`committee_id`),
            KEY `idx_resolutions_status` (`status`),
            CONSTRAINT `fk_resolutions_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk_resolutions_committee` FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `publications` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `document_type` ENUM('ordinance','resolution') NOT NULL,
            `document_id` INT UNSIGNED NOT NULL,
            `publication_ref` VARCHAR(100) NOT NULL,
            `plain_summary` TEXT DEFAULT NULL,
            `published_by` INT UNSIGNED DEFAULT NULL,
            `published_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_publications_user` (`published_by`),
            CONSTRAINT `fk_publications_user` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `audit_logs` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED DEFAULT NULL,
            `action` VARCHAR(100) NOT NULL,
            `table_name` VARCHAR(100) DEFAULT NULL,
            `target_table` VARCHAR(100) DEFAULT NULL,
            `record_id` INT UNSIGNED DEFAULT NULL,
            `target_id` INT UNSIGNED DEFAULT NULL,
            `old_value` TEXT DEFAULT NULL,
            `new_value` TEXT DEFAULT NULL,
            `location` VARCHAR(150) DEFAULT NULL,
            `ip_address` VARCHAR(45) DEFAULT NULL,
            `details` TEXT DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_audit_user` (`user_id`),
            CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `ai_validation_reports` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `document_type` ENUM('ordinance','resolution') NOT NULL,
            `document_id` INT UNSIGNED NOT NULL,
            `validation_status` ENUM('passed','flagged','failed') NOT NULL DEFAULT 'flagged',
            `completeness_score` INT NOT NULL DEFAULT 0,
            `similarity_score` FLOAT NOT NULL DEFAULT 0,
            `similar_document_type` ENUM('ordinance','resolution') DEFAULT NULL,
            `similar_document_id` INT UNSIGNED DEFAULT NULL,
            `similar_document_no` VARCHAR(50) DEFAULT NULL,
            `completeness_details` JSON DEFAULT NULL,
            `similarity_details` JSON DEFAULT NULL,
            `ai_summary` TEXT DEFAULT NULL,
            `recommendation` TEXT DEFAULT NULL,
            `raw_response` LONGTEXT DEFAULT NULL,
            `validated_by` INT UNSIGNED DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_aivr_document` (`document_type`, `document_id`),
            KEY `fk_aivr_validator` (`validated_by`),
            CONSTRAINT `fk_aivr_validator` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `review_logs` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `document_type` ENUM('ordinance','resolution') NOT NULL,
            `document_id` INT UNSIGNED NOT NULL,
            `action` VARCHAR(100) NOT NULL,
            `reason` TEXT DEFAULT NULL,
            `reviewed_by` INT UNSIGNED DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_rl_document` (`document_type`, `document_id`),
            KEY `fk_rl_reviewer` (`reviewed_by`),
            CONSTRAINT `fk_rl_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `amendments` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `document_type` ENUM('ordinance','resolution') NOT NULL,
            `document_id` INT UNSIGNED NOT NULL,
            `amendment_no` VARCHAR(100) NOT NULL,
            `description` TEXT NOT NULL,
            `changes` LONGTEXT NOT NULL,
            `status` ENUM('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
            `amended_by` INT UNSIGNED DEFAULT NULL,
            `amended_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_amend_document` (`document_type`, `document_id`),
            KEY `fk_amend_user` (`amended_by`),
            CONSTRAINT `fk_amend_user` FOREIGN KEY (`amended_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `monitoring_logs` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `document_type` ENUM('ordinance','resolution') NOT NULL,
            `document_id` INT UNSIGNED NOT NULL,
            `implementation_status` ENUM('pending','ongoing','completed','delayed') NOT NULL,
            `implementation_notes` TEXT NOT NULL,
            `logged_by` INT UNSIGNED DEFAULT NULL,
            `logged_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_ml_document` (`document_type`, `document_id`),
            KEY `fk_ml_user` (`logged_by`),
            CONSTRAINT `fk_ml_user` FOREIGN KEY (`logged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `public_consultations` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `document_id` INT UNSIGNED NOT NULL,
            `document_type` ENUM('ordinance','resolution') NOT NULL,
            `hearing_date` DATE NOT NULL,
            `venue` VARCHAR(255) NOT NULL,
            `total_participants` INT NOT NULL DEFAULT 0,
            `total_opinions` INT NOT NULL DEFAULT 0,
            `sentiment_summary` VARCHAR(100) DEFAULT NULL,
            `summary_report` TEXT DEFAULT NULL,
            `report_file_url` VARCHAR(500) DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_pc_document` (`document_type`, `document_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ];

    foreach ($schemaQueries as $sql) {
        try {
            $db->exec($sql);
        } catch (\Throwable $ex) {
            echo "<p style='color:orange;'>Notice executing query: " . htmlspecialchars($ex->getMessage()) . "</p>";
        }
    }
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");


    echo "<p style='color:green;'>✓ Database tables created/verified successfully.</p>";

    // Seed default admin users if empty
    try {
        $userCheck = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ((int)$userCheck === 0) {
            $passHash = password_hash('password123', PASSWORD_BCRYPT);
            $users = [
                ['name' => 'Super Administrator', 'email' => 'superadmin@csjdm.gov.ph', 'role' => 'super_admin'],
                ['name' => 'Legislative Staff', 'email' => 'staff@csjdm.gov.ph', 'role' => 'legislative_staff'],
                ['name' => 'Committee Chair', 'email' => 'committee@csjdm.gov.ph', 'role' => 'committee_member'],
                ['name' => 'SP Member', 'email' => 'spmember@csjdm.gov.ph', 'role' => 'sp_member']
            ];
            $stmtUser = $db->prepare("INSERT INTO users (name, email, password, role, is_active, created_at) VALUES (:name, :email, :pass, :role, 1, NOW())");
            foreach ($users as $u) {
                $stmtUser->execute([':name' => $u['name'], ':email' => $u['email'], ':pass' => $passHash, ':role' => $u['role']]);
            }
            echo "<p style='color:green;'>✓ Default users created.</p>";
        }
    } catch (\Throwable $ex) {}



    // Sample Committees
    try {
        $committees = [
            ['name' => 'Committee on Laws, Rules, and Internal Government', 'jurisdiction' => 'Review of legal compliance, ordinances, resolutions, and council rules.'],
            ['name' => 'Committee on Public Safety, Order, and Transportation', 'jurisdiction' => 'Traffic management, public safety, local police, and transport regulation.'],
            ['name' => 'Committee on Health, Sanitation, and Environment', 'jurisdiction' => 'Solid waste management, healthcare services, public markets, and environmental protection.'],
            ['name' => 'Committee on Education, Youth, and Sports Development', 'jurisdiction' => 'Scholarships, youth programs, sports facilities, and school infrastructure.'],
            ['name' => 'Committee on Finance, Budget, and Appropriations', 'jurisdiction' => 'City budget, revenue generation, local taxation, and financial appropriations.']
        ];

        foreach ($committees as $comm) {
            $stmt = $db->prepare("SELECT id FROM committees WHERE name = :name");
            $stmt->execute([':name' => $comm['name']]);
            if (!$stmt->fetch()) {
                $db->prepare("INSERT INTO committees (name, jurisdiction, is_active, created_at) VALUES (:name, :jurisdiction, 1, NOW())")
                   ->execute([':name' => $comm['name'], ':jurisdiction' => $comm['jurisdiction']]);
            }
        }
    } catch (\Throwable $ex) {}

    $commList = [];
    try {
        $commList = $db->query("SELECT id FROM committees")->fetchAll(PDO::FETCH_COLUMN);
    } catch (\Throwable $ex) {}


    // Sample Ordinances
    $sampleOrdinances = [
        [
            'no' => 'ORD-2026-0001',
            'title' => 'An Ordinance Establishing the Comprehensive Ecological Solid Waste Management Program of San Jose del Monte, Bulacan',
            'subject' => 'Environmental Protection & Waste Management',
            'content' => "WHEREAS, Republic Act No. 9003 mandates all LGUs to adopt a systematic waste management system;\n\nNOW THEREFORE, BE IT ORDAINED by the Sangguniang Panlungsod of CSJDM:\n\nSECTION 1. Mandatory Segregation - All households and commercial establishments shall segregate waste into biodegradable, recyclable, and residual.\n\nSECTION 2. Penalties - Violators shall be fined PHP 1,000 for 1st offense, PHP 3,000 for 2nd offense.\n\nSECTION 3. Separability Clause - If any provision is held invalid, others remain in effect.\n\nSECTION 4. Effectivity Clause - This ordinance shall take effect 15 days upon publication.",
            'status' => 'published',
            'comm_id' => $commList[2] ?? 1,
            'date' => '2026-01-10',
            'ai_summary' => 'Mandates strict household and commercial waste segregation in CSJDM with penalties ranging from 1,000 to 3,000 pesos.',
            'pub_summary' => 'Inaatas sa lahat ng residente at negosyo sa San Jose del Monte na magbukod ng nabubulok at di-nabubulok na basura. May parusang multa sa mga lalabag.'
        ],
        [
            'no' => 'ORD-2026-0002',
            'title' => 'An Ordinance Implementing a One-Way Traffic Scheme and Anti-Illegal Parking Along Tungkong Mangga Main Thoroughfares',
            'subject' => 'Public Safety and Transportation',
            'content' => "WHEREAS, traffic congestion in Tungkong Mangga requires immediate legislative intervention;\n\nNOW THEREFORE, BE IT ORDAINED by the Sangguniang Panlungsod of CSJDM:\n\nSECTION 1. One-Way Route - Designated segments of Quirino Highway shall be declared one-way during peak hours (6 AM - 9 AM).\n\nSECTION 2. Towing Policy - Illegally parked vehicles shall be towed at the owner's expense.\n\nSECTION 3. Effectivity Clause - Takes effect immediately upon Mayor's approval.",
            'status' => 'enacted',
            'comm_id' => $commList[1] ?? 1,
            'date' => '2026-02-01',
            'ai_summary' => 'Establishes a peak-hour one-way traffic route in Tungkong Mangga and strict towing of illegally parked vehicles.',
            'pub_summary' => null
        ],
        [
            'no' => 'ORD-2026-0003',
            'title' => 'An Ordinance Institutionalizing the CSJDM Youth Sports Development Program and Annual Inter-Barangay Olympics',
            'subject' => 'Youth Development & Sports',
            'content' => "WHEREAS, the Constitution mandates the promotion of physical education and sports for youth development;\n\nNOW THEREFORE, BE IT ORDAINED:\n\nSECTION 1. Inter-Barangay Olympics - An annual sports competition shall be held every summer.\n\nSECTION 2. Appropriations - PHP 2,000,000 shall be allocated annually from the youth development fund.\n\nSECTION 3. Effectivity Clause - Takes effect upon approval.",
            'status' => 'approved',
            'comm_id' => $commList[3] ?? 1,
            'date' => '2026-02-15',
            'ai_summary' => 'Establishes an annual Inter-Barangay sports league with an annual budget allocation of 2 Million Pesos.',
            'pub_summary' => null
        ],
        [
            'no' => 'ORD-2026-0004',
            'title' => 'An Ordinance Regulating the Operation of Public Markets and Night Bazaars in Barangay Muzon',
            'subject' => 'Trade, Commerce & Local Economy',
            'content' => "WHEREAS, economic activity in Barangay Muzon requires structured sanitation and vendor zoning guidelines;\n\nNOW THEREFORE, BE IT ORDAINED:\n\nSECTION 1. Vendor Permits - All night bazaar vendors must secure a special Mayor's Permit.\n\nSECTION 2. Sanitation Code - Vendors must provide their own trash bins.\n\nSECTION 3. Effectivity Clause - Takes effect upon publication.",
            'status' => 'endorsed',
            'comm_id' => $commList[0] ?? 1,
            'date' => '2026-03-01',
            'ai_summary' => 'Requires special vendor permits and mandatory trash bins for night bazaars in Barangay Muzon.',
            'pub_summary' => null
        ],
        [
            'no' => 'ORD-2026-0005',
            'title' => 'An Ordinance Providing Senior Citizens Discount on Local City Hall Administrative Processing Fees',
            'subject' => 'Social Services & Senior Welfare',
            'content' => "WHEREAS, the city values the contribution of senior citizens and seeks to ease their administrative expenses;\n\nNOW THEREFORE, BE IT ORDAINED:\n\nSECTION 1. 50% Fee Discount - Senior citizens residing in CSJDM shall enjoy 50% discount on birth/marriage certificate processing fees.\n\nSECTION 2. Effectivity Clause - Immediate effectivity.",
            'status' => 'under_review',
            'comm_id' => $commList[4] ?? 1,
            'date' => '2026-03-10',
            'ai_summary' => 'Grants a 50% discount on civil registry administrative fees for CSJDM senior citizens.',
            'pub_summary' => null
        ]
    ];

    foreach ($sampleOrdinances as $ord) {
        $check = $db->prepare("SELECT id FROM ordinances WHERE ordinance_no = :no");
        $check->execute([':no' => $ord['no']]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            $stmt = $db->prepare("INSERT INTO ordinances (ordinance_no, title, subject, content, author_id, committee_id, status, ai_summary, date_filed, created_at, updated_at) 
                                  VALUES (:no, :title, :subject, :content, 1, :comm_id, :status, :ai_summary, :date, NOW(), NOW())");
            $stmt->execute([
                ':no' => $ord['no'],
                ':title' => $ord['title'],
                ':subject' => $ord['subject'],
                ':content' => $ord['content'],
                ':comm_id' => $ord['comm_id'],
                ':status' => $ord['status'],
                ':ai_summary' => $ord['ai_summary'],
                ':date' => $ord['date'],
            ]);
            $ordId = $db->lastInsertId();

            if ($ord['status'] === 'published' && $ord['pub_summary']) {
                $db->prepare("INSERT INTO publications (document_type, document_id, publication_ref, plain_summary, published_by, published_at) 
                              VALUES ('ordinance', :doc_id, :ref, :summary, 1, NOW())")
                   ->execute([
                       ':doc_id' => $ordId,
                       ':ref' => 'PUB-2026-001',
                       ':summary' => $ord['pub_summary']
                   ]);
            }
        }
    }

    // Sample Resolutions
    $sampleResolutions = [
        [
            'no' => 'RES-2026-0001',
            'title' => 'A Resolution Expressing Strong Support for the Construction of the New CSJDM General Hospital Expansion Wing',
            'subject' => 'Public Health Facilities',
            'status' => 'published',
            'comm_id' => $commList[2] ?? 1,
            'date' => '2026-01-15',
            'pub_summary' => 'Resolusyon ng suporta ng Sangguniang Panlungsod sa pagtatayo ng bagong hospital wing sa CSJDM General Hospital.'
        ],
        [
            'no' => 'RES-2026-0002',
            'title' => 'A Resolution Commending the Top Topnotchers of the 2026 Licensure Examination for Teachers from CSJDM',
            'subject' => 'Commendations and Awards',
            'status' => 'enacted',
            'comm_id' => $commList[3] ?? 1,
            'date' => '2026-02-05',
            'pub_summary' => null
        ],
        [
            'no' => 'RES-2026-0003',
            'title' => 'A Resolution Authorizing the City Mayor to Enter into a Memorandum of Agreement with DICT for Free Public Wi-Fi Access',
            'subject' => 'Information Technology & Infrastructure',
            'status' => 'approved',
            'comm_id' => $commList[0] ?? 1,
            'date' => '2026-02-20',
            'pub_summary' => null
        ]
    ];

    foreach ($sampleResolutions as $res) {
        $check = $db->prepare("SELECT id FROM resolutions WHERE resolution_no = :no");
        $check->execute([':no' => $res['no']]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            $stmt = $db->prepare("INSERT INTO resolutions (resolution_no, title, subject, content, author_id, committee_id, status, date_filed, created_at, updated_at) 
                                  VALUES (:no, :title, :subject, 'WHEREAS, the Sangguniang Panlungsod deems it necessary to pass this resolution...', 1, :comm_id, :status, :date, NOW(), NOW())");
            $stmt->execute([
                ':no' => $res['no'],
                ':title' => $res['title'],
                ':subject' => $res['subject'],
                ':comm_id' => $res['comm_id'],
                ':status' => $res['status'],
                ':date' => $res['date'],
            ]);
            $resId = $db->lastInsertId();

            if ($res['status'] === 'published' && $res['pub_summary']) {
                $db->prepare("INSERT INTO publications (document_type, document_id, publication_ref, plain_summary, published_by, published_at) 
                              VALUES ('resolution', :doc_id, :ref, :summary, 1, NOW())")
                   ->execute([
                       ':doc_id' => $resId,
                       ':ref' => 'PUB-2026-002',
                       ':summary' => $res['pub_summary']
                   ]);
            }
        }
    }

    echo "<h3 style='color:green;'>SUCCESS: Online Database Populated Successfully!</h3>";
    echo "<p><a href='https://orlms.onrender.com/portal'>Click here to view the Public Portal</a></p>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error Seeding Database: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
