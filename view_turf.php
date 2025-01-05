<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }

    // Check if location parameter is provided
    if (!isset($_GET['location'])) {
        echo "Location parameter is required!";
        die;
    }

    // Get the selected location from the form
    $location = $_GET['location'];

    // Fetch turfs based on the selected location
    $sql = "SELECT f.FieldID, f.Field_Name, f.Street, f.City, f.HourlyRate, f.Description, 
                   IFNULL(AVG(r.Rating), 0) AS AverageRating
            FROM field f
            LEFT JOIN ratings r ON f.FieldID = r.FieldID
            WHERE f.City = ?
            GROUP BY f.FieldID";
            
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $location);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Turfs</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
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
        <h1>Available Turfs in <?php echo htmlspecialchars($location); ?></h1>
        <table>
            <tr>
                <th>Field Name</th>
                <th>Street</th>
                <th>City</th>
                <th>Hourly Rate</th>
                <th>Average Rating</th>
                <th>Description</th>
                <th>Book Now</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Field_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['Street']); ?></td>
                <td><?php echo htmlspecialchars($row['City']); ?></td>
                <td><?php echo htmlspecialchars($row['HourlyRate']); ?></td>
                <td><?php echo htmlspecialchars($row['AverageRating']); ?></td>
                <td><?php echo htmlspecialchars($row['Description']); ?></td>
                <td>
                    <form action="booking.php" method="GET">
                        <input type="hidden" name="field_id" value="<?php echo htmlspecialchars($row['FieldID']); ?>">
                        <input type="submit" value="Book Now">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>