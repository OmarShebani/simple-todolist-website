<html>

<head>
    <title>TodoList Website</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <?php
    require 'create-database.php';
    $conn->query("USE todo_list");
    session_start();

    if (isset($_SESSION['user-id'])) {
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE user_id = ?');
        $stmt->bind_param("i", $_SESSION['user-id']);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();

        if (!$result) {
            require 'logout.php';
        }

    ?>
    <script>
        function switch_visibility(button) {
            let entry = button.parentElement.previousElementSibling.querySelector('.visible');
            let editField = button.parentElement.previousElementSibling.querySelector('.hidden');
            let editButton = button.parentElement.querySelector('.visible');
            let doneButton = button.parentElement.querySelector('.hidden');

            editField.removeAttribute('hidden');
            editButton.setAttribute('hidden', 'hidden');
            doneButton.removeAttribute('hidden');
            entry.setAttribute('hidden', 'hidden');
        }
    </script>
    <?php

        echo '<header>';
        echo '<a href="index.php">';
        echo '<h1>TodoList</h1>';
        echo '</a>';
        echo '<div class="button-container">';
        echo '<button class="headerButton" onClick="window.location.href=\'edit-account.php\';">Edit Profile</button>';
        echo '<button class="headerButton" onClick="window.location.href=\'logout.php\';">Logout</button>';
        echo '</div>';
        echo '</header>';

        echo '<div id="table-container" class="container">';
        
        echo '<h2>Your TodoList<h2>';    
        echo '<h4>This character "¬" can not be entered:<h4>';
        echo '<form action="list-manager.php" method="post">';
        echo    '<input class="inputField" type="text" name="newEntry" placeholder="Entry" maxlength="80" pattern="[^¬]*" required title="* Please enter a valid entry">';
        echo    '<input class="button" type="submit" name="addEntry" value="Add">';
        echo '</form>';

        echo '<h3>Current Todos</h3>';

        echo '<table>';
        echo '<thead>';
        echo    '<tr>';
        echo    '<th width="300">Task</th> <th></th> <th></th>'; 
        echo    '</tr>';
        echo '</thead>';

        echo '<tbody>';

        $stmt = $conn->prepare("SELECT list FROM users_lists WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user-id']);
        $stmt->execute();
        $stmt->bind_result($currentEntries);
        $stmt->fetch();
        $stmt->close();
    
        if ($currentEntries) {
            $entryNum = 1;
            foreach (explode("¬", $currentEntries) as $entry) {
                echo '<tr>';

                echo '<td> <div class="visible">'. $entryNum .' - '. $entry .'</div>
              <input class="hidden inputField" type="text" name="editField" placeholder="Edit entry" value="'. $entry .'" maxlength="80" hidden> </td>';

                echo '<td> <button class="visible" onClick="switch_visibility(this);">Edit</button>
                <button class="hidden" hidden onClick="window.location.href=\'list-manager.php?editedEntry=\' + encodeURIComponent(this.parentElement.previousElementSibling.querySelector(\'.inputField\').value) + \'&entryNum='. $entryNum .'\';">
                Done</button> </td>';

                echo '<td> <a href="list-manager.php?delEntryNum=' . $entryNum . '">Delete</a> </td>';

                echo '</tr>';
                $entryNum += 1;
            }
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }


    if (!isset($_SESSION['user-id'])) {
        $username = $loginErr = "";
        if (isset($_GET['errcode'])) {
            $username = $_GET['username'];
            $loginErr = "* Login Failed";
        }

        echo '<header>';
        echo    '<a href="index.php"> <h1>TodoList</h1> </a>';
        echo '</header>';

        echo '<div class="container content-container">';

        echo '<h3>Login</h3>
              <form action="check-login.php" method="post">';
        echo    '<input class="inputField" type="text" name="username" placeholder="Username" value="'. $username .'"
                maxlength="20" pattern="[a-zA-Z ]+" title="* Invalid username, please try again" required> <br><br>';

        echo    '<input class="inputField" type="password" name="password" placeholder="Password" maxlength="32"
                pattern="[a-zA-Z0-9+-=*&$^%@ ]{8,32}" title="* Invalid password, please try again" required>';

        echo    '<br> <input class="button" type="submit" name="loginButton" value="Login">';
        echo    '<span class="error"> '. $loginErr .'</span>';
        echo '</form>';

        echo '<a href="register.php"> <h4>Create a new account</h4> </a>';
        echo '</div>';
    }
    ?>
</body>

</html>