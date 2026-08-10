# ORLMS BPA Presentation Guide
*Direct at detalyadong gabay sa pagpapaliwanag ng BPA flowchart sa inyong defense.*

---

## 🏛️ Ang 3 Main Phases ng Legislative Lifecycle

Para mas madaling ma-gets ng panel, hinati natin ang **11-step** BPA diagram sa **tatlong phase**:

```
[ Phase 1: Paghahanda & AI Check ] ➔ [ Phase 2: Deliberasyon & Botohan ] ➔ [ Phase 3: Pagpapatupad, Review & Monitoring ]
```

---

### 📂 PHASE 1: Paghahanda at Pagsusuri ng AI (Steps 1 - 2)

* **Step 1: Document Filing (Drafting)**  
  * **🖥️ Module sa Sidebar:** `Ordinances` / `Resolutions` (sa ilalim ng **DOCUMENTS**)
  * **🗣️ Script:**  
    > *"Dito nag-e-encode ang Legislative Staff ng title, subject, at content ng batas. Pagka-submit, automatic na gagawa ang system ng Tracking Number at ise-save ang draft status sa database natin."*
* **Step 2: AI Validation Engine**  
  * **🖥️ Module sa Sidebar:** `AI Validation Reports` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script:**  
    > *"Ipapadala ng system ang draft sa Groq API (Llama AI) para sa dalawang bagay: (1) **Completeness Check** para makita kung may kulang na legal parts, at (2) **Similarity Check** para malaman kung may katulad o duplicate na batas sa database natin."*
* **Desisyon: AI Validation Passed?**  
  * **🖥️ Module sa Sidebar:** `AI Validation Reports` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script:**  
    > *"Kapag Passed ang AI validation, tuloy sa susunod na hakbang. Kapag Failed o Flagged (may kapareho), ibabalik sa Staff para baguhin o i-revise."*

---

### ⚖️ PHASE 2: Pagdedebate at Botohan ng Konseho (Steps 3 - 7)

* **Step 3: First Reading & Committee Referral**  
  * **🖥️ Module sa Sidebar:** `Ordinances` / `Resolutions` List
  * **🗣️ Script:**  
    > *"Dito babasahin ang title ng batas sa First Reading. Pagkatapos, i-a-assign ng SP Secretary ang batas sa tamang Committee (halimbawa: Committee on Laws). Ang status nito ay magiging `under_review`."*
* **Step 4: Committee Review & Deliberation**  
  * **🖥️ Module sa Sidebar:** `Review and Endorsement` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script:**  
    > *"Pag-aaralan ng committee members ang batas kasama ang AI validation report. Magpapasya sila kung ito ay: (1) **Endorsed** para ma-approve, (2) **Returned** para ipa-revise, o (3) **Rejected** para i-archive."*
* **Step 5: Second Reading (Debate & Amendments)**  
  * **🖥️ Module sa Sidebar:** `Amendments and Revisions` (sa ilalim ng **RECORDS**)
  * **🗣️ Script:**  
    > *"Dito pagdedebatihan ng buong konseho ang batas. Kung may mga pagbabago o amendments, i-e-encode ito ng staff sa system bago ipadala ang batas para sa final voting."*
* **Step 6: Third & Final Reading (Voting)**  
  * **🖥️ Module sa Sidebar:** `Approval and Enactment` (sa ilalim ng **WORKFLOW**)
  * **🗣️ Script:**  
    > *"Dito na po ang huling botohan sa Plenary Session. Magpapasok ng boto ang bawat SP Member (Yes, No, Abstain), at ang system na mismo ang magbibilang at magtatala ng voting results."*
* **Step 7: Executive Signature / Attestation**  
  * **🖥️ Module sa Sidebar:** `Approval and Enactment` (Enact Screen)
  * **🗣️ Script:**  
    > *"Ipadadala ang batas sa Mayor (LCE) para pirmahan. Kung **Signed**, magiging active na batas (enacted). Kung **Vetoed** naman (tinanggihan), babalik ang batas sa Step 3 (Committee Referral) gamit ang red dashed line."*

---

### 📝 PHASE 3: Pagpapatupad, Review, at Monitoring (Steps 8 - 11)

* **Step 8: AI Summary & Portal Publication**  
  * **🖥️ Module sa Sidebar:** `Publications` (sa ilalim ng **RECORDS**)
  * **🗣️ Script:**  
    > *"Pagka-enact, i-po-post ng Staff ang batas sa web portal. Gagamit ulit ng Llama AI para mag-generate ng **Plain-Language Summary** (simpleng buod na walang legal jargon) para madaling maintindihan ng mga residente sa ating Public Portal."*
* **Step 9: Provincial Review (Sangguniang Panlalawigan)**  
  * **🖥️ Module sa Sidebar:** `Implementation Monitoring` (sa ilalim ng **RECORDS**)
  * **🗣️ Script:**  
    > *"Manual na re-reviewhin ng Probinsya ng Bulacan ang batas. Kung **Approved**, tuloy sa pag-archive. Kung **Disapproved**, magiging rejected (Step 10). Kung **With Comments** naman, babalik sa Step 5 (Second Reading) gamit ang yellow dashed line para i-amend ng konseho."*
* **Step 10: Enactment & Database Archiving**  
  * **🖥️ Module sa Sidebar:** `Archive` (sa ilalim ng **RECORDS**) / `Audit Logs`
  * **🗣️ Script:**  
    > *"Kapag approved na sa lahat ng antas, ang batas ay permanenteng mapupunta sa **Archive** module bilang active law. Kasabay nito, itatala ng system ang lahat ng naging transaction logs sa ating **Audit Logs** module para sa seguridad."*
* **Step 11: Implementation Monitoring**  
  * **🖥️ Module sa Sidebar:** `Implementation Monitoring` (sa ilalim ng **RECORDS**)
  * **🗣️ Script:**  
    > *"Dito nag-e-encode ang staff ng progress ng batas (Pending, Ongoing, o Completed) habang ito ay ipinapatupad sa buong lungsod para matiyak ang accountability."*
