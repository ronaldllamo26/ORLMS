# ORLMS - Future Plans and Recommendations for Capstone Defense

Ito ang mga inirekomendang karagdagang features na maaari nating ipatupad sa susunod na yugto ng pagde-develop para mas mapahanga ang inyong thesis panel sa inyong final defense.

---

## 1. AI Citizen Concierge (Chatbot) 🤖
* **Deskripsyon:** Isang chat panel sa Public Portal kung saan pwedeng mag-query ang mamamayan gamit ang plain language (Tagalog/Taglish/English) para magtanong tungkol sa mga ordinansa at resolusyon.
* **Paano Ito Gagana:** Gagamitin ang naka-configure na Groq API key sa system para mag-interpret ng mga tanong.
* **Mga Halimbawang Tanong:**
  * *"May curfew ba para sa mga kabataan sa San Jose del Monte?"*
  * *"Saan pwedeng magsumite ng reklamo ukol sa basura?"*
  * *"Ano ang parusa sa mga ilegal na parking sa gilid ng daan?"*

## 2. System Database Backup & Export Utility 💾
* **Deskripsyon:** Isang utility interface sa Administration panel kung saan ang Super Admin ay maaaring:
  * Mag-back up at mag-download ng kabuuang database bilang `.sql` file sa isang click.
  * Mag-export ng kabuuang listahan ng active ordinances at resolutions sa Excel (CSV) format.
* **Bakit Ito Mahalaga:** Madalas itong itanong ng panels ukol sa disaster recovery at data portability.

## 3. In-App Notifications Center 🔔
* **Deskripsyon:** Isang bell icon sa tuktok na navigation bar (navbar) na nagpapakita ng alerts batay sa tungkulin (role) ng naka-login na user.
* **Mga Sample Notification:**
  * **Committee Member:** *"Bagong Ordinance [ORD-2026-001] ay nai-assign sa iyong komite para sa review."*
  * **Legislative Staff:** *"Aprubado na ng Mayor ang Resolution [RES-2026-004], maaari na itong i-publish."*
  * **SP Member:** *"May nakabinbing dokumento na naghihintay ng iyong boto o approval."*

## 4. AI-Generated Plain Language Summary (TL;DR) 📝
* **Deskripsyon:** Sa detalye ng bawat batas sa Public Registry, magpapakita ang system ng maikli at simpleng buod (2 hanggang 3 pangungusap) na binuo ng AI para mas madaling maintindihan ng karaniwang mamamayan ang mahahabang legal na dokumento.

## 5. Category-Specific Attachment Rules & DILG Compliance Checklists 📑
* **Deskripsyon:** Pagdaragdag ng dynamic attachment checklists batay sa kategorya ng Ordinansa (hal. Appropriation/Budget, Revenue/Tax, Health, Zoning).
* **Paano Ito Gagana:** Pagkatapos pumili ng kategorya sa drafting phase, awtomatikong hihingi ang system ng mga tiyak na dokumento batay sa DILG Memorandum Circulars (hal. *Certificate of Funds Availability* para sa Budget, o *Minutes of Public Consultation* para sa Tax ordinances).
* **Bakit Ito Mahalaga:** Mas lalong mapagtutugma ang AI Universal Text Check sa mga tiyak na kategorya ng mga lokal na patakaran sa LGU.

---


## 📅 Status ng System
* [x] **MFA / OTP Email Configuration** - Inilipat ang receiver sa `orlms2026@gmail.com` para sa inyong group testing.
* [x] **Dynamic APP_URL Detection** - Awtomatikong nakikilala kung localhost o ngrok link ang gamit para hindi masira ang design at links ng inyong mga kaklase.
* [x] **Clean Repository** - Tinanggal ang mga temporary at test files para malinis ang git push.
