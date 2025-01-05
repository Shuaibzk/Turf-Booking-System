<?php
    session_start();
    include("database.php");
    include("functions.php");

    if ($_SERVER['REQUEST_METHOD'] == "POST"){
        $username = $_POST['username'];
        $password = $_POST['password'];
        

        if (!empty($username) && !empty($password)){
            // read from database
            $query = "select * from user where Name = '$username' limit 1";
            $result = mysqli_query($con, $query);
            // $query = "SELECT * FROM user WHERE UserID = '$id' limit 1";


            if ($result){
                

                if ($result && mysqli_num_rows($result) > 0){
                    
                    $user_data = mysqli_fetch_assoc($result);

                    if ($user_data['Password'] === $password){
                    // if (isset($user_data['password']) && $user_data['password'] === $password){
                        // assign session variables
                        $_SESSION['UserID'] = $user_data['UserID'];
                        // redirect
                        if ($user_data['Role'] === 'Admin'){
                            header("Location: admin.php");
                            die;
                        }
                        else if($user_data['Role'] === 'client'){
                            header("Location: client.php");
                            die;
                        }
                        else{
                            header("Location: owner.php");
                            die;
                        }
                        // header("Location: index.php");
                        
                        
                    }
                }
                
            echo "Wrong username or password";
            
        }

        else{
            echo "Please fill all fields";
        }
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #181818; /* Darker background */
        }

        .container {
            background-color: #282828; /* Slightly lighter box */
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
            border: none;
            border-radius: 3px;
            background-color: #383838;
            color: #fff;
        }

        button {
            background-color: #4CAF50; /* Green button */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
        }

        a {
            color: #007bff; /* Blue link color */
            text-decoration: none;
            text-align: center;
            display: block;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Login</h2>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <button type="submit">Login</button>
            <a href="signup.php">Don't Have an Account? Signup</a>
        </form>
    </div>

</body>
</html>