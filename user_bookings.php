<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }

    $user_id = $user_data['UserID'];

    // Fetch bookings for the logged-in user
    $query = "SELECT b.BookingID, b.BookingDate, b.StartTime, b.EndTime, b.Status, f.Field_Name, f.City, f.Street
              FROM booking b
              JOIN field f ON b.FieldID = f.FieldID
              WHERE b.UserID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $bookings_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
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
        h1 {
            color: #333;
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
        <a href="user_bookings.php">Bookings</a>
        <a href="transaction.php">Transaction</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>My Bookings</h1>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>Field Name</th>
                <th>City</th>
                <th>Street</th>
                <th>Booking Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $bookings_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['BookingID']); ?></td>
                <td><?php echo htmlspecialchars($row['Field_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['City']); ?></td>
                <td><?php echo htmlspecialchars($row['Street']); ?></td>
                <td><?php echo htmlspecialchars($row['BookingDate']); ?></td>
                <td><?php echo htmlspecialchars($row['StartTime']); ?></td>
                <td><?php echo htmlspecialchars($row['EndTime']); ?></td>
                <td><?php echo $row['Status'] ? 'Paid' : 'Due'; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>