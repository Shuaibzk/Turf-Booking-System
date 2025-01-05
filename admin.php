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
    <title>Admin Dashboard</title>
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
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="manage_users.php">Manage Users</a>
        <!-- <a href="manage_bookings.php">Manage Bookings</a> -->
        <a href="admin_approve.php">Approval</a>
        <!-- <a href="reports.php">Reports</a> -->
        <!-- <a href="profile.php">Profile</a> -->
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Here you can manage users, bookings, view reports, and more.</p>
        <!-- Additional content and features for the admin can be added here -->
    </div>

</body>
</html>