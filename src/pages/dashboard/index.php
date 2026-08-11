<?php
/**
 * ORLMS - Dashboard View (Tailwind CSS)
 *
 * @var array $stats
 * @var array $recentOrdinances
 * @var array $recentResolutions
 * @var array $chartStatusData
 * @var array $committeeStats
 */

// Initialize variables to prevent IDE/Static analysis warnings
$stats = $stats ?? [];
$recentOrdinances = $recentOrdinances ?? [];
$recentResolutions = $recentResolutions ?? [];
$chartStatusData = $chartStatusData ?? [];
$committeeStats = $committeeStats ?? [];

// Helper: returns Tailwind CSS badge classes based on document status
function statusBadge(string $status): string {
    return match($status) {
        'draft'        => 'bg-slate-100 text-slate-700 border-slate-200',
        'submitted'    => 'bg-blue-50 text-blue-700 border-blue-200',
        'under_review' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'endorsed'     => 'bg-purple-50 text-purple-700 border-purple-200',
        'approved'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'enacted'      => 'bg-teal-50 text-teal-700 border-teal-200',
        'published'    => 'bg-amber-50 text-amber-700 border-amber-200',
        'rejected'     => 'bg-rose-50 text-rose-700 border-rose-200',
        'archived'     => 'bg-gray-150 text-gray-700 border-gray-200',
        'implemented'  => 'bg-green-50 text-green-700 border-green-200',
        'amended'      => 'bg-orange-50 text-orange-700 border-orange-200',
        default        => 'bg-slate-100 text-slate-700 border-slate-200',
    };
}
?>

<!-- Page Header -->
<div class="mb-6 pb-4 border-b border-gray-250 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1">
            System overview as of <?= date('F d, Y') ?>
        </p>
    </div>
    <?php if (in_array($_SESSION['user_role'], ['legislative_staff', 'super_admin'])): ?>
    <div class="flex gap-3">
        <a href="<?= APP_ROOT_URL ?>/ordinance/create" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-medium rounded-md shadow-sm transition duration-150">
            New Ordinance
        </a>
        <a href="<?= APP_ROOT_URL ?>/resolution/create" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 border border-slate-350 text-slate-700 text-sm font-medium rounded-md shadow-sm transition duration-150">
            New Resolution
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- ── Statistics Row ─────────────────────────────────────── -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Ordinances</div>
        <div class="text-3xl font-extrabold text-slate-800 mt-2"><?= number_format($stats['total_ordinances']) ?></div>
        <div class="text-xs text-slate-400 mt-1.5">All ordinances on record</div>
    </div>

    <div class="bg-white border border-slate-200 border-t-4 border-t-accent rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Resolutions</div>
        <div class="text-3xl font-extrabold text-primary mt-2"><?= number_format($stats['total_resolutions']) ?></div>
        <div class="text-xs text-slate-400 mt-1.5">All resolutions on record</div>
    </div>

    <div class="bg-white border border-slate-200 border-t-4 border-t-emerald-500 rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Enacted</div>
        <div class="text-3xl font-extrabold text-emerald-600 mt-2"><?= number_format($stats['enacted']) ?></div>
        <div class="text-xs text-slate-400 mt-1.5">Enacted documents</div>
    </div>

    <div class="bg-white border border-slate-200 border-t-4 border-t-rose-500 rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">For Review</div>
        <div class="text-3xl font-extrabold text-rose-600 mt-2"><?= number_format($stats['pending_review']) ?></div>
        <div class="text-xs text-slate-400 mt-1.5">Awaiting review</div>
    </div>

</div>

<!-- ── Second Stats Row ───────────────────────────────────── -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Drafts</div>
        <div class="text-3xl font-extrabold text-slate-800 mt-2"><?= number_format($stats['draft']) ?></div>
        <div class="text-xs text-slate-400 mt-1.5">Work in progress</div>
    </div>

    <div class="bg-white border border-slate-200 border-t-4 border-t-red-500 rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rejected</div>
        <div class="text-3xl font-extrabold text-red-600 mt-2"><?= number_format($stats['rejected']) ?></div>
        <div class="text-xs text-slate-400 mt-1.5">Archived rejections</div>
    </div>

    <div class="bg-white border border-slate-200 border-t-4 border-t-accent rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Documents</div>
        <div class="text-3xl font-extrabold text-primary mt-2">
            <?= number_format($stats['total_ordinances'] + $stats['total_resolutions']) ?>
        </div>
        <div class="text-xs text-slate-400 mt-1.5">Ordinances and resolutions combined</div>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200">
        <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase">Current Year</div>
        <div class="text-3xl font-extrabold text-slate-800 mt-2"><?= date('Y') ?></div>
        <div class="text-xs text-slate-400 mt-1.5">Legislative session</div>
    </div>

</div>

<!-- ── Analytics Charts Row ───────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Status Distribution Doughnut Chart -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="border-b border-slate-200 pb-3 mb-5 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 flex items-center">
                <i class="bi bi-pie-chart-fill mr-2 text-sky-500"></i> Status Distribution
            </h2>
        </div>
        <div class="h-[280px] relative flex justify-center items-center">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Committee Distribution Bar Chart -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="border-b border-slate-200 pb-3 mb-5 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 flex items-center">
                <i class="bi bi-bar-chart-fill mr-2 text-amber-500"></i> Committee Distribution
            </h2>
        </div>
        <div class="h-[280px] relative">
            <canvas id="committeeChart"></canvas>
        </div>
    </div>
</div>

<!-- ── Recent Documents ───────────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Recent Ordinances -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-800">Recent Ordinances</h2>
            <a href="<?= APP_ROOT_URL ?>/ordinance" class="inline-flex items-center px-3 py-1 bg-white hover:bg-slate-50 border border-slate-250 text-slate-700 text-xs font-semibold rounded shadow-sm transition">
                View All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-650 font-bold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">No.</th>
                        <th class="px-5 py-3.5">Title</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Date Filed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (!empty($recentOrdinances)): ?>
                        <?php foreach ($recentOrdinances as $ord): ?>
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="px-5 py-3.5 align-middle">
                                <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ord['id'] ?>" class="font-semibold text-primary hover:text-accent transition">
                                    <?= htmlspecialchars($ord['ordinance_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="truncate block max-w-[220px] sm:max-w-[280px]" title="<?= htmlspecialchars($ord['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($ord['title']) > 45
                                        ? substr($ord['title'], 0, 45) . '...'
                                        : $ord['title']
                                    ) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= statusBadge($ord['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $ord['status'])) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle text-slate-500">
                                <?= $ord['date_filed']
                                    ? date('M d, Y', strtotime($ord['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-450 bg-slate-50/20">
                                No ordinances on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Resolutions -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-800">Recent Resolutions</h2>
            <a href="<?= APP_ROOT_URL ?>/resolution" class="inline-flex items-center px-3 py-1 bg-white hover:bg-slate-50 border border-slate-250 text-slate-700 text-xs font-semibold rounded shadow-sm transition">
                View All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-650 font-bold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">No.</th>
                        <th class="px-5 py-3.5">Title</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Date Filed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (!empty($recentResolutions)): ?>
                        <?php foreach ($recentResolutions as $res): ?>
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="px-5 py-3.5 align-middle">
                                <a href="<?= APP_ROOT_URL ?>/resolution/view/<?= $res['id'] ?>" class="font-semibold text-primary hover:text-accent transition">
                                    <?= htmlspecialchars($res['resolution_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="truncate block max-w-[220px] sm:max-w-[280px]" title="<?= htmlspecialchars($res['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($res['title']) > 45
                                        ? substr($res['title'], 0, 45) . '...'
                                        : $res['title']
                                    ) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= statusBadge($res['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $res['status'])) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle text-slate-500">
                                <?= $res['date_filed']
                                    ? date('M d, Y', strtotime($res['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-450 bg-slate-50/20">
                                No resolutions on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js and Initialization Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- 1. Doughnut Chart: Status Distribution ---
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = <?= json_encode(array_values($chartStatusData)) ?>;
    const statusLabels = <?= json_encode(array_keys($chartStatusData)) ?>;
    const totalDocs = statusData.reduce((a, b) => a + b, 0);
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    '#64748b', // Slate for Drafts
                    '#0084FF', // Sky Blue for In Review
                    '#10b981', // Emerald/Green for Enacted
                    '#F2A900', // Gold for Published
                    '#ef4444'  // Red for Rejected
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 10,
                        font: { size: 11, family: "'Inter', sans-serif" }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let percentage = totalDocs > 0 ? ((value / totalDocs) * 100).toFixed(1) + '%' : '0%';
                            return ` ${label}: ${value} (${percentage})`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

    // --- 2. Bar Chart: Committee Distribution ---
    const committeeCtx = document.getElementById('committeeChart').getContext('2d');
    const committeeRaw = <?= json_encode($committeeStats) ?>;
    
    const committeeLabels = committeeRaw.map(item => {
        let name = item.name || 'General';
        // Clean up common long titles for cleaner graphs
        return name.replace('Committee on ', '').replace(' and Privileges', '').replace(' Laws, ', '');
    });
    const ordinanceCounts = committeeRaw.map(item => parseInt(item.ordinance_count) || 0);
    const resolutionCounts = committeeRaw.map(item => parseInt(item.resolution_count) || 0);

    new Chart(committeeCtx, {
        type: 'bar',
        data: {
            labels: committeeLabels,
            datasets: [
                {
                    label: 'Ordinances',
                    data: ordinanceCounts,
                    backgroundColor: '#0C2340', // CSJDM Deep Blue
                    borderRadius: 4
                },
                {
                    label: 'Resolutions',
                    data: resolutionCounts,
                    backgroundColor: '#F2A900', // CSJDM Gold
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 10,
                        font: { size: 11, family: "'Inter', sans-serif" }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
