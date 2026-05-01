-- Optional connection test database
-- This is not required for the Harry's Hot Sauce app.
-- Keep this only if you want a simple table to test whether MySQL connections work.

CREATE SCHEMA IF NOT EXISTS `CONNECTION`;

DROP TABLE IF EXISTS `CONNECTION`.`CONNECTION_TEST`;

CREATE TABLE `CONNECTION`.`CONNECTION_TEST` (
    `CT_ID` INT NOT NULL AUTO_INCREMENT,
    `CT_NAME` VARCHAR(45) NULL,
    `CT_COLOR` VARCHAR(45) NULL,
    PRIMARY KEY (`CT_ID`)
);

INSERT INTO `CONNECTION`.`CONNECTION_TEST` (`CT_NAME`, `CT_COLOR`) VALUES
('Brenden', 'Red'),
('Harmony', 'Blue'),
('Bobby', 'Yellow'),
('Toby', 'Brown');

SELECT * FROM `CONNECTION`.`CONNECTION_TEST`;
