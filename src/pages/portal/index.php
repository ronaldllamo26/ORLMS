<?php
/**
 * ORLMS - Public Portal Index View (Tailwind CSS)
 *
 * Variables:
 *   $publications   — search results of published documents
 *   $search, $type, $year — current search filter inputs
 *   $availableYears — array of years for the filter dropdown
 */

// Initialize variables to prevent undefined variable warnings
$publications   = $publications ?? [];
$search         = $search ?? '';
$type           = $type ?? '';
$year           = $year ?? '';
$availableYears = $availableYears ?? [];
?>


<!-- Branding Header Section -->
<div class="bg-gradient-to-br from-primary to-[#20456e] text-white rounded-xl p-9 mb-7 text-center shadow-md">
    <h1 class="text-2xl md:text-3xl font-bold mb-2 tracking-wide">
        Municipal Ordinance & Resolution Portal
    </h1>
    <p class="text-sm text-white/85 max-w-[650px] mx-auto mb-6 leading-relaxed">
        Search and access official copies of active laws, regulations, and resolutions enacted by the Municipal Legislative Council.
    </p>

    <!-- Unified Search Bar -->
    <form method="GET" action="<?= APP_ROOT_URL ?>/portal"
          class="bg-white rounded-xl p-2.5 grid grid-cols-1 md:grid-cols-[1fr_auto_auto_auto] gap-2.5 max-w-[850px] mx-auto shadow-lg">
        
        <div class="flex items-center pl-2.5 bg-slate-50/50 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="search" class="w-full bg-transparent border-none py-2 outline-none text-sm text-slate-800 focus:ring-0 placeholder-slate-400"
                   value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search by title, subject, or keywords...">
        </div>

        <select name="type" class="bg-transparent border-0 border-t md:border-t-0 md:border-l border-slate-200 py-2.5 px-2.5 text-xs font-semibold text-slate-500 focus:outline-none focus:ring-0 md:w-[140px] cursor-pointer">
            <option value="">All Types</option>
            <option value="ordinance" <?= $type === 'ordinance' ? 'selected' : '' ?>>Ordinances</option>
            <option value="resolution" <?= $type === 'resolution' ? 'selected' : '' ?>>Resolutions</option>
        </select>

        <select name="year" class="bg-transparent border-0 border-t md:border-t-0 md:border-l border-slate-200 py-2.5 px-2.5 text-xs font-semibold text-slate-500 focus:outline-none focus:ring-0 md:w-[120px] cursor-pointer">
            <option value="">All Years</option>
            <?php foreach ($availableYears as $y): ?>
            <option value="<?= htmlspecialchars($y) ?>" <?= $year === $y ? 'selected' : '' ?>>
                <?= htmlspecialchars($y) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-accent hover:bg-accent-dark text-primary font-bold text-xs rounded transition duration-150 shadow-sm focus:outline-none">
            Search
        </button>

    </form>
</div>

<!-- Search Results -->
<div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

    <!-- Left: Left-column guide -->
    <div class="bg-white border border-slate-200/60 rounded-xl p-5 shadow-sm">
        <h3 class="text-sm font-bold text-primary mb-3">
            Registry Information
        </h3>
        <div class="text-xs leading-relaxed text-slate-500 flex flex-col gap-3">
            <p>
                This database contains all legislative actions officially enacted by the Municipal Legislative Council and published for public awareness.
            </p>
            <p>
                <strong class="text-slate-700">Ordinances:</strong> Local laws that are binding on all citizens within municipal jurisdiction.
            </p>
            <p>
                <strong class="text-slate-700">Resolutions:</strong> Formal expressions of policy, opinions, or internal actions of the council.
            </p>
        </div>
    </div>

    <!-- Right: Search Result Items -->
    <div>
        <div class="text-[13px] text-slate-500 mb-3.5 font-semibold">
            Showing <?= count($publications) ?> published document(s)
            <?php if (!empty($search) || !empty($type) || !empty($year)): ?>
            matching your filter (<a href="<?= APP_ROOT_URL ?>/portal" class="text-accent hover:text-accent-dark transition">Clear filters</a>)
            <?php endif; ?>
        </div>

        <?php if (empty($publications)): ?>
        <div class="bg-white border border-slate-200/60 rounded-xl py-12 px-6 text-center shadow-sm">
            <div class="mb-4 inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-50 text-slate-400 border border-slate-100 shadow-sm">
                <svg xmlns="http://www.w3.org/2050/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="text-base font-bold text-slate-800 mb-1.5">
                No Published Records Found
            </div>
            <div class="text-xs text-slate-500">
                Try checking the search spelling, or clearing filter values.
            </div>
        </div>
        <?php else: ?>
            <div class="flex flex-col gap-4">
                <?php foreach ($publications as $pub): ?>
                <div class="bg-white border border-slate-200/60 rounded-xl p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-left">
                    
                    <div class="flex justify-between items-start mb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] font-bold text-white px-2 py-0.5 rounded tracking-wide uppercase <?= $pub['document_type'] === 'ordinance' ? 'bg-teal-650 bg-emerald-600' : 'bg-indigo-600' ?>">
                                <?= $pub['document_type'] ?>
                            </span>
                            <span class="font-bold text-primary text-sm font-mono tracking-tight">
                                <?= htmlspecialchars($pub['doc_no'] ?? '') ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400">
                            Published on <strong class="text-slate-650"><?= date('M d, Y', strtotime($pub['published_at'])) ?></strong>
                        </span>
                    </div>

                    <h2 class="text-base md:text-lg font-bold text-primary mb-3 leading-snug hover:text-accent transition">
                        <a href="<?= APP_ROOT_URL ?>/portal/view/<?= $pub['document_type'] ?>/<?= $pub['document_id'] ?>">
                            <?= htmlspecialchars($pub['doc_title'] ?? '') ?>
                        </a>
                    </h2>

                    <?php if (!empty($pub['plain_summary'])): ?>
                    <div class="text-[13px] leading-relaxed text-slate-500 bg-slate-50/50 border-l-4 border-accent p-3 rounded-r-lg mb-4 text-left">
                        <strong class="text-slate-700">Public Summary:</strong>
                        <?= htmlspecialchars(
                            strlen($pub['plain_summary']) > 240
                            ? substr($pub['plain_summary'], 0, 240) . '...'
                            : $pub['plain_summary']
                        ) ?>
                    </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center text-xs text-slate-450 mt-2">
                        <div>
                            Author: <strong class="text-slate-600"><?= htmlspecialchars($pub['author_name'] ?? 'Unknown') ?></strong>
                            <?php if ($pub['date_filed']): ?>
                             &bull; Filed: <?= date('M d, Y', strtotime($pub['date_filed'])) ?>
                            <?php endif; ?>
                        </div>
                        <a href="<?= APP_ROOT_URL ?>/portal/view/<?= $pub['document_type'] ?>/<?= $pub['document_id'] ?>"
                           class="font-bold text-accent hover:text-accent-dark transition">
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
                    O
                </div>
                <div style="text-align: left;">
                    <div style="font-size: 13.5px; font-weight: 700; line-height: 1;">ORLMS AI</div>
                    <span style="font-size: 10px; color: #f2a900; font-weight: 600;">CSJDM Legislative AI Assistant</span>
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
                <span style="font-size: 9.5px; font-weight: 700; color: #0c2340; margin-bottom: 2px; text-transform: uppercase;">ORLMS AI</span>
                <div style="background-color: #ffffff; border: 1px solid #dee2e6; color: #212529; border-radius: 4px 12px 12px 12px; padding: 10px 14px; font-size: 12.5px; line-height: 1.6; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    Magandang araw po! Ako si <strong>ORLMS AI</strong>, ang opisyal na Legislative AI Assistant ng San Jose del Monte. Mayroon po ba kayong katanungan tungkol sa ating mga ordinansa, resolusyon, o lokal na regulasyon?
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

    // Markdown parser for AI responses
    function parseMarkdown(str) {
        if (!str) return "";
        let safe = str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Bold **text**
        safe = safe.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Bullets * text
        safe = safe.replace(/^\*\s+(.*)$/gim, '<div style="margin-left:8px; margin-bottom:2px;">• $1</div>');
        // Newlines
        safe = safe.replace(/\n/g, '<br>');

        return safe;
    }

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
            bubble.innerHTML = parseMarkdown(text);
        } else {
            bubble.style.backgroundColor = "#0c2340";
            bubble.style.color = "#ffffff";
            bubble.style.borderRadius = "12px 12px 4px 12px";
            bubble.style.border = "none";
            bubble.textContent = text;
        }

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
        label.textContent = "ORLMS AI";

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
                appendMessage("ORLMS AI", data.reply, true);
            } else {
                appendMessage("ORLMS AI", data.reply || "Paumanhin po, nagkaroon ng error. Subukan muli.", true);
            }
        })
        .catch(err => {
            removeTypingIndicator();
            appendMessage("ORLMS AI", "Paumanhin po, hindi makakonekta sa server. Pakisiguro na active ang connection.", true);
            console.error("Chatbot Error: ", err);
        });
    });
});
</script>
