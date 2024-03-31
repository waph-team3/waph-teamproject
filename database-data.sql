-- if the table exists, delete it
DROP TABLE IF EXISTS users;

-- create a new table
CREATE TABLE users(
	username VARCHAR(100) PRIMARY KEY,
	password VARCHAR(100) NOT NULL,
	fullname VARCHAR(100),
	otheremail VARCHAR(100),
	phone VARCHAR(15)
);

-- insert data to the table users

INSERT INTO users(username, password) VALUES ('admin', md5('123'));
INSERT INTO users(username, password) VALUES ('test', md5('test'));

DROP TABLE IF EXISTS posts;

CREATE TABLE posts(
	postID VARCHAR(100) PRIMARY KEY,
	title VARCHAR(100) NOT NULL,
	content VARCHAR(100),
	posttitle VARCHAR(100),
	owner VARCHAR(100),
	FOREIGN KEY (owner) REFERENCES users (username) ON DELETE CASCADE
);
