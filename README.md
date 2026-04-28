<div align="center">

# 🌱 Noble Nest Academy

### A Comprehensive Learning Management System for Early Childhood Development

*Ages 0 – 10 · 1200+ Activities · AI-Powered · Multilingual*

<br>

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-22C55E?style=flat-square)](LICENSE)

<br>

> Noble Nest Academy is a production-ready LMS built with Laravel 12, designed to nurture every child through interactive, multilingual, AI-powered learning — from their first words to their first lines of code.

<br>

[Get Started](#-quick-start) · [View Features](#-features) · [Curriculum](#-curriculum) · [AI Integration](#-ai-integration) · [Contributing](#-contributing)

</div>

---

<br>

## ✨ Features

<br>

| | Feature | Description |
|---|---|---|
| 📚 | **1200+ Activities** | Carefully crafted content spanning 72 monthly units for ages 0–6 |
| 🤖 | **AI-Powered Learning** | Personalized experiences with intelligent content generation |
| 🌍 | **Multilingual** | English · French · Russian · Mandarin · Spanish · Korean |
| 🎮 | **Interactive Tools** | Tracing, games, quizzes, videos, and creative activities |
| 👨‍👩‍👧‍👦 | **Parent Dashboard** | Child progress tracking and family profile management |
| 🏫 | **Educator Portal** | Course management, student analytics, and content authoring |
| 📱 | **Mobile-First UI** | Fully responsive Bootstrap 5 interface for all devices |
| 🎯 | **STEM Curriculum** | Robotics, coding, and design for ages 7–10 |
| 📊 | **Assessment Engine** | IQ and personality assessments with career guidance |
| 🔒 | **COPPA / GDPR** | Parental consent flows and child-safe data policies |

<br>

---

<br>

## 🏗️ Architecture

<br>

### Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 12 · PHP 8.2+ · MySQL 8.0+ |
| **Frontend** | Blade Templates · Bootstrap 5.x · Alpine.js |
| **Caching** | Redis |
| **Media** | S3-compatible storage · HLS streaming |
| **AI** | LLM APIs · TTS · Video synthesis · Image generation |

<br>

### Project Structure

```
noblenest-academy/
│
├── scripts/
│   ├── fetch_repo.php              # GitHub repository fetcher
│   └── install_lms_scaffold.php   # LMS installer
│
├── artisan                         # Root-level Artisan shim
│
└── noblenest-academy/              # Main Laravel 12 application
    ├── app/
    │   ├── Models/
    │   ├── Http/Controllers/
    │   └── Services/
    ├── resources/
    │   ├── views/                  # Blade templates
    │   ├── lang/                   # Localization files
    │   └── fonts/                  # Self-hosted fonts
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── routes/
    ├── .env.example
    └── composer.json
```

> **Why the nested folder?**
> The outer layer is a scaffolding/fetch wrapper. The real app lives inside `noblenest-academy/`. The root-level `artisan` shim automatically delegates every command to `noblenest-academy/artisan` — no manual `cd` required.

<br>

---

<br>

## 🔄 Workflow

<br>

### Platform Workflow

```
┌─────────────────────────────────────────────────────────────────┐
│                        NOBLE NEST ACADEMY                       │
└─────────────────────────────────────────────────────────────────┘

  CONTENT PIPELINE                      LEARNING JOURNEY
  ─────────────────                      ───────────────────────

  Staff Brief                            Child / Parent
      │                                       │
      ▼                                       ▼
  AI Generation ──────────────────►   Age-Based Onboarding
  (Text · TTS · Video · Image)              │
      │                              ┌───────┴────────┐
      ▼                              │                │
  Moderation Queue              Parent (0–6)     STEM (7–10)
      │                              │                │
      ▼                         Activities       Robotics
  Published Content             Assessments      Coding
      │                         Progress         Game Design
      ▼                              │                │
  Redis Cache                        └───────┬────────┘
      │                                      │
      ▼                                      ▼
  Served via CDN                    Parent Dashboard
                                    + Educator Portal
                                          │
                                          ▼
                                   Reports & Badges
```

<br>

### AI Content Generation Pipeline

```
  1. AUTHOR       Staff creates seed briefs and activity templates
       │
       ▼
  2. GENERATE     AI produces text, video, images, and TTS audio
       │
       ▼
  3. MODERATE     Outputs reviewed by safety moderation queue
       │
       ▼
  4. PUBLISH      Approved content goes live per locale and age tier
```

<br>

---

<br>

## 🚀 Quick Start

<br>

### Prerequisites

- PHP **8.2+** with extensions: `pdo` `pdo_mysql` `openssl` `mbstring` `tokenizer` `xml` `ctype` `json` `bcmath` `fileinfo`
- Composer **2.x**
- MySQL **8.0+** or MariaDB **10.6+**
- Node.js **18+** & npm

<br>

### Option 1 — Standard Setup

**1. Install PHP dependencies**

```bash
cd noblenest-academy
composer install
```

**2. Create environment file**

```bash
cp .env.example .env
php artisan key:generate
```

**3. Configure the database**

Edit `noblenest-academy/.env` with your credentials:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=noblenest
DB_USERNAME=noblenest
DB_PASSWORD=your_password
```

Then create the database in MySQL:

```sql
CREATE DATABASE noblenest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'noblenest'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON noblenest.* TO 'noblenest'@'localhost';
FLUSH PRIVILEGES;
```

**4. Run migrations and seeders**

```bash
php artisan migrate --seed
```

**5. Build front-end assets** *(optional)*

```bash
npm install && npm run build
```

**6. Start the development server**

```bash
php artisan serve
```

Open **http://localhost:8000** in your browser.

<br>

### Option 2 — Fetch & Install Scripts

```bash
# Fetch the latest Laravel app
php scripts/fetch_repo.php

# Install LMS scaffold
php scripts/install_lms_scaffold.php --dest=noblenest-academy

# Then complete setup as per Option 1
```

<br>

### Running Artisan from the Repository Root

The root shim proxies commands directly to the Laravel app:

```bash
# All equivalent to: cd noblenest-academy && php artisan <command>
php artisan migrate
php artisan serve
php artisan tinker
```

<br>

---

<br>

## 🔑 Default Credentials

> All default passwords are `Password1!`

| Role | Email |
|---|---|
| Admin | `admin@noblenest.test` |
| Teacher | `teacher@noblenest.test` |
| Parent | `parent@noblenest.test` |
| Student | `student@noblenest.test` |

<br>

---

<br>

## 📚 Curriculum

<br>

### Parent Academy *(0–6 years)*

- Foundations of Early Childhood Psychology
- Etiquette & Mannerism Training
- Royal Etiquette for Children
- Cultural Parenting Styles — Japanese · Chinese · Scandinavian
- Safety, Hygiene & Independence Building

<br>

### Early Years Curriculum *(0–6 years)*

72 monthly units · 1200+ activities across 8 core domains:

| Domain | Domain |
|---|---|
| Language & Literacy | Numeracy & Early Math |
| Cognitive Development | Fine & Gross Motor Skills |
| Social-Emotional Learning | Creative Arts |
| Cultural Studies | Practical Life Skills |

**Language Training** *(EN · FR · RU · ZH · ES · KO)*

- Phonics & Letter Formation
- Vocabulary & Dialogues
- Stroke-order Tracing *(Mandarin)*
- Pronunciation with TTS Recording & Playback

<br>

### STEM Pathway *(7–10 years)*

- Algorithmic Puzzles
- Robotics Simulation *(Level 1 & 2)*
- Block-Based Coding with Blockly
- Game Design Basics
- Web Basics with Bootstrap
- Electronics Foundations

<br>

### Assessments

| Type | Details |
|---|---|
| **IQ Testing** | Pattern recognition, spatial rotation, analogies |
| **Personality Profile** | Kid-safe Big Five assessment |
| **Learning Reports** | Career cluster recommendations |

<br>

---

<br>

## 🎨 User Interface

Built with **Bootstrap 5.x** and **Blade Templates**:

- Mobile-first responsive design
- WCAG 2.1 accessibility-first components
- Gamification — badges, streaks, and certificates
- Locale switcher for multilingual support
- AI-powered onboarding assistant

**Fonts**

| Font | Usage |
|---|---|
| **Baloo 2** | Display headings — child and parent surfaces |
| **Nunito** | Body text — parent and child interfaces |
| **Inter** | Admin and teacher dashboards |

<br>

---

<br>

## 🤖 AI Integration

<br>

### AI Assistant

- First-login onboarding guide
- Multilingual support *(EN · FR · RU · ZH · ES · KO)*
- Child-safe conversation policies
- Age-appropriate activity recommendations

<br>

### Supported AI Providers

| Capability | Purpose |
|---|---|
| **LLM** | Text generation and translations |
| **TTS** | Multilingual audio narration |
| **Video** | Lesson video synthesis |
| **Image** | Illustration generation |

<br>

---

<br>

## 🔒 Security & Compliance

| Area | Implementation |
|---|---|
| **COPPA / GDPR** | Parental consent flows and data minimization |
| **Content Moderation** | Safety blocklists and human review queue |
| **Age Gating** | Activity supervision requirements |
| **Data Privacy** | Encrypted storage and retention policies |
| **Account Management** | User deletion and full data export |

<br>

---

<br>

## 📊 Accreditation

Noble Nest Academy maps to international early years frameworks:

- **EYFS** — England Early Years Foundation Stage
- **National Frameworks** — Regional early childhood standards
- **SCORM / xAPI** — Export formats for external review and LRS integration

<br>

---

<br>

## 💰 Pricing

| Plan | Price | Includes |
|---|---|---|
| **Free** | $0 | Limited preview · 1 child profile |
| **Starter** | $7–9 / mo | Full 0–6 curriculum · 1 child |
| **Family** | $14–19 / mo | Up to 4 child profiles · all languages · STEM |
| **School / Center** | Custom | Per-seat pricing · SSO · analytics dashboards |

<br>

---

<br>

## 🛠️ Development

<br>

### Running Tests

```bash
php artisan test
```

### Code Quality

```bash
php artisan pint              # Code formatting
php ./vendor/bin/phpstan      # Static analysis
```

### CI / CD

GitHub Actions pipeline runs on every push:

1. PHPUnit test suite
2. Pint code formatting check
3. PHPStan static analysis
4. Automated deployment to staging / production

<br>

---

<br>

## 📖 Documentation

- [Laravel Documentation](https://laravel.com/docs) — Framework guide
- [Bootstrap 5 Docs](https://getbootstrap.com/docs) — UI components
- [Alpine.js Docs](https://alpinejs.dev) — Lightweight reactivity

<br>

---

<br>

## 🤝 Contributing

Contributions are welcome. Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes with clear messages
4. Open a pull request against `main`

<br>

---

<br>

## 🆘 Support

| Channel | Link |
|---|---|
| 📝 Existing issues | [GitHub Issues](https://github.com/fahmadiqbal1/noblenest-academy/issues) |
| 💬 Discussions | [GitHub Discussions](https://github.com/fahmadiqbal1/noblenest-academy/discussions) |
| 🐛 Report a bug | [New Issue](https://github.com/fahmadiqbal1/noblenest-academy/issues/new) |
| 📞 Owner | [@fahmadiqbal1](https://github.com/fahmadiqbal1) |

<br>

---

<br>

<div align="center">

**Noble Nest Academy** · Built with ❤️ for early childhood education

*Laravel 12 · Bootstrap 5 · PHP 8.2+*

</div>

