# BPLO System - Installation Guide

## Quick Start Guide

### Step 1: Database Setup

You have **TWO OPTIONS** for setting up the database:

#### Option A: Fresh Installation (Recommended)
If you're starting fresh or want to reset everything:

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "New" to create a database
3. Name it: `bplo_ilagan`
4. Click "Import" tab
5. Choose file: `database/bplo_schema.sql`
6. Click "Go"

**OR via Command Line:**
```bash
mysql -u root -p
CREATE DATABASE bplo_ilagan;
exit;

mysql -u root -p bplo_ilagan < database/bplo_schema.sql
```

#### Option B: Update Existing Database
If you already have the old database and want to add new tables:

1. Open phpMyAdmin
2. Select your `bplo_ilagan` database
3. Click "Import" tab
4. Choose file: `database/update_database.sql`
5. Click "Go"

**OR via Command Line:**
```bash
mysql -u root -p bplo_ilagan < database/update_database.sql
```

### Step 2: Configure Database Connection

1. Open `config/database.php`
2. Update these lines if needed:
```php
private $host = "localhost";
private $db_name = "bplo_ilagan";
private $username = "root";
private $password = "";  // Add your MySQL password here
```

### Step 3: Start the Application

#### Using XAMPP:
1. Copy the project folder to `C:\xampp\htdocs\`
2. Start Apache and MySQL from XAMPP Control Panel
3. Open browser: `http://localhost/System-ad/`

#### Using PHP Built-in Server:
```bash
cd C:\xampp\htdocs\System-ad
php -S localhost:8000
```
Then open: `http://localhost:8000/`

### Step 4: Login

#### Admin Portal
- URL: `http://localhost/System-ad/login.php`
- Username: `admin`
- Password: `admin123`

#### Applicant Portal
- URL: `http://localhost/System-ad/applicant/login.php`
- Email: `applicant@test.com`
- Password: `applicant123`

## Troubleshooting

### Error: "Column not found: 1054 Unknown column 'first_name'"
**Solution:** Your database is missing the new tables. Run the update script:
```bash
mysql -u root -p bplo_ilagan < database/update_database.sql
```

### Error: "Access denied for user 'root'@'localhost'"
**Solution:** Update your password in `config/database.php`

### Error: "SQLSTATE[HY000] [1049] Unknown database 'bplo_ilagan'"
**Solution:** Create the database first:
```sql
CREATE DATABASE bplo_ilagan;
```

### Error: "Notice: Only variables should be passed by reference"
**Solution:** This has been fixed in the latest code. Make sure you have the updated files.

### Blank Page or White Screen
**Solution:** 
1. Enable error reporting by adding to the top of `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
2. Check Apache error logs in `C:\xampp\apache\logs\error.log`

## Database Tables Created

After successful installation, you should have these tables:
- ✅ users (Admin accounts)
- ✅ applicants (Applicant accounts)
- ✅ businesses (Business records)
- ✅ applications (Permit applications)
- ✅ permits (Issued permits)
- ✅ payments (Payment records)
- ✅ renewals (Renewal records)
- ✅ application_documents (Document uploads)

## Verify Installation

1. Login to Admin Portal
2. Check Dashboard - you should see statistics
3. Navigate to Applications menu
4. Navigate to Applicants menu
5. Try logging into Applicant Portal
6. Submit a test application

## Default Test Data

The system includes sample data:
- 3 sample businesses
- 3 sample permits
- 3 sample payments
- 1 admin user
- 1 applicant user
- 2 sample applications

## Security Recommendations

After installation:
1. ✅ Change default admin password
2. ✅ Change default applicant password
3. ✅ Update database credentials
4. ✅ Disable error display in production
5. ✅ Set proper file permissions

## Need Help?

If you encounter any issues:
1. Check the error message carefully
2. Verify database connection in `config/database.php`
3. Ensure all tables are created (check phpMyAdmin)
4. Check Apache/PHP error logs
5. Make sure XAMPP Apache and MySQL are running

## System Requirements

- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7 or higher
- ✅ Apache/Nginx web server
- ✅ PDO PHP Extension
- ✅ Modern web browser (Chrome, Firefox, Edge, Safari)

## File Permissions (Linux/Mac)

```bash
chmod -R 755 .
chmod -R 777 uploads
```

## Success!

If you can see the portal selection page and login successfully, your installation is complete! 🎉
