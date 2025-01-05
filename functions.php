<?php

function check_login($con){

    // check if user is logged in
    if (isset($_SESSION['UserID'])){
        $id = $_SESSION['UserID'];
        $query = "SELECT * FROM user WHERE UserID = '$id' limit 1";

        $result = mysqli_query($con, $query);


        // if user is logged in, return true
        if($result && mysqli_num_rows($result) > 0){
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
    }

    // redirect to login

    header("Location: login.php");
    die;
}
}

?>