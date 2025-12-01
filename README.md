# BPLO Ilagan - Business Permit and Licensing Office System

A comprehensive web-based system for managing business permits and licensing operations with dual portals for Admin and Applicants.

## Features

### Admin Portal
- **Dashboard** - Overview with statistics (businesses, applications, payments, permits, renewals)
- **Business Management** - Full CRUD operations for registered businesses
- **Application Management** - Review, approve/reject applications with status tracking
- **Document Verification** - Approve or reject submitted documents
- **Permit Management** - Issue, manage, and track business permits
- **Payment Management** - Verify payments, approve/reject transactions
- **Renewal Management** - Handle permit renewals
- **Applicant Management** - View and manage applicant accounts
- **User Management** - Admin user account management

### Applicant Portal
- **Registration & Login** - Secure applicant account creation
- **Dashboard** - Personal application statistics
- **New Application** - Submit business permit applications
- **Application Tracking** - View status of submitted applications
- **Profile Management** - Update personal information

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5, JavaScript
- **Icons**: Material Design Icons
- **Architecture**: MVC Pattern with PDO

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- phpMyAdmin (optional)

### Setup Instructions

1. **Clone or Download the Project**
   ```bash
   git clone <repository-url>
   cd bplo-ilagan
   ```

2. **Database Setup**
   - Open phpMyAdmin or MySQL command line
   - Create a new database named `bplo_ilagan`
   - Import the database schema:
     ```bash
     mysql -u root -p bplo_ilagan < database/bplo_schema.sql
     ```
   - Or import via phpMyAdmin: Import > Choose file > `database/bplo_schema.sql`

3. **Configure Database Connection**
   - Open `config/database.php`
   - Update the database credentials:
     ```php
     private $host = "localhost";
     private $db_name = "bplo_ilagan";
     private $username = "root";
     private $password = "your_password";
     ```

4. **Set Permissions** (Linux/Mac)
   ```bash
   chmod -R 755 .
   chmod -R 777 uploads
   ```

5. **Start the Server**
   
   **Using PHP Built-in Server:**
   ```bash
   php -S localhost:8000
   ```
   
   **Using XAMPP/WAMP:**
   - Copy project to `htdocs` or `www` folder
   - Access via `http://localhost/bplo-ilagan`

## Default Login Credentials

### Admin Portal
- URL: `http://localhost:8000/login.php`
- Username: `admin`
- Password: `admin123`

### Applicant Portal
- URL: `http://localhost:8000/applicant/login.php`
- Email: `applicant@test.com`
- Password: `applicant123`

**⚠️ IMPORTANT: Change default passwords after first login!**

## Project Structure

```
bplo-ilagan/
├── config/
│   ├── database.php          # Database configuration
│   └── session.php           # Session management
├── modules/
│   ├── business.php          # Business CRUD operations
│   ├── permit.php            # Permit management
│   ├── payment.php           # Payment processing
│   ├── renewal.php           # Renewal operations
│   ├── user.php              # User management
│   ├── applicant.php         # Applicant operations
│   └── application.php       # Application management
├── includes/
│   ├── navbar.php            # Top navigation bar
│   ├── sidebar.php           # Side menu
│   └── footer.php            # Footer
├── applicant/
│   ├── login.php             # Applicant login
│   ├── register.php          # Applicant registration
│   ├── dashboard.php         # Applicant dashboard
│   ├── applications.php      # View applications
│   ├── new_application.php   # Submit new application
│   └── logout.php            # Logout
├── database/
│   └── bplo_schema.sql       # Database schema
├── src/
│   └── assets/               # CSS, JS, images
├── login.php                 # Admin login
├── dashboard.php             # Admin dashboard
├── businesses.php            # Business management
├── permits.php               # Permit management
├── payments.php              # Payment management
├── renewals.php              # Renewal management
├── applications.php          # Application management
├── applicants.php            # Applicant management
├── users.php                 # User management
└── logout.php                # Admin logout
```

## Database Schema

### Main Tables
- `users` - Admin user accounts
- `applicants` - Applicant accounts
- `businesses` - Registered businesses
- `applications` - Business permit applications
- `permits` - Issued permits
- `payments` - Payment records
- `renewals` - Renewal records
- `application_documents` - Document uploads

## Usage Guide

### For Administrators

1. **Login** to admin portal
2. **Review Applications** from the Applications menu
3. **Verify Documents** - Check uploaded documents
4. **Verify Payments** - Confirm payment receipts
5. **Update Status** - Approve or reject applications
6. **Issue Permits** - Generate permits for approved applications
7. **Manage Businesses** - Add/edit business information
8. **Track Renewals** - Monitor permit expiration dates

### For Applicants

1. **Register** an account on the applicant portal
2. **Login** with your credentials
3. **Submit Application** - Fill out business permit form
4. **Upload Documents** - Attach required documents
5. **Track Status** - Monitor application progress
6. **View Results** - Check approval/rejection status

## Security Features

- Password hashing using PHP `password_hash()`
- SQL injection prevention with PDO prepared statements
- XSS protection with `htmlspecialchars()`
- Session-based authentication
- Role-based access control (Admin/Staff)
- Input validation and sanitization

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Troubleshooting

### Database Connection Error
- Check database credentials in `config/database.php`
- Ensure MySQL service is running
- Verify database exists

### Login Issues
- Clear browser cache and cookies
- Check if session is enabled in PHP
- Verify credentials in database

### Permission Denied
- Check file permissions (755 for folders, 644 for files)
- Ensure web server has read/write access

## Future Enhancements

- Email notifications for application status
- SMS alerts for permit expiration
- Online payment integration
- Document upload functionality
- Report generation (PDF)
- Advanced search and filtering
- Audit trail logging
- Multi-language support

## Support

For issues and questions:
- Email: support@bplo-ilagan.gov.ph
- Phone: (078) XXX-XXXX

## License

Copyright © 2024 LGU Ilagan - BPLO. All rights reserved.

## Credits

Developed for LGU Ilagan Business Permit and Licensing Office
