<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    // Check if the user is an admin
    if ($user_data['Role'] !== 'Admin') {
        header("Location: index.php");
        die;
    }

    // Handle approval
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
        $request_id = $_POST['request_id'];
        // Fetch the request details
        $query = "SELECT * FROM approval_requests WHERE RequestID = '$request_id'";
        $result = mysqli_query($con, $query);
        $request = mysqli_fetch_assoc($result);

        if ($request) {
            // Insert the approved turf into the field table
            $query = "INSERT INTO field (Field_Name, City, HourlyRate, Street, UserID) VALUES ('{$request['Field_Name']}', '{$request['City']}', '{$request['HourlyRate']}', '{$request['Street']}', '{$request['UserID']}')";
            mysqli_query($con, $query);

            // Update the request status to approved
            $query = "UPDATE approval_requests SET Status = 'approved' WHERE RequestID = '$request_id'";
            mysqli_query($con, $query);

            echo "Turf approved and added successfully!";
        }
    }

    // Handle decline
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['decline'])) {
        $request_id = $_POST['request_id'];
        // Update the request status to declined
        $query = "UPDATE approval_requests SET Status = 'declined' WHERE RequestID = '$request_id'";
        mysqli_query($con, $query);

        echo "Turf request declined!";
    }

    // Fetch all pending requests
    $query = "SELECT * FROM approval_requests WHERE Status = 'pending'";
    $result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approve Turfs</title>
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
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="manage_users.php">Manage Users</a>
        <!-- <a href="manage_bookings.php">Manage Bookings</a> -->
        <a href="admin_approve.php">Approval</a>
        <!-- <a href="profile.php">Profile</a> -->
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Approve Turf Requests</h1>
        <table>
            <tr>
                <th>Request ID</th>
                <th>Field Name</th>
                <th>City</th>
                <th>Hourly Rate</th>
                <th>Street</th>
                <th>User ID</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['RequestID']); ?></td>
                <td><?php echo htmlspecialchars($row['Field_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['City']); ?></td>
                <td><?php echo htmlspecialchars($row['HourlyRate']); ?></td>
                <td><?php echo htmlspecialchars($row['Street']); ?></td>
                <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['RequestID']); ?>">
                        <input type="submit" name="approve" value="Approve">
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['RequestID']); ?>">
                        <input type="submit" name="decline" value="Decline">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>