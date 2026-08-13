<?php
/**
 * ORLMS - System Backup & Data Export Dashboard
 *
 * @var array $tableCounts
 */
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">System Backup & Export</li>
            </ul>
            <h1 class="page-title">
                <i class="bi bi-database-fill-gear me-2 text-primary"></i> System Backup & Data Export
            </h1>
            <p class="page-subtitle">Disaster recovery and data portability utilities for Administrator</p>
        </div>
    </div>
</div>

<!-- Grid Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    <!-- Card 1: SQL Database Backup -->
    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="bi bi-file-earmark-code-fill"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Database Backup (.SQL)</h2>
                    <span class="text-xs text-slate-500">Full PostgreSQL Schema & Data Export</span>
                </div>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                Mag-download ng kompletong <code>.sql</code> backup file ng buong PostgreSQL database kasama ang lahat ng tables, users, ordinances, resolutions, AI reports, at audit logs.
            </p>
        </div>
        <a href="<?= APP_ROOT_URL ?>/backup/downloadSql" class="btn btn-primary btn-sm flex items-center justify-center gap-2">
            <i class="bi bi-download"></i> Download Full SQL Backup
        </a>
    </div>

    <!-- Card 2: Excel / CSV Export -->
    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-lg">
                    <i class="bi bi-file-earmark-excel-fill"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Legislative Documents Export (.CSV)</h2>
                    <span class="text-xs text-slate-500">Excel-compatible document registry</span>
                </div>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                Mag-export ng kompletong spreadsheet ng lahat ng active Ordinances at Resolutions sa format na pwedeng buksan sa Microsoft Excel o Google Sheets.
            </p>
        </div>
        <a href="<?= APP_ROOT_URL ?>/backup/exportCsv" class="btn btn-outline btn-sm flex items-center justify-center gap-2">
            <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Download Excel (CSV) File
        </a>
    </div>

</div>

<!-- Table Counts Summary -->
<div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
        <i class="bi bi-hdd-stack-fill text-slate-500"></i> Database Record Summary
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
        <?php foreach ($tableCounts as $table => $count): ?>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded">
                <div class="text-lg font-bold text-primary"><?= number_format($count) ?></div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mt-0.5">
                    <?= htmlspecialchars(str_replace('_', ' ', $table)) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
