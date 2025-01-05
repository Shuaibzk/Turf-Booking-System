<?php 

    session_start();

    if (isset($_SESSION['UserID'])) {
        // deleting session
        unset($_SESSION['UserID']);
    
    }

    header('Location: login.php');

?>