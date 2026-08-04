# ArenaSync

ArenaSync is a multi-game esports tournament management platform developed for the CSE470 Software Engineering course at BRAC University.

## Technology Stack

- PHP 8.2
- Laravel 12
- Blade Templates
- Tailwind CSS
- MySQL
- JavaScript
- Vite

## Architecture

The project follows the Model-View-Controller (MVC) architecture.

- Model: `app/Models`
- View: `resources/views`
- Controller: `app/Http/Controllers`
- Routes: `routes`
- Database migrations: `database/migrations`
- Tests: `tests`

## Team Feature Ownership

### Member 01 — Tournament Lifecycle & Registration

- F1 Game Management System
- F2 Tournament Creation & Management
- F3 Tournament Category & Status Tracking
- F7 Tournament Registration System
- F8 Participant Approval System

### Member 02 — Team Ecosystem & User Analytics

- F4 Team Creation & Profile Management
- F5 Team Request & Invitation System
- F6 Team Role & Leave Team
- F16 Player & Team Performance History
- F20 Search, Filter & Dashboard

### Member 03 — Payment & Match Setup

- F9 Free/Paid Tournament Setup
- F10 Dummy Payment Submission
- F11 Payment Verification
- F12 Match Schedule & Timeline
- F13 Livestream Link Management

### Member 04 — Results & Tournament Engagement

- F14 Result Submission & Verification
- F15 Leaderboard & Ranking
- F17 Sponsor Management
- F18 Tournament Chat & Announcement
- F19 Verified Organizer Badge

## Local Installation

```bash
git clone <repository-url>
cd ArenaSync-Multi-Game-Esports-Tournament-Management-Platform
composer install
npm install
copy .env.example .env
php artisan key:generate