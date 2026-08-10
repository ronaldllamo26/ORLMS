<?php
/**
 * ORLMS - Dashboard View
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

// Helper: returns a CSS badge class based on document status
function statusBadge(string $status): string {
    return match($status) {
        'draft'        => 'badge-draft',
        'submitted'    => 'badge-submitted',
        'under_review' => 'badge-under-review',
        'endorsed'     => 'badge-endorsed',
        'approved'     => 'badge-approved',
        'enacted'      => 'badge-enacted',
        'published'    => 'badge-published',
        'rejected'     => 'badge-rejected',
        'archived'     => 'badge-archived',
        'implemented'  => 'badge-implemented',
        'amended'      => 'badge-amended',
        default        => 'badge-draft',
    };
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                System overview as of <?= date('F d, Y') ?>
            </p>
        </div>
        <?php if (in_array($_SESSION['user_role'], ['legislative_staff', 'super_admin'])): ?>
        <div class="d-flex gap-8">
            <a href="<?= APP_ROOT_URL ?>/ordinance/create" class="btn btn-primary btn-sm">
                New Ordinance
            </a>
            <a href="<?= APP_ROOT_URL ?>/resolution/create" class="btn btn-outline btn-sm">
                New Resolution
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Statistics Row ─────────────────────────────────────── -->
<div class="row row-4" style="margin-bottom: 24px;">

    <div class="stat-card">
        <div class="stat-label">Total Ordinances</div>
        <div class="stat-value"><?= number_format($stats['total_ordinances']) ?></div>
        <div class="stat-meta">All ordinances on record</div>
    </div>

    <div class="stat-card accent">
        <div class="stat-label">Total Resolutions</div>
        <div class="stat-value"><?= number_format($stats['total_resolutions']) ?></div>
        <div class="stat-meta">All resolutions on record</div>
    </div>

    <div class="stat-card success">
        <div class="stat-label">Enacted</div>
        <div class="stat-value"><?= number_format($stats['enacted']) ?></div>
        <div class="stat-meta">Enacted documents</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">For Review</div>
        <div class="stat-value"><?= number_format($stats['pending_review']) ?></div>
        <div class="stat-meta">Awaiting review</div>
    </div>

</div>

<!-- ── Second Stats Row ───────────────────────────────────── -->
<div class="row row-4" style="margin-bottom: 28px;">

    <div class="stat-card">
        <div class="stat-label">Drafts</div>
        <div class="stat-value"><?= number_format($stats['draft']) ?></div>
        <div class="stat-meta">Work in progress</div>
    </div>

    <div class="stat-card danger">
        <div class="stat-label">Rejected</div>
        <div class="stat-value"><?= number_format($stats['rejected']) ?></div>
        <div class="stat-meta">Archived rejections</div>
    </div>

    <div class="stat-card accent">
        <div class="stat-label">Total Documents</div>
        <div class="stat-value">
            <?= number_format($stats['total_ordinances'] + $stats['total_resolutions']) ?>
        </div>
        <div class="stat-meta">Ordinances and resolutions combined</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Current Year</div>
        <div class="stat-value"><?= date('Y') ?></div>
        <div class="stat-meta">Legislative session</div>
    </div>

</div>

<!-- ── Analytics Charts Row ───────────────────────────────── -->
<div class="row row-2" style="margin-bottom: 28px; gap: 20px; display: flex !important; flex-wrap: wrap !important;">
    <!-- Status Distribution Doughnut Chart -->
    <div class="card" style="flex: 1; min-width: 300px; padding: 20px;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
            <h2 class="card-title" style="font-size: 15px; font-weight: 700; color: #0C2340; margin: 0;">
                <i class="bi bi-pie-chart-fill me-2" style="color: #0084FF;"></i> Status Distribution
            </h2>
        </div>
        <div style="height: 280px; position: relative; display: flex; justify-content: center; align-items: center;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Committee Distribution Bar Chart -->
    <div class="card" style="flex: 1; min-width: 300px; padding: 20px;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
            <h2 class="card-title" style="font-size: 15px; font-weight: 700; color: #0C2340; margin: 0;">
                <i class="bi bi-bar-chart-fill me-2" style="color: #F2A900;"></i> Committee Distribution
            </h2>
        </div>
        <div style="height: 280px; position: relative;">
            <canvas id="committeeChart"></canvas>
        </div>
    </div>
</div>

<!-- ── Recent Documents ───────────────────────────────────── -->
<div class="row row-2">

    <!-- Recent Ordinances -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Ordinances</h2>
            <a href="<?= APP_ROOT_URL ?>/ordinance"
               class="btn btn-outline btn-sm">
                View All
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date Filed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrdinances)): ?>
                        <?php foreach ($recentOrdinances as $ord): ?>
                        <tr>
                            <td>
                                <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ord['id'] ?>"
                                   style="font-weight:500; color:var(--color-primary);">
                                    <?= htmlspecialchars($ord['ordinance_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($ord['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($ord['title']) > 45
                                        ? substr($ord['title'], 0, 45) . '...'
                                        : $ord['title']
                                    ) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= statusBadge($ord['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $ord['status'])) ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= $ord['date_filed']
                                    ? date('M d, Y', strtotime($ord['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="table-empty">
                                No ordinances on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Resolutions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Resolutions</h2>
            <a href="<?= APP_ROOT_URL ?>/resolution"
               class="btn btn-outline btn-sm">
                View All
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date Filed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentResolutions)): ?>
                        <?php foreach ($recentResolutions as $res): ?>
                        <tr>
                            <td>
                                <a href="<?= APP_ROOT_URL ?>/resolution/view/<?= $res['id'] ?>"
                                   style="font-weight:500; color:var(--color-primary);">
                                    <?= htmlspecialchars($res['resolution_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($res['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($res['title']) > 45
                                        ? substr($res['title'], 0, 45) . '...'
                                        : $res['title']
                                    ) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= statusBadge($res['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $res['status'])) ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= $res['date_filed']
                                    ? date('M d, Y', strtotime($res['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="table-empty">
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
