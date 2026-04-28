
Noble Nest Academy - Learning Management System PHP Laravel Bootstrap

Noble Nest Academy is a comprehensive Learning Management System (LMS) designed specifically for early childhood development (ages 0-10). Our platform features 1200+ multilingual interactive activities, AI-powered content generation, and personalized learning experiences.

🌟 Core Features
📚 1200+ Multilingual Activities - Carefully crafted content for ages 0-6 across multiple domains
🤖 AI-Powered Learning - Personalized experiences and intelligent content generation
🌍 Multilingual Support - English, French, Russian, Mandarin Chinese, Spanish, Korean
🎮 Interactive Tools - Tracing, games, quizzes, videos, and creative activities
👨‍👩‍👧‍👦 Parent Dashboard - Track child progress and manage family profiles
🏫 Educator Portal - Course management and student analytics
📱 Mobile-First Design - Responsive Bootstrap 5 UI for all devices
🎯 STEM Curriculum - Robotics, coding, and design for ages 7-10
📊 Assessment Engine - IQ and personality assessments with career guidance
🏗️ Architecture Overview

Tech Stack

Backend: Laravel 12 + PHP 8.2+ with MySQL 8.0+
Frontend: Blade Templates + Bootstrap 5.x + Alpine.js
Database: MySQL/MariaDB with Redis caching
Media: S3-compatible object storage with HLS streaming
AI Integration: LLM APIs for content generation, TTS, video synthesis

Project Structure

noblenest-academy/ ├── README.md (this file) ├── scripts/ │ ├── fetch_repo.php # GitHub repository fetcher │ └── install_lms_scaffold.php # LMS installer ├── artisan # Root-level Artisan shim └── noblenest-academy/ # Main Laravel 12 application ├── app/ │ ├── Models/ │ ├── Http/Controllers/ │ └── Services/ ├── resources/ │ ├── views/ # Blade templates │ ├── lang/ # Localization files │ └── fonts/ # Self-hosted fonts ├── database/ │ ├── migrations/ │ └── seeders/ ├── routes/ ├── .env.example └── composer.json


## Quick Start (for developers)

This repository contains two things in the same directory tree:

1. **Root meta-layer** – a shim `artisan` and helper scripts that let you run
   `php artisan <command>` from the repo root without `cd`-ing into the app folder.
2. **`noblenest-academy/` subfolder** – the actual Laravel 12 application.

> **Why the nested folder?**  
> The outer layer is a scaffolding/fetch wrapper (see *GitHub Repo Fetcher* section
> below). The real app lives inside `noblenest-academy/`. The root-level `artisan`
> shim automatically delegates every command to `noblenest-academy/artisan`, so you
> never need to change directory manually.

### Prerequisites

- **PHP 8.2+** with extensions: `pdo`, `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Composer 2.x**
- **MySQL 8.0+** or **MariaDB 10.6+**
- **Node.js 18+** & npm (for front-end assets)

### Installation

#### Option 1: Quick Setup (from repo root)

# 1. Install PHP dependencies
cd noblenest-academy
composer install

# 2. Create environment file
cp .env.example .env
php artisan key:generate

# 3. Configure database
# Edit noblenest-academy/.env with your MySQL credentials


Edit `noblenest-academy/.env` and set your MySQL credentials:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=noblenest
DB_USERNAME=noblenest
DB_PASSWORD=your_password
```

Create the database in MySQL first:

```sql
CREATE DATABASE noblenest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'noblenest'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON noblenest.* TO 'noblenest'@'localhost';
FLUSH PRIVILEGES;
```

# 4. Run migrations and seeders
php artisan migrate --seed

# 5. Build front-end assets (optional)
npm install && npm run build

# 6. Start development server
php artisan serve

# Open http://localhost:8000 in your browser

Option 2: Using Fetch & Install Scripts

# Fetch the latest Laravel app
php scripts/fetch_repo.php

# Install LMS scaffold
php scripts/install_lms_scaffold.php --dest=noblenest-academy

# Complete setup as per Option 1

Running Artisan from Repository Root
The root artisan shim proxies commands to the Laravel app:

# From repo root - equivalent to cd noblenest-academy && php artisan
php artisan migrate
php artisan serve
php artisan tinker

🔑 Default Login Credentials
All default passwords are Password1!

Role	Email
Admin	admin@noblenest.test
Teacher	teacher@noblenest.test
Parent	parent@noblenest.test
Student	student@noblenest.test
📚 Curriculum Overview
Parent Academy (0–6 years)
Foundations of Early Childhood Psychology
Etiquette & Mannerism Training
Royal Etiquette for Children
Cultural Parenting Styles (Japanese, Chinese, Scandinavian)
Safety, Hygiene & Independence Building
Early Years Curriculum (0–6)
72 monthly units with 1200+ activities across domains:

Language & Literacy
Numeracy & Early Math
Cognitive Development
Fine & Gross Motor Skills
Social-Emotional Learning
Creative Arts
Cultural Studies
Practical Life Skills
Language Training (EN, FR, RU, ZH, ES, KO)
Phonics & Letter Formation
Vocabulary & Dialogues
Stroke-order Tracing (Mandarin)
Pronunciation & TTS
Recording & Playback
STEM Pathway (7–10)
Algorithmic Puzzles
Robotics Simulation (L1, L2)
Block-Based Coding with Blockly
Game Design Basics
Web Basics with Bootstrap
Electronics Foundations
Assessments
IQ Testing: Pattern recognition, spatial rotation, analogies
Personality Profile: Kid-safe Big-Five assessment
Learning Reports: Career cluster recommendations
🎨 User Interface
Built with Bootstrap 5.x and Blade Templates:

Mobile-first responsive design
Accessibility-first components (WCAG 2.1)
Gamification with badges, streaks, and certificates
Locale switcher for multilingual support
AI-powered onboarding assistant
Fonts
Baloo 2: Display headings (kid/parent surfaces)
Nunito: Body text (parent/kid interfaces)
Inter: Admin/teacher dashboards
Download fonts guide

🤖 AI Integration
Content Generation Pipeline
Authoring: Staff creates seed briefs
Generation: AI generates text, video, images, TTS
Moderation: AI outputs reviewed by moderation queue
Publishing: Approved content published per locale
AI Assistant
First-login onboarding guide
Multilingual support (EN, FR, RU, ZH, ES, KO)
Child-safe conversation policies
Age-appropriate recommendations
Supported Providers
LLM: Text generation & translations
TTS: Multilingual audio narration
Video: Lesson video synthesis
Image: Illustration generation
🔒 Security & Compliance
COPPA/GDPR Aligned: Parental consent flows and data minimization
Content Moderation: Safety blocklists and human review
Age Gating: Activity supervision requirements
Data Privacy: Encrypted storage and retention policies
Account Management: User deletion and data export options
📊 Accreditation
Noble Nest Academy maps to international early years frameworks:

EYFS (England)
National Frameworks (Regional standards)
SCORM/xAPI (Export formats for external review)
💰 Pricing Strategy
Plan	Price	Features
Free	$0	Limited preview, 1 child profile
Starter	$7–9/mo	Full 0–6 curriculum for 1 child
Family	$14–19/mo	Up to 4 child profiles, all languages, STEM
School/Center	Custom	Per-seat pricing, SSO, dashboards
🛠️ Development
Running Tests
bash
php artisan test
Code Quality
bash
php artisan pint              # Code formatting
php ./vendor/bin/phpstan      # Static analysis
CI/CD
GitHub Actions pipeline runs:

PHPUnit tests
Pint code formatting
PHPStan static analysis
Automated deployment to staging/production
📖 Documentation
Laravel Documentation - Framework guide
Bootstrap 5 Docs - UI components
LocalizationGuide - Multilingual setup (if available)
🤝 Contributing
Contributions are welcome! Please review our Contributing Guidelines (if available).

📄 License
The Laravel framework is licensed under the MIT License.

🆘 Support
For issues and questions:

📝 Check existing GitHub Issues
💬 Start a Discussion
🐛 Report a Bug
📞 Contact
Owner: @fahmadiqbal1
Repository: noblenest-academy
Built with ❤️ for early childhood education

Code

---

## **Recommended Repository Metadata**

### **Description** (160 chars max):
Comprehensive LMS for early childhood development (0-10 years) with 1200+ multilingual AI-powered activities, built with Laravel & Bootstrap.

