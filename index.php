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

        if ($result) {
            echo '
            <header>
                <a href="index.php">
                    <h1>TodoList</h1>
                </a>
                <div class="button-container"> 
                <button class="headerButton" onClick="window.location.href=\'edit-account.php\';">Edit Profile</button>
                <button class="headerButton" onClick="window.location.href=\'logout.php\';">Logout</button>
                </div>
                </header>

            <div class="container">
                <div class="content-container">
                    <h2>Your TodoList<h2>
                            <h4>This character "¬" can not be entered:<h4>
                                    <form action="list-manager.php" method="post">
                                        <input class="inputField" type="text" name="newEntry" placeholder="Entry" maxlength="80"
                                            pattern="[^¬]*" required title="* Please enter a valid entry">
                                        <input class="button" type="submit" name="addEntry" value="Add">
                                    </form>
                </div>
            </div>
        
            <div class="table-container">
                <h3>Current Todos</h3>
                <table>
                    <thead>
                        <tr>
                            <th width="300">Task</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
            
                    <tbody>
                    </tbody>
                </table>
            </div>
             ';

            $stmt = $conn->prepare("SELECT list FROM users_lists WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user-id']);
            $stmt->execute();
            $stmt->bind_result($currentEntries);
            $stmt->fetch();
            $stmt->close();

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
            echo '</div>';

        } else {
            require 'logout.php';
        }
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

        echo    '<br ><input class="button" type="submit" name="loginButton" value="Login">';
        echo    '<span class="error"> '. $loginErr .'</span>';
        echo '</form>';

        echo '<a href="register.php"> <h4>Create a new account</h4> </a>';
        echo '</div>';
    }
    ?>
</body>

</html>