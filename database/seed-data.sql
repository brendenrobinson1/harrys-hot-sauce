-- Harry's Hot Sauce Seed Data
-- Import this after schema.sql

USE `HARRYS`;

INSERT INTO `Order_status` VALUES
(NULL,'Entered','Order is entered but not yet processed',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Processed','Order is processed (filled) but not yet Shipped',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Shipped','Order is Shipped but not yet Invoiced',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Invoiced','Order is Invoiced but not yet Closed',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Closed','Order is Completed, with no other action expected',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'On Hold','Order is on hold and is not to be processed',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL);

INSERT INTO `Accounts_type` VALUES
(NULL,'Customer','Customer Account',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Employee','Employee Account',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Vendor','Vendor Account',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Altshipto','Alternate Shipto Entry',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Shipto','Shipto Entry',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Billto','Billto Entry',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL);

INSERT INTO `General_lookup` VALUES
(NULL,'Payment Type','Credit Card','VISA',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Payment Type','Credit Card','MASTERCARD',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Payment Type','Credit Card','DISCOVERER',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Payment Type','Credit Card','AMERICAN EXPRESS',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Payment Type','Credit Card','DEBIT',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Payment Type','Non Credit Card','CASH',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Payment Type','Non Credit Card','CHECK',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Control','Order Number','000001',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Shipping Method','Shipping Method','USPS',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Shipping Method','Shipping Method','Delivery Truck',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Shipping Method','Shipping Method','FED EX',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Shipping Method','Shipping Method','UPS',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Shipping Method','Shipping Method','Customer Pickup',NOW(),NULL,NOW(),'Autoloaded',NULL,NULL),
(NULL,'Employee Position','Employee Position','Sales Staff',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Employee Position','Employee Position','Inventory Spec',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Employee Position','Employee Position','Shipper',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Employee Position','Employee Position','Accountant',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Employee Position','Employee Position','Manager',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Employee Position','Employee Position','Security',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Employee Status','Employee Status','Active',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Employee Status','Employee Status','Inactive',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Shipping Cost','USPS','12.95',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Shipping Cost','Delivery Truck','21.95',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Shipping Cost','FED EX','13.55',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded'),
(NULL,'Shipping Cost','Customer Pickup','2.95',NOW(),NULL,NOW(),'Autoloaded',NOW(),'Autoloaded');
