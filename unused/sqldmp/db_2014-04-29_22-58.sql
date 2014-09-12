CREATE DATABASE IF NOT EXISTS a2381334_project;

USE a2381334_project;

DROP TABLE IF EXISTS clouds;

CREATE TABLE `clouds` (
  `id` int(10) NOT NULL,
  `cloudnum` int(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `cloudname` varchar(100) NOT NULL,
  `url` varchar(300) NOT NULL,
  `number` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`number`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO clouds VALUES("1","2","","","G Drive","{\"access_token\":\"ya29.1.AADtN_WB9Vfc7c84am8QQvUR1grGz0AfIJIaLUtJT27gmwAG_LwWqHd259B5R7BKL7eInQ\",\"token_type\":\"Bearer\",\"expires_in\":3600,\"refresh_token\":\"1\\/8-PIhkoy8bUAGO7zZK6XjtiOe3nG8d9Q0BGyNPlwi9w\",\"created\":1398112418}","1");
INSERT INTO clouds VALUES("1","1","shazvi@outlook.com","KÊqƒ¯}×€JR","Cubby","https://webdav.cubby.com:443/","2");



DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(300) NOT NULL, /* probably not needed */
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO users VALUES("1","shazvi","$1$MOhl.P7o$.oKW97R4bPB85QhMs9B4v.","shazvi@outlook.com","");



