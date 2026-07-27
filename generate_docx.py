import sys
import subprocess

try:
    import docx
except ImportError:
    subprocess.check_call([sys.executable, "-m", "pip", "install", "python-docx"])
    import docx

from docx import Document
from docx.shared import Pt, Inches
from docx.enum.text import WD_ALIGN_PARAGRAPH

doc = Document()

# Set default font to Times New Roman, 12pt
style = doc.styles['Normal']
font = style.font
font.name = 'Times New Roman'
font.size = Pt(12)

def add_centered_bold_paragraph(doc, text, size=12):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run(text)
    run.bold = True
    run.font.size = Pt(size)
    return p

def add_centered_paragraph(doc, text):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run(text)
    return p

# --- Title Page ---
# Adding spacing to center vertically roughly
for _ in range(3):
    doc.add_paragraph()

add_centered_bold_paragraph(doc, "DESIGN AND DEVELOPMENT OF AN INTELLIGENT LEGISLATIVE MANAGEMENT SYSTEM WITH GROQ LLM-POWERED SIMILARITY CHECKS, AUTOMATED COMPLETENESS VERIFICATION, AND PLAIN-LANGUAGE PUBLIC REGISTRY FOR LOCAL GOVERNMENT UNITS")

for _ in range(4):
    doc.add_paragraph()

add_centered_paragraph(doc, "A Capstone")
add_centered_paragraph(doc, "Presented to the Faculty of")
add_centered_paragraph(doc, "The College of Computing Studies")
add_centered_paragraph(doc, "Bestlink College of the Philippines")

for _ in range(4):
    doc.add_paragraph()

add_centered_paragraph(doc, "In Partial Fulfillment")
add_centered_paragraph(doc, "of the Requirements for the Degree")
add_centered_paragraph(doc, "Bachelor of Science in Information Technology")

for _ in range(4):
    doc.add_paragraph()

add_centered_bold_paragraph(doc, "CHRISTIAN FERNAND A. LUGTU")
add_centered_bold_paragraph(doc, "RONALD E. LLAMO")
add_centered_bold_paragraph(doc, "KENNETH B. ADVINCULA")
add_centered_bold_paragraph(doc, "SHEENA MAE-ANN L. ROTO")
add_centered_bold_paragraph(doc, "JULIANA G. GOPOLE")

for _ in range(4):
    doc.add_paragraph()

add_centered_paragraph(doc, "February 2026")

doc.add_page_break()

add_centered_bold_paragraph(doc, "TABLE OF CONTENTS")

doc.add_page_break()

# --- Chapter 1 ---

def add_heading(doc, text, level=1):
    p = doc.add_paragraph()
    run = p.add_run(text)
    run.bold = True
    if level == 1:
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run.font.size = Pt(14)
    else:
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        run.font.size = Pt(12)
        if level == 3:
            run.italic = True
    return p

def add_paragraph(doc, text):
    p = doc.add_paragraph(text)
    p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    p.paragraph_format.first_line_indent = Inches(0.5)
    return p

def add_bullet(doc, text):
    p = doc.add_paragraph(text, style='List Bullet')
    p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    return p

add_heading(doc, "PART I – INTRODUCTION", level=1)

add_heading(doc, "1.1 Project Background and Motivation", level=2)
add_paragraph(doc, "In the Philippines, Local Government Units (LGUs) operate based on ordinances and resolutions enacted by their respective Local Legislative Councils. These local policies serve as guidelines for public health, business permits, community safety, and environmental protection. Given the critical importance of these documents, their processing requires speed, accuracy, and organization. However, the reality is that most municipal offices still manage these documents manually. Physical paper drafts are routed from one office to another, meeting minutes are written down in physical folders, and tracking the implementation of enacted laws remains a significant challenge. This outdated, paper-heavy system frequently leads to workflow delays, misplaced files, and disorganized record-keeping. These ongoing challenges motivated the research team to develop a modern solution that digitizes and expedites the entire legislative process.")

add_heading(doc, "1.2 Problem Statement", level=2)
add_paragraph(doc, "Beyond the workflow delays caused by manual processing, LGUs face deeper, more complex issues in policy-making. First, legislative secretaries lack an automated tool to determine whether a newly drafted ordinance is redundant or conflicts with existing laws. Second, the manual verification of a draft’s legal completeness—such as checking for penal clauses or effectivity dates—is a tedious process that takes hours. Third, ordinary citizens often find it difficult to understand these local laws due to dense legal jargon, and they lack a centralized, easily accessible online platform to view these policies.")

add_heading(doc, "1.3 Project Vision and Scope", level=2)
add_paragraph(doc, "The vision of this project is to develop an \"Intelligent Legislative Management System\" that will serve as a comprehensive, all-in-one platform for LGUs. The primary goal is to make the creation, tracking, and management of local laws digital, fast, and transparent through the integration of modern AI technology.")

add_heading(doc, "1.3.1 In-Scope", level=3)
p = doc.add_paragraph("The scope of this system includes the following functionalities:")
p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
add_bullet(doc, "Ordinance and Resolution Registry: Digital creation, editing, and uploading of drafts with automated tracking numbers.")
add_bullet(doc, "AI Validation Engine: Integration of the Groq Cloud API (Llama LLMs) to automatically detect semantic similarities with existing laws and to verify the structural completeness of submitted drafts.")
add_bullet(doc, "Committee Review and Voting Tracker: A digital routing system for committee reviews and a module for recording the official votes of council members.")
add_bullet(doc, "Public Registry Portal: A guest-accessible website where citizens can search for laws and read plain-language summaries generated by AI.")
add_bullet(doc, "User Management and Audit Trail: Implementation of Role-Based Access Control (RBAC) to ensure security, alongside automated JSON logging of all system activities.")

add_heading(doc, "1.3.2 Out-of-Scope", level=3)
p = doc.add_paragraph("The system has the following limitations and exclusions:")
p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
add_bullet(doc, "National Laws Comparison: The AI similarity checker will only scan the local database of the LGU. It does not possess the capability to cross-reference national laws or Republic Acts on the internet.")
add_bullet(doc, "Automatic Decision Making: The AI operates purely as a decision-support tool, providing warnings and suggestions. The final authority to approve or reject a draft ordinance remains entirely with the human council members.")
add_bullet(doc, "Offline AI Scan: The AI validation features require an active internet connection to communicate with the Groq Cloud API. However, basic document encoding will remain functional offline.")
add_bullet(doc, "Advanced Hardware Security: The system's security is handled purely at the software level. It does not incorporate hardware tokens, biometric logins, or blockchain technology.")

add_heading(doc, "1.4 Objectives and Goals", level=2)

add_heading(doc, "1.4.1 General Objective", level=3)
add_paragraph(doc, "To develop a web-based Intelligent Legislative Management System for Local Government Units that expedites document tracking, secures policy records, and provides accessible public registries using the PHP MVC framework, MySQL database, and AI-powered compliance checks.")

add_heading(doc, "1.4.2 Specific Objectives", level=3)
add_bullet(doc, "To create a responsive web portal capable of tracking the lifecycle of local laws from initial drafting to official enactment.")
add_bullet(doc, "To integrate the system with the Groq Cloud API (Llama models) to automatically verify the presence of essential legal components in draft ordinances prior to committee review.")
add_bullet(doc, "To design an AI semantic similarity checker that alerts legislative secretaries of redundant or conflicting terms by cross-referencing new drafts against archived municipal records.")
add_bullet(doc, "To implement Role-Based Access Control (RBAC) to secure the system and maintain detailed audit logs of all user actions.")
add_bullet(doc, "To evaluate the completed system based on the ISO/IEC 25010 software quality standards, specifically focusing on functional suitability, performance efficiency, security, usability, and reliability.")

add_heading(doc, "1.5 Significance and Relevance", level=2)

add_heading(doc, "1.5.1 Theoretical Significance", level=3)
add_paragraph(doc, "This study is anchored on the E-Governance Theory and the Technology Acceptance Model (TAM). The E-Governance Theory posits that the integration of information technology (such as web portals and AI) enhances government services by increasing efficiency, transparency, and public accessibility. Furthermore, the Technology Acceptance Model explains that the adoption of a new system by users—in this case, legislative secretaries and councilors—depends heavily on its perceived ease of use and perceived usefulness. This study will demonstrate how artificial intelligence can theoretically and practically streamline legislative processes in local governments.")

add_heading(doc, "1.5.2 Practical Significance", level=3)
add_paragraph(doc, "This system will significantly improve the daily operations of LGUs. For Legislative Secretaries and Councilors, it will drastically reduce the time wasted on manual document checking, as the AI will automatically detect errors and duplications. For the Citizens, the system will foster greater civic awareness, as local laws will be easily accessible online and translated into easily understandable plain-language summaries.")

add_heading(doc, "1.6 Definition of Terms", level=2)
p = doc.add_paragraph("To ensure clarity for all readers, the following technical terms are defined operationally as used in this study:")
p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
add_bullet(doc, "Intelligent Legislative Management System (ILMS): The web-based software developed in this study to digitize and manage the legislative documents of a local government unit.")
add_bullet(doc, "Local Government Unit (LGU): The local administrative branch of the government (such as a municipality or city) that will serve as the primary user of the system.")
add_bullet(doc, "Ordinance: A local law enacted by the municipal council that has a permanent and general effect on the community.")
add_bullet(doc, "Resolution: An official statement or decision made by the council, typically addressing a specific issue or temporary concern.")
add_bullet(doc, "Semantic Similarity: The capability of artificial intelligence to understand the context and meaning of text, allowing it to determine if two different legislative drafts have the same intent despite using different wordings.")
add_bullet(doc, "Large Language Model (LLM): A type of artificial intelligence (such as Llama) designed to understand and generate human-like text.")
add_bullet(doc, "Groq Cloud API: The cloud service utilized to rapidly connect the web system to the LLM for AI processing.")
add_bullet(doc, "Role-Based Access Control (RBAC): A security mechanism that restricts system access and capabilities based on the designated role of the user (e.g., Administrator vs. Secretary).")
add_bullet(doc, "Plain-Language Summary: The translation of complex legal jargon and legislative texts into simple, easily understandable words for the general public.")

add_heading(doc, "1.7 Structure of the Document", level=2)
add_paragraph(doc, "This document is organized into several sections to facilitate a clear understanding of the study. Chapter 1 introduces the background, problem statement, and objectives of the project. Chapter 2 reviews the related literature and previous studies that provided the foundation for the system. Chapter 3 explains the methodology and system design that will be utilized in developing the software.")

output_path = r'c:\xampp\htdocs\Capstone\Chapter_1_Final_Format.docx'
doc.save(output_path)
print(f"Document saved to {output_path}")
