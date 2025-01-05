<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Field Booking - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            padding: 20px;
            text-align: center;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 50px 0;
        }
        .header h1 {
            margin: 0;
            font-size: 3em;
        }
        .content {
            margin: 20px 0;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        input[type="date"] {
            padding: 10px;
            margin: 10px 0;
            width: 200px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
        select[name="location"] {
            padding: 10px;
            margin: 10px 0;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="user_bookings.php">Bookings</a>
        <a href="transaction.php">Transaction</a>
        <a href="user_profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="header">
        <h1>Welcome to Football Field Booking</h1>
    </div>

    <div class="container">
        <div class="content">
            <p>Book your favorite football field easily and quickly.</p>
            <p>Choose your location and date to see available turfs:</p>
            <form action="view_turf.php" method="GET">
                <select name="location" required>
                    <option value="Chittagong">Chittagong</option>
                    <option value="Dhaka">Dhaka</option>
                </select>
                
                <br><br>
                <input type="submit" value="View Turfs">
            </form>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 Football Field Booking. All rights reserved.</p>
    </div>

</body>
</html>