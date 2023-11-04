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

    $username = $newPassword = $repeatedPassword = $usernameErr = $repeatedPasswordErr =  "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require 'connect-database.php';
        $conn->query("USE todo_list");

        $username = $_POST["username"];
        $newPassword =  $_POST["newPassword"];
        $repeatedPassword =  $_POST["repeatedPassword"];

        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $result = $stmt->num_rows;
        $stmt->close();

        if ($result > 0) {
            $usernameErr = "* The username has been used";
        } else {
            $usernameErr = "";
        }

        if (empty($repeatedPassword)) {
            $repeatedPasswordErr = "* Please repeat the new password";
        } elseif ($repeatedPassword !== $newPassword) {
            $repeatedPasswordErr = "* The passwords you entered don't match, please try again";
        } else {
            $repeatedPasswordErr = "";
        }

        if (!$usernameErr && !$repeatedPasswordErr) {
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
        value="<?php echo $username;?>" pattern="[a-zA-Z ]+" title="* Please enter a valid username" required>
        <div class="error"> <?php echo $usernameErr;?> </div><br><br>
        
        <h3>Your New Password:</h3>
        <h5>The password has to be at least 8 characters and can only contain [a-z, A-Z, 0-9, + -, =, *, &, £, $, @].</h5>
        <input class="inputField" type="password" name="newPassword" placeholder="New Password" maxlength="32" 
        pattern="[a-zA-Z0-9+-=*&$^%@ ]{8,32}" title="* Please enter a valid password" required>
        <br><br>

        <input class="inputField" type="password" name="repeatedPassword" placeholder="Repeat Password" maxlength="32">
        <div class="error"> <?php echo $repeatedPasswordErr;?> </div><br><br>

        <input class="button" type="submit" name="submit" value="Create Account">
        <input class="button" type="button" name="cancel" value="Cancel" onClick="window.location.href='index.php';">
    </form>
</div>
</body>
</html>