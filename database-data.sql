--if the table exists, delete it
drop table if exists users;

--create a new table
create table users(
	username varchar(50) PRIMARY KEY,
	password varchar(100) NOT NULL);

--insert data to the table users
LOCK TABLES 'users' WRITE;
INSERT INTO users(username,password) values ('admin', md5('123'));
UNLOCK TABLES;