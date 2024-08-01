<?php
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Database connection
    $conn = new mysqli("localhost", "root", "", "csd_system");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL query with JOIN to fetch fullname and user_type from id_emp table
    $query = "SELECT *
              FROM id_emp e
              WHERE e.username = '$username'";
    
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if ($password === $row['password']) {
            // Store session data
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['desig_id'] = $row['desig_id'];
            $_SESSION['group_id'] = $row['group_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['middle_name'] = $row['middle_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['is_created'] = $row['is_created'];
            $_SESSION['user_type'] = $row['user_type'];

            // Get current year and month
            $curr_year = date("Y");
            $curr_month = date("n"); // numeric representation of month without leading zeros

            // Determine the current pair
            $pairs = [
                1 => [1, 2],
                2 => [3, 4],
                3 => [5, 6],
                4 => [7, 8],
                5 => [9, 10],
                6 => [11, 12]
            ];
            $curr_pair = array_search($curr_month, array_column($pairs, 0)) ? array_search($curr_month, array_column($pairs, 0)) : array_search($curr_month, array_column($pairs, 1));

            // Prepare the SQL query to get order details for the current pair and year
            $query = "SELECT o.user_id, od.item_id, od.quantity
                      FROM orders o
                      INNER JOIN order_details od ON o.order_id = od.order_id
                      WHERE YEAR(o.date_and_time) = $curr_year
                      AND MONTH(o.date_and_time) IN ({$pairs[$curr_pair][0]}, {$pairs[$curr_pair][1]})";
            
            $result = $conn->query($query);

            // Create 2D array with user_id as key and an associative array of item_id => quantity as value
            $order_data = [];
            while ($row = $result->fetch_assoc()) {
                $user_id = $row['user_id'];
                $item_id = $row['item_id'];
                $quantity = $row['quantity'];
                
                if (!isset($order_data[$user_id])) {
                    $order_data[$user_id] = [];
                }
                
                if (!isset($order_data[$user_id][$item_id])) {
                    $order_data[$user_id][$item_id] = 0;
                }
                
                $order_data[$user_id][$item_id] += $quantity;
            }

            // Store the order data in the session
            $_SESSION['order_data'] = $order_data;

            // Determine redirection based on user_type
            if ($row['user_type'] === 'user') {
                header('Location: user_dashboard.php');
                exit;
            } elseif ($row['user_type'] === 'admin') {
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $_SESSION['error_message'] = "Invalid user type.";
            }
        } else {
            $_SESSION['error_message'] = "Incorrect password.";
        }
    } else {
        $_SESSION['error_message'] = "User not found.";
    }

    $conn->close();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Clear error message after displaying it
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
} else {
    $error_message = '';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<style>
/* Your CSS styling */
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
  <div class="left">
    <div class="topic">
      <h2>Canteen Store Department</h2>
    </div>
    <div class="image-container">
      <img src="./images/loginlogo.png" alt="Login Image" style="width: 275px; height: auto;">
    </div>
  </div>
  <div class="right">
    <h1>Login Credentials</h1>
    <?php if (!empty($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
    <form method="post">
      <label for="username" class="bold-label">Username:</label>
      <input type="text" id="username" name="username" placeholder="Type your username" required>
      <label for="password" class="bold-label">Password:</label>
      <input type="password" id="password" name="password" placeholder="Type your password" required>
      <button type="submit" name="login">Submit</button>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
