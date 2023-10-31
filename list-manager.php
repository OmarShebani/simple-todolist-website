<?php
    require 'connect-database.php';
    $conn->query("USE todo_list");
    session_start();
    
    $stmt = $conn->prepare("SELECT list FROM users_lists WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user-id']);
    $stmt->execute();
    $stmt->bind_result($currentEntries);
    $stmt->fetch();
    $stmt->close();

    if (isset($_POST['newEntry'])) {
        $newEntry = $_POST['newEntry'];
        
        if ($currentEntries) {
            $currentEntries .= '¬' . $newEntry;
        } else {
            $currentEntries = $newEntry;
        }

        $stmt = $conn->prepare("UPDATE users_lists SET list = ? WHERE user_id = ?");
        $stmt->bind_param("si", $currentEntries, $_SESSION['user-id']);
        $stmt->execute();
        $stmt->close();

        header("Location:index.php");
        exit();
    }

    if (isset($_GET['editedEntry']) && isset($_GET['entryNum'])) {
        $editedEntry = urldecode($_GET['editedEntry']);
        $entryNum = (int)$_GET['entryNum'];
                
        $entries_array = explode("¬", $currentEntries);
        $entries_array[$entryNum - 1] = $editedEntry;
        $currentEntries = implode("¬", $entries_array);

        $stmt = $conn->prepare("UPDATE users_lists SET list = ? WHERE user_id = ?");
        $stmt->bind_param("si", $currentEntries, $_SESSION['user-id']);
        $stmt->execute();
        $stmt->close();

        header("Location:index.php");
        exit();
    }

    if (isset($_GET['delEntryNum'])) {
        $delEntryNum = (int)$_GET['delEntryNum'];
                
        $entries_array = explode("¬", $currentEntries);
        unset($entries_array[$delEntryNum - 1]);
        $currentEntries = implode("¬", array_values($entries_array));
                    
        $stmt = $conn->prepare("UPDATE users_lists SET list = ? WHERE user_id = ?");
        $stmt->bind_param("si", $currentEntries, $_SESSION['user-id']);
        $stmt->execute();
        $stmt->close();

        header("Location:index.php");
        exit();
    }
?>