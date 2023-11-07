<?php
require 'connect-database.php';
if ($conn->query("SHOW DATABASES LIKE 'todo_list'")->num_rows === 0) {

    $conn->query("CREATE DATABASE todo_list");
    $conn->query("USE todo_list");

    $conn->query("CREATE TABLE users(
                    user_id INT AUTO_INCREMENT,
                    username VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                    password VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                    PRIMARY KEY(user_id)
                )");

    $conn->query("CREATE TABLE users_lists(
                    user_id INT,
                    list TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL,
                    PRIMARY KEY(user_id),
                    CONSTRAINT FK_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
                )");

    $conn->query("CREATE TRIGGER UsersAfterInsert
                    AFTER INSERT ON users
                    FOR EACH ROW
                    BEGIN
                        INSERT INTO users_lists (user_id, list) VALUES (NEW.user_id, '');
                    END");
}
?>