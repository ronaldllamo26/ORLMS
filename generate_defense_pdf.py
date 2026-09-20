import os
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        self.setFont("Helvetica-Bold", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        
        # Header (pages > 1)
        if self._pageNumber > 1:
            self.drawString(54, 11 * 72 - 36, "ORLMS - Capstone Panel Defense Q&A & Survival Guide")
            self.drawRightString(8.5 * 72 - 54, 11 * 72 - 36, "STRICTLY FOR GROUP PRE-DEFENSE REVIEW")
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.5)
            self.line(54, 11 * 72 - 42, 8.5 * 72 - 54, 11 * 72 - 42)
            
        # Footer
        self.setFont("Helvetica", 8)
        footer_text = f"Page {self._pageNumber} of {page_count}"
        self.drawRightString(8.5 * 72 - 54, 36, footer_text)
        self.drawString(54, 36, "ORLMS: Ordinance and Resolution Legislative Management System with AI")
        self.setStrokeColor(colors.HexColor("#E2E8F0"))
        self.setLineWidth(0.5)
        self.line(54, 46, 8.5 * 72 - 54, 46)
        
        self.restoreState()

def build_pdf(filename="ORLMS_Capstone_Panel_Defense_QA_Guide.pdf"):
    doc = SimpleDocTemplate(
        filename,
        pagesize=letter,
        leftMargin=50,
        rightMargin=50,
        topMargin=50,
        bottomMargin=50
    )

    styles = getSampleStyleSheet()

    # Color Palette
    primary_color = colors.HexColor("#1E3A8A")   # Navy Blue
    secondary_color = colors.HexColor("#0D9488") # Deep Teal
    accent_amber = colors.HexColor("#D97706")    # Warm Amber
    dark_slate = colors.HexColor("#0F172A")
    body_color = colors.HexColor("#334155")

    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=18,
        leading=22,
        textColor=primary_color,
        spaceAfter=3
    )

    subtitle_style = ParagraphStyle(
        'DocSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=10.5,
        leading=14,
        textColor=secondary_color,
        spaceAfter=8
    )

    badge_style = ParagraphStyle(
        'Badge',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=11,
        textColor=colors.HexColor("#64748B"),
        spaceAfter=10
    )

    section_header_style = ParagraphStyle(
        'SectionHeader',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=12.5,
        leading=15,
        textColor=primary_color,
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )

    q_straight_style = ParagraphStyle(
        'QuestionStraight',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9.5,
        leading=13,
        textColor=dark_slate
    )

    ans_straight_style = ParagraphStyle(
        'AnswerStraight',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=12.5,
        textColor=body_color
    )

    detailed_q_style = ParagraphStyle(
        'DetailedQuestion',
        parent=styles['Heading3'],
        fontName='Helvetica-Bold',
        fontSize=10,
        leading=13.5,
        textColor=dark_slate,
        spaceBefore=7,
        spaceAfter=3,
        keepWithNext=True
    )

    detailed_ans_style = ParagraphStyle(
        'DetailedAnswer',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12,
        textColor=body_color,
        spaceAfter=5
    )

    table_hdr_style = ParagraphStyle(
        'TableHdr',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11,
        textColor=colors.white
    )

    table_body_style = ParagraphStyle(
        'TableBody',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=11.5,
        textColor=body_color
    )

    table_body_bold = ParagraphStyle(
        'TableBodyBold',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=11.5,
        textColor=primary_color
    )

    story = []

    # Title & Metadata
    story.append(Paragraph("ORLMS: Capstone Panel Defense Master Guide", title_style))
    story.append(Paragraph("Ordinance and Resolution Legislative Management System with AI Integration", subtitle_style))
    story.append(Paragraph("<b>Tech Stack:</b> PHP (MVC) | MySQL | Groq LLM API | Bootstrap & Vanilla CSS | MFA / OTP Security", badge_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceAfter=10))

    # =========================================================================
    # PART 1: RAPID-FIRE STRAIGHT ANSWER CHEAT SHEET
    # =========================================================================
    story.append(Paragraph("PART 1: ⚡ RAPID-FIRE STRAIGHT-ANSWER CHEAT SHEET", section_header_style))
    story.append(Paragraph("<font size='8' color='#64748B'><i>(Maikli at direktang sagot para madaling matandaan sa defense nang walang paligoy-ligoy)</i></font>", badge_style))

    straight_qa_list = [
        (
            "1. Ano ang pinaka-purpose ng system ninyo?",
            "<b>I-digitize at pabilisin ang buong legislative lifecycle ng LGU</b>—mula sa paggawa ng draft, committee review, approvals, hanggang sa pag-publish sa mamamayan at pag-monitor ng implementasyon."
        ),
        (
            "2. Bakit kailangan pa ng AI sa system ninyo?",
            "Bilang <b>decision-support tool</b>: tumutulong ang AI mag-check ng formatting at potential conflicts sa drafts, at gumagawa ng 2-sentence plain-language summary para madaling maintindihan ng mamamayan ang batas."
        ),
        (
            "3. Pinapalitan ba ng AI ang mga Konsehal, Mayor, o Abogado?",
            "<b>Hindi po. 100% human-in-the-loop kami</b>—ang AI ay nagmumungkahi lang (assistant), ngunit ang lahat ng desisyon, boto, at approval ay eksklusibong hawak ng mga halal na opisyal."
        ),
        (
            "4. Safe ba ang data kapag ipinasa sa AI API?",
            "<b>Opo</b>, dahil tanging public text at non-confidential drafts lamang ang ipinapasa, at grounded ang system prompt para sumagot lamang base sa ibinigay na data nang walang data leakage."
        ),
        (
            "5. Paano ninyo sinisigurong hindi madadaya ang approvals at access?",
            "Gumagamit kami ng <b>Role-Based Access Control (RBAC)</b>, <b>MFA / OTP via Email</b> sa login, at <b>Tamper-Evident Audit Logs</b> na nagtatala ng IP, timestamp, at user sa bawat galaw."
        ),
        (
            "6. Paano ninyo pinoprotektahan ang database laban sa SQL Injection at XSS?",
            "Lahat ng database queries ay gumagamit ng <b>PDO Prepared Statements</b> na may parameter binding, at may HTML sanitization sa lahat ng user inputs."
        ),
        (
            "7. Bakit kailangan pa ng Implementation Monitoring kung pasado na ang batas?",
            "Dahil ang tunay na silbi ng batas ay nasa execution—minomonitor ng system kung anong departamento ang inatasan, kung nagpasa sila ng IRR, at ano ang actual compliance status sa komunidad."
        ),
        (
            "8. Paano kapag inamyendahan o pinawalang-bisa ang isang lumang ordinansa?",
            "May <b>Amendments Tracking</b> kami kung saan nali-link ang bagong batas sa luma; mamarkahan ang luma bilang 'Amended' o 'Repealed' habang nananatili itong naka-archive para sa historical reference."
        ),
        (
            "9. Bakit hindi na lang Google Drive o shared folder ang gamitin ng LGU?",
            "Walang workflow validation, role-based permissions, formal approval routing, public search portal, at audit trail ang Google Drive—lahat ng iyon ay automated at centralized sa ORLMS."
        ),
        (
            "10. Ano ang Disaster Recovery / Backup plan ninyo kapag nag-crash ang server?",
            "May built-in <b>Database Backup Utility</b> ang Admin kung saan makakapag-generate ng 1-click full <code>.sql</code> database dumps at CSV exports para sa local at off-site recovery."
        ),
        (
            "11. Sino ang primary beneficiaries ng system?",
            "Ang <b>LGU Legislative Body</b> (para sa mabilis at organized na trabaho) at ang <b>General Public / Mamamayan</b> (para sa madaling paghahanap at pag-intindi sa mga lokal na batas)."
        ),
        (
            "12. Ano ang major limitations ng system ninyo ngayon?",
            "Kailangan ng internet para sa external AI API services, at ang e-signatures sa ngayon ay internal system-verified authentication pa lamang, hindi pa hardware-based PNPKI."
        ),
        (
            "13. Ano ang architecture at stack na ginamit ninyo?",
            "Naka-<b>MVC (Model-View-Controller) Architecture</b> kami gamit ang <b>PHP (Backend)</b>, <b>MySQL (Database)</b>, <b>Bootstrap / Vanilla CSS (Frontend)</b>, at <b>Groq Cloud API (LLM Engine)</b>."
        ),
        (
            "14. Paano gumagana ang inyong OTP / MFA?",
            "Pagkatapos mag-input ng tamang password, nagge-generate ang server ng time-limited 6-digit OTP code na ipinapadala sa rehistradong email ng kawani bago tuluyang makapasok sa dashboard."
        ),
        (
            "15. Paano kapag may tinanong ang panel na hindi niyo alam ang sagot?",
            "Sabihin agad nang propesyonal: <i>'Thank you for that insight, panel. That specific edge case is outside our current scope, but we will gladly include it as a key recommendation for future enhancement.'</i>"
        ),
    ]

    for q, a in straight_qa_list:
        qa_data = [
            [
                Paragraph(f"<b>Q:</b> {q}", q_straight_style)
            ],
            [
                Paragraph(f"<b>Ans:</b> {a}", ans_straight_style)
            ]
        ]
        qa_box = Table(qa_data, colWidths=[512])
        qa_box.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#F8FAFC")),
            ('BOX', (0, 0), (-1, -1), 0.5, colors.HexColor("#CBD5E1")),
            ('LINEBELOW', (0, 0), (-1, 0), 0.5, colors.HexColor("#E2E8F0")),
            ('TOPPADDING', (0, 0), (-1, -1), 4),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
            ('LEFTPADDING', (0, 0), (-1, -1), 6),
            ('RIGHTPADDING', (0, 0), (-1, -1), 6),
        ]))
        story.append(qa_box)
        story.append(Spacer(1, 4))

    story.append(Spacer(1, 10))

    # =========================================================================
    # PART 2: IN-DEPTH TOPICS & DETAILED EXPLANATIONS
    # =========================================================================
    story.append(Paragraph("PART 2: 📖 IN-DEPTH TOPICS & TECHNICAL DETAILS", section_header_style))

    # Topic 1: 7-Step Lifecycle
    story.append(Paragraph("Detailed Workflow: 7-Step Legislative Lifecycle", detailed_q_style))
    story.append(Paragraph(
        "<b>1. Drafting / Ingestion:</b> Lumilikha ng draft ang Legislative Staff kalakip ang metadata at draft text.<br/>"
        "<b>2. AI Validation (Decision Support):</b> Tumutulong mag-scan ng potential formatting gaps at completeness.<br/>"
        "<b>3. Committee Review & Amendments:</b> Ipapasa sa nakatalagang Committee para sa review notes at formal amendments.<br/>"
        "<b>4. Multi-Level Approval:</b> Dadaan sa voting / signature endorsement (Sanggunian at Mayor's Office).<br/>"
        "<b>5. Enactment & Public Publication:</b> Awtomatikong nagiging accessible sa Public Portal Registry.<br/>"
        "<b>6. Post-Enactment Monitoring:</b> Sinusubaybayan ang implementing agencies at compliance updates.<br/>"
        "<b>7. Archival & History:</b> Naitatago ang historical records at amendments sa centralized digital archive.",
        detailed_ans_style
    ))
    story.append(Spacer(1, 4))

    # Topic 2: Security & Architecture
    story.append(Paragraph("Security Architecture & Defenses", detailed_q_style))
    story.append(Paragraph(
        "• <b>Authentication:</b> Password hashing gamit ang <code>password_hash(PASSWORD_BCRYPT)</code> + Time-based 6-digit Email OTP.<br/>"
        "• <b>Access Control:</b> Strict Role Middleware sa PHP controllers (Super Admin, SP Member, Staff, Committee Head, Public).<br/>"
        "• <b>Audit Trail:</b> Bawat login, edit, delete, at status change ay may entry sa <code>audit_logs</code> table kasama ang User, IP, Timestamp, at Target Record.<br/>"
        "• <b>Database Safety:</b> 100% Prepared Statements via PDO laban sa SQL Injections.",
        detailed_ans_style
    ))
    story.append(Spacer(1, 10))

    # =========================================================================
    # PART 3: GROUP ROLES ASSIGNMENT TABLE
    # =========================================================================
    story.append(Paragraph("PART 3: 👥 RECOMMENDED GROUP ROLES & DEFENSE STRATEGY", section_header_style))

    table_data = [
        [
            Paragraph("Group Role", table_hdr_style),
            Paragraph("Primary Topics to Answer", table_hdr_style),
            Paragraph("Key Responsibilities During Defense", table_hdr_style)
        ],
        [
            Paragraph("<b>Project Lead / Presenter</b>", table_body_bold),
            Paragraph("Background, Objectives, Scope & Limitations, Problem Statement, LGU Impact, Public Portal overview.", table_body_style),
            Paragraph("Bukas at sara ng presentation; sumasalo kapag general questions o executive summary ang hinihingi ng panel.", table_body_style)
        ],
        [
            Paragraph("<b>Lead Developer / Backend</b>", table_body_bold),
            Paragraph("MVC Architecture, Database relationships, Ordinance lifecycle logic, SQL security, Backup utility.", table_body_style),
            Paragraph("Naka-standby sa code at database schema; nagpapaliwanag ng back-end logic sa bawat proseso.", table_body_style)
        ],
        [
            Paragraph("<b>UI / Security Specialist</b>", table_body_bold),
            Paragraph("RBAC permissions, MFA / OTP email verification, Audit logs, Responsive UX, Form validations.", table_body_style),
            Paragraph("Nagde-demo ng live system habang may nagpapaliwanag; sumasagot sa security at user access.", table_body_style)
        ],
        [
            Paragraph("<b>AI & Integrations Specialist</b>", table_body_bold),
            Paragraph("Groq LLM API, AI Validation algorithms, Plain Language TL;DR, Post-enactment monitoring.", table_body_style),
            Paragraph("Sumasagot sa lahat ng tanong ukol sa AI safety, prompt grounding, hallucination control, at monitoring.", table_body_style)
        ]
    ]

    col_widths = [115, 210, 187]
    role_table = Table(table_data, colWidths=col_widths, repeatRows=1)
    role_table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), primary_color),
        ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor("#CBD5E1")),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
        ('TOPPADDING', (0, 0), (-1, -1), 5),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 5),
        ('LEFTPADDING', (0, 0), (-1, -1), 6),
        ('RIGHTPADDING', (0, 0), (-1, -1), 6),
    ]))
    story.append(role_table)
    story.append(Spacer(1, 10))

    # =========================================================================
    # PART 4: DEFENSE SURVIVAL RULES
    # =========================================================================
    story.append(Paragraph("PART 4: 🛡️ TEAM SURVIVAL RULES & LIVE DEMO STRATEGY", section_header_style))

    tips_content = [
        [
            Paragraph(
                "<b>💡 GOLDEN DEFENSE SURVIVAL RULES FOR THE TEAM:</b><br/>"
                "1. <b>Never Argue with the Panel:</b> Tanggapin ang kanilang punto. Sabihin: <i>'Thank you for that valuable insight, panel. We will incorporate that in our future recommendations.'</i><br/>"
                "2. <b>Live Demo Flow:</b> Sundin ang natural na daloy: <b>Login with OTP → Dashboard → Create Ordinance → AI Validate → Committee Review → Multi-Level Approval → Public Portal View → Audit Log Verification</b>.<br/>"
                "3. <b>Team Synergy:</b> Huwag mag-interrupt o mag-kontrahan sa harap ng panel. Mag-nod at mag-supplement lamang kapag tapos na magsalita ang ka-grupo.<br/>"
                "4. <b>Stick to Scope:</b> Huwag mangakong 'nandiyan na' ang feature kung wala pa; sabihing nasa future enhancements roadmap ito.",
                ans_straight_style
            )
        ]
    ]
    tips_table = Table(tips_content, colWidths=[512])
    tips_table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#EFF6FF")),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#93C5FD")),
        ('TOPPADDING', (0, 0), (-1, -1), 7),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 7),
        ('LEFTPADDING', (0, 0), (-1, -1), 9),
        ('RIGHTPADDING', (0, 0), (-1, -1), 9),
    ]))
    story.append(tips_table)

    # Build PDF
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Master PDF Successfully generated: {filename}")

if __name__ == '__main__':
    build_pdf("ORLMS_Capstone_Panel_Defense_QA_Guide.pdf")
