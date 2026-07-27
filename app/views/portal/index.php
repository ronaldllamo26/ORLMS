<?php
/**
 * ORLMS - Public Portal Index View
 *
 * Variables:
 *   $publications   — search results of published documents
 *   $search, $type, $year — current search filter inputs
 *   $availableYears — array of years for the filter dropdown
 */
?>

<!-- Branding Header Section -->
<div style="background: linear-gradient(135deg, var(--color-primary), #20456e);
            color:#fff; border-radius:var(--radius-lg); padding:36px;
            margin-bottom:28px; text-align:center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);">
    <h1 style="font-size:26px; font-weight:700; margin-bottom:8px; letter-spacing:0.5px;">
        Municipal Ordinance & Resolution Portal
    </h1>
    <p style="font-size:14px; color:rgba(255,255,255,0.85); max-width:650px; margin:0 auto 24px; line-height:1.6;">
        Search and access official copies of active laws, regulations, and resolutions enacted by the Municipal Legislative Council.
    </p>

    <!-- Unified Search Bar -->
    <form method="GET" action="<?= APP_ROOT_URL ?>/portal"
          style="background:#ffffff; border-radius:var(--radius-lg); padding:10px;
                 display:grid; grid-template-columns:1fr auto auto auto; gap:10px;
                 max-width:850px; margin:0 auto; box-shadow:0 8px 20px rgba(0,0,0,0.15);">
        
        <div style="display:flex; align-items:center; padding-left:10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="search" class="form-control"
                   value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search by title, subject, or keywords..."
                   style="border:none; padding:8px 0; outline:none; box-shadow:none; font-size:14px; width:100%; color:var(--color-text);">
        </div>

        <select name="type" class="form-control form-select"
                style="border:none; border-left:1px solid var(--color-border);
                       border-radius:0; padding:4px 10px; font-size:13px; font-weight:500;
                       outline:none; width:140px; color:var(--color-text-muted);">
            <option value="">All Types</option>
            <option value="ordinance" <?= $type === 'ordinance' ? 'selected' : '' ?>>Ordinances</option>
            <option value="resolution" <?= $type === 'resolution' ? 'selected' : '' ?>>Resolutions</option>
        </select>

        <select name="year" class="form-control form-select"
                style="border:none; border-left:1px solid var(--color-border);
                       border-radius:0; padding:4px 10px; font-size:13px; font-weight:500;
                       outline:none; width:120px; color:var(--color-text-muted);">
            <option value="">All Years</option>
            <?php foreach ($availableYears as $y): ?>
            <option value="<?= htmlspecialchars($y) ?>" <?= $year === $y ? 'selected' : '' ?>>
                <?= htmlspecialchars($y) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary"
                style="padding:10px 24px; font-size:13px; font-weight:600;
                       border-radius:var(--radius-lg); background-color:var(--color-accent);
                       border-color:var(--color-accent); color:var(--color-primary);">
            Search
        </button>

    </form>
</div>

<!-- Search Results -->
<div class="row row-1-2" style="align-items:start; gap:24px;">

    <!-- Left: Left-column guide -->
    <div style="background:#ffffff; border:1px solid var(--color-border-light);
                border-radius:var(--radius-lg); padding:20px; box-shadow:var(--shadow-sm);">
        <h3 style="font-size:14px; font-weight:700; color:var(--color-primary); margin-bottom:12px;">
            Registry Information
        </h3>
        <div style="font-size:12px; line-height:1.7; color:var(--color-text-muted);">
            <p style="margin-bottom:12px;">
                This database contains all legislative actions officially enacted by the Municipal Legislative Council and published for public awareness.
            </p>
            <p style="margin-bottom:12px;">
                <strong>Ordinances:</strong> Local laws that are binding on all citizens within municipal jurisdiction.
            </p>
            <p style="margin-bottom:0;">
                <strong>Resolutions:</strong> Formal expressions of policy, opinions, or internal actions of the council.
            </p>
        </div>
    </div>

    <!-- Right: Search Result Items -->
    <div>
        <div style="font-size:13px; color:var(--color-text-muted); margin-bottom:14px; font-weight:500;">
            Showing <?= count($publications) ?> published document(s)
            <?php if (!empty($search) || !empty($type) || !empty($year)): ?>
            matching your filter (<a href="<?= APP_ROOT_URL ?>/portal" style="color:var(--color-accent);">Clear filters</a>)
            <?php endif; ?>
        </div>

        <?php if (empty($publications)): ?>
        <div style="background:#ffffff; border:1px solid var(--color-border-light);
                    border-radius:var(--radius-lg); padding:48px 24px; text-align:center;
                    box-shadow:var(--shadow-sm);">
            <div style="margin-bottom:16px; display:inline-flex; align-items:center; justify-content:center;
                        width:56px; height:56px; border-radius:50%; background-color:#f8f9fa;
                        color:var(--color-text-light); border:1px solid var(--color-border-light);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div style="font-size:15px; font-weight:600; color:var(--color-text); margin-bottom:6px;">
                No Published Records Found
            </div>
            <div style="font-size:12px; color:var(--color-text-muted);">
                Try checking the search spelling, or clearing filter values.
            </div>
        </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:16px;">
                <?php foreach ($publications as $pub): ?>
                <div style="background:#ffffff; border:1px solid var(--color-border-light);
                            border-radius:var(--radius-lg); padding:24px; box-shadow:var(--shadow-sm);
                            transition:transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow)';"
                     onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                    
                    <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:10px;">
                        <div>
                            <span class="badge <?= $pub['document_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                                  style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; padding:3px 8px; margin-right:8px;">
                                <?= $pub['document_type'] ?>
                            </span>
                            <span style="font-weight:700; color:var(--color-primary); font-size:14px; font-family:monospace;">
                                <?= htmlspecialchars($pub['doc_no'] ?? '') ?>
                            </span>
                        </div>
                        <span style="font-size:12px; color:var(--color-text-muted);">
                            Published on <strong><?= date('M d, Y', strtotime($pub['published_at'])) ?></strong>
                        </span>
                    </div>

                    <h2 style="font-size:17px; font-weight:700; color:var(--color-primary); margin-bottom:12px; line-height:1.4;">
                        <a href="<?= APP_ROOT_URL ?>/portal/view/<?= $pub['document_type'] ?>/<?= $pub['document_id'] ?>"
                           style="color:inherit; text-decoration:none;">
                            <?= htmlspecialchars($pub['doc_title'] ?? '') ?>
                        </a>
                    </h2>

                    <?php if (!empty($pub['plain_summary'])): ?>
                    <div style="font-size:13px; line-height:1.6; color:var(--color-text-muted);
                                background:#fcfcfc; border-left:3px solid var(--color-accent);
                                padding:8px 12px; border-radius:0 var(--radius) var(--radius) 0;
                                margin-bottom:16px;">
                        <strong>Public Summary:</strong>
                        <?= htmlspecialchars(
                            strlen($pub['plain_summary']) > 240
                            ? substr($pub['plain_summary'], 0, 240) . '...'
                            : $pub['plain_summary']
                        ) ?>
                    </div>
                    <?php endif; ?>

                    <div style="display:flex; justify-content:space-between; align-items:center;
                                font-size:12px; color:var(--color-text-light);">
                        <div>
                            Author: <strong style="color:var(--color-text-muted);"><?= htmlspecialchars($pub['author_name'] ?? 'Unknown') ?></strong>
                            <?php if ($pub['date_filed']): ?>
                             &bull; Filed: <?= date('M d, Y', strtotime($pub['date_filed'])) ?>
                            <?php endif; ?>
                        </div>
                        <a href="<?= APP_ROOT_URL ?>/portal/view/<?= $pub['document_type'] ?>/<?= $pub['document_id'] ?>"
                           style="font-weight:600; color:var(--color-accent); text-decoration:none;">
                            Read More &rarr;
                        </a>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
