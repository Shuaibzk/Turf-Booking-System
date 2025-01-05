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

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['reset_password'])) {
            $user_id = $_POST['user_id'];
            $new_password = 'defaultpassword'; // Default password without hashing
            $query = "UPDATE user SET Password = '$new_password' WHERE UserID = '$user_id'";
            mysqli_query($con, $query);
            echo "Password reset successfully!";
        } elseif (isset($_POST['add_user'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password']; // Password without hashing
            $role = $_POST['role'];
            $query = "INSERT INTO user (Name, Email, Password, Role) VALUES ('$name', '$email', '$password', '$role')";
            mysqli_query($con, $query);
            echo "User added successfully!";
        } elseif (isset($_POST['edit_user'])) {
            $user_id = $_POST['user_id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $query = "UPDATE user SET Name = '$name', Email = '$email', Role = '$role' WHERE UserID = '$user_id'";
            mysqli_query($con, $query);
            echo "User updated successfully!";
        }
    }

    // Fetch all users
    $query = "SELECT * FROM user";
    $result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        input[type="text"], input[type="email"], input[type="password"], select {
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
        .actions form {
            display: inline;
        }
        .actions input[type="text"], .actions input[type="email"], .actions select {
            width: auto;
            display: inline-block;
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
        <h1>Manage Users</h1>
        <h2>Add User</h2>
        <form method="post">
            <input type="hidden" name="add_user" value="1">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
            <input type="submit" value="Add User">
        </form>

        <h2>All Users</h2>
        <table>
            <tr>
                <th>UserID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                <td><?php echo htmlspecialchars($row['Role']); ?></td>
                <td class="actions">
                    <form method="post">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['UserID']); ?>">
                        <input type="submit" name="reset_password" value="Reset Password">
                    </form>
                    <form method="post">
                        <input type="hidden" name="edit_user" value="1">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['UserID']); ?>">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($row['Name']); ?>" required>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>" required>
                        <select name="role" required>
                            <option value="User" <?php if ($row['Role'] == 'User') echo 'selected'; ?>>User</option>
                            <option value="Admin" <?php if ($row['Role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                        </select>
                        <input type="submit" value="Edit">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>