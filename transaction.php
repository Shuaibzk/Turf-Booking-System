<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }

    // Initialize the variable
    $transaction_already_done = false;

    // Get the selected booking ID from the URL
    if (!isset($_GET['booking_id'])) {
        echo "No booking ID provided!";
        die;
    }

    $booking_id = $_GET['booking_id'];

    // Check if the transaction is already made
    $sql = "SELECT Status, FieldID FROM booking WHERE BookingID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if ($booking['Status']) {
        $transaction_already_done = true;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['make_transaction']) && !$transaction_already_done) {
        // Update booking status to true
        $sql = "UPDATE booking SET Status = true WHERE BookingID = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            $transaction_success = true;
        } else {
            $transaction_success = false;
        }
    }

    // Handle rating submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rating'])) {
        $rating = $_POST['rating'];
        $field_id = $booking['FieldID'];
        $user_id = $user_data['UserID'];

        // Check if the user has already rated this booking
        $sql = "SELECT * FROM ratings WHERE BookingID = ? AND UserID = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insert the rating into the ratings table
            $sql = "INSERT INTO ratings (BookingID, UserID, FieldID, Rating) VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("iiii", $booking_id, $user_id, $field_id, $rating);
            $stmt->execute();

            echo "Rating submitted successfully!";
        } else {
            echo "You have already rated this booking.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
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
        .success-message {
            color: green;
        }
        .redirect-button {
            margin-top: 20px;
        }
        .rating-form {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="show_booking.php">Bookings</a>
        <a href="transaction.php">Transaction</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Transaction</h1>
        <?php if ($transaction_already_done) { ?>
            <p class="success-message">Transaction already done!</p>
            <form action="index.php" method="GET" class="redirect-button">
                <input type="submit" value="Go to Home Page">
            </form>
        <?php } else { ?>
            <form method="POST">
                <input type="hidden" name="make_transaction" value="1">
                <input type="submit" value="Make Transaction">
            </form>
            <?php if (isset($transaction_success) && $transaction_success) { ?>
                <p class="success-message">Transaction successful!</p>
                <form method="POST" class="rating-form">
                    <label for="rating">Rate the Turf:</label>
                    <select id="rating" name="rating" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <input type="hidden" name="submit_rating" value="1">
                    <input type="submit" value="Submit Rating">
                </form>
                <form action="index.php" method="GET" class="redirect-button">
                    <input type="submit" value="Go to Home Page">
                </form>
            <?php } elseif (isset($transaction_success) && !$transaction_success) { ?>
                <p class="success-message">Transaction failed. Please try again.</p>
            <?php } ?>
        <?php } ?>
    </div>

</body>
</html>