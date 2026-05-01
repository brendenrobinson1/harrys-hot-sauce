-- Harry's Hot Sauce Database Schema
-- Clean version for GitHub portfolio project
-- Import this file before seed-data.sql

CREATE SCHEMA IF NOT EXISTS `HARRYS`;
USE `HARRYS`;

DROP TABLE IF EXISTS `Invoice`;
DROP TABLE IF EXISTS `Shipping`;
DROP TABLE IF EXISTS `Order_details`;
DROP TABLE IF EXISTS `Order_headers`;
DROP TABLE IF EXISTS `Shopping_cart`;
DROP TABLE IF EXISTS `LoginLog`;
DROP TABLE IF EXISTS `Login`;
DROP TABLE IF EXISTS `Employee`;
DROP TABLE IF EXISTS `Vendor`;
DROP TABLE IF EXISTS `Inventory`;
DROP TABLE IF EXISTS `Products`;
DROP TABLE IF EXISTS `Images`;
DROP TABLE IF EXISTS `Payment_method`;
DROP TABLE IF EXISTS `Privileges`;
DROP TABLE IF EXISTS `General_lookup`;
DROP TABLE IF EXISTS `Accounts`;
DROP TABLE IF EXISTS `Accounts_type`;
DROP TABLE IF EXISTS `Order_status`;

CREATE TABLE `Accounts` (
    `Account_id` INT NOT NULL AUTO_INCREMENT,
    `Account_lastname` VARCHAR(240) NOT NULL,
    `Account_firstname` VARCHAR(240) NOT NULL,
    `Account_middlename` VARCHAR(240) NULL,
    `Account_name_suffix` VARCHAR(240) NULL,
    `Account_altname` VARCHAR(240) NULL,
    `Account_address1` VARCHAR(240) NOT NULL,
    `Account_address2` VARCHAR(240) NULL,
    `Account_city` VARCHAR(240) NOT NULL,
    `Account_state_region` VARCHAR(240) NOT NULL,
    `Account_postal_code` VARCHAR(20) NOT NULL,
    `Account_country` VARCHAR(240) NOT NULL,
    `Account_type_id` INT NOT NULL,
    `Account_status` VARCHAR(240) NULL,
    `Account_email_address` VARCHAR(240) NOT NULL,
    `Account_send_email_yn` VARCHAR(10) NOT NULL DEFAULT 'Y',
    `Account_salutation` VARCHAR(240) NULL,
    `Account_phone_number` VARCHAR(25) NOT NULL,
    `Account_fax_number` VARCHAR(25) NULL,
    `Account_text_number` VARCHAR(25) NULL,
    `Account_send_text_yn` VARCHAR(10) NOT NULL DEFAULT 'N',
    `Account_created_date` DATETIME NOT NULL,
    `Account_created_by` VARCHAR(240) NOT NULL,
    `Account_last_update_date` DATETIME NULL,
    `Account_last_update_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Account_id`)
);

CREATE TABLE `Accounts_type` (
    `AT_id` INT NOT NULL AUTO_INCREMENT,
    `AT_type` VARCHAR(240) NOT NULL,
    `AT_description` VARCHAR(240) NOT NULL,
    `AT_start_date` DATETIME NULL,
    `AT_end_date` DATETIME NULL,
    `AT_created_date` DATETIME NOT NULL,
    `AT_created_by` VARCHAR(240) NOT NULL,
    `AT_last_updated_date` DATETIME NULL,
    `AT_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`AT_id`)
);

CREATE TABLE `Vendor` (
    `Vendor_id` INT NOT NULL AUTO_INCREMENT,
    `Vendor_company_name` VARCHAR(240) NOT NULL,
    `Vendor_contact_name` VARCHAR(240) NOT NULL,
    `Vendor_contact_title` VARCHAR(240) NOT NULL,
    `Vendor_account_id` INT NOT NULL,
    `Vendor_credit_rating` INT NULL,
    `Vendor_created_date` DATE NOT NULL,
    `Vendor_created_by` VARCHAR(240) NOT NULL,
    `Vendor_last_updated_date` DATETIME NULL,
    `Vendor_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Vendor_id`)
);

CREATE TABLE `Shopping_cart` (
    `SC_id` INT NOT NULL AUTO_INCREMENT,
    `SC_account_id` INT NOT NULL,
    `SC_inventory_id` INT NOT NULL,
    `SC_order_quantity` INT NOT NULL,
    `SC_unit_price` DECIMAL(10,2) NOT NULL,
    `SC_discount_percentage` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `SC_discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `SC_created_date` DATETIME NOT NULL,
    `SC_created_by` VARCHAR(240) NOT NULL,
    `SC_last_updated_date` DATETIME NULL,
    `SC_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`SC_id`)
);

CREATE TABLE `Shipping` (
    `Shipping_id` INT NOT NULL AUTO_INCREMENT,
    `Shipping_order_details_id` INT NOT NULL,
    `Shipping_shipped_date` DATETIME NULL,
    `Shipping_tracking_number` VARCHAR(240) NULL,
    `Shipping_shipped_quantity` INT NOT NULL DEFAULT 0,
    `Shipping_method` VARCHAR(240) NOT NULL,
    `Shipping_freight_costs` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Shipping_handling_costs` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Shipping_created_date` DATETIME NOT NULL,
    `Shipping_created_by` VARCHAR(240) NOT NULL,
    `Shipping_last_updated_date` DATETIME NULL,
    `Shipping_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Shipping_id`)
);

CREATE TABLE `Products` (
    `Products_id` INT NOT NULL AUTO_INCREMENT,
    `Products_image` VARCHAR(240) NULL,
    `Products_name` VARCHAR(240) NOT NULL,
    `Products_item_number` INT NOT NULL,
    `Products_display_name` VARCHAR(240) NULL,
    `Products_unit_price` DECIMAL(10,2) NOT NULL,
    `Products_discounted_unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Products_quantity_per_unit` INT NOT NULL DEFAULT 1,
    `Products_image_id` INT NULL,
    `Products_description` VARCHAR(2000) NOT NULL,
    `Products_status` VARCHAR(240) NULL,
    `Products_start_date` DATETIME NULL,
    `Products_end_date` DATETIME NULL,
    `Products_notes` VARCHAR(240) NULL,
    `Products_created_date` DATETIME NOT NULL,
    `Products_create_by` VARCHAR(240) NOT NULL,
    `Products_last_updated_date` DATETIME NULL,
    `Products_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Products_id`)
);

CREATE TABLE `Privileges` (
    `Privileges_id` INT NOT NULL AUTO_INCREMENT,
    `Privileges_name` VARCHAR(240) NOT NULL,
    `Privileges_start_date` DATETIME NULL,
    `Privileges_end_date` DATETIME NULL,
    `Privileges_status` VARCHAR(240) NOT NULL,
    `Privileges_created_date` DATETIME NOT NULL,
    `Privileges_created_by` VARCHAR(240) NOT NULL,
    `Privileges_last_updated_date` DATETIME NULL,
    `Privileges_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Privileges_id`)
);

CREATE TABLE `Payment_method` (
    `PM_id` INT NOT NULL AUTO_INCREMENT,
    `PM_type` VARCHAR(240) NOT NULL,
    `PM_number` VARCHAR(240) NOT NULL,
    `PM_card_security_code` INT NOT NULL,
    `PM_exp_date` DATETIME NOT NULL,
    `PM_account_id` INT NOT NULL,
    `PM_start_date` DATETIME NOT NULL,
    `PM_end_date` DATETIME NULL,
    `PM_created_date` DATETIME NOT NULL,
    `PM_created_by` VARCHAR(240) NOT NULL,
    `PM_last_updated_date` DATETIME NULL,
    `PM_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`PM_id`)
);

CREATE TABLE `Order_status` (
    `OS_id` INT NOT NULL AUTO_INCREMENT,
    `OS_name` VARCHAR(240) NOT NULL,
    `OS_description` VARCHAR(240) NOT NULL,
    `OS_start_date` DATETIME NULL,
    `OS_end_date` DATETIME NULL,
    `OS_created_date` DATETIME NOT NULL,
    `OS_created_by` VARCHAR(240) NOT NULL,
    `OS_last_updated_date` DATETIME NULL,
    `OS_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`OS_id`)
);

CREATE TABLE `Order_headers` (
    `Order_header_id` INT NOT NULL AUTO_INCREMENT,
    `Order_header_number` INT NOT NULL,
    `Order_header_orderdate` DATETIME NOT NULL,
    `Order_header_salesperson_id` INT NULL,
    `Order_header_account_id` INT NOT NULL,
    `Order_header_status_id` INT NOT NULL,
    `Order_header_discount_percentage` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Order_header_discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Order_header_pm_id` INT NULL,
    `Order_header_created_date` DATETIME NOT NULL,
    `Order_header_created_by` VARCHAR(240) NOT NULL,
    `Order_header_last_updated_date` DATETIME NULL,
    `Order_header_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Order_header_id`)
);

CREATE TABLE `Order_details` (
    `Order_details_id` INT NOT NULL AUTO_INCREMENT,
    `Order_details_header_id` INT NOT NULL,
    `Order_details_line_number` INT NOT NULL,
    `Order_details_date` DATETIME NOT NULL,
    `Order_details_inventory_id` INT NOT NULL,
    `Order_details_ordered_quantity` INT NOT NULL,
    `Order_details_cancelled_quantity` INT NOT NULL DEFAULT 0,
    `Order_details_unit_price` DECIMAL(10,2) NOT NULL,
    `Order_details_discount_percentage` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Order_details_discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Order_details_created_date` DATETIME NOT NULL,
    `Order_details_created_by` VARCHAR(240) NOT NULL,
    `Order_details_last_updated_date` DATETIME NULL,
    `Order_details_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Order_details_id`)
);

CREATE TABLE `LoginLog` (
    `LoginLog_id` INT NOT NULL AUTO_INCREMENT,
    `LoginLog_login_username` VARCHAR(240) NOT NULL,
    `LoginLog_login_password` VARCHAR(240) NOT NULL,
    `LoginLog_login_id` INT NULL,
    `LoginLog_login_date` DATETIME NOT NULL,
    `LoginLog_logout_date` DATETIME NULL,
    PRIMARY KEY (`LoginLog_id`)
);

CREATE TABLE `Login` (
    `Login_id` INT NOT NULL AUTO_INCREMENT,
    `Login_username` VARCHAR(240) NOT NULL,
    `Login_password` VARCHAR(240) NOT NULL,
    `Login_end_date` DATETIME NULL,
    `Login_status` VARCHAR(240) NULL,
    `Login_employee_id` INT NULL,
    `Login_account_id` INT NOT NULL,
    `Login_last_login_date` DATETIME NULL,
    `Login_created_date` DATETIME NOT NULL,
    `Login_created_by` VARCHAR(240) NOT NULL,
    `Login_last_update_date` DATETIME NULL,
    `Login_last_update_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Login_id`)
);

CREATE TABLE `Invoice` (
    `Invoice_id` INT NOT NULL AUTO_INCREMENT,
    `Invoice_number` INT NOT NULL,
    `Invoice_date` DATETIME NOT NULL,
    `Invoice_shipped_id` INT NOT NULL,
    `Invoice_created_date` DATETIME NOT NULL,
    `Invoice_created_by` VARCHAR(240) NOT NULL,
    `Invoice_last_updated_date` DATETIME NULL,
    `Invoice_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Invoice_id`)
);

CREATE TABLE `Inventory` (
    `Inventory_id` INT NOT NULL AUTO_INCREMENT,
    `Inventory_product_id` INT NOT NULL,
    `Inventory_units_in_stock` INT NOT NULL DEFAULT 0,
    `Inventory_units_on_order` INT NOT NULL DEFAULT 0,
    `Inventory_Vendor_id` INT NULL,
    `Inventory_Vendor_item_number` INT NULL,
    `Inventory_unit_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `Inventory_weight` DECIMAL(10,2) NULL,
    `Inventory_size` VARCHAR(240) NULL,
    `Inventory_color` VARCHAR(240) NULL,
    `Inventory_unit_of_measure` INT NULL,
    `Inventory_safety_stock_level` INT NOT NULL DEFAULT 0,
    `Inventory_reorder_quantity` INT NOT NULL DEFAULT 0,
    `Inventory_locator` VARCHAR(240) NULL,
    `Inventory_created_date` DATETIME NOT NULL,
    `Inventory_created_by` VARCHAR(240) NOT NULL,
    `Inventory_last_updated_date` DATETIME NULL,
    `Inventory_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Inventory_id`)
);

CREATE TABLE `Images` (
    `Images_id` INT NOT NULL AUTO_INCREMENT,
    `Images_url` VARCHAR(240) NOT NULL,
    `Images_created_date` DATETIME NOT NULL,
    `Images_created_by` VARCHAR(240) NOT NULL,
    `Images_last_updated_date` DATETIME NULL,
    `Images_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Images_id`)
);

CREATE TABLE `General_lookup` (
    `GL_id` INT NOT NULL AUTO_INCREMENT,
    `GL_type` VARCHAR(240) NOT NULL,
    `GL_name` VARCHAR(240) NOT NULL,
    `GL_data` VARCHAR(240) NOT NULL,
    `GL_start_date` DATETIME NULL,
    `GL_end_date` DATETIME NULL,
    `GL_created_date` DATETIME NOT NULL,
    `GL_created_by` VARCHAR(240) NOT NULL,
    `GL_last_updated_date` DATETIME NULL,
    `GL_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`GL_id`)
);

CREATE TABLE `Employee` (
    `Employee_id` INT NOT NULL AUTO_INCREMENT,
    `Employee_number` INT NOT NULL,
    `Employee_account_id` INT NOT NULL,
    `Employee_manager_id` INT NULL,
    `Employee_status` VARCHAR(240) NULL,
    `Employee_start_date` DATETIME NULL,
    `Employee_end_date` DATETIME NULL,
    `Employee_privilege_id` INT NOT NULL,
    `Employee_photo_url` VARCHAR(240) NULL,
    `Employee_position_title` VARCHAR(240) NOT NULL,
    `Employee_created_date` DATETIME NOT NULL,
    `Employee_created_by` VARCHAR(240) NOT NULL,
    `Employee_last_updated` DATETIME NULL,
    `Employee_last_updated_by` VARCHAR(240) NULL,
    PRIMARY KEY (`Employee_id`)
);
