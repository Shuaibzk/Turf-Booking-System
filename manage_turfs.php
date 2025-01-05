<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $city = $_POST['city'];
        $street = $_POST['street'];
        $rate = $_POST['rate'];
        $user_id = $user_data['UserID']; // Assuming UserID is stored in session data

        if (!empty($name) && !empty($city) && !empty($street) && !empty($rate)) {
            // Insert the request into the approval_requests table
            $query = "INSERT INTO approval_requests (Field_Name, City, HourlyRate, Street, UserID, Status) VALUES ('$name', '$city', '$rate', '$street', '$user_id', 'pending')";
            mysqli_query($con, $query);
            $message = "Turf request sent for approval!";
        } else {
            $message = "Please fill all fields!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Turfs</title>
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
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"], button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #555;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="owner.php">Dashboard</a>
        <a href="manage_turfs.php">Add Turfs</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Add New Turf</h1>
        <?php if (isset($message)) { ?>
            <p class="message"><?php echo $message; ?></p>
        <?php } ?>
        <form method="post">
            <label for="name">Turf Name:</label>
            <input type="text" id="name" name="name">

            <label for="city">City:</label>
            <input type="text" id="city" name="city">

            <label for="street">Street:</label>
            <input type="text" id="street" name="street">

            <label for="rate">Hourly Rate:</label>
            <input type="text" id="rate" name="rate"><br><br>

            <input type="submit" value="Add Turf">
            <button type="button" onclick="window.location.href='owner.php'">Back to Dashboard</button>
        </form>
    </div>

</body>
</html>