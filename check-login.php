<?php
    session_start();    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    require "connect-database.php";
    $conn->query("USE todo_list");
        
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_assoc();

    if (password_verify($password, $result['password'])) {
        $_SESSION['user-id'] = $result['user_id'];
        header("Location:index.php");
    } else {
        header("Location:index.php?username=". urlencode($username) ."&errcode");
    }
?>