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

    // Ensure audit_logs table schema is up-to-date
    try {
        $db->exec("ALTER TABLE audit_logs ADD COLUMN location VARCHAR(150) DEFAULT NULL");
        echo "<p style='color:green;'>✓ Ensured 'location' column exists on audit_logs table.</p>";
    } catch (\Throwable $ex) {
        // Ignore if error or already exists
    }

    // Sample Committees
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

    $commList = $db->query("SELECT id FROM committees")->fetchAll(PDO::FETCH_COLUMN);

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
