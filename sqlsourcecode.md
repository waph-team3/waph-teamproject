# Source code to Markdown
This file is automatically created by a script. Please delete this line and replace with the course and your team information accordingly.
## /database-account.sql
```sql
create database waph_team;
create user 'team3'@'localhost' IDENTIFIED BY '1234';
grant ALL on waph_team.* TO 'team3'@'localhost';
```
## /database-data.sql
```sql
-- if the table exists, delete it
DROP TABLE IF EXISTS users;

-- create a new table
CREATE TABLE users(
    username VARCHAR(100) PRIMARY KEY,
    password VARCHAR(100) NOT NULL,
    fullname VARCHAR(100),
    otheremail VARCHAR(100),
    phone VARCHAR(15),
    disabled BOOLEAN DEFAULT 0
);

-- insert data to the table users
INSERT INTO users(username, password, fullname, otheremail, phone) VALUES ('admin', MD5('123'), 'Administrator', 'admin@example.com', '1234567890');
INSERT INTO users(username, password, fullname, otheremail, phone) VALUES ('test', MD5('test'), 'Test User', 'test@example.com', '9876543210');
INSERT INTO users(username, password, fullname, otheremail, phone,disabled) VALUES ('test1', MD5('test1'), 'Test User', 'test@example.com', '9876543210','0');
INSERT INTO users(username, password, fullname, otheremail, phone,disabled) VALUES ('test2', MD5('test2'), 'Test User', 'test@example.com', '9876543210','1');

-- if the table exists, delete it
DROP TABLE IF EXISTS posts;

-- create a new table
CREATE TABLE posts(
    postID VARCHAR(100) PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content VARCHAR(100),
    posttitle VARCHAR(100),
    owner VARCHAR(100),
    FOREIGN KEY (owner) REFERENCES users (username) ON DELETE CASCADE
);

-- if the table exists, delete it
DROP TABLE IF EXISTS comments;

-- create a new table
CREATE TABLE comments(
    commentID VARCHAR(100) PRIMARY KEY,
    content VARCHAR(255) NOT NULL,
    postID VARCHAR(100),
    commenter VARCHAR(100),
    FOREIGN KEY (postID) REFERENCES posts (postID) ON DELETE CASCADE,
    FOREIGN KEY (commenter) REFERENCES users (username) ON DELETE CASCADE
);

-- if the table exists, delete it
DROP TABLE IF EXISTS super_users;

-- create a new table
CREATE TABLE super_users (
    username VARCHAR(100) PRIMARY KEY,
    password VARCHAR(100) NOT NULL,
    fullname VARCHAR(100),
    email VARCHAR(100)
);


INSERT INTO super_users(username, password, fullname, email) VALUES ('super', MD5('123'), 'Super user', 'su@example.com');
```
