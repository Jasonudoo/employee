DROP DATABASE IF EXISTS `www_employeedb`;
CREATE DATABASE www_employeedb;
USE www_employeedb;

DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE IF NOT EXISTS `tbl_user`(
	`UID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`USER_ID` VARCHAR(50) NOT NULL,
	`USER_PASSWORD` VARCHAR(32) NOT NULL,
	`FIRST_NAME` VARCHAR(50) NULL,
	`LAST_NAME` VARCHAR(50) NULL,
	`CONTACT_NUMBER` VARCHAR(20) NULL,
	`CONTACT_EMAIL` VARCHAR(50) NOT NULL,
	`STATUS` TINYINT NOT NULL DEFAULT '0',
	`PASSWORD_SEED` VARCHAR(10) NOT NULL,
	`RESETPASSWORD` VARCHAR(64) NULL,
	`ADD_DATETIME` DATETIME NOT NULL,
	`UPDATE_DATEIME` DATETIME NULL,
	`ADDED` VARCHAR(100) NULL,
	`UPDATED` VARCHAR(100) NULL,
	`LAST_LOGIN_TIME` DATETIME NULL,
	`IP_ADDRESS` VARCHAR(100) NULL,
	PRIMARY KEY(`UID`),
	INDEX IDX_USER (`USER_ID`),
	INDEX IDX_RESETPWD(`RESETPASSWORD`)	
)ENGINE=INNODB CHARACTER SET utf8;
INSERT INTO `tbl_user` (
`UID` ,
`USER_ID` ,
`USER_PASSWORD` ,
`FIRST_NAME` ,
`LAST_NAME` ,
`CONTACT_NUMBER` ,
`CONTACT_EMAIL` ,
`STATUS` ,
`PASSWORD_SEED` ,
`RESETPASSWORD` ,
`ADD_DATETIME` ,
`UPDATE_DATEIME` ,
`ADDED` ,
`UPDATED` ,
`LAST_LOGIN_TIME` ,
`IP_ADDRESS`
)
VALUES (
'100000', 'admin', '77c1364897c995b28cdd58b468e5c50c', 'Jason', 'Williams', '18965015460', 'jason@netwebx.com', '0', '87692', NULL , NOW( ) , NULL , '100000', NULL , NULL , NULL
);

DROP TABLE IF EXISTS `tbl_employee_info`;
CREATE TABLE IF NOT EXISTS `tbl_employee_info`(
	`EMPLOYEE_ID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`FIRST_NAME` VARCHAR(100) NOT NULL,
	`LAST_NAME` VARCHAR(100) NOT NULL,
	`EMAIL` VARCHAR(100) NOT NULL,
	`PHONE_NUMBER` VARCHAR(100) NOT NULL,
	`INTERNAL_EXTENS` VARCHAR(20) NOT NULL,
	`FILE_NAME` VARCHAR(200) NOT NULL,
	`NAME_INDEX` VARCHAR(2) NOT NULL,
	`ADD_DATETIME` DATETIME NOT NULL,
	`UPDATE_DATETIME` DATETIME NULL,
	`ADDED` VARCHAR(50) NULL,
	`UPDATED` VARCHAR(50) NULL,
	PRIMARY KEY(`EMPLOYEE_ID`),
	INDEX IDX_NAME(`FIRST_NAME`, `LAST_NAME`),
	INDEX IDX_EMAIL(`EMAIL`),
	INDEX IDX_NAME_INDEX(`NAME_INDEX`)
) ENGINE=INNODB CHARACTER SET utf8;

DROP TABLE IF EXISTS `tbl_employee_photo`;
CREATE TABLE IF NOT EXISTS `tbl_employee_photo`(
	`PHOTO_ID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`EMPLOYEE_ID` INTEGER UNSIGNED NOT NULL,
	`PHOTO_NAME` VARCHAR(255) NOT NULL,
	`PHOTO_TAG` TEXT NULL,
	`PHOTO_PATH` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`PHOTO_ID`),
	INDEX IDX_EMPLOYEE(`EMPLOYEE_ID`)
) ENGINE=INNODB CHARACTER SET UTF8;

DROP TABLE IF EXISTS `tbl_employee_meta`;
CREATE TABLE IF NOT EXISTS `tbl_employee_meta`(
	`META_ID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`EMPLOYEE_ID` INTEGER UNSIGNED NOT NULL,
	`META_KEY` VARCHAR(255) NOT NULL,
	`META_VALUE` LONGTEXT NULL,
	PRIMARY KEY (`META_ID`),
	INDEX IDX_EMPLOYEE(`EMPLOYEE_ID`)
) ENGINE=INNODB CHARACTER SET utf8;

DROP TABLE IF EXISTS `tbl_options`;
CREATE TABLE IF NOT EXISTS `tbl_options`(
	`OPTION_ID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`OPTION_NAME` VARCHAR(100) NOT NULL,
	`OPTION_VALUE` TEXT NULL,
	`AUTOLOAD` ENUM('YES','NO'),
	PRIMARY KEY(`OPTION_ID`),
	INDEX IDX_OPTION(`OPTION_NAME`)
) ENGINE=INNODB CHARACTER SET UTF8;

INSERT INTO `tbl_options`(`OPTION_ID`, `OPTION_NAME`, `OPTION_VALUE`, `AUTOLOAD`) VALUES(100000, 'WEBSITE_DATETIME_FORMAT', 'Y-m-d H:i:s', 'YES');
INSERT INTO `tbl_options`(`OPTION_ID`, `OPTION_NAME`, `OPTION_VALUE`, `AUTOLOAD`) VALUES(100001, 'WEBSITE_URL', '', 'NO');
INSERT INTO `tbl_options`(`OPTION_ID`, `OPTION_NAME`, `OPTION_VALUE`, `AUTOLOAD`) VALUES(100002, 'WEBSITE_PAGESIZE', '10', 'YES');
INSERT INTO `tbl_options`(`OPTION_ID`, `OPTION_NAME`, `OPTION_VALUE`, `AUTOLOAD`) VALUES(100003, 'WEBSITE_PHOTO_UPLOAD_PATH', '', 'NO');

DROP TABLE IF EXISTS `tbl_sessions`;
CREATE TABLE IF NOT EXISTS `tbl_sessions`(
  `SESSIONS` varchar(100) NOT NULL default '',
  `SESSIONS_USER_NAME` varchar(40) NULL,
  `SESSIONS_IPADR` varchar(100) NOT NULL default '',
  `SESSIONS_BROWSER` varchar(64) NOT NULL default '',
  `SESSIONS_BROWSER_VER` varchar(10) NOT NULL default '',
  `SESSIONS_OS` varchar(64) NOT NULL default '',
  `SESSIONS_LOGIN` INTEGER UNSIGNED NULL,
  `SESSIONS_LAST` INTEGER UNSIGNED NULL,
  `SESSION_LOCK` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY (`SESSIONS`),
  INDEX IDX_user(`SESSIONS_USER_NAME`)
) ENGINE=INNODB CHARACTER SET utf8;