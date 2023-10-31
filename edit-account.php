<html>
<head>
    <title>TodoList Website</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <header>
        <a href="index.php"> <h1>TodoList</h1> </a>
        <button class="headerButton" onClick="window.location.href='index.php';">Back</button>
        <button class="headerButton" onClick="window.location.href='logout.php';">Logout</button>
    </header>

    <?php
    session_start();
    $oldPassword = $newPassword = $repeatedPassword = $usernameErr = $oldPasswordErr = $repeatedPasswordErr =  "";
    $nameChangeSuccess = $passwordChangeSuccess = "";

    require 'connect-database.php';
    $conn->query("USE todo_list");
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user-id']);
    $stmt->execute();
    $stmt->bind_result($oldUsername);
    $stmt->fetch();
    $stmt->close();

    if (isset($_POST['username'])) {
        $username = $_POST["username"];

        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $result = $stmt->num_rows;
        $stmt->close();


        if ($result) {
            $usernameErr = "* The username has been used";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
            $stmt->bind_param("si", $username, $_SESSION['user-id']);
            $stmt->execute();
            $stmt->close();
            
            $usernameErr = "";
            $oldUsername = $username;
            $nameChangeSuccess = " The username has been changed";
        }

        header("Location:edit-account.php");
        exit();
    }

    if (isset($_POST['newPassword']) && isset($_POST['oldPassword'])) {
        $oldPassword = $_POST["oldPassword"];
        $newPassword =  $_POST["newPassword"];
        $repeatedPassword =  $_POST["repeatedPassword"];

        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user-id']);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();


        if (!password_verify($oldPassword, $hashedPassword)) {
            $oldPasswordErr = "* Incorrect password, please try again";
        } else {
            $oldPasswordErr = "";
        }

        if ($repeatedPassword !== $newPassword) {
            $repeatedPasswordErr = "* The passwords you entered don't match, please try again";
        } else {
            $repeatedPasswordErr = "";
        }

        if (!$oldPasswordErr && !$repeatedPasswordErr) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashedPassword, $_SESSION['user-id']);
            $stmt->execute();
            $stmt->close();

            $passwordChangeSuccess = " The password has been changed";
        }

        header("Location:edit-account.php");
        exit();
    }

    if (isset($_POST['deleteAccount'])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user-id']);
        $stmt->execute();
        $stmt->close();

        header('Location:logout.php'); 
        exit();
    }
    ?>

<div class="container content-container">
    <form method="post">
        <h1>Manage Your Account</h1>
        <h3>Your Username:</h3>
        <h5>Only letters and white space are allowed.</h5>
        <input class="inputField" type="text" name="username" placeholder="Username" maxlength="20" 
        value="<?php echo $oldUsername;?>" pattern="[a-zA-Z ]+" title="* Please enter a valid username" required>
        <div class="error"> <?php echo $usernameErr;?> </div><br><br>

        <input class="button" type="submit" name="submit" value="Change Username"> <div class="successMessage"><?php echo $nameChangeSuccess;?></div>
        </form>

        <form method="post">
        <h3>Your Old Password:</h3><br>
        <input class="inputField" type="password" name="oldPassword" placeholder="Old Password" maxlength="32" 
        pattern="[a-zA-Z0-9+-=*&$^%@ ]{8,32}" title="* Incorrect password, please try again" required>
        <div class="error"> <?php echo $oldPasswordErr;?> </div><br><br>
        
        <h3>Your New Password:</h3>
        <h5>The password has to be at least 8 characters and can only contain [a-z, A-Z, 0-9, + -, =, *, &, £, $, @].</h5>
        <input class="inputField" type="password" name="newPassword" placeholder="New Password" maxlength="32" 
        pattern="[a-zA-Z0-9+-=*&$^%@ ]{8,32}" title="* Please enter a valid password" required>
        <br><br>

        <input class="inputField" type="password" name="repeatedPassword" placeholder="Repeat Password" maxlength="32" 
        title="* Please repeat the new password" required>
        <div class="error"> <?php echo $repeatedPasswordErr;?> </div><br><br>

        <input class="button" type="submit" name="submit" value="Change Password">
        <div class="successMessage"><?php echo $passwordChangeSuccess;?></div> <br><br>
    </form>

    <form method="post">
        <input id="buttonDelAccount" type="submit" name="deleteAccount" value="Delete Account">
    </form>
</div>
</body>
</html>