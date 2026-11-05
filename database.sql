-- ==========================================================
-- Database Schema for Phishing Simulation Project
-- Compatible with XAMPP MySQL / MariaDB & phpMyAdmin
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `phishing_simulation`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `phishing_simulation`;

-- ----------------------------------------------------------
-- 1. Table structure for table `users` (Admin Authentication)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. Table structure for table `settings` (SMTP & API Config)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `smtp_host` VARCHAR(255) DEFAULT 'smtp-relay.brevo.com',
    `smtp_user` VARCHAR(255) DEFAULT '',
    `smtp_pass` VARCHAR(255) DEFAULT '',
    `smtp_port` INT DEFAULT 587,
    `smtp_crypto` VARCHAR(20) DEFAULT 'tls',
    `brevo_api_key` VARCHAR(255) DEFAULT '',
    `from_email` VARCHAR(255) DEFAULT 'nobitanobi0272@gmail.com',
    `from_name` VARCHAR(255) DEFAULT 'Ravindranath Yarlagadda',
    `admin_server_url` VARCHAR(255) DEFAULT 'http://10.10.0.190/phishing-simulation',
    `phish_server_url` VARCHAR(255) DEFAULT NULL,
    `cloak_phish_domain` TINYINT(1) DEFAULT 1,
    `admin_bypass_key` VARCHAR(100) DEFAULT 'phishguard-bypass',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. Table structure for table `targets` (Tracked Employees/Targets)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `targets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `first_name` VARCHAR(100) DEFAULT NULL,
    `last_name` VARCHAR(100) DEFAULT NULL,
    `name` VARCHAR(150) DEFAULT NULL,
    `department` VARCHAR(100) DEFAULT NULL,
    `position` VARCHAR(100) DEFAULT NULL,
    `sent` TINYINT(1) DEFAULT 0,
    `opened` TINYINT(1) DEFAULT 0,
    `clicked` TINYINT(1) DEFAULT 0,
    `submitted` TINYINT(1) DEFAULT 0,
    `submitted_data_payload` TEXT DEFAULT NULL,
    `reported` TINYINT(1) DEFAULT 0,
    `reported_at` DATETIME DEFAULT NULL,
    `last_activity` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. Table structure for table `tracking_logs` (Event Logs)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tracking_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `campaign_id` INT DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `details` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_camp_id` (`campaign_id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. Table structure for table `email_templates` (Gophish-style Templates)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_templates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL UNIQUE,
    `subject` VARCHAR(255) NOT NULL,
    `sender_name` VARCHAR(150) DEFAULT 'Ravindranath Yarlagadda',
    `sender_email` VARCHAR(255) DEFAULT '',
    `body_html` MEDIUMTEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. Table structure for table `campaigns` (Campaigns History)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campaigns` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `template_name` VARCHAR(150) NULL,
    `include_report_link` TINYINT(1) DEFAULT 0,
    `phish_server_url` VARCHAR(255) NULL DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT 'In progress',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `completed_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 7. Table structure for table `campaign_targets` (Campaign Specific Enrolled Targets)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campaign_targets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `campaign_id` INT NOT NULL,
    `target_id` INT DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) DEFAULT NULL,
    `last_name` VARCHAR(100) DEFAULT NULL,
    `position` VARCHAR(100) DEFAULT NULL,
    `department` VARCHAR(100) DEFAULT NULL,
    `sent` TINYINT(1) DEFAULT 0,
    `opened` TINYINT(1) DEFAULT 0,
    `clicked` TINYINT(1) DEFAULT 0,
    `submitted` TINYINT(1) DEFAULT 0,
    `submitted_data_payload` TEXT DEFAULT NULL,
    `reported` TINYINT(1) DEFAULT 0,
    `reported_at` DATETIME DEFAULT NULL,
    `last_activity` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ct_camp_id` (`campaign_id`),
    INDEX `idx_ct_camp_email` (`campaign_id`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 8. Table structure for table `quiz_results` (Awareness Quiz)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quiz_results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) DEFAULT NULL,
    `score` INT NOT NULL DEFAULT 0,
    `total` INT NOT NULL DEFAULT 3,
    `passed` TINYINT(1) NOT NULL DEFAULT 0,
    `q1_answer` VARCHAR(50) DEFAULT NULL,
    `q2_answer` VARCHAR(50) DEFAULT NULL,
    `q3_answer` VARCHAR(50) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Seed Initial / Migrated Data
-- ----------------------------------------------------------

-- Default Admin User
INSERT INTO `users` (`username`, `password_hash`) 
VALUES ('admin', '$2y$10$uI2AcjTvUlcZs4G7BIrYWuQ8aoe7maHBsW7zn22CvdfguGXn4tSNW')
ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`);

-- Settings
INSERT INTO `settings` (`id`, `smtp_host`, `smtp_user`, `smtp_pass`, `smtp_port`, `smtp_crypto`, `brevo_api_key`, `from_email`, `from_name`)
VALUES (1, 'smtp.gmail.com', 'nobitanobi0272@gmail.com', 'bbajqzhbaclrzbux', 587, 'tls', '', 'nobitanobi0272@gmail.com', 'Ravindranath Yarlagadda')
ON DUPLICATE KEY UPDATE 
    `smtp_host` = VALUES(`smtp_host`),
    `smtp_user` = VALUES(`smtp_user`),
    `smtp_pass` = VALUES(`smtp_pass`),
    `smtp_port` = VALUES(`smtp_port`),
    `smtp_crypto` = VALUES(`smtp_crypto`),
    `brevo_api_key` = VALUES(`brevo_api_key`),
    `from_email` = VALUES(`from_email`),
    `from_name` = VALUES(`from_name`);

-- Default Campaign
INSERT INTO `campaigns` (`id`, `name`, `template_name`, `status`)
VALUES (1, 'Q4 Employee Phishing Awareness Assessment', 'IT Security Alert', 'In progress')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Email Templates
INSERT INTO `email_templates` (`id`, `name`, `subject`, `sender_name`, `body_html`) VALUES
(1, 'IT Security Password Expiry', 'Action Required: Microsoft 365 / Corporate Password Expiry Notice', 'Ravindranath Yarlagadda', 
'<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
  <div style="background-color: #0078d4; color: white; padding: 20px;">
    <h2 style="margin: 0; font-size: 20px;">IT Information Security Notice</h2>
    <p style="margin: 5px 0 0; font-size: 13px; opacity: 0.9;">Identity & Access Management</p>
  </div>
  <div style="padding: 24px; color: #334155; line-height: 1.6;">
    <p>Dear {{.FirstName}},</p>
    <p>Your network account password is scheduled to expire in <strong>24 hours</strong>. To maintain uninterrupted access to your email, cloud drive, and corporate apps, please verify your credentials immediately.</p>
    <div style="text-align: center; margin: 28px 0;">
      <a href="{{.URL}}" style="background-color: #0078d4; color: white; padding: 12px 28px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">Keep Current Password / Verify Access</a>
    </div>
    <p style="font-size: 13px; color: #64748b;">If no action is taken, your access will be temporarily locked.</p>
    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
    <p style="font-size: 12px; color: #94a3b8;">IT Security Dept &bull; Internal Simulation Awareness</p>
  </div>
  <img src="{{.TrackingURL}}" alt="" width="1" height="1" border="0" style="display:inline-block;width:1px!important;height:1px!important;border:none;margin:0;padding:0;" />
</div>'),

(2, 'HR Remote Work Policy', 'Important: Mandatory HR Policy Update & Employee Acknowledgement', 'Ravindranath Yarlagadda',
'<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
  <div style="background-color: #10b981; color: white; padding: 20px;">
    <h2 style="margin: 0; font-size: 20px;">Human Resources Department</h2>
    <p style="margin: 5px 0 0; font-size: 13px; opacity: 0.9;">Workplace Policy Update</p>
  </div>
  <div style="padding: 24px; color: #334155; line-height: 1.6;">
    <p>Hello {{.FirstName}},</p>
    <p>Human Resources has released an updated Remote Work Policy and Health Benefit Schedule. All employees are required to review the policy and acknowledge receipt.</p>
    <div style="text-align: center; margin: 28px 0;">
      <a href="{{.URL}}" style="background-color: #10b981; color: white; padding: 12px 28px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">Review & Sign Policy Document</a>
    </div>
    <p style="font-size: 13px; color: #64748b;">Please complete this within 2 business days.</p>
    <p style="margin-top: 20px;">Regards,<br>Human Resources</p>
  </div>
  <img src="{{.TrackingURL}}" alt="" width="1" height="1" border="0" style="display:inline-block;width:1px!important;height:1px!important;border:none;margin:0;padding:0;" />
</div>'),

(3, 'Finance Payroll Deposit', 'Urgent: Discrepancy in Direct Deposit Banking Information', 'Ravindranath Yarlagadda',
'<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; border-top: 5px solid #f59e0b;">
  <div style="padding: 24px; color: #334155; line-height: 1.6;">
    <h3 style="color: #b45309; margin-top: 0;">Payroll Notification: Action Required</h3>
    <p>Dear {{.FirstName}},</p>
    <p style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 10px 14px; color: #92400e;">
      We noticed an unverified discrepancy in your direct deposit account information for the upcoming payroll run.
    </p>
    <p>Please log in securely to verify your payout information to ensure on-time salary disbursement.</p>
    <div style="text-align: center; margin: 25px 0;">
      <a href="{{.URL}}" style="background-color: #d97706; color: white; padding: 12px 26px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">Verify Direct Deposit Details</a>
    </div>
    <p style="font-size: 12px; color: #94a3b8;">Payroll Operations &bull; Strictly Confidential</p>
  </div>
  <img src="{{.TrackingURL}}" alt="" width="1" height="1" border="0" style="display:inline-block;width:1px!important;height:1px!important;border:none;margin:0;padding:0;" />
</div>'),

(4, 'Evidence Photo Scan (Attachment)', 'FWD: Hidden content found in employee photo', 'Ravindranath Yarlagadda',
'<div style="font-family: Arial, sans-serif; color: #333; background-color: #fff; padding: 15px;">
  <p>Hi,</p>
  <p>I found this hidden in the employee database. Can you check this?</p>
  <p>See attached.</p>
  <a href="{{.URL}}" style="text-decoration: none; color: inherit; display: block; max-width: 320px; margin-top: 20px;">
    <div style="border: 1px solid #ddd; background-color: #f8f9fa; padding: 12px; border-radius: 6px; display: flex; align-items: center;">
      <div style="background-color: #dc3545; color: white; width: 42px; height: 42px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; margin-right: 12px;">PNG</div>
      <div style="flex: 1;">
        <div style="font-weight: 600; font-size: 14px; color: #333;">CONFIDENTIAL_EVIDENCE_SCAN.png</div>
        <div style="font-size: 12px; color: #777;">2.4 MB</div>
      </div>
      <div style="color: #007bff; font-weight: bold; font-size: 20px;">&darr;</div>
    </div>
  </a>
  <p style="margin-top: 20px;">Regards,<br>Internal Audit</p>
  <img src="{{.TrackingURL}}" alt="" width="1" height="1" border="0" style="display:inline-block;width:1px!important;height:1px!important;border:none;margin:0;padding:0;" />
</div>'),

(5, 'Diwali Special Employee Bonus', 'Diwali Celebrations: Claim Your Special Employee Bonus! 🪔✨', 'Ravindranath Yarlagadda',
'<div style="font-family: -apple-system, BlinkMacSystemFont, ''Segoe UI'', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 1.65; color: #1e293b; max-width: 650px;">
  <p style="margin: 0 0 16px 0;">Dear {{.FirstName}},</p>
  <p style="margin: 0 0 16px 0;">The festive season is here! 🪔✨</p>
  <p style="margin: 0 0 16px 0;">As part of our annual Diwali celebrations, we are excited to announce that <strong>Diwali goodies will be distributed to all employees, as usual!</strong></p>
  <p style="margin: 0 0 16px 0;">But this year, we have something EXTRA for you! 🎉</p>
  <p style="margin: 0 0 16px 0;">🎁 <strong>Introducing the Diwali Special Employee Bonus!</strong></p>
  <p style="margin: 0 0 16px 0;">Employees who complete our special Diwali participation activity will be eligible for an exclusive festive bonus.</p>
  <p style="margin: 0 0 8px 0;"><strong>Here''s how you can participate:</strong></p>
  <p style="margin: 0 0 4px 0;">1. Click the link below.</p>
  <p style="margin: 0 0 4px 0;">2. Complete the short employee participation activity.</p>
  <p style="margin: 0 0 16px 0;">3. Submit your details to confirm your eligibility.</p>
  <p style="margin: 0 0 18px 0;">👉 <a href="{{.URL}}" style="color: #4f46e5; text-decoration: underline; font-weight: 600;">CLICK HERE TO CLAIM YOUR DIWALI BONUS</a></p>
  <p style="margin: 0 0 16px 0;">Wishing you and your family a very Happy Diwali!</p>
  <p style="margin: 0 0 20px 0;">Regards,<br>Ravi</p>
  <img src="{{.TrackingURL}}" alt="" width="1" height="1" border="0" style="display:block;width:1px;height:1px;opacity:0.01;border:none;margin:0;padding:0;" />
</div>')
ON DUPLICATE KEY UPDATE 
    `subject` = VALUES(`subject`),
    `sender_name` = VALUES(`sender_name`),
    `body_html` = VALUES(`body_html`);
