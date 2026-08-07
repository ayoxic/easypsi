from pathlib import Path
from reportlab.lib.colors import HexColor, Color
from reportlab.lib.pagesizes import A4
from reportlab.lib.utils import ImageReader, simpleSplit
from reportlab.pdfgen import canvas
from math import cos, pi, sin


PAGE_W, PAGE_H = A4
MARGIN = 28
LEFT_COL_W = 175
GUTTER = 30
RIGHT_X = MARGIN + LEFT_COL_W + GUTTER
RIGHT_W = PAGE_W - RIGHT_X - MARGIN

BLUE = HexColor("#1d4f9c")
LIGHT_BLUE = HexColor("#5ea2ff")
MUTED = HexColor("#666666")
LIGHT = HexColor("#d8d8d8")
DARK = HexColor("#2b2b2b")
VERY_LIGHT = HexColor("#f4f4f4")
PHONE_ICON_PATH = Path("tmp/pdfs/phone_icon.png")
PHONE_ICON = None


def hr(c, x, y, w):
    c.setStrokeColor(LIGHT)
    c.setLineWidth(0.8)
    c.line(x, y, x + w, y)


def section_title(c, x, y, title, width):
    c.setFont("Helvetica", 10)
    c.setFillColor(MUTED)
    c.drawString(x, y, title.upper())
    hr(c, x, y - 4, width)
    return y - 18


def get_phone_icon():
    global PHONE_ICON
    if PHONE_ICON is None and PHONE_ICON_PATH.exists():
        PHONE_ICON = ImageReader(str(PHONE_ICON_PATH))
    return PHONE_ICON


def draw_star(c, cx, cy, outer_r=3.0, inner_r=1.35, fill=LIGHT_BLUE):
    pts = []
    for i in range(10):
        angle = -pi / 2 + i * pi / 5
        r = outer_r if i % 2 == 0 else inner_r
        pts.extend([cx + cos(angle) * r, cy + sin(angle) * r])
    path = c.beginPath()
    path.moveTo(pts[0], pts[1])
    for i in range(2, len(pts), 2):
        path.lineTo(pts[i], pts[i + 1])
    path.close()
    c.setFillColor(fill)
    c.setStrokeColor(fill)
    c.drawPath(path, stroke=0, fill=1)


def draw_contact_icon(c, kind, x, y, size=7.5):
    c.setStrokeColor(MUTED)
    c.setFillColor(MUTED)
    c.setLineWidth(0.9)
    half = size / 2

    if kind == "phone":
        icon = get_phone_icon()
        if icon is not None:
            c.drawImage(icon, x - 5.2, y - 5.2, width=10.4, height=10.4, mask="auto")
        else:
            c.circle(x, y, 2.6, stroke=0, fill=1)
    elif kind == "mail":
        c.rect(x - half, y - half + 0.4, size, size - 1.2, stroke=1, fill=0)
        c.line(x - half, y + half - 0.8, x, y - 0.4)
        c.line(x, y - 0.4, x + half, y + half - 0.8)
    elif kind == "link":
        r = 2.1
        c.circle(x - 1.5, y, r, stroke=1, fill=0)
        c.circle(x + 1.5, y, r, stroke=1, fill=0)
        c.line(x - 0.2, y + 1.3, x + 0.2, y + 1.3)
        c.line(x - 0.2, y - 1.3, x + 0.2, y - 1.3)
    elif kind == "pin":
        c.circle(x, y + 1.4, 2.1, stroke=1, fill=0)
        path = c.beginPath()
        path.moveTo(x, y - half + 0.2)
        path.lineTo(x - 2.0, y + 0.1)
        path.lineTo(x + 2.0, y + 0.1)
        path.close()
        c.drawPath(path, stroke=1, fill=0)


def wrapped_text(c, text, x, y, width, font="Helvetica", size=8.5, color=DARK, leading=11):
    c.setFont(font, size)
    c.setFillColor(color)
    lines = simpleSplit(text, font, size, width)
    for line in lines:
        c.drawString(x, y, line)
        y -= leading
    return y


def wrapped_bullets(c, items, x, y, width, bullet_r=2.4, font="Helvetica", size=8.2, color=DARK, leading=10.5, gap=6):
    for item in items:
        lines = simpleSplit(item, font, size, width - 14)
        c.setFillColor(LIGHT)
        c.circle(x + bullet_r, y - 3, bullet_r, stroke=0, fill=1)
        c.setFillColor(color)
        c.setFont(font, size)
        first = True
        for line in lines:
            c.drawString(x + 12, y, line)
            y -= leading
            if first:
                first = False
        y -= gap
    return y


def info_row(c, entries, x, y, size=7.2):
    c.setFont("Helvetica", size)
    c.setFillColor(MUTED)
    cursor = x
    for kind, label in entries:
        draw_contact_icon(c, kind, cursor + 5, y + 1.5, size=7)
        cursor += 13
        c.drawString(cursor, y, label)
        cursor += c.stringWidth(label, "Helvetica", size) + 14


def proficiency_dots(c, x, y, filled):
    for i in range(5):
        c.setFillColor(BLUE if i < filled else HexColor("#d9dce3"))
        c.circle(x + i * 10, y, 2.4, stroke=0, fill=1)


def skill_links(c, x, y, title, skills, width):
    y = section_title(c, x, y, title, width)
    rows = []
    row = []
    current = 0
    for skill in skills:
        w = c.stringWidth(skill, "Helvetica", 7.8) + 16
        if current + w > width and row:
            rows.append(row)
            row = []
            current = 0
        row.append((skill, w))
        current += w + 6
    if row:
        rows.append(row)

    for row in rows:
        cursor = x
        for skill, w in row:
            c.setFont("Helvetica", 7.8)
            c.setFillColor(LIGHT_BLUE)
            c.drawString(cursor, y, skill)
            c.setStrokeColor(LIGHT)
            c.setLineWidth(0.6)
            c.line(cursor, y - 2, cursor + c.stringWidth(skill, "Helvetica", 7.8), y - 2)
            cursor += w
        y -= 18
    return y - 4


def draw_online_item(c, x, y, label, value, letter):
    c.setFillColor(VERY_LIGHT)
    c.circle(x + 10, y - 2, 9, stroke=0, fill=1)
    c.setFont("Helvetica-Bold", 8)
    c.setFillColor(LIGHT_BLUE)
    c.drawCentredString(x + 10, y - 4.5, letter)
    c.setFont("Helvetica", 8.2)
    c.setFillColor(BLUE)
    c.drawString(x + 24, y + 2, label)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 7.2)
    c.drawString(x + 24, y - 9, value)
    return y - 28


def entry_header(c, title, org, date_text, loc_text, x, y, width):
    c.setFont("Helvetica-Bold", 9)
    c.setFillColor(BLUE)
    c.drawString(x, y, title)
    y -= 11
    c.setFont("Helvetica", 8)
    c.setFillColor(LIGHT_BLUE)
    c.drawString(x, y, org)
    right = x + width
    c.setFont("Helvetica", 7.2)
    c.setFillColor(MUTED)
    c.drawRightString(right - 128, y, date_text)
    c.drawRightString(right, y, loc_text)
    return y - 10


def education_header(c, title, org, date_text, loc_text, x, y, width, right_note=None):
    c.setFont("Helvetica-Bold", 8.8)
    c.setFillColor(BLUE)
    c.drawString(x, y, title)
    y -= 10
    c.setFont("Helvetica", 7.8)
    c.setFillColor(LIGHT_BLUE)
    c.drawString(x, y, org)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 7)
    c.drawString(x + 2, y - 10, f"{date_text}    {loc_text}")
    if right_note:
        c.drawRightString(x + width, y - 5, right_note)
    return y - 24


def project_block(c, title, stack, bullets, x, y, width):
    c.setFont("Helvetica-Bold", 8.8)
    c.setFillColor(BLUE)
    c.drawString(x, y, title)
    y -= 10
    c.setFont("Helvetica", 7.4)
    c.setFillColor(MUTED)
    c.drawString(x, y, stack)
    y -= 10
    return wrapped_bullets(c, bullets, x + 2, y, width - 2, size=7.5, leading=9.5, gap=4)


def certification_item(c, x, y, title, org):
    c.setFont("Helvetica", 8.4)
    c.setFillColor(LIGHT_BLUE)
    c.drawString(x, y, title)
    c.setFont("Helvetica", 7.4)
    c.setFillColor(MUTED)
    c.drawString(x, y - 10, org)
    return y - 24


def build_pdf(out_path: Path):
    out_path.parent.mkdir(parents=True, exist_ok=True)
    c = canvas.Canvas(str(out_path), pagesize=A4)
    c.setTitle("Ayoub Janina Resume")

    # Page 1
    y = PAGE_H - 34
    c.setFont("Helvetica-Bold", 18)
    c.setFillColor(BLUE)
    c.drawString(MARGIN, y, "AYOUB JANINA")
    y -= 13
    c.setFont("Helvetica", 8.2)
    c.setFillColor(LIGHT_BLUE)
    c.drawString(MARGIN, y, "Senior Software Developer | Data & Software Engineering Student")
    y -= 12
    info_row(
        c,
        [
            ("phone", "+212 645 532 991"),
            ("mail", "ayoubjanina2015@gmail.com"),
            ("link", "ayoxic.vercel.app"),
            ("pin", "Rabat / Casablanca, Morocco"),
        ],
        MARGIN,
        y,
    )

    left_y = PAGE_H - 92
    right_y = PAGE_H - 92

    # Left column page 1
    left_y = section_title(c, MARGIN, left_y, "Key Achievements", LEFT_COL_W)
    c.setFillColor(VERY_LIGHT)
    c.circle(MARGIN + 12, left_y - 2, 10, stroke=0, fill=1)
    draw_star(c, MARGIN + 12, left_y - 3.5, outer_r=3.2, inner_r=1.45)
    c.setFont("Helvetica-Bold", 8.5)
    c.setFillColor(BLUE)
    c.drawString(MARGIN + 30, left_y + 2, "Organizer, INSEA 'Game of Codes'")
    c.drawString(MARGIN + 30, left_y - 8, "event")
    left_y = wrapped_text(
        c,
        "Contributed to organizing a programming and problem-solving event for students, supporting a collaborative coding community at INSEA.",
        MARGIN + 30,
        left_y - 22,
        LEFT_COL_W - 30,
        size=7.2,
        color=MUTED,
        leading=9,
    ) - 10

    c.setFillColor(VERY_LIGHT)
    c.circle(MARGIN + 12, left_y - 2, 10, stroke=0, fill=1)
    c.setStrokeColor(LIGHT_BLUE)
    c.setLineWidth(1.25)
    c.line(MARGIN + 7.5, left_y - 2, MARGIN + 10.2, left_y + 0.8)
    c.line(MARGIN + 7.5, left_y - 2, MARGIN + 10.2, left_y - 4.8)
    c.line(MARGIN + 16.5, left_y - 2, MARGIN + 13.8, left_y + 0.8)
    c.line(MARGIN + 16.5, left_y - 2, MARGIN + 13.8, left_y - 4.8)
    c.setFont("Helvetica-Bold", 8.5)
    c.setFillColor(BLUE)
    c.drawString(MARGIN + 30, left_y + 2, "Multidisciplinary Technical Portfolio")
    left_y = wrapped_text(
        c,
        "Built practical experience across full-stack development, databases, mobile applications, computer vision, and data analytics while pursuing engineering studies.",
        MARGIN + 30,
        left_y - 12,
        LEFT_COL_W - 30,
        size=7.2,
        color=MUTED,
        leading=9,
    ) - 8

    left_y = section_title(c, MARGIN, left_y, "Languages", LEFT_COL_W)
    langs = [
        ("Arabic", "Native", 5),
        ("French", "Proficient", 4),
        ("English", "Proficient", 4),
        ("Spanish", "Beginner", 1),
    ]
    for name, level, dots in langs:
        c.setFont("Helvetica", 8.2)
        c.setFillColor(BLUE)
        c.drawString(MARGIN, left_y, name)
        c.setFillColor(MUTED)
        c.setFont("Helvetica", 7.2)
        c.drawRightString(MARGIN + 126, left_y, level)
        proficiency_dots(c, MARGIN + 138, left_y + 1, dots)
        left_y -= 18

    left_y -= 4
    c.setFont("Helvetica", 10)
    c.setFillColor(MUTED)
    c.drawString(MARGIN, left_y, "SKILLS")
    hr(c, MARGIN, left_y - 4, LEFT_COL_W)
    left_y -= 18

    left_y = skill_links(c, MARGIN, left_y, "Programming Languages", ["Python", "PHP", "JavaScript", "TypeScript", "Java", "SQL", "HTML5", "CSS"], LEFT_COL_W)
    left_y = skill_links(c, MARGIN, left_y, "Backend & APIs", ["Laravel", "REST APIs", "Authentication & Authorization", "API Integration", "Postman"], LEFT_COL_W)
    left_y = skill_links(c, MARGIN, left_y, "Frameworks & Web Development", ["Responsive web design", "Laravel", "React", "Next.js", "React Native", "Node.js", "Tailwind CSS", "MVC Architecture", "Eloquent ORM"], LEFT_COL_W)
    left_y = skill_links(c, MARGIN, left_y, "Databases & Data Management", ["MySQL", "PostgreSQL", "Firebase", "ETL Pipelines", "dbt", "BigQuery"], LEFT_COL_W)

    # Right column page 1
    right_y = section_title(c, RIGHT_X, right_y, "Summary", RIGHT_W)
    summary = (
        "Results-driven Data and Software Engineering student at INSEA with practical experience designing and delivering full-stack web applications, "
        "secure backend services, database-driven platforms, mobile applications, and AI-powered analytics solutions. Proficient in PHP, Laravel, JavaScript, "
        "Python, SQL, MySQL, PostgreSQL, React, Next.js, TypeScript, Docker, Git, Linux, Firebase, REST APIs, CI/CD fundamentals, and cloud-oriented development practices. "
        "Strong in building scalable application architectures, authentication and authorization systems, API integrations, relational database models, automated testing, "
        "secure coding, and responsive user interfaces. Combines solid software engineering principles with hands-on project delivery in computer vision, data analytics, "
        "and real-time applications, and is ready to contribute to a full-time junior software engineering, backend, or full-stack development role."
    )
    right_y = wrapped_text(c, summary, RIGHT_X, right_y, RIGHT_W, size=7.25, color=MUTED, leading=9.2) - 10

    right_y = section_title(c, RIGHT_X, right_y, "Experience", RIGHT_W)
    right_y = entry_header(c, "Software Developer", "EasyPsi", "2026", "Casablanca, Morocco", RIGHT_X, right_y, RIGHT_W)
    bullets = [
        "Built a multilingual learning platform in three languages with support for four academic levels and structured lesson delivery across courses, quizzes, and exercises.",
        "Implemented a complete premium system with two access tiers, subscription duration management, payment redirection, and protected educational content.",
        "Created an admin dashboard to manage students, videos, supports, quizzes, and account access from one centralized interface.",
        "Redesigned the user experience across the platform, including the homepage, navigation, progress tracking, email verification, and responsive student flows.",
    ]
    right_y = wrapped_bullets(c, bullets, RIGHT_X + 2, right_y, RIGHT_W - 2, size=7.25, color=MUTED, leading=8.8, gap=3) - 4

    right_y = section_title(c, RIGHT_X, right_y, "Education", RIGHT_W)
    right_y = education_header(
        c,
        "Data and Software Engineering | First-Year Student",
        "Institut National de Statistique et d'Economie Appliquee (INSEA), Rabat",
        "2025 - Present",
        "Rabat, Morocco",
        RIGHT_X,
        right_y,
        RIGHT_W,
    )
    right_y = education_header(
        c,
        "Two-year intensive Mathematics and Physics preparatory program",
        "Classes Preparatoires aux Grandes Ecoles - MP | Lycee Mohammed V, Casablanca",
        "2023 - 2025",
        "Casablanca, Morocco",
        RIGHT_X,
        right_y,
        RIGHT_W,
    )
    right_y = education_header(
        c,
        "Baccalaureat Sciences Mathematiques A",
        "Lycee Tarik Ibno Ziad | Casablanca",
        "2022 - 2023",
        "Casablanca, Morocco",
        RIGHT_X,
        right_y,
        RIGHT_W,
        right_note="Average\n16 / 20",
    ) - 6

    right_y = section_title(c, RIGHT_X, right_y, "Projects", RIGHT_W)
    right_y = project_block(
        c,
        "Educational Learning Platform",
        "Paid Client Project | Laravel, PHP, MySQL, JavaScript, HTML/CSS",
        [
            "Designed the functional structure of an educational platform with course categories, student accounts, protected learning areas, and premium access planning",
            "Planned Laravel backend features with MySQL authentication, validation, database relationships, CRUD management, and admin workflows for courses, lessons, students, and subscriptions",
            "Built responsive user flows for course discovery and structured learning content while applying secure development practices such as input validation, CSRF protection, and access control logic",
        ],
        RIGHT_X,
        right_y,
        RIGHT_W,
    ) - 8
    right_y = project_block(
        c,
        "Football Player Chemistry Analysis System",
        "Python, YOLOv8, OpenCV, Streamlit, Data Analysis",
        [
            "Developed a Python video analytics pipeline using YOLOv8 and OpenCV to detect players and the ball from football match footage",
            "Implemented tracking and positioning logic to analyze movement, proximity, possession, and pass-related interactions between players",
            "Generated chemistry labels, player heatmaps, pass-network visualizations, and a Streamlit dashboard using hybrid chemistry scoring models",
        ],
        RIGHT_X,
        right_y,
        RIGHT_W,
    )

    c.showPage()

    # Page 2
    left_y = PAGE_H - 34
    left_y = section_title(c, MARGIN, left_y, "Skills", LEFT_COL_W)
    left_y = skill_links(c, MARGIN, left_y, "AI, Data & Analytics", ["Pandas", "OpenCV", "YOLOv8", "Streamlit", "Airflow", "Computer Vision", "Object Detection", "Object Tracking"], LEFT_COL_W)
    left_y = skill_links(c, MARGIN, left_y, "Testing & Quality", ["PHPUnit", "Pest", "Clean Code", "Debugging"], LEFT_COL_W)

    left_y = section_title(c, MARGIN, left_y, "Find Me Online", LEFT_COL_W)
    left_y = draw_online_item(c, MARGIN, left_y, "GitHub", "github.com/ayoxic", "G")
    left_y = draw_online_item(c, MARGIN, left_y, "LinkedIn", "linkedin.com/in/ayoubjanina", "in")

    right_y = PAGE_H - 34
    right_y = section_title(c, RIGHT_X, right_y, "Projects", RIGHT_W)
    right_y = project_block(
        c,
        "GPS Treasure Hunt Mobile Application",
        "React Native, JavaScript, Firebase, Geolocation, Realtime Database",
        [
            "Developed a location-based mobile game that unlocks missions when users reach real-world destinations",
            "Implemented Haversine-based distance calculations, sequential mission unlocking, and time-sensitive countdown challenges to structure gameplay",
            "Integrated Firebase anonymous auth session, Realtime Database sync, and local storage persistence to preserve progress across sessions",
        ],
        RIGHT_X,
        right_y,
        RIGHT_W,
    ) - 8
    right_y = project_block(
        c,
        "INSEA Institutional Website",
        "PHP, MySQL, HTML, CSS, JavaScript",
        [
            "Created a bilingual institutional website concept with French and Arabic content for academic programs, partnerships, and student life",
            "Designed an academic information model covering programs, levels, semesters, periods, modules, and subjects",
            "Built responsive PHP pages with MySQL-backed authentication and database connectivity to organize institutional information",
        ],
        RIGHT_X,
        right_y,
        RIGHT_W,
    ) - 8

    right_y = section_title(c, RIGHT_X, right_y, "Certifications", RIGHT_W)
    right_y = certification_item(c, RIGHT_X, right_y, "Data Engineer Associate", "DataCamp")
    right_y = certification_item(c, RIGHT_X, right_y, "Database Programming with SQL", "Oracle Academy")
    right_y = certification_item(c, RIGHT_X, right_y, "Fundamentals of Deep Learning", "NVIDIA Deep Learning Institute")
    right_y = certification_item(c, RIGHT_X, right_y, "Pandas Certificate", "Kaggle")

    c.save()


if __name__ == "__main__":
    build_pdf(Path("output/pdf/ayoub_janina_resume.pdf"))
