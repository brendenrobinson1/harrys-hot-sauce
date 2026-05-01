# Security Audit – Harry's Hot Sauce

## Overview

This document reviews security concerns found in the Harry's Hot Sauce PHP/MySQL e-commerce application and outlines recommended fixes.

The purpose of this audit is to demonstrate application security awareness, secure coding practices, and vulnerability remediation.

---

## 1. SQL Injection Risk

### Risk
Some database queries are built by directly inserting variables into SQL strings.

### Why This Matters
If user-controlled input reaches a query without protection, an attacker may be able to manipulate the SQL command.

### Recommended Fix
Use prepared statements with bound parameters.

```php 
$stmt = $link->prepare("SELECT * FROM Accounts WHERE Account_id = ?");
$stmt->bind_param("i", $accountID);
$stmt->execute();
Status 

Planned improvement.

2. Weak Password Hashing
Risk

The application uses older password hashing logic.

Why This Matters

Weak hashes such as MD5 can be cracked quickly if the database is exposed.

Recommended Fix

Use PHP’s built-in password functions.

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

Verify with:

password_verify($enteredPassword, $storedHash);
Status

Planned improvement.

3. Cross-Site Scripting XSS Risk
Risk

Some database or user-supplied values may be printed directly onto pages.

Why This Matters

If output is not escaped, malicious JavaScript could run in a user’s browser.

Recommended Fix

Escape output before displaying it.

echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
Status

Planned improvement.

4. Session Security
Risk

The application uses sessions for login state.

Why This Matters

Session IDs should be regenerated after login to reduce session fixation risk.

Recommended Fix
session_regenerate_id(true);

Use this immediately after successful login.

Status

Planned improvement.

5. Database Nullability Review
Risk

Incorrect NULL and NOT NULL settings can cause inserts to fail or allow incomplete data.

Example

Failed login attempts may not have a valid login ID, so this field should allow NULL:

LoginLog_login_id INT NULL
Status

Improved in updated database schema.

Summary

This audit identifies common beginner-level security issues and documents planned secure coding improvements.

Focus areas:

Prepared statements
Secure password hashing
Output escaping
Session hardening
Cleaner database constraints

Then add this line to your main `README.md` under Security:

```markdown
See [Security Audit](docs/security-audit.md) for details.
