<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }

    // Check if the user is an admin or owner
    if ($user_data['Role'] == 'Admin' || $user_data['Role'] == 'turf_owner') {
        echo "You cannot book a turf.";
        die;
    }

    // Check if field_id parameter is provided
    if (!isset($_GET['field_id'])) {
        echo "Field ID parameter is required!";
        die;
    }

    // Get the selected field ID from the URL
    $field_id = $_GET['field_id'];

    // Fetch field details based on the selected field ID
    $sql = "SELECT Field_Name, HourlyRate FROM field WHERE FieldID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $field_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the field is found
    if ($result->num_rows == 0) {
        echo "Field not found!";
        die;
    }

    $field = $result->fetch_assoc();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $date = $_POST['date'];

        // Insert booking into the booking table with Status = false
        $sql = "INSERT INTO booking (UserID, FieldID, StartTime, EndTime, BookingDate, Status)
                VALUES (?, ?, ?, ?, ?, false)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("iisss", $user_data['UserID'], $field_id, $start_time, $end_time, $date);
        $stmt->execute();
        $booking_id = $stmt->insert_id;

        // Insert transaction into the transaction table with Status = 0
        // $sql = "INSERT INTO booking (BookingID, Status) VALUES (?, 0)";
        // $stmt = $con->prepare($sql);
        // $stmt->bind_param("i", $booking_id);
        // $stmt->execute();

        // Redirect to transaction page
        header("Location: transaction.php?booking_id=" . $booking_id);
        die;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Turf</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="time"], input[type="date"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="bookings.php">Bookings</a>
        <a href="transaction.php">Transaction</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Book Turf: <?php echo htmlspecialchars($field['Field_Name']); ?></h1>
        <p>Hourly Rate: $<?php echo htmlspecialchars($field['HourlyRate']); ?></p>
        <form method="POST">
            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>
            </div>
            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <input type="submit" value="Book">
        </form>
    </div>

</body>
</html>