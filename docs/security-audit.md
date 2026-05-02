# 🔐 Security Audit – Harry's Hot Sauce

## Overview

This document identifies security vulnerabilities in the Harry's Hot Sauce PHP/MySQL e-commerce application and outlines recommended improvements.

The goal of this audit is to demonstrate secure coding awareness and the ability to identify and mitigate common web application vulnerabilities.

---

## 1. SQL Injection

### Risk

Some queries are constructed using direct variable insertion into SQL statements.

### Why It Matters

Unvalidated input can allow attackers to manipulate database queries.

### Fix

Use prepared statements with parameter binding.

Example:
$stmt = $link->prepare("SELECT * FROM Accounts WHERE Account_id = ?");
$stmt->bind_param("i", $accountID);
$stmt->execute();

### Status

Planned Improvement

---

## 2. Weak Password Hashing

### Risk

The application uses outdated hashing methods such as MD5.

### Why It Matters

Weak hashes are easily cracked if the database is compromised.

### Fix

Use PHP’s built-in password hashing:

Example:
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

Verify with:
password_verify($enteredPassword, $storedHash);

### Status

Planned Improvement

---

## 3. Cross-Site Scripting (XSS)

### Risk

User input may be displayed without proper sanitization.

### Why It Matters

Malicious scripts could execute in users' browsers.

### Fix

Escape output:

Example:
echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

### Status

Planned Improvement

---

## 4. Session Security

### Risk

Session IDs are not regenerated after login.

### Why It Matters

This can allow session fixation attacks.

### Fix

Example:
session_regenerate_id(true);

### Status

Planned Improvement

---

## 5. Database Design (Nullability)

### Risk

Improper NULL constraints can cause errors or incomplete data.

### Example

LoginLog_login_id INT NULL

### Status

Improved

---

## Summary

This project demonstrates awareness of common web vulnerabilities and outlines practical remediation steps.

### Key Focus Areas:

* Prepared statements (SQL Injection prevention)
* Secure password hashing
* Output sanitization
* Session security
* Database integrity


