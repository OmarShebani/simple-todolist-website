<?php
require 'connect-database.php';
if (!$conn->query("SHOW DATABASES LIKE 'todo_list'")) {

    $queries = "CREATE DATABASE todo_list;
                USE todo_list;
                
                CREATE TABLE users(
                    user_id INT AUTO_INCREMENT,
                    username VARCHAR(50) NOT NULL,
                    password VARCHAR(250) NOT NULL,
                    PRIMARY KEY(user_id)
                );
                
                CREATE TABLE users_lists(
                    user_id INT,
                    list TEXT NULL,
                    PRIMARY KEY(user_id),
                    CONSTRAINT FK_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
                );
                
                DELIMITER //
                CREATE TRIGGER UsersAfterInsert
                AFTER INSERT ON users
                FOR EACH ROW
                BEGIN
                    INSERT INTO users_lists (user_id, list) VALUES (NEW.user_id, '');
                END;
                //
                DELIMITER ;";

    $conn->multi_query($queries);
}                                   // doesn't work :-(
?>