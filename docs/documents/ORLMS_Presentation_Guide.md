# ORLMS Capstone Presentation & Live Demo Study Guide
**Ordinance and Resolution Lifecycle Management System (ORLMS)**
*Client: Sangguniang Panlungsod, City of San Jose del Monte, Bulacan*
*Tech Stack: PHP MVC Framework, PostgreSQL Database (Supabase), Cloud Hosting (HostForge), AI Engine (Groq Cloud API - Llama Models)*

---

## 📌 PANIMULA (Project Overview)

Ito ang inyong gabay sa pag-aaral at pagpapaliwanag ng system para sa inyong mga kaklase, guro, o defense panel. 

### 1. Ano ang ORLMS?
Ang **ORLMS** ay isang intelligent legislative management system na ginawa para i-digitize at pabilisin ang buong siklo ng paggawa ng lokal na batas (ordinansa at resolusyon) sa Sangguniang Panlungsod ng San Jose del Monte, Bulacan.

### 2. Ano ang pinagkaiba nito sa manual na proseso (Value-Add)?
* **Dati (Manual):** Gumagamit lang ng Microsoft Excel at mga physical paper logbook. Walang paraan para malaman kung ang bagong ordinansa ay duplicate o salungat sa mga lumang batas kundi sa pamamagitan ng pagbabasa ng physical cabinets o alaala ng staff.
* **Ngayon (ORLMS):** Digital na ang buong filing, routing, at botohan. Higit sa lahat, may **AI Validation** gamit ang Groq API upang awtomatikong i-check kung kulang ang mga bahagi ng ordinansa at kung may kapareha na itong umiiral na batas sa database. May **Public Portal** pa na may **AI-Generated Plain-Language Summaries** para madaling maintindihan ng mamamayan ang mga batas.

---

## 📊 MASTER PROCESS FLOW (BPA) SCRIPT
*Gamitin ang script na ito habang ipinapakita ang inyong **Master BPA Flowchart**.*

> "Mga bro/panel, ito ang buong daloy ng ating system mula sa pag-file ng draft na ordinance o resolution hanggang sa pagpapatupad nito:"

* **Step 1: Document Filing (Drafting)**
  * **🖥️ Module sa Sidebar:** `Ordinances` / `Resolutions` (sa ilalim ng **DOCUMENTS**)
  * **🗣️ Script para sa Panel:**  
    > *"Dito nag-e-encode ang Legislative Staff ng title, subject, at content ng batas. Pagka-submit, automatic na gagawa ang system ng Tracking Number at ise-save ang draft status sa database natin."*
* **Step 2: AI Validation Engine**
  * **🖥️ Module sa Sidebar:** `AI Validation Reports` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script para sa Panel:**  
    > *"Ipapadala ng system ang draft sa Groq API (Llama AI) para sa dalawang bagay: (1) **Completeness Check** para makita kung may kulang na legal parts, at (2) **Similarity Check** para malaman kung may katulad o duplicate na batas sa database natin."*
* **Desisyon: AI Validation Passed?**
  * **🖥️ Module sa Sidebar:** `AI Validation Reports` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script para sa Panel:**  
    > *"Kapag Passed ang AI validation, tuloy sa susunod na hakbang. Kapag Failed o Flagged (may kapareho), ibabalik sa Staff para baguhin o i-revise."*
* **Step 3: First Reading & Committee Referral**
  * **🖥️ Module sa Sidebar:** `Ordinances` / `Resolutions` List
  * **🗣️ Script para sa Panel:**  
    > *"Dito babasahin ang title ng batas sa First Reading. Pagkatapos, i-a-assign ng SP Secretary ang batas sa tamang Committee (halimbawa: Committee on Laws). Ang status nito ay magiging `under_review`."*
* **Step 4: Committee Review & Deliberation**
  * **🖥️ Module sa Sidebar:** `Review and Endorsement` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script para sa Panel:**  
    > *"Pag-aaralan ng committee members ang batas kasama ang AI validation report. Magpapasya sila kung ito ay: (1) **Endorsed** para ma-approve, (2) **Returned** para ipa-revise, o (3) **Rejected** para i-archive."*
* **Step 5: Second Reading (Debate & Amendments)**
  * **🖥️ Module sa Sidebar:** `Amendments and Revisions` (sa ilalim ng **RECORDS**)
  * **🗣️ Script para sa Panel:**  
    > *"Dito pagdedebatihan ng buong konseho ang batas. Kung may mga pagbabago o amendments, i-e-encode ito ng staff sa system bago ipadala ang batas para sa final voting."*
* **Step 6: Third & Final Reading (Voting)**
  * **🖥️ Module sa Sidebar:** `Approval and Enactment` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script para sa Panel:**  
    > *"Dito na po ang huling botohan sa Plenary Session. Magpapasok ng boto ang bawat SP Member (Yes, No, Abstain), at ang system na mismo ang magbibilang at magtatala ng voting results."*
* **Step 7: Executive Signature / Attestation**
  * **🖥️ Module sa Sidebar:** `Approval and Enactment` (Enact Screen)
  * **🗣️ Script para sa Panel:**  
    > *"Ipadadala ang batas sa Mayor (LCE) para pirmahan. Kung **Signed**, magiging active na batas (enacted). Kung **Vetoed** naman (tinanggihan), babalik ang batas sa Step 3 (Committee Referral) gamit ang red dashed line."*
* **Step 8: AI Summary & Portal Publication**
  * **🖥️ Module sa Sidebar:** `Publications` (sa ilalim ng **RECORDS**)
  * **🗣️ Script para sa Panel:**  
    > *"Pagka-enact, i-po-post ng Staff ang batas sa web portal. Gagamit ulit ng Llama AI para mag-generate ng **Plain-Language Summary** (simpleng buod na walang legal jargon) para madaling maintindihan ng mga residente sa ating Public Portal."*
* **Step 9: Provincial Review (Sangguniang Panlalawigan)**
  * **🖥️ Module sa Sidebar:** `Implementation Monitoring` (sa ilalim ng **RECORDS**)
  * **🗣️ Script para sa Panel:**  
    > *"Manual na re-reviewhin ng Probinsya ng Bulacan ang batas. Kung **Approved**, tuloy sa pag-archive. Kung **Disapproved**, magiging rejected (Step 10). Kung **With Comments** naman, babalik sa Step 5 (Second Reading) gamit ang yellow dashed line para i-amend ng konseho."*
* **Step 10: Enactment & Database Archiving**
  * **🖥️ Module sa Sidebar:** `Archive` (sa ilalim ng **RECORDS**) / `Audit Logs`
  * **🗣️ Script para sa Panel:**  
    > *"Kapag approved na sa lahat ng antas, ang batas ay permanenteng mapupunta sa **Archive** module bilang active law. Kasabay nito, itatala ng system ang lahat ng naging transaction logs sa ating **Audit Logs** module para sa seguridad."*
* **Step 11: Implementation Monitoring**
  * **🖥️ Module sa Sidebar:** `Implementation Monitoring` (sa ilalim ng **RECORDS**)
  * **🗣️ Script para sa Panel:**  
    > *"Dito nag-e-encode ang staff ng progress ng batas (Pending, Ongoing, o Completed) habang ito ay ipinapatupad sa buong lungsod para matiyak ang accountability."*

---

## 💻 LIVE SYSTEM DEMO WALKTHROUGH SCRIPT
*Gawin ang mga sumusunod na pag-click sa screen habang binabasa ang script:*

### Yugto 1: Login at Dashboard
* **Action:** I-open ang web app, mag-log in gamit ang Admin/Staff account, ipakita ang Dashboard.
* **Script:** 
  > *"Mag-log in tayo sa ORLMS. Pagpasok natin, ito ang **Dashboard**. Dito makikita ng administrador at secretariat ang live overview ng mga ordinansa at resolusyon (ilan ang draft, under review, na-enact, o na-reject), pati na rin ang workload ng bawat committee."*

### Yugto 2: Pag-encode ng Batas (Filing Module)
* **Action:** Mag-click sa "Ordinances", i-click ang "Create New Ordinance", lagyan ng sample title (hal. *"Ordinance No. 2026-101: Ecological Solid Waste Management of CSJDM"*), mag-type ng kaunting katawan ng batas, i-click ang **Submit**.
* **Script:** 
  > *"Pumunta tayo sa **Ordinances Module** at mag-click ng **Create New**. I-encode natin ang pamagat at nilalaman. Pag-click natin ng **Submit**, gagawan ito ng system ng unique Tracking Number at itatabi sa **Supabase PostgreSQL database**."*

### Yugto 3: AI Validation (Ang Special AI Feature)
* **Action:** Sa listahan ng ordinances, hanapin ang kakahain lang na batas at i-click ang **"AI Validate"** button. Maghintay habang naglo-load ang JSON response mula sa Groq API.
* **Script:** 
  > *"I-click natin ang **AI Validate**. Sa likod ng system, kinakausap ng PHP natin ang **Groq Cloud API** para iproseso ng **Llama model**. 
  > 
  > Ayon sa resulta: nakakuha ito ng **95% Completeness Score** dahil nahanap ng AI ang Title, Enacting Clause, at Effectivity Date. Nagsagawa rin ang AI ng **Semantic Similarity Check** at nakita niyang wala itong katulad o katunggali sa ating mga lumang batas, kaya ang status nito ay **Passed**."*

### Yugto 4: Committee Assignment at Review
* **Action:** Pumunta sa **Committee Assignment**, i-assign ang bagong ordinansa sa *Committee on Laws*. Ipakita ang **Review Module** kung saan ang committee chair ay pwedeng magsulat ng deliberation notes at pumili ng recommendation.
* **Script:** 
  > *"Ngayon, i-a-assign natin ito sa **Committee on Laws**. Ang mga miyembro ng komite ay may access para basahin ang draft at ang AI validation report. Dito sa **Review Module**, pwede silang mag-encode ng kanilang deliberation notes at i-endorse ang draft para sa ikalawa at ikatlong pagbasa."*

### Yugto 5: Plenary Votation at Mayor's Signature
* **Action:** Pumunta sa **Approval/Voting Module**, ipakita ang simulation ng botohan (Yes/No counts), at ang form kung saan pipirmahan ng Mayor ang batas.
* **Script:** 
  > *"Sa **Voting Module**, maitatala natin ang boto ng mga SP Members habang ginaganap ang Plenary Session. Kapag nakuha ang mayorya, ipapadala ito sa Mayor. Dito sa **LCE Panel**, pwedeng i-update ng Mayor ang status bilang **Signed**."*

### Yugto 6: Public Portal at AI Summaries
* **Action:** Pumunta sa **Publications Module**, i-click ang **Publish** sa aprubadong batas. Tapos i-open ang **Portal Controller/View (Citizen Portal)**. Ipakita ang search bar at ang in-enact na batas na may kasamang **AI Plain-Language Summary**.
* **Script:** 
  > *"Kapag na-enact na ang batas, ipa-publish natin ito. Awtomatikong mag-ge-generate ang AI ng **Plain-Language Summary**. 
  > 
  > Dito naman sa **Public Portal**, ang mga mamamayan ng San Jose del Monte Bulacan ay pwedeng mag-search ng mga active laws nang hindi na pumupunta sa city hall. Mababasa nila ang simpleng buod ng batas na ginawa ng AI para hindi sila maguluhan sa mahihirap na terminong legal."*

---

## ❓ MGA POSIBLENG ITANONG SA DEFENSE (Q&A Cheat Sheet)

1. **Q: Bakit PostgreSQL ang pinili niyo at hindi MySQL/MariaDB na karaniwang gamit sa XAMPP?**
   * **A:** *"Pinili namin ang PostgreSQL dahil ito ay mas matatag para sa mga kumplikadong relasyon ng datos, malalaking dokumento, at mabilis na index searches na kailangan ng ating system. Bukod doon, madali itong i-host sa cloud platforms gaya ng Supabase para sa DBaaS (Database-as-a-Service)."*
2. **Q: Saan niyo iho-host ang system niyo kapag natapos na?**
   * **A:** *"Iho-host po namin ang web application sa **HostForge** (isang Cloud Web Hosting provider) dahil sinusuportahan nito ang PHP runtime na kailangan ng ating system, habang ang database naman ay ligtas na naka-deploy sa cloud ng **Supabase** upang mapaghiwalay ang data tier at application tier."*
3. **Q: Paano kinakausap ng PHP system niyo ang AI (Llama)?**
   * **A:** *"Ginamit po namin ang **Groq Cloud API** gamit ang secure HTTPS request. Nagpapadala ang PHP system natin ng structured prompt na naglalaman ng draft text, at ibinabalik naman ng Groq API ang resulta bilang isang structured JSON object na binabasa at sine-save ng ating controllers sa database."*
4. **Q: Pwede bang pakialaman o baguhin ang mga logs sa audit trail?**
   * **A:** *"Hindi po, dahil ang bawat insert sa `audit_logs` table ay automatic na ginagawa ng system sa tuwing may nagaganap na transaction sa system (CRUD, Login), at ang access sa audit log viewer ay limitado lamang sa Super Administrator."*

---

### 💡 Tip para sa iyo:
Buksan mo itong `.md` file sa iyong computer, i-copy at i-paste sa Google Docs o Microsoft Word, at i-save bilang **PDF** para madali mong mabasa sa iyong cellphone o ma-print bago ang inyong klase o presentation!
