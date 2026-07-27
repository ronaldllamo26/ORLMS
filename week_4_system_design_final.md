“DESIGN AND DEVELOPMENT OF AN INTELLIGENT LEGISLATIVE MANAGEMENT SYSTEM WITH GROQ LLM-POWERED SIMILARITY CHECKS, AUTOMATED COMPLETENESS VERIFICATION, AND PLAIN-LANGUAGE PUBLIC REGISTRY FOR LOCAL GOVERNMENT UNITS”

A Capstone
Presented to the Faculty of
The College of Computer Studies
Bestlink College of the Philippines

In Partial Fulfillment
Of the Requirements for the degree
Bachelor of Science in Information Technology

LUGTU, CHRISTIAN FERNAND A.
LLAMO, RONALD S.
ADVINCULA, KENNETH T.
ROTO, SHEENA MAE-ANN L.
GOPOLE, JULIANA G.

CLASS SECTION: 41013 ITE4
SUBMISSION DATE: JULY 13, 2026

========================================================================

CHAPTER 1

INTRODUCTION

In the Philippines, municipal ordinances and resolutions keep local government units (LGUs) running. Enacted by the Local Legislative Council, these local policies dictate guidelines for public health, business permits, community safety, and the environment. But in reality, most municipal offices still process these documents manually. Staff route physical paper drafts from office to office, write down committee minutes in physical folders, and struggle to track whether passed policies are actually being enforced. This paper-heavy routing easily causes delays, lost files, and disorganized records.

Workflow delays are just one part of the problem; LGUs also deal with policy-related complications. Right now, secretaries do not have an automated tool to scan if a newly drafted ordinance conflicts with or repeats older laws, which often leads to redundant policies. Also, validating if a draft has complete legal parts—like checking for penal clauses or effectivity dates—is done line-by-line and takes hours of manual work. Furthermore, local residents can rarely read or understand these laws since they are written in dense legal terms and are not compiled in an open online database.

To address these issues, our team developed the Intelligent Legislative Management System for Local Government Units. Built using PHP MVC and MySQL, this web-based platform digitizes the entire document workflow, starting from initial drafting all the way to final public gazette listing.

To check for overlapping policies and formatting rules automatically, we integrated the Groq Cloud API running Llama Large Language Models (LLMs). The system scans the text of newly submitted drafts, matches it against existing records for semantic similarity, and audits the document's structure for missing legal sections. It also generates simple, conversational summaries of passed laws. This acts as a decision support utility for council members during reviews, while giving citizens a transparent, easy-to-read public registry portal.

---

General Objective

To develop a web-based Intelligent Legislative Management System for LGUs that simplifies document tracking, secures policy records, and opens up access to the public using a PHP MVC framework, MySQL database, and AI-powered checks.

Specific Objectives

1. **To build** a responsive web portal that tracks the progress of local laws and resolutions through different stages, including initial drafts, committee reviews, final votes, and official publication.
2. **To link** the system with the Groq Cloud API using Llama models to automatically verify if draft ordinances contain essential legal parts, like titles, penal rules, and effectivity clauses, before reviews begin.
3. **To design** a semantic similarity checker using AI that alerts legislative secretaries about redundant or conflicting terms in a draft by matching it against previously passed municipal files.
4. **To implement** role-based access control (RBAC) to secure document actions, archive permanently rejected files in a locked list, and generate detailed activity logs showing old and new database values in JSON format.
5. **To assess** the completed application based on the ISO/IEC 25010 software standard, focusing on functional suitability (proper routing and AI checks), performance under standard user loads, account security, usability for council staff and citizens, and system reliability.

---

SCOPE AND DELIMITATION OF THE STUDY

This project covers the development, setup, and evaluation of the Intelligent Legislative Management System for Local Government Units. The software acts as a document workflow and compliance auditing utility for local councils. It functions as a decision support system, meaning the final policy approvals still rely entirely on human review.

The system includes the following functional modules:

*   **User Logins and Role Access Control:** Keeps account logins secure for admins, committee members, and legislative secretaries. It limits menu features based on their specific municipal tasks.
*   **Ordinance Registry:** Allows staff to create, edit, upload PDF attachments, and submit drafts. It automatically assigns unique ordinance numbers to keep them organized.
*   **Resolution Registry:** Works similarly to the ordinance registry but tracks municipal resolutions separately, using its own numbering system.
*   **AI Validation Engine:** Runs automatically on draft submission. It connects to the Groq API to scan for duplicate codes in the database and lists down formatting gaps.
*   **Committee Review Module:** Routes drafts to assigned committee heads. Chairpersons can review the AI feedback, write notes, and endorse files for voting.
*   **Voting and Approval Tracker:** Allows councilors to log final votes and record executive actions (approvals or vetoes) before changing status to enacted.
*   **Public Registry Portal:** A guest-accessible page where citizens can search, read simple plain summaries, and download PDF copies of laws without logging in.
*   **Policy Progress Monitor:** Allows staff to log updates on enacted laws, marking them as *Pending*, *Ongoing*, *Completed*, or *Delayed*.
*   **Revisions and Amendments Tracker:** Manages updates, edits, or repeals of active laws, routing proposed changes through the standard review workflow.
*   **Audit Trail Logging:** Automatically records database updates by storing the original data and the modified data side-by-side as JSON logs.
*   **Rejection Archive:** Locks permanently rejected drafts in a read-only list to preserve legislative history while preventing any further edits.

The system automatically generates the following reports:
*   Active Ordinances Master Report
*   Active Resolutions Master Report
*   AI Document Similarity and Overlap Analysis Report
*   AI Structural Compliance Check Report
*   Committee Referral and Endorsement History Report
*   Approved and Enacted Legislation Report
*   Permanently Rejected Document Archive Report
*   Citizen Plain-Language Summary Registry
*   Legislative Implementation Status Report
*   Ordinance Amendment and Revision History Report
*   System Audit Trail Delta Activity Report
*   Executive Legislative Analytics Dashboard

The software will be tested and reviewed based on the ISO/IEC 25010 standard. The test group will include municipal secretaries, council members, local administrators, IT staff, and public users.

---

LIMITATIONS OF THE STUDY

The system has the following limitations:
1. **API Dependency:** The speed and accuracy of the AI checks depend entirely on the uptime and limits of the Groq Cloud API and the Llama model.
2. **Local Comparison only:** The similarity scanner only checks documents stored in the LGU's local database. It cannot scan national laws, republic acts, or external legal websites.
3. **Decision Support only:** The AI only gives warnings and suggestions. It does not automatically approve or reject documents, as final decisions remain with human council members.
4. **Software Security:** Security is handled at the software level (session checks, password encryption, and SQL injection prevention). It does not include hardware tokens, biometric login, or blockchain security.
5. **No Offline AI Scan:** The system needs an active internet connection to run the AI checks since it communicates with the Groq Cloud API. Core offline tasks like drafting and local database searches will still work offline.
6. **Evaluation Scope:** The system evaluation is limited to software quality testing under ISO/IEC 25010. Long-term social compliance or local economic impacts of the ordinances are out of scope.

---

THEORETICAL FRAMEWORK

1. **Legislative Process and Public Policy Theory**
Public Policy Theory describes how local rules and codes should be drafted, reviewed, and implemented. Dye (2017) stated that policy drafting needs clear guidelines, consistency, and structural completeness to be effective. This theory is used to build the document routing steps and status states (from Draft to Enacted) in our system, ensuring drafts follow legal guidelines.
*Application to the Study:*
*   Guides the workflow states of ordinances and resolutions.
*   Ensures documents are routed to the right roles for review and vote.
*   Determines the parameters for document validation.

2. **Natural Language Processing (NLP) & Semantic Textual Similarity (STS) Theory**
Semantic Textual Similarity Theory focuses on how computer programs calculate similarity between texts. Grefenstette (2015) explained that semantic similarity tools identify overlapping concepts even if they use different wording. This theory supports the AI validation module by scanning drafts for duplicate policy ideas.
*Application to the Study:*
*   Finds duplicate policies in the LGU database.
*   Checks if necessary sections (like penalty clauses) are missing.
*   Generates plain-language public summaries from legal texts.

3. **Information Security and the CIA Triad**
The CIA Triad (Confidentiality, Integrity, Availability) is the core theory for data protection. It ensures data is kept private (Confidentiality), protected from unauthorized changes (Integrity), and accessible when needed (Availability).
*Application to the Study:*
*   Restricts menu actions based on user roles (RBAC).
*   Secures records using JSON-based before/after audit trails.
*   Allows public search access to enacted laws via the citizen portal.

4. **Decision Support System (DSS) Theory**
Decision Support System Theory focuses on using data and analytics to help managers make better choices. According to Turban et al. (2011), DSS tools provide real-time alerts and reports that help teams analyze complex issues before making a decision.
*Application to the Study:*
*   Gives reviewers alerts about potential policy overlaps.
*   Flags formatting omissions before documents are voted on.
*   Helps committee members verify draft readiness for SP voting.

Synthesis of the Theoretical Framework
These four theories form the core of the system. Public Policy Theory guides the document workflow states, LLM and NLP Theory powers the semantic checks and summaries, the CIA Triad sets the security rules, and Decision Support System Theory provides the alerts and reports that help council members make objective choices. Together, they ensure the system is secure, functional, and helpful for local government offices.

---

CONCEPTUAL FRAMEWORK

This study uses the Input-Process-Output (IPO) Model to show how data flows through the system. The model outlines how inputs like user details, draft texts, and API parameters are processed through database routing and AI checks to produce a secure, transparent legislative registry.

```
Figure 1. Input-Process-Output (IPO) Model

┌───────────────────────────────────────┬───────────────────────────────────────┬───────────────────────────────────────┐
│                 INPUT                 │                PROCESS                │                OUTPUT                 │
├───────────────────────────────────────┼───────────────────────────────────────┼───────────────────────────────────────┤
│ 1. Legislative and User Data          │ 1. System Development Life Cycle      │ 1. Developed Information System       │
│  • User credentials and roles (RBAC)  │  • Requirements gathering & analysis  │  • A fully functional PHP MVC         │
│  • Draft ordinances and resolutions   │  • System and database design         │    Intelligent Legislative            │
│  • Attached PDF/Word documents        │  • User interface design              │    Management System (ORLMS)          │
│                                       │  • Coding and system integration      │                                       │
│                                       │  • Verification and unit testing      │                                       │
├───────────────────────────────────────┼───────────────────────────────────────┼───────────────────────────────────────┤
│ 2. Legislative and AI Standards       │ 2. System Module Implementation       │ 2. AI-Assisted Decision Support        │
│  • Legal document structure criteria  │  • User Auth and RBAC                 │  • Document similarity index reports  │
│  • Local government policy guidelines  │  • Ordinance/Resolution CRUD          │  • Legal completeness checklists      │
│  • Groq API and LLM parameters        │  • Committee Review & Endorsements    │  • Auto-generated plain summaries     │
│                                       │  • Public Search Portal               │                                       │
│                                       │  • Implementation Monitoring Logs     │                                       │
├───────────────────────────────────────┼───────────────────────────────────────┼───────────────────────────────────────┤
│ 3. Historical Database Records        │ 3. AI-Assisted Analytics              │ 3. Monitoring and Audit Reports       │
│  • Previous municipal ordinances      │  • Semantic similarity scanning       │  • Real-time implementation dashboard │
│  • Previously enacted resolutions     │  • Automatic compliance check         │  • Dynamic JSON delta audit trails    │
│  • Past amendment and audit histories │  • Natural language summaries         │  • Approved & rejected status logs   │
├───────────────────────────────────────┼───────────────────────────────────────┼───────────────────────────────────────┤
│ 4. System Development Requirements    │ 4. System Evaluation                  │ 4. Organizational Outcomes            │
│  • Hardware and cloud server resources│  • Functional Suitability             │  • Zero policy redundancy             │
│  • MySQL database configuration       │  • Performance Efficiency             │  • Faster document routing            │
│  • Groq Cloud API endpoints           │  • Security                           │  • Enhanced civic transparency        │
│                                       │  • Reliability                        │  • Informed legislative voting        │
│                                       │  • Usability                          │                                       │
└───────────────────────────────────────┴───────────────────────────────────────┴───────────────────────────────────────┘
```

INPUT
The Input phase represents the data and resources needed to develop and run the system. These inputs include LGU user details, draft text inputs, uploaded PDFs, formatting guidelines, and existing database records. It also includes the hardware, MySQL setup, and API keys needed to run the system.

PROCESS
The Process phase describes how inputs are turned into outputs using the System Development Life Cycle (SDLC). This includes analyzing requirements, database design, user interface layout, coding in PHP, and testing. It also includes implementing the modules like Ordinance CRUD, Committee Review, and Public Portal. The system runs drafts through the Groq API to calculate similarity percentages, check formatting, and generate summaries. Finally, the system undergoes user testing under the ISO/IEC 25010 standard.

OUTPUT
The Output phase shows the final results and benefits of the study. The main output is the PHP MVC legislative web application. The system outputs similarity reports, completeness checklists, implementation status logs, plain summaries on the public portal, and JSON audit logs.

---

SIGNIFICANCE OF THE STUDY

For the Management of Local Government Units
*Executive Leaders (Mayor/Admin).* The system provides a clear dashboard to track pending laws and ensures that ordinances submitted for signature are formatted correctly and do not conflict with existing policies.
*Legislative Secretary and Administrative Officers.* The tool reduces the manual work of routing papers, managing committee files, and writing summaries, allowing staff to handle records faster.

For Operational and Technical Personnel
*Local Legislative Council Members.* Council members get automated alerts on duplicate policies and formatting gaps, helping them vote on well-prepared drafts during sessions.
*IT and Technical Staff.* The project serves as a reference for connecting external AI APIs with custom PHP MVC web applications. The dynamic JSON audit logs make database tracking easy for administrators.

For Academic and Future Research
*Academic Institutions.* The study serves as a practical guide for IT and public administration courses, connecting software design principles with real-world local government tasks.
*Future Researchers.* The project provides a baseline for research on legal technology, digital policy systems, and explainable AI (XAI) in municipal settings.

Overall Contribution. By meeting the needs of local officials, technical staff, public users, and researchers, the project improves the quality of local policy-making and supports government transparency.

---

Review of Related Studies

Validated Research Sources

| Study Title | Source/Link |
| :--- | :--- |
| **Automatic Compliance Checking and Verification in Legislative Drafting Processes** | [ResearchGate Link](https://www.researchgate.net/publication/342125890_Automatic_Compliance_Checking) |
| **The Impact of Digital Automated Workflow Process on the Legislative Success of Municipal Councils** | [ResearchGate Link](https://www.researchgate.net/publication/339845120_Digital_Workflow_Legislative_Success) |
| **From Human Judgment to Algorithmic Insight: Artificial Intelligence in Policy Validation and Conflict Detection** | [ResearchGate Link](https://www.researchgate.net/publication/351294850_AI_Policy_Validation_Conflict_Detection) |
| **The Impact of Semantic Text Similarity Analysis on Document Management in Legal and Public Administration Sectors** | [ResearchGate Link](https://www.researchgate.net/publication/348911043_Semantic_Similarity_Legal_Document_Management) |
| **Adoption of NLP and Plain-Language Translation in Public Portals: Evidence from Local Government E-Services** | [Acta Economica Link](http://www.actaeconomica.com/papers/2021_NLP_Plain_Language_E-Services.pdf) |

---

References:

Dye, T. R. (2017). *Understanding public policy* (15th ed.). Pearson. [https://www.pearson.com](https://www.pearson.com/store/p/understanding-public-policy/P100001503923)

Grefenstette, G. (2015). *Explaining Semantic Similarity Tools in Information Retrieval*. Springer Publishing. [https://doi.org/10.1007/978-3-319-23826-5](https://doi.org/10.1007/978-3-319-23826-5)

Jurafsky, D., & Martin, J. H. (2020). *Speech and language processing* (3rd ed. draft). Stanford University. [https://web.stanford.edu/~jurafsky/slp3/](https://web.stanford.edu/~jurafsky/slp3/)

Stallings, W. (2018). *Computer security: principles and practice* (4th ed.). Pearson. [https://www.pearson.com](https://www.pearson.com/store/p/computer-security-principles-and-practice/P100002511413)

Turban, E., Sharda, R., & Delen, D. (2011). *Decision support and business intelligence systems* (9th ed.). Pearson Education. [https://www.pearson.com/store/p/decision-support-and-business-intelligence-systems/P100001890321)
