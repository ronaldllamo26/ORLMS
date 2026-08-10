<?php
/**
 * ORLMS - Post Enactment Tracking View
 *
 * Used by Legislative Staff to track documents that have been enacted
 * and need to pass through offline signature / review stages.
 *
 * Variables:
 *   $ordinances  — array of enacted ordinances
 *   $resolutions — array of enacted resolutions
 */
$ordinances  = $ordinances  ?? [];
$resolutions = $resolutions ?? [];
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Post-Enactment Tracking</h1>
        <p class="page-subtitle">Manage offline signatures (Mayor, Presiding Officer) and Provincial Review.</p>
    </div>
</div>

<!-- ─── ORDINANCES ──────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <h2 class="card-title">Ordinances (<?= count($ordinances) ?>)</h2>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="post-enactment-ordinances-table">
            <thead>
                <tr>
                    <th style="width:140px;">Doc No.</th>
                    <th>Title</th>
                    <th style="width:160px;">Author</th>
                    <th style="width:200px;">Current Status</th>
                    <th style="width:160px; text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ordinances)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px; color:var(--color-text-muted);">
                            No ordinances pending post-enactment steps.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ordinances as $ord): ?>
                    <tr>
                        <td style="font-weight:700; color:var(--color-primary);">
                            <?= htmlspecialchars($ord['ordinance_no']) ?>
                        </td>
                        <td>
                            <div style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                 title="<?= htmlspecialchars($ord['title']) ?>">
                                <?= htmlspecialchars($ord['title']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($ord['author_name'] ?? 'Unknown') ?></td>
                        <td>
                            <?php
                            $statusLabel = ucwords(str_replace('_', ' ', $ord['status']));
                            $statusClass = match($ord['status']) {
                                'enacted'              => 'badge-enacted',
                                'certified'            => 'badge-endorsed',
                                'signed_lce'           => 'badge-approved',
                                'vetoed'               => 'badge-rejected',
                                'sp_review_approved'   => 'badge-approved',
                                'sp_review_disapproved'=> 'badge-rejected',
                                'sp_review_comments'   => 'badge-draft',
                                default                => 'badge-draft',
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>">
                                <?= htmlspecialchars($statusLabel) ?>
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <button type="button"
                                    class="btn btn-sm btn-primary"
                                    onclick="openUpdateModal(<?= $ord['id'] ?>, 'ordinance', '<?= htmlspecialchars($ord['ordinance_no'], ENT_QUOTES) ?>', '<?= $ord['status'] ?>')">
                                Update Status
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ─── RESOLUTIONS ─────────────────────────────────────────────── -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Resolutions (<?= count($resolutions) ?>)</h2>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="post-enactment-resolutions-table">
            <thead>
                <tr>
                    <th style="width:140px;">Doc No.</th>
                    <th>Title</th>
                    <th style="width:160px;">Author</th>
                    <th style="width:200px;">Current Status</th>
                    <th style="width:160px; text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resolutions)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px; color:var(--color-text-muted);">
                            No resolutions pending post-enactment steps.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resolutions as $res): ?>
                    <tr>
                        <td style="font-weight:700; color:var(--color-primary);">
                            <?= htmlspecialchars($res['resolution_no']) ?>
                        </td>
                        <td>
                            <div style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                 title="<?= htmlspecialchars($res['title']) ?>">
                                <?= htmlspecialchars($res['title']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($res['author_name'] ?? 'Unknown') ?></td>
                        <td>
                            <?php
                            $statusLabel = ucwords(str_replace('_', ' ', $res['status']));
                            $statusClass = match($res['status']) {
                                'enacted'              => 'badge-enacted',
                                'certified'            => 'badge-endorsed',
                                'signed_lce'           => 'badge-approved',
                                'vetoed'               => 'badge-rejected',
                                'sp_review_approved'   => 'badge-approved',
                                'sp_review_disapproved'=> 'badge-rejected',
                                'sp_review_comments'   => 'badge-draft',
                                default                => 'badge-draft',
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>">
                                <?= htmlspecialchars($statusLabel) ?>
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <button type="button"
                                    class="btn btn-sm btn-primary"
                                    onclick="openUpdateModal(<?= $res['id'] ?>, 'resolution', '<?= htmlspecialchars($res['resolution_no'], ENT_QUOTES) ?>', '<?= $res['status'] ?>')">
                                Update Status
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ─── UPDATE STATUS MODAL ─────────────────────────────────────── -->
<div id="updateStatusModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
            z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:var(--radius); width:100%;
                max-width:500px; margin:auto; box-shadow:var(--shadow-lg);">
        <form action="<?= APP_ROOT_URL ?>/post_enactment/updateStatus" method="POST">
            <!-- Modal Header -->
            <div style="padding:20px 24px; border-bottom:1px solid var(--color-border);
                        display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--color-text);">
                    Update Post-Enactment Status
                </h3>
                <button type="button" onclick="closeUpdateModal()"
                        style="background:none; border:none; cursor:pointer;
                               font-size:20px; color:var(--color-text-muted); line-height:1;">
                    &times;
                </button>
            </div>

            <!-- Modal Body -->
            <div style="padding:24px;">
                <input type="hidden" name="id"   id="modal_doc_id"   value="">
                <input type="hidden" name="type" id="modal_doc_type" value="">

                <div style="margin-bottom:16px;">
                    <label class="form-label">Document Number</label>
                    <input type="text" class="form-input" id="modal_doc_no" readonly
                           style="background:var(--color-bg-alt);">
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Current Status</label>
                    <input type="text" class="form-input" id="modal_current_status" readonly
                           style="background:var(--color-bg-alt);">
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label" for="new_status">
                        New Status <span style="color:#dc3545;">*</span>
                    </label>
                    <select class="form-select" name="new_status" id="new_status" required>
                        <option value="">-- Select next step --</option>
                        <optgroup label="Local Signatures">
                            <option value="<?= STATUS_CERTIFIED ?>">Certified (SP Sec &amp; Presiding Officer)</option>
                            <option value="<?= STATUS_SIGNED_LCE ?>">Signed by Mayor (LCE)</option>
                            <option value="<?= STATUS_VETOED ?>">Vetoed by Mayor</option>
                        </optgroup>
                        <optgroup label="Provincial Review">
                            <option value="<?= STATUS_SP_REVIEW_APPROVED ?>">Approved by Province (SP)</option>
                            <option value="<?= STATUS_SP_REVIEW_DISAPPROVED ?>">Disapproved by Province</option>
                            <option value="<?= STATUS_SP_REVIEW_COMMENTS ?>">Reviewed with Comments</option>
                        </optgroup>
                        <optgroup label="Final Step">
                            <option value="<?= STATUS_PUBLISHED ?>">Mark for Publication / Posting</option>
                        </optgroup>
                    </select>
                </div>
                <p style="font-size:12px; color:var(--color-text-muted); margin:0;">
                    Ensure that physical documents have been signed or received before updating the status here.
                </p>
            </div>

            <!-- Modal Footer -->
            <div style="padding:16px 24px; border-top:1px solid var(--color-border);
                        display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeUpdateModal()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdateModal(id, type, docNo, currentStatus) {
    document.getElementById('modal_doc_id').value   = id;
    document.getElementById('modal_doc_type').value = type;
    document.getElementById('modal_doc_no').value   = docNo;

    var formatted = currentStatus.replace(/_/g, ' ');
    formatted = formatted.charAt(0).toUpperCase() + formatted.slice(1);
    document.getElementById('modal_current_status').value = formatted;

    var modal = document.getElementById('updateStatusModal');
    modal.style.display = 'flex';
}

function closeUpdateModal() {
    document.getElementById('updateStatusModal').style.display = 'none';
}

// Close on backdrop click
document.getElementById('updateStatusModal').addEventListener('click', function(e) {
    if (e.target === this) closeUpdateModal();
});
</script>
