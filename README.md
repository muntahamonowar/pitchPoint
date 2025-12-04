# PitchPoint

A comprehensive web-based platform connecting entrepreneurs with investors, enabling project pitching, investment management, and collaboration.

(When downloading the zip file, Change the name **pitchPoint-main** to **pitchPoint** )

## Overview

PitchPoint is a multi-role application that facilitates the connection between entrepreneurs and investors. Entrepreneurs can create and manage projects, pitch their ideas, and receive investments. Investors can explore projects, express interest, make investments, and communicate with entrepreneurs. The platform also includes administrative and staff interfaces for managing the system.

## Features

### For Entrepreneurs
- **Project Management**: Create, edit, and manage project pitches
- **Project Showcase**: Upload cover images, pitch decks, and project proposals
- **Profile Management**: Customize profile with company information
- **Messaging**: Communicate with investors and admins
- **Analytics**: Track project performance
- **Project Visibility**: Control project visibility (public/private)

### For Investors
- **Project Exploration**: Browse and search available projects
- **Investment Management**: Make investments and track portfolio
- **Interest Tracking**: Express interest in projects
- **Messaging**: Communicate with entrepreneurs
- **Project Details**: View comprehensive project information

### For Staff
- **Project Review**: Review and approve projects
- **User Management**: Manage users and projects
- **System Administration**: Monitor platform activity

### For Administrators
- **Dashboard**: Overview of platform statistics
- **User Management**: Manage all users (entrepreneurs, investors, staff)
- **Project Management**: Approve, reject, or manage projects
- **Investment Tracking**: Monitor all investments
- **Email Management**: Handle system communications
- **Notifications**: Send system-wide notifications

## Project Structure

```
pitchPoint/
├── auth/                    # Centralized authentication system
│   ├── controller/          # Auth controllers (login, signup, logout)
│   ├── model/               # User, admin, staff models
│   ├── helpers/             # CSRF, rate limiting, mail helpers
│   ├── config/              # Database configuration
│   └── waf/                 # Web Application Firewall
│
├── pitchpoint_entrepreneur/ # Entrepreneur portal
│   ├── public/              # Public-facing pages
│   ├── actions/             # Form handlers and business logic
│   ├── includes/            # Shared includes (header, footer, bootstrap)
│   └── uploads/             # User-uploaded files
│
├── pitchpoint_investor/     # Investor portal
│   ├── app/
│   │   ├── controllers/     # MVC controllers
│   │   ├── models/          # Data models
│   │   ├── views/           # View templates
│   │   └── config/          # Configuration files
│   └── index/               # Front controller
│
├── pitchpoint_staff/        # Staff portal
│   ├── app/                 # Application code
│   └── public/              # Public pages
│
├── pitchpoint_admin/        # Admin portal
│   ├── controllers/         # Admin controllers
│   ├── models/              # Admin models
│   └── views/               # Admin views
│
├── payment/                 # Payment processing
│   └── tests/               # Payment tests
│
└── tests/                   # Global test suite
```

## Requirements

- **PHP**: >= 7.4
- **MySQL**: 5.7+ or MariaDB 10.3+
- **Web Server**: Apache 
- **Composer**: For dependency management
- **PHP Extensions**:
  - PDO
  - PDO_MySQL
  - mbstring
  - openssl
  - session

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd pitchPoint
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Database Setup

1. Create a MySQL database named `pitchpoint`:

```sql
CREATE DATABASE pitchpoint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema (if you have a SQL dump file):

```bash
mysql -u root -p pitchpoint < database_schema.sql
```

### 4. Configure Database Connection

Update the database configuration in:
- `auth/config/db.php`
- `pitchpoint_entrepreneur/includes/db.php`
- `pitchpoint_investor/app/config/database.php`
- `pitchpoint_staff/config/db.php`

Default configuration:
```php
$host = '127.0.0.1';
$db   = 'pitchpoint';
$user = 'root';
$pass = '';
```

### 5. Web Server Configuration

#### Apache (.htaccess)
Ensure mod_rewrite is enabled and configure your virtual host to point to the project root.

#### XAMPP Setup
If using XAMPP, place the project in `htdocs/pitchPoint/` and access via:
- `http://localhost/pitchPoint/auth/loginPortal.php`

### 6. File Permissions

Ensure the uploads directory is writable:

```bash
chmod -R 755 pitchpoint_entrepreneur/uploads/
```

## Configuration

### Base URLs

Update base URLs in the following files if your installation path differs:

- `pitchpoint_entrepreneur/includes/functions.php`: Update `base_url()` and `root_url()` functions
- `pitchpoint_investor/app/config/config.php`: Update `BASE_URL` constant

### Email Configuration

Configure email settings in:
- `auth/helpers/mail.php` - For user verification emails
- `auth/helpers/mailAdmin.php` - For admin verification emails

### Timezone

Default timezone is set to `Europe/Copenhagen` in `pitchpoint_entrepreneur/includes/bootstrap.php`. Update as needed.

## Usage

### Access Points

1. **Guest**: 'pitchPoint\pitchpoint_staff\public\index.php'
    - For guests who did not sign up
1. **Login Portal**: `/pitchPoint/auth/loginPortal.php`
   - Choose between User, Staff, or Admin login

2. **User Login**: `/pitchPoint/auth/login.php`
   - For entrepreneurs and investors


### User Roles

- **Guest**: Can view projects
- **Entrepreneur**: Can create and manage projects
- **Investor**: Can explore projects and make investments
- **Staff**: Can review and manage projects
- **Admin**: Full system access

### Authentication Flow

All authentication is centralized in the `auth/` folder:
- Login: `/pitchPoint/auth/login.php`
- Signup: `/pitchPoint/auth/signUp.php`
- Logout: `/pitchPoint/auth/logout.php`

After login, users are redirected based on their role:
- Entrepreneurs → `/pitchPoint/pitchpoint_entrepreneur/public/index.php`
- Investors → `/pitchPoint/pitchpoint_investor/index/investorindex.php?url=project/explore`
- Staff → `/pitchPoint/pitchpoint_staff/public/index.php`
- Admins → `/pitchPoint/pitchpoint_admin/index.php`

## Security Features

- **CSRF Protection**: All forms use CSRF tokens
- **Rate Limiting**: Login and signup attempts are rate-limited
- **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
- **Email Verification**: Required for account activation
- **WAF (Web Application Firewall)**: Basic protection against common attacks
- **Session Management**: Secure session handling
- **Input Validation**: Server-side validation on all inputs

## Testing

Using PHPUnit directly:

```bash
php vendor/bin/phpunit
```

Test files are located in:
- `auth/tests/` - Authentication tests
- `payment/tests/` - Payment processing tests
- `pitchpoint_investor/tests/` - Investor portal tests
- `tests/` - Global tests

## Database Schema

Key tables:
- `users` - User accounts
- `entrepreneurs` - Entrepreneur profiles
- `investors` - Investor profiles
- `projects` - Project listings
- `messages` - User messages
- `investments` - Investment records
- `categories` - Project categories
- `administrator` - Admin accounts
- `staff` - Staff accounts
- `email_management` - Email communications

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Testing**: PHPUnit
- **Dependency Management**: Composer

## Development

### Code Style

- Follow PSR-4 autoloading standards
- Use strict types: `declare(strict_types=1);`
- Use prepared statements for all database queries
- Escape all output using `htmlspecialchars()` or helper functions

### Helper Functions

Common helper functions available:
- `h($string)` - HTML escape
- `base_url($path)` - Generate URLs for public pages
- `root_url($path)` - Generate URLs for root-level files
- `redirect($path)` - Redirect to a URL
- `csrf_token()` - Generate CSRF token
- `csrf_validate($token)` - Validate CSRF token
- `flash_set($type, $message)` - Set flash message
- `current_user()` - Get current logged-in user
- `require_login()` - Require user to be logged in

## Troubleshooting

### Database Connection Issues

- Verify database credentials in config files
- Ensure MySQL service is running
- Check database name matches configuration
- check port number

### File Upload Issues

- Check `uploads/` directory permissions
- Verify `upload_max_filesize` and `post_max_size` in php.ini

### Session Issues

- Ensure sessions are enabled in PHP
- Check session save path permissions
- Verify session cookie settings

### CSS Not Loading

- Check base URL configuration in `functions.php`
- Verify file paths are correct
- Clear browser cache




