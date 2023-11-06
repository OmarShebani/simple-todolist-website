<html>
<head>
    <title>TodoList Website</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <header>
        <a href="index.php"><h1>TodoList</h1></a>
    </header>

    <?php
    session_start();
    if (isset($_SESSION['user-id'])) {
        require 'logout.php';
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require 'connect-database.php';
        $conn->query("USE todo_list");

        $username = $_SESSION['username'] = trim($_POST["username"]);
        $newPassword =  $_POST["newPassword"];
        $repeatedPassword =  $_POST["repeatedPassword"];

        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $result = $stmt->num_rows;
        $stmt->close();

        if ($result > 0) {
            $_SESSION['usernameErr'] = "* The username has been used";
        }

        if (empty($repeatedPassword)) {
            $_SESSION['repeatedPasswordErr'] = "* Please repeat the new password";
        } elseif ($repeatedPassword !== $newPassword) {
            $_SESSION['repeatedPasswordErr'] = "* The passwords you entered don't match, please try again";
        }

        if (!isset($_SESSION['usernameErr']) && !isset($_SESSION['repeatedPasswordErr'])) {
            $hashed_password = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare('INSERT INTO users(username, password) VALUES(?, ?)');
            $stmt->bind_param("ss", $username, $hashed_password);
            $stmt->execute();

            $id = $stmt->insert_id;
            $stmt->close();
            
            $_SESSION['user-id'] = $id;

            header("Location:index.php");
            exit();
        }
    }
    ?>

<div class="container">
    <form method="post">
        <h1>Register</h1>
        <h3>Your Username:</h3>
        <h5>Only letters and white space are allowed.</h5>
        <input class="inputField" type="text" name="username" placeholder="Username" maxlength="20" 
        value="<?php
            if (isset($_SESSION['username'])) {
                echo $_SESSION['username'];
                unset($_SESSION['username']);
            }
                ?>"
        pattern="[a-zA-Z ]+" title="* Please enter a valid username" required>
        <span class="error"> 
        <?php
            if (isset($_SESSION['usernameErr'])) {
                echo $_SESSION['usernameErr'];
                unset($_SESSION['usernameErr']);
            }
        ?>
        </span>
        <br><br>
        
        <h3>Your New Password:</h3>
        <h5>The password has to be at least 8 characters and can only contain [a-z, A-Z, 0-9, + -, =, *, &, £, $, @].</h5>
        <input class="inputField" type="password" name="newPassword" placeholder="New Password" maxlength="32" 
        pattern="[a-zA-Z0-9+-=*&$^%@ ]{8,32}" title="* Please enter a valid password" required>
        <br><br>

        <input class="inputField" type="password" name="repeatedPassword" placeholder="Repeat Password" maxlength="32">
        <span class="error">
        <?php
            if (isset($_SESSION['repeatedPasswordErr'])) {
                echo $_SESSION['repeatedPasswordErr'];
                unset($_SESSION['repeatedPasswordErr']);
            }
        ?>
        </span>
        <br><br>

        <input class="button" type="submit" name="submit" value="Create Account">
        <input class="button" type="button" name="cancel" value="Cancel" onClick="window.location.href='index.php';">
    </form>
</div>
</body>
</html>