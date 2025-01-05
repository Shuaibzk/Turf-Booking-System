<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $user_id = $user_data['UserID'];

        if (!empty($name) && !empty($email) && !empty($phone)) {
            $query = "UPDATE user SET Name = ?, Email = ?, Phone = ? WHERE UserID = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
            $stmt->execute();

            echo "Profile updated successfully!";
        } else {
            echo "Please fill all fields!";
        }
    }

    // Handle booking cancellation
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
        $booking_id = $_POST['booking_id'];

        // Delete related ratings first
        $query = "DELETE FROM ratings WHERE BookingID = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();

        // Delete the booking
        $query = "DELETE FROM booking WHERE BookingID = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();

        echo "Booking cancelled successfully!";
    }

    // Handle turf deletion
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_turf'])) {
        $field_id = $_POST['field_id'];

        // Check if there are any bookings for this turf
        $query = "SELECT COUNT(*) AS booking_count FROM booking WHERE FieldID = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $field_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['booking_count'] == 0) {
            // Delete the turf
            $query = "DELETE FROM field WHERE FieldID = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $field_id);
            $stmt->execute();

            echo "Turf deleted successfully!";
        } else {
            echo "Cannot delete turf with existing bookings!";
        }
    }

    // Fetch user details
    $user_id = $user_data['UserID'];
    $query = "SELECT * FROM user WHERE UserID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();

    // Fetch bookings for the turf owner
    $query = "SELECT b.BookingID, b.BookingDate, b.StartTime, b.EndTime, b.Status, f.Field_Name, u.Name AS UserName
              FROM booking b
              JOIN field f ON b.FieldID = f.FieldID
              JOIN user u ON b.UserID = u.UserID
              WHERE f.UserID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $bookings_result = $stmt->get_result();

    // Fetch turfs for the turf owner
    $query = "SELECT * FROM field WHERE UserID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $turfs_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Profile</title>
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
            padding: 20px;
        }
        h1, h2 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"], input[type="email"], input[type="tel"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_turfs.php">Manage Turfs</a>
        <a href="owner_profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Owner Profile</h1>
        <h2>Update Profile</h2>
        <form method="post">
            <input type="hidden" name="update_profile" value="1">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required>
            <input type="submit" value="Update Profile">
        </form>

        <h2>Bookings</h2>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>Field Name</th>
                <th>User Name</th>
                <th>Booking Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $bookings_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['BookingID']); ?></td>
                <td><?php echo htmlspecialchars($row['Field_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['UserName']); ?></td>
                <td><?php echo htmlspecialchars($row['BookingDate']); ?></td>
                <td><?php echo htmlspecialchars($row['StartTime']); ?></td>
                <td><?php echo htmlspecialchars($row['EndTime']); ?></td>
                <td><?php echo $row['Status'] ? 'Paid' : 'Due'; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($row['BookingID']); ?>">
                        <input type="submit" name="cancel_booking" value="Cancel Booking">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h2>My Turfs</h2>
        <table>
            <tr>
                <th>Field ID</th>
                <th>Field Name</th>
                <th>City</th>
                <th>Street</th>
                <th>Hourly Rate</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $turfs_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['FieldID']); ?></td>
                <td><?php echo htmlspecialchars($row['Field_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['City']); ?></td>
                <td><?php echo htmlspecialchars($row['Street']); ?></td>
                <td><?php echo htmlspecialchars($row['HourlyRate']); ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="field_id" value="<?php echo htmlspecialchars($row['FieldID']); ?>">
                        <input type="submit" name="delete_turf" value="Delete Turf">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>