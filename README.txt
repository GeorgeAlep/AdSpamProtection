# Ad Protection System

The Ad Protection System is designed to protect ads from fraudulent clicks by tracking user interactions and blocking suspicious activities. It also provides an admin dashboard for managing blocked IPs, viewing permanently blocked IPs, and configuring the system.

## Prerequisites

- PHP (version 7.4 or higher recommended)
- MySQL (version 5.7 or higher recommended)
- Web server (e.g., Apache, Nginx)
- FingerprintJS (optional for enhanced user tracking)

## Setup

### 1. Database Setup

1. Create a new MySQL database for the project.
2. Import the provided SQL schema into the database. This will set up the necessary tables for the system.

### 2. Configuration

1. Update the `config.php` file with the appropriate database connection details:
   - `$host`: Your database host (usually `localhost`).
   - `$db`: The name of the database you created.
   - `$user`: Your database username.
   - `$pass`: Your database password.
   - `$charset`: The character set (default is `utf8mb4`).

### 3. Admin Credentials

An admin user has been provided for the dashboard with the following details:
- **Username**: `admin`
- **Password**: `user`

For security reasons, the password stored in the database is hashed. If you wish to add new admins, ensure that their passwords are hashed using PHP's `password_hash()` function before storing them in the database.

### 4. Integration

To integrate the Ad Protection System into your website:

1. Place the project files in the desired directory of your website.
2. Include the necessary project files in the sections of your website where you want the ad protection logic to run.
3. Adjust the CSS and frontend components to match the theme and style of your website, if necessary.

## Usage

1. **Admin Dashboard**: Access the admin dashboard by navigating to the `login.php` page. Use the provided admin credentials to log in. From the dashboard, you can:
   - View and manage blocked IP addresses.
   - Permanently block specific IP addresses or ranges.
   - Change the admin password.
   - Configure script settings, including enabling FingerprintJS for enhanced user tracking.

2. **Ad Protection Modes**:
   - **Single Mode**: Blocks a user's IP address if they engage in suspicious activity on a single ad.
   - **All Mode**: Blocks a user's IP address if they engage in suspicious activity across all ads on the website.

3. **Ad Protection**: The system automatically tracks user interactions with ads. Suspicious activities, such as rapid consecutive clicks, will result in the user's IP being temporarily blocked. The system also supports FingerprintJS integration for more accurate user tracking.

4. **Permanent IP Blocking**: From the admin dashboard, you can permanently block specific IP addresses or IP ranges. This is useful for blocking known malicious IPs.

5. **Settings**: The system settings can be adjusted from the admin dashboard. This includes parameters like the click threshold, block duration, and enabling FingerprintJS.

## Security

The system has been designed with security in mind:

- SQL queries use prepared statements to prevent SQL injection.
- User input is validated and sanitized.
- The system protects against Cross-Site Scripting (XSS) attacks.
- Proper authentication and authorization mechanisms are in place for the admin dashboard.
- Passwords are securely hashed using PHP's `password_hash()` function.
- The system supports integration with FingerprintJS for enhanced user tracking.

## Further Development

The system is modular and can be extended with additional features. Some potential enhancements include:

- Integrating with other tracking libraries or services for more comprehensive user tracking.
- Setting up notifications for the website admin when certain thresholds are reached.
- Implementing machine learning algorithms to detect patterns of fraudulent activity.