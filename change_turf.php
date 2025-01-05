<?php
    session_start();
    include("database.php");
    include("functions.php");

    $user_data = check_login($con);

    if (!$user_data){
        header("Location: login.php");
        die;
    }

    // Check if the turf ID is provided in the URL
    if (!isset($_POST['FieldID'])) {
        echo "No turf ID provided!";
        die;
    }

    // Get the turf ID from the URL
    $turf_id = $_GET['FieldID'];

    // Fetch the current turf details
    $query = "SELECT * FROM field WHERE FieldID = '$turf_id' AND UserID = '{$user_data['UserID']}'";
    $result = mysqli_query($con, $query);
    $turf = mysqli_fetch_assoc($result);

    if (!$turf) {
        echo "Turf not found or you do not have permission to edit this turf.";
        die;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $city = $_POST['city'];
        $street = $_POST['street'];
        $rate = $_POST['rate'];
        $description = $_POST['description'];

        if (!empty($name) && !empty($city) && !empty($street) && !empty($rate) && !empty($description)) {
            $query = "UPDATE field SET Field_Name = '$name', City = '$city', HourlyRate = '$rate', Street = '$street', Description = '$description' WHERE FieldID = '$turf_id' AND UserID = '{$user_data['UserID']}'";
            mysqli_query($con, $query);
            echo "Turf updated successfully!";
        } else {
            echo "Please fill all fields!";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Turf Information</title>
</head>
<body>
    <h1>Change Turf Information</h1>
    <form method="post">
        <label for="name">Turf Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($turf['Field_Name']); ?>"><br>

        <label for="city">City:</label><br>
        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($turf['City']); ?>"><br>

        <label for="street">Street:</label><br>
        <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($turf['Street']); ?>"><br>

        <label for="rate">Hourly Rate:</label><br>
        <input type="text" id="rate" name="rate" value="<?php echo htmlspecialchars($turf['HourlyRate']); ?>"><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description"><?php echo htmlspecialchars($turf['Description']); ?></textarea><br>

        <label for="average_rating">Average Rating:</label><br>
        <input type="text" id="average_rating" name="average_rating" value="<?php echo htmlspecialchars($turf['AverageRating']); ?>" readonly><br><br>

        <input type="submit" value="Update Turf">
        <button type="button" onclick="window.location.href='owner_dashboard.php'">Back to Dashboard</button>
    </form>
</body>
</html>