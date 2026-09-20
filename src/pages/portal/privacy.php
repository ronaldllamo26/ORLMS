<?php
/**
 * ORLMS - Data Privacy Policy & Compliance Manual
 * Republic Act No. 10173 (Data Privacy Act of 2012)
 * National Privacy Commission (NPC) Compliance
 */
?>
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <!-- Breadcrumb -->
    <nav class="flex text-xs font-semibold text-slate-500 space-x-2">
        <a href="<?= APP_URL ?>/portal" class="hover:text-primary transition">Public Portal</a>
        <span>/</span>
        <span class="text-slate-800">Data Privacy Policy</span>
    </nav>

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-primary to-primary-light text-white rounded-2xl p-8 shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 translate-x-8 -translate-y-8 w-64 h-64 bg-accent/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 px-3 py-1 rounded-full text-xs font-semibold text-accent-light mb-3">
                    <i class="bi bi-shield-lock-fill"></i> Republic Act No. 10173 Compliance
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Data Privacy & AI Governance Policy
                </h1>
                <p class="text-sm text-white/80 mt-2 max-w-2xl leading-relaxed">
                    Opisyal na Patakaran sa Proteksyon ng Datos ng Sangguniang Panlungsod, Lungsod ng San Jose del Monte, Bulacan.
                </p>
            </div>
            <button onclick="document.getElementById('erasureModal').classList.remove('hidden')" 
                    class="bg-accent hover:bg-accent-dark text-primary font-bold px-4 py-2.5 rounded-xl shadow transition text-xs flex items-center gap-2 shrink-0">
                <i class="bi bi-trash3-fill"></i> Request Data Erasure
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php 
    $flashSuccess = \Controller::getFlash('success');
    $flashError   = \Controller::getFlash('error');
    if ($flashSuccess): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl flex items-start gap-3 text-sm shadow-sm">
        <i class="bi bi-check-circle-fill text-emerald-600 text-lg mt-0.5"></i>
        <div>
            <h4 class="font-bold text-emerald-900">Kahilingan Naitala!</h4>
            <p><?= htmlspecialchars($flashSuccess) ?></p>
        </div>
    </div>
    <?php elseif ($flashError): ?>
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl flex items-start gap-3 text-sm shadow-sm">
        <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg mt-0.5"></i>
        <div>
            <h4 class="font-bold text-rose-900">Hindi Naitala</h4>
            <p><?= htmlspecialchars($flashError) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Compliance Pillars Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-lg mb-3">
                <i class="bi bi-building-lock"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Controller</h3>
            <p class="text-sm font-extrabold text-slate-800 mt-1">CSJDM Sangguniang Panlungsod</p>
            <p class="text-[11px] text-slate-500 mt-1">Personal Information Controller (PIC)</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center text-lg mb-3">
                <i class="bi bi-key-fill"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Encryption</h3>
            <p class="text-sm font-extrabold text-slate-800 mt-1">AES-256 + TLS 1.3</p>
            <p class="text-[11px] text-slate-500 mt-1">Authenticated At-Rest & In-Transit</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-lg mb-3">
                <i class="bi bi-person-badge"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">DPO Office</h3>
            <p class="text-sm font-extrabold text-slate-800 mt-1">dpo@csjdm.gov.ph</p>
            <p class="text-[11px] text-slate-500 mt-1">Designated Compliance Officer</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg mb-3">
                <i class="bi bi-file-earmark-check"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Subject Rights</h3>
            <p class="text-sm font-extrabold text-slate-800 mt-1">Sec. 16, RA 10173</p>
            <p class="text-[11px] text-slate-500 mt-1">Access, Rectify & Right to Delete</p>
        </div>
    </div>

    <!-- Formal Policy Document Articles -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100 text-slate-700">
        <!-- Article 1 -->
        <div class="p-6 sm:p-8 space-y-3">
            <div class="flex items-center gap-2 text-primary font-extrabold text-sm uppercase tracking-wide">
                <i class="bi bi-file-text"></i> Artikulo I: Pahayag ng Patakaran (Statement of Policy)
            </div>
            <p class="text-sm leading-relaxed text-slate-600">
                Ang Pamahalaang Lungsod ng San Jose del Monte, sa pamamagitan ng Sangguniang Panlungsod (ORLMS), ay buong-pusong nakatuon sa paggalang at pagpapanatili ng karapatan sa pagkapribado ng bawat mamamayan alinsunod sa <strong>Republic Act No. 10173 (Data Privacy Act of 2012)</strong>, ang Implementing Rules and Regulations (IRR) nito, at mga alituntunin ng <strong>National Privacy Commission (NPC)</strong>.
            </p>
            <p class="text-sm leading-relaxed text-slate-600">
                Ang sistemang ORLMS ay ginawa upang maging bukas ang mga ipinasang Ordinansa at Resolusyon habang mahigpit na pinangangalagaan ang anumang personal at sensitibong impormasyon (Personal Identifiable Information o PII).
            </p>
        </div>

        <!-- Article 2 -->
        <div class="p-6 sm:p-8 space-y-3">
            <div class="flex items-center gap-2 text-primary font-extrabold text-sm uppercase tracking-wide">
                <i class="bi bi-database-check"></i> Artikulo II: Impormasyong Kinokolekta (Data We Collect)
            </div>
            <p class="text-sm leading-relaxed text-slate-600">
                Sa paggamit ng publiko sa ORLMS, ang mga sumusunod lamang na datos ang maaaring makolekta batay sa kusa at may-pabatid na pahintulot (freely given consent):
            </p>
            <ul class="list-disc list-inside text-sm space-y-1 text-slate-600 ml-2">
                <li><strong>Public Consultations & Inquiries:</strong> Buong pangalan, email address, contact number, at opisyal na mensahe/komento sa mga nakabimbing panukalang batas.</li>
                <li><strong>Technical Logs:</strong> IP address, browser type, at timestamps para lamang sa layuning panseguridad (Audit Trail laban sa cyber-attacks at brute-force attempts).</li>
                <li><strong>Citizen AI Assistant:</strong> Mga tanong ukol sa ordinansa upang makapagbigay ng agarang legal information guidance (walang PII na itinatala ang model).</li>
            </ul>
        </div>

        <!-- Article 3 -->
        <div class="p-6 sm:p-8 space-y-3">
            <div class="flex items-center gap-2 text-primary font-extrabold text-sm uppercase tracking-wide">
                <i class="bi bi-shield-check"></i> Artikulo III: Seguridad at Encryption (Security Measures)
            </div>
            <p class="text-sm leading-relaxed text-slate-600">
                Ipinatutupad ng ORLMS ang pinakamataas na pamantayan sa Cybersecurity:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5 mb-1">
                        <i class="bi bi-lock-fill text-teal-600"></i> AES-256-CBC with HMAC-SHA256
                    </span>
                    Naka-encrypt ang sensitibong data sa database gamit ang militar-grade authenticated encryption.
                </div>
                <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5 mb-1">
                        <i class="bi bi-globe2 text-blue-600"></i> TLS 1.3 HTTPS In-Transit
                    </span>
                    Lahat ng koneksyon mula sa browser patungo sa server ay protektado ng end-to-end Transport Layer Security.
                </div>
                <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5 mb-1">
                        <i class="bi bi-fingerprint text-indigo-600"></i> Multi-Factor Authentication (MFA)
                    </span>
                    Mahigpit na 2-minute dynamic cryptographic OTP sa pamamagitan ng TLS SMTP para sa lahat ng legislative staff.
                </div>
                <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5 mb-1">
                        <i class="bi bi-journal-text text-amber-600"></i> Immutable Audit Trail
                    </span>
                    Bawat aktibidad, pag-login, at transaksyon ay may kumpletong forensic audit log alinsunod sa COA at NPC rules.
                </div>
            </div>
        </div>

        <!-- Article 4 -->
        <div class="p-6 sm:p-8 space-y-3">
            <div class="flex items-center gap-2 text-primary font-extrabold text-sm uppercase tracking-wide">
                <i class="bi bi-person-check-fill"></i> Artikulo IV: Karapatan ng May-ari ng Datos (Rights of Data Subjects)
            </div>
            <p class="text-sm leading-relaxed text-slate-600">
                Alinsunod sa <strong>Seksyon 16 ng RA 10173</strong>, kinikilala at ipinagkakaloob ng Lungsod ang sumusunod na karapatan:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <strong class="text-slate-800 block mb-1">1. Right to Access & Rectify</strong>
                    Karapatang mabatid at iwasto ang anumang maling impormasyong nakatala.
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <strong class="text-slate-800 block mb-1">2. Right to Object</strong>
                    Karapatang tumanggi sa pagproseso ng personal na impormasyon.
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <strong class="text-slate-800 block mb-1">3. Right to Erasure / Delete</strong>
                    Karapatang hilingin ang pagbura o pag-block ng datos ("Right to be Forgotten").
                </div>
            </div>
        </div>

        <!-- Article 5 -->
        <div class="p-6 sm:p-8 space-y-3">
            <div class="flex items-center gap-2 text-primary font-extrabold text-sm uppercase tracking-wide">
                <i class="bi bi-cpu-fill"></i> Artikulo V: AI Governance & Prompt Defense
            </div>
            <p class="text-sm leading-relaxed text-slate-600">
                Ang AI engine ng ORLMS (pinatatakbo sa pamamagitan ng secure Groq Cloud) ay sumusunod sa <strong>Ethical AI Framework</strong>:
            </p>
            <ul class="list-disc list-inside text-sm space-y-1 text-slate-600 ml-2">
                <li>Hindi ginagamit ang mga tanong o feedback ng mamamayan para magsanay (train) ng public AI models.</li>
                <li>May aktibong boundary fencing laban sa Prompt Injection, Jailbreaking, at Data Exfiltration attacks.</li>
                <li>Ang mga desisyon sa pagpapasa ng ordinansa ay nananatiling 100% nasa kamay ng mga inihalal na Konsehal at Opisyal ng Lungsod (Human-in-the-Loop oversight).</li>
            </ul>
        </div>
    </div>

    <!-- Data Erasure Request Modal -->
    <div id="erasureModal" class="hidden fixed inset-0 z-[2000] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-200">
            <button onclick="document.getElementById('erasureModal').classList.add('hidden')" 
                    class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 text-lg">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Kahilingan sa Pagbura ng Datos</h3>
                    <p class="text-xs text-slate-500">Right to Erasure / Blocking (Sec. 16, RA 10173)</p>
                </div>
            </div>

            <form action="<?= APP_URL ?>/portal/requestDataDeletion" method="POST" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Buong Pangalan <span class="text-rose-500">*</span></label>
                    <input type="text" name="requester_name" required placeholder="Hal. Juan Dela Cruz"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="requester_email" required placeholder="juan@gmail.com"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Contact Number (Opsyonal)</label>
                        <input type="text" name="requester_phone" placeholder="0917-000-0000"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Dahilan / Impormasyong Nais Pabura <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Tukuyin ang komento o feedback na nais mong ipatanggal sa opisyal na talaan..."
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none resize-none"></textarea>
                </div>

                <!-- Consent Checkbox -->
                <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="consent" value="1" required class="mt-0.5 rounded text-primary focus:ring-primary">
                        <span class="text-[11px] text-slate-600 leading-snug">
                            Pinatutunayan ko na ako ang may-ari ng naturang datos at humihiling ako ng opisyal na pagbura alinsunod sa Data Privacy Act of 2012. Nauunawaan ko na ang kahilingang ito ay sasailalim sa pagsusuri ng Sangguniang Panlungsod Records Officer.
                        </span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('erasureModal').classList.add('hidden')"
                            class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition">
                        Kanselahin
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold shadow transition flex items-center gap-1.5">
                        <i class="bi bi-send-fill"></i> Isumite ang Kahilingan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
