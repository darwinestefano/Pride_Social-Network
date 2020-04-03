

/* Standard password @Bcde123 */

/*User 1 - Aline Fernandes*/
INSERT INTO users (user_name, user_surname, user_email, user_password, user_dob, user_registration_date) 
VALUES ('Aline', 'Fernandes', 'aline@mail.com', SHA1('@Bcde123'),'1987-07-01', NOW());

INSERT INTO profileimg (profileimg_user_id, profileimg_status) VALUES ('1','1');


/*User 2 - Darwin Machado*/
INSERT INTO users (user_name, user_surname, user_email, user_password, user_dob, user_registration_date) 
VALUES ('Darwin', 'Machado', 'darwin@mail.com', SHA1('@Bcde123'),'1994-09-01', NOW());

INSERT INTO profileimg (profileimg_user_id, profileimg_status) VALUES ('2','1');


/*User 3 - Juliana Izu*/
INSERT INTO users (user_name, user_surname, user_email, user_password, user_dob, user_registration_date) 
VALUES ('Juliana', 'Izu', 'juliana@mail.com', SHA1('@Bcde123'),'1989-02-01', NOW());

INSERT INTO profileimg (profileimg_user_id, profileimg_status) VALUES ('3','1');



