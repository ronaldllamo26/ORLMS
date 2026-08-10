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

<!-- Floating AI Chatbot Widget -->
<div class="chatbot-wrapper no-print" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; font-family: 'Inter', sans-serif;">
    <!-- Chat Toggle Button -->
    <button id="chatbot-toggle-btn" style="width: 60px; height: 60px; border-radius: 50%; background-color: #0c2340; border: 3px solid #f2a900; color: #f2a900; font-size: 26px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 25px rgba(12, 35, 64, 0.35); transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1); outline: none;">
        <i class="bi bi-chat-dots-fill"></i>
    </button>

    <!-- Chat Box -->
    <div id="chatbot-box" style="display: none; width: 370px; height: 490px; max-height: 80vh; background: #ffffff; border: 1px solid #dee2e6; border-top: 4px solid #0c2340; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.22); position: absolute; bottom: 80px; right: 0; flex-direction: column; overflow: hidden; animation: slideUp 0.25s ease;">
        <!-- Header -->
        <div style="background-color: #0c2340; color: #ffffff; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #f2a900;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f2a900; color: #0c2340; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800;">
                    T
                </div>
                <div style="text-align: left;">
                    <div style="font-size: 13.5px; font-weight: 700; line-height: 1;">Tanya SP</div>
                    <span style="font-size: 10px; color: #f2a900; font-weight: 600;">LGU AI Assistant</span>
                </div>
            </div>
            <button id="chatbot-close-btn" style="background: none; border: none; color: #ffffff; font-size: 20px; cursor: pointer; padding: 0; line-height: 1;">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; background-color: #f8f9fa;">
            <!-- Welcome Message -->
            <div style="display: flex; flex-direction: column; align-items: flex-start; max-width: 85%; align-self: flex-start; text-align: left;">
                <span style="font-size: 9.5px; font-weight: 700; color: #0c2340; margin-bottom: 2px; text-transform: uppercase;">Tanya SP</span>
                <div style="background-color: #ffffff; border: 1px solid #dee2e6; color: #212529; border-radius: 4px 12px 12px 12px; padding: 10px 14px; font-size: 12.5px; line-height: 1.6; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    Magandang araw po! Ako si <strong>Tanya SP</strong>, ang AI Legislative Assistant ng San Jose del Monte. Mayroon po ba kayong katanungan tungkol sa mga opisyal na ordinansa o resolusyon natin?
                </div>
            </div>
        </div>

        <!-- Input Form Footer -->
        <form id="chatbot-form" style="border-top: 1px solid #dee2e6; padding: 12px; display: flex; gap: 8px; background-color: #ffffff; margin: 0;">
            <input type="text" id="chatbot-input" placeholder="Magtanong po dito..." style="flex: 1; border: 1px solid #ced4da; border-radius: 20px; padding: 8px 16px; font-size: 13px; outline: none; transition: border-color 0.2s;" autocomplete="off" required>
            <button type="submit" style="width: 36px; height: 36px; border-radius: 50%; background-color: #0c2340; border: none; color: #f2a900; font-size: 16px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.2s;">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>
</div>

<style>
    @keyframes slideUp {
        from { transform: translateY(15px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<!-- Chatbot Javascript Logic -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.getElementById("chatbot-toggle-btn");
    const closeBtn = document.getElementById("chatbot-close-btn");
    const chatBox = document.getElementById("chatbot-box");
    const chatForm = document.getElementById("chatbot-form");
    const chatInput = document.getElementById("chatbot-input");
    const messagesContainer = document.getElementById("chatbot-messages");

    // Toggle open/close chat widget
    toggleBtn.addEventListener("click", function() {
        if (chatBox.style.display === "none" || chatBox.style.display === "") {
            chatBox.style.display = "flex";
            chatInput.focus();
            toggleBtn.style.transform = "scale(0.9)";
        } else {
            chatBox.style.display = "none";
            toggleBtn.style.transform = "scale(1)";
        }
    });

    closeBtn.addEventListener("click", function() {
        chatBox.style.display = "none";
        toggleBtn.style.transform = "scale(1)";
    });

    // Helper to append message bubble to box
    function appendMessage(sender, text, isAi = false) {
        const wrapper = document.createElement("div");
        wrapper.style.display = "flex";
        wrapper.style.flexDirection = "column";
        wrapper.style.alignItems = isAi ? "flex-start" : "flex-end";
        wrapper.style.maxWidth = "85%";
        if (isAi) {
            wrapper.style.alignSelf = "flex-start";
            wrapper.style.textAlign = "left";
        } else {
            wrapper.style.alignSelf = "flex-end";
            wrapper.style.textAlign = "right";
        }

        const label = document.createElement("span");
        label.style.fontSize = "9.5px";
        label.style.fontWeight = "700";
        label.style.color = isAi ? "#0c2340" : "#6c757d";
        label.style.marginBottom = "2px";
        label.style.textTransform = "uppercase";
        label.textContent = sender;

        const bubble = document.createElement("div");
        bubble.style.fontSize = "12.5px";
        bubble.style.lineHeight = "1.6";
        bubble.style.padding = "10px 14px";
        bubble.style.boxShadow = "0 2px 4px rgba(0,0,0,0.02)";
        
        if (isAi) {
            bubble.style.backgroundColor = "#ffffff";
            bubble.style.border = "1px solid #dee2e6";
            bubble.style.color = "#212529";
            bubble.style.borderRadius = "4px 12px 12px 12px";
            bubble.style.whiteSpace = "pre-line"; // Preserves line breaks for cleaner display
        } else {
            bubble.style.backgroundColor = "#0c2340";
            bubble.style.color = "#ffffff";
            bubble.style.borderRadius = "12px 12px 4px 12px";
            bubble.style.border = "none";
        }

        bubble.textContent = text;

        wrapper.appendChild(label);
        wrapper.appendChild(bubble);
        messagesContainer.appendChild(wrapper);

        // Auto scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Append a typing loader indicator
    let typingIndicator = null;
    function showTypingIndicator() {
        typingIndicator = document.createElement("div");
        typingIndicator.style.display = "flex";
        typingIndicator.style.flexDirection = "column";
        typingIndicator.style.alignItems = "flex-start";
        typingIndicator.style.maxWidth = "85%";
        typingIndicator.style.alignSelf = "flex-start";
        typingIndicator.style.textAlign = "left";

        const label = document.createElement("span");
        label.style.fontSize = "9.5px";
        label.style.fontWeight = "700";
        label.style.color = "#0c2340";
        label.style.marginBottom = "2px";
        label.style.textTransform = "uppercase";
        label.textContent = "Tanya SP";

        const bubble = document.createElement("div");
        bubble.style.backgroundColor = "#ffffff";
        bubble.style.border = "1px solid #dee2e6";
        bubble.style.color = "#6c757d";
        bubble.style.borderRadius = "4px 12px 12px 12px";
        bubble.style.padding = "10px 14px";
        bubble.style.fontSize = "12.5px";
        bubble.style.fontStyle = "italic";
        bubble.textContent = "Sumusulat...";

        typingIndicator.appendChild(label);
        typingIndicator.appendChild(bubble);
        messagesContainer.appendChild(typingIndicator);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function removeTypingIndicator() {
        if (typingIndicator) {
            typingIndicator.remove();
            typingIndicator = null;
        }
    }

    // Handle form submit (Sending message)
    chatForm.addEventListener("submit", function(e) {
        e.preventDefault();
        
        const messageText = chatInput.value.trim();
        if (!messageText) return;

        // 1. Add user message bubble
        appendMessage("Mamamayan", messageText, false);
        chatInput.value = "";

        // 2. Show typing loading
        showTypingIndicator();

        // 3. Make AJAX request to /portal/chat
        const formData = new FormData();
        formData.append("message", messageText);

        fetch("<?= APP_URL ?>/portal/chat", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            removeTypingIndicator();
            if (data.success) {
                appendMessage("Tanya SP", data.reply, true);
            } else {
                appendMessage("Tanya SP", data.reply || "Paumanhin po, nagkaroon ng error. Subukan muli.", true);
            }
        })
        .catch(err => {
            removeTypingIndicator();
            appendMessage("Tanya SP", "Paumanhin po, hindi makakonekta sa server. Pakisiguro na active ang connection.", true);
            console.error("Chatbot Error: ", err);
        });
    });
});
</script>
