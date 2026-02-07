# URL Shortener

A lightweight, self-hosted URL shortening service built with PHP 8.0+. Includes an analytics dashboard for tracking link performance and visitor metrics.

[![PHP Version](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## Overview

This project provides a simple yet robust URL shortening solution with built-in analytics. Originally developed in June 2020, it's designed for ease of deployment and minimal dependencies, making it suitable for personal projects or small to medium-scale deployments.

### Key Features

- Modern PHP 8.0+ with strict type declarations
- PSR-12 coding standards compliance
- RESTful API endpoints for programmatic access
- Analytics dashboard with click tracking and visitor statistics
- Responsive UI with dark/light theme support
- Security-hardened (prepared statements, input validation, XSS protection)

## Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Composer
- Apache with mod_rewrite (or nginx with equivalent configuration)

### Setup

```bash
# Clone the repository
git clone https://github.com/muratcemeren/url-shortener.git
cd url-shortener

# Install dependencies
composer install

# Import database schema
mysql -u username -p database_name < sql/schema.sql

# Configure database connection
cp config/database.php.example config/database.php
# Edit config/database.php with your credentials

# Set permissions (if needed)
chmod -R 755 public/
```

### Development Server

```bash
php -S localhost:8000 -t public
```

### Production Deployment

For production environments, configure Apache virtual host or nginx server block to point to the `public/` directory. Ensure `.htaccess` is enabled for proper URL rewriting.

## Configuration

Edit `config/database.php` to set your database connection parameters:

```php
return [
    'host' => 'localhost',
    'db'   => 'url_shortener',
    'user' => 'your_username',
    'pass' => 'your_password',
    'charset' => 'utf8mb4'
];
```

## Usage

### Web Interface

1. Navigate to your installation URL
2. Enter a long URL in the input field
3. Click "Shorten" to generate a short URL
4. Copy and share the shortened link

### API Endpoints

#### Shorten a URL

```http
POST /api/shorten
Content-Type: application/json

{
  "url": "https://example.com/very/long/url/path"
}

Response:
{
  "success": true,
  "short_url": "http://localhost:8000/abc123",
  "code": "abc123"
}
```

#### Retrieve Statistics

```http
GET /api/stats

Response:
{
  "total_urls": 150,
  "total_clicks": 1234,
  "recent_urls": [...]
}
```

#### List All URLs

```http
GET /api/urls

Response:
[
  {
    "id": 1,
    "original_url": "https://example.com/...",
    "short_code": "abc123",
    "clicks": 45,
    "created_at": "2024-01-15 10:30:00"
  },
  ...
]
```

## Project Structure

```
url-shortener/
├── config/             # Configuration files
│   └── database.php   # Database connection settings
├── public/             # Web root (document root for web server)
│   ├── index.php      # Front controller and routing
│   ├── .htaccess      # Apache rewrite rules
│   └── assets/        # Static assets (CSS, JS)
├── src/                # Application source code
│   ├── Database/      # Database connection (Singleton pattern)
│   └── Services/      # Business logic layer
├── sql/                # Database schema and migrations
│   └── schema.sql     # Initial database structure
├── views/              # View templates
├── vendor/             # Composer dependencies
└── composer.json       # PHP dependencies
```

## Architecture

The application follows a simple MVC-inspired architecture:

- **Database Layer**: Singleton pattern for connection management with PDO
- **Service Layer**: Encapsulates business logic for URL shortening and analytics
- **View Layer**: Server-side rendered templates with minimal JavaScript
- **Routing**: Front controller pattern in `public/index.php`

## Security Considerations

- All database queries use prepared statements to prevent SQL injection
- User input is validated and sanitized before processing
- HTML output is escaped to prevent XSS attacks
- `.htaccess` includes security headers (when using Apache)
- No sensitive data stored in client-side code

## Technology Stack

- **Backend**: PHP 8.0+ with PDO extension
- **Database**: MySQL/MariaDB
- **Frontend**: Vanilla JavaScript, modern CSS
- **Standards**: PSR-12 coding style, PSR-4 autoloading

## Development

Code formatting is enforced using PHP-CS-Fixer:

```bash
# Check code style
composer format-check

# Automatically fix code style
composer format
```

## Credits

Developed and maintained by **Murat Cem Eren**

- GitHub: [@muratcemeren](https://github.com/muratcemeren)
- LinkedIn: [linkedin.com/in/muratcemeren](https://linkedin.com/in/muratcemeren)

## License

This project is licensed under the MIT License. See LICENSE file for details.
