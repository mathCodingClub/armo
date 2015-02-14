-- create database
CREATE DATABASE IF NOT EXISTS mcc_irc CHARACTER SET=utf8 COLLATE=utf8_swedish_ci;

-- change storage engine
USE mcc_irc;
SET default_storage_engine=MYISAM;

-- assumes that users exists

-- quotes
CREATE  TABLE IF NOT EXISTS mcc_irc.quotes (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  userId int,
  message text,  
  timestamp timestamp not null
  );

CREATE INDEX IF NOT EXISTS userid on mcc_irc.quotes(id);