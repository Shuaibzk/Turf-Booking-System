<?php
    session_start();
    include("database.php");
    include("functions.php");

    if ($_SERVER['REQUEST_METHOD'] == "POST"){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $role = $_POST['role'];

        if (!empty($username) && !empty($email) && !empty($password) && !empty($phone)){
            // save to database
            $query = "insert into user (Role, Password, Email, Name, Phone) 
                    values ('$role', '$password', '$email', '$username', '$phone')";

            mysqli_query($con, $query);
            header("Location: login.php");
            die;
        }

        else{
            echo "Please fill all fields";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #181818; /* Darker background */
        }

        .container {
            background-color:#282828; /* Slightly lighter box */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        select {
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
        <h2>Signup</h2>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="email" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Password">
            <input type="tel" name="phone" placeholder="Phone">
            <select name="role" >
                <option value="client">Client</option>
                <option value="turf_owner">Turf Owner</option>
            </select>
            <button type="submit">Signup</button>
            <a href="login.php">Already Have an Account?</a>
        </form>
    </div>

</body>
</html>
