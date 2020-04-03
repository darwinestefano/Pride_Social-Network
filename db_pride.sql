CREATE DATABASE pride;

USE pride; 

CREATE TABLE users(
user_id	MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
user_name VARCHAR(255) NOT NULL,
user_surname VARCHAR(255) NOT NULL,
user_email VARCHAR(255) NOT NULL UNIQUE,
user_phone VARCHAR(255),
user_dob date NOT NULL,	
user_gender CHAR(1),
user_description VARCHAR(255),
user_password CHAR(40) NOT NULL,
user_relationship_status  VARCHAR(30),
user_location VARCHAR(30),	
user_registration_date date NOT NULL
) ENGINE=InnoDB;

CREATE TABLE profileimg(
profileimg_id INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
profileimg_user_id MEDIUMINT NOT NULL,
profileimg_status INT(11) NOT NULL,
FOREIGN KEY (profileimg_user_id) REFERENCES users(user_id)
)ENGINE=InnoDB;

CREATE TABLE friend(
friend_id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
friend_user_id MEDIUMINT NOT NULL,
friend_friend_id MEDIUMINT NOT NULL,
FOREIGN KEY (friend_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;
					
CREATE TABLE feed(
feed_id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
feed_user_id MEDIUMINT NOT NULL,
feed_date_time datetime NOT NULL,
feed_message VARCHAR(255),
feed_photo VARCHAR(255),
FOREIGN KEY (feed_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

CREATE TABLE album(
album_id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
album_user_id MEDIUMINT NOT NULL,
album_date_post	MEDIUMINT NOT NULL,
FOREIGN KEY (album_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

CREATE TABLE post_photo(
post_photo_id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
post_photo_date_post datetime NOT NULL,
post_photo_album_id MEDIUMINT NOT NULL,
post_photo_photo VARCHAR(255),
FOREIGN KEY (post_photo_album_id) REFERENCES album(album_id)	
) ENGINE=InnoDB;

CREATE TABLE comments(
comment_id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
comment_feed_id MEDIUMINT NOT NULL,
comment_user_id MEDIUMINT NOT NULL,
comment_date datetime NOT NULL,
comment_comment VARCHAR(255),
FOREIGN KEY (comment_feed_id) REFERENCES feed(feed_id)
) ENGINE=InnoDB;

CREATE TABLE likes(
likes_id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
likes_feed_id MEDIUMINT,
likes_post_photo_id MEDIUMINT,
likes_user_id MEDIUMINT NOT NULL,
FOREIGN KEY (likes_feed_id) REFERENCES feed(feed_id),
FOREIGN KEY (likes_post_photo_id) REFERENCES post_photo(post_photo_id)
) ENGINE=InnoDB;

CREATE TABLE testimonial(
testimonial_id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
testimonial_user_id MEDIUMINT NOT NULL,
testimonial_testimonial VARCHAR(255) NOT NULL,
FOREIGN KEY (testimonial_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

CREATE TABLE logs(
logs_id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
logs_userid mediumint NOT NULL,
logs_friendid mediumint(9)	NOT NULL,
logs_username varchar(256), 
logs_message LONGTEXT NOT NULL,
logs_time date NOT NULL,
FOREIGN KEY (logs_userid) REFERENCES users(user_id),
FOREIGN KEY (logs_friendid) REFERENCES friend(friend_main_user_id)  
)ENGINE=InnoDB;



