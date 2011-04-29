create database authentication;

use authentication;

CREATE TABLE users (
  user_name char(50) NOT NULL,
  password char(32) NOT NULL,
  PRIMARY KEY (user_name)
) type=MyISAM;

GRANT SELECT, INSERT, UPDATE, DELETE ON authentication.users TO 'lucy'@'localhost' IDENTIFIED BY 'secret';

