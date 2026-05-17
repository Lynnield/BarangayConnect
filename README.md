# BarangayConnect

BarangayConnect is a professional, modern SaaS-style Document Request and Appointment System designed for local government units (Barangays). Built with Laravel 11 and Tailwind CSS 4.0.

## 🚀 Key Features

- **Modern Dashboard**: Responsive UI with dark mode support.
- **Authentication & Security**:
    - Multi-Factor Authentication (MFA) via Email OTP or Authenticator App (TOTP).
    - Recovery codes for account access.
    - Security headers (CSP, HSTS, X-Frame-Options) and middleware protection.
    - Admin Impersonation (Login-as-User) for troubleshooting.
- **Warning & Alert System**: Context-aware system alerts for overdue appointments, delayed requests, and security risks.
- **Document Management**:
    - Request processing workflow (Pending → Under Review → Ready → Released).
    - Automated PDF generation for clearances and certificates.
- **Appointment System**: Slot-based appointment scheduling with calendar view.
- **Data Management**:
    - Robust CSV/JSON Import & Export with detailed validation reporting.
    - Automated daily database and weekly file backups.
- **Reporting**: Scheduled daily, weekly, and monthly reports delivered via email.

## 🛠 Tech Stack

- **Framework**: Laravel 11
- **Frontend**: Tailwind CSS 4.0, Lucide Icons
- **Database**: SQLite (default), MySQL compatible
- **Tools**: Vite, Chart.js, DomPDF, Google2FA

## 📥 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Setup Steps
1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-repo/barangay-connect.git
   cd barangay-connect
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize Database**:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Build Assets**:
   ```bash
   npm run build
   ```

6. **Start Development Server**:
   ```bash
   php artisan serve
   ```

## ⚙️ Configuration

### MFA Setup
Ensure your mail driver is configured in `.env` to receive Email OTPs. For Authenticator apps, the system generates TOTP secrets automatically.

### Automated Tasks
Configure a cron job to run the Laravel scheduler every minute:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Security Headers
Adjust security settings in `config/security.php` or via `.env`:
- `SECURITY_CSP_ENABLED=true`
- `SECURITY_HSTS_ENABLED=true` (Production only)

## 📖 Usage Instructions

### Admin Panel
- **Dashboard**: View real-time stats and system warnings.
- **Residents**: Import community members using the CSV template.
- **Requests**: Manage document applications and generate PDFs.
- **Backups**: Access manual and automated backups from Settings.

### Resident Portal
- **Requests**: Submit new document requests and track status.
- **Appointments**: Schedule visits for document pickup.
- **Security**: Enable MFA from the Account Security settings.

## 📄 Documentation

- **API Routes**: Defined in `routes/web.php` and `routes/console.php`.
- **Database Schema**: Managed via migrations in `database/migrations`.
- **Services**: Business logic is centralized in `app/Services/`.

## 🛡 Security & Audit
All sensitive actions (logins, MFA changes, data exports) are logged in the `Audit Logs` accessible via the Admin panel.

---
© 2026 BarangayConnect. All rights reserved.
