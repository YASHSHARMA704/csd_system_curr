<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "csd_system";

session_start();

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Sorry, Connection with database is not built " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="all.min.css">
    <style>
        body {
            background-color: #e6f7ff; /* Light blue background color */
            font-family: Arial, sans-serif;
        }

        .section-title {
            margin-top: 20px;
            color: #2c3e50; /* Darker shade for heading */
            font-weight: bold;
        }

        .table-container {
            margin-top: 20px;
            background-color: #ffffff; /* White background for table */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .no-orders {
            text-align: center;
            font-size: 1.2rem;
            color: #95a5a6;
            margin-top: 20px;
        }

        .total-price {
            font-weight: bold;
        }

        h4 {
            color: #3498db; /* Bright blue color for Order ID heading */
            margin-bottom: 10px;
        }

        .table thead th {
            background-color: #ecf0f1; /* Light grey background for table header */
            color: #2c3e50; /* Dark text color for table header */
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Very light grey for zebra striping */
        }

        .table tbody tr:hover {
            background-color: #e0f7fa; /* Light cyan hover effect */
        }

        .btn-primary {
            background-color: #3498db; /* Bright blue for primary button */
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9; /* Darker blue for hover effect */
            border-color: #2980b9;
        }

        .btn-back {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .temp
        {
            margin-top: 110px;
            margin-right: 180px;
        }
    </style>
</head>
<body>

    <!-- Back Button -->
    <!-- <a href="user_dashboard.php" class="btn btn-secondary btn-back">Back</a> -->
    <a href="user_dashboard.php" class="btn btn-secondary btn-back font-weight-bold temp">&lt; Back</a>
    
    <!-- navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <!-- Current Orders Section -->
        <h2 class="section-title">Current Orders</h2>
        <div class="table-container">
            <?php
            $query = "SELECT * FROM orders WHERE user_id = $user_id AND status = 1";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 0) {
                echo "<div class='no-orders'>No current orders.</div>";
            } else {
                while ($order = mysqli_fetch_assoc($result)) {
                    $order_id = $order['order_id'];
                    echo "<h4>Order ID: $order_id</h4>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Sno.</th>";
                    echo "<th>Item ID</th>";
                    echo "<th>Item Name</th>";
                    echo "<th>Category</th>";
                    echo "<th>Description</th>";
                    echo "<th>Quantity</th>";
                    echo "<th>Price</th>";
                    echo "<th>Unit</th>";
                    echo "<th>Remarks</th>";
                    echo "<th>Date and Time</th>"; 
                    echo "<th>Actions</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $item_query = "SELECT od.*, i.category, i.description, i.Unit as unit, i.Remarks as remarks, od.date_and_time FROM order_details od JOIN items i ON od.item_id = i.itemId WHERE od.order_id = $order_id";
                    $item_result = mysqli_query($conn, $item_query);
                    $serial_number = 1;
                    $total_price = 0;

                    while ($item = mysqli_fetch_assoc($item_result)) {
                        $item_id = $item['item_id'];
                        $item_name = $item['item_name'];
                        $category = $item['category'];
                        $description = $item['description'];
                        $quantity = $item['quantity'];
                        $unit = $item['unit'];
                        $price = $item['price'];
                        $remarks = $item['remarks'];
                        $date_and_time = $item['date_and_time']; // Added Column
                        $total_price += $price * $quantity;

                        echo "<tr>";
                        echo "<td>$serial_number</td>";
                        echo "<td>$item_id</td>";
                        echo "<td>$item_name</td>";
                        echo "<td>$category</td>";
                        echo "<td>$description</td>";
                        echo "<td>$quantity</td>";
                        echo "<td>" . number_format($price, 2) . "</td>";
                        echo "<td>$unit</td>";
                        echo "<td>$remarks</td>";
                        echo "<td>$date_and_time</td>"; // Added Column
                        echo "<td><a href='item_details.php?item_id=$item_id' class='btn btn-primary'>View Details</a></td>";
                        echo "</tr>";

                        $serial_number++;
                    }

                    echo "<tr>";
                    echo "<td colspan='9' class='text-right total-price'>Total Price</td>";
                    echo "<td class='total-price' colspan='2'>" . number_format($total_price, 2) . "</td>";
                    echo "</tr>";

                    echo "</tbody>";
                    echo "</table>";
                }
            }
            ?>
        </div>

        <!-- Approved Orders Section -->
        <h2 class="section-title">Approved Orders</h2>
        <div class="table-container">
            <?php
            $query = "SELECT * FROM orders WHERE user_id = $user_id AND status = 2";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 0) {
                echo "<div class='no-orders'>No approved orders.</div>";
            } else {
                while ($order = mysqli_fetch_assoc($result)) {
                    $order_id = $order['order_id'];
                    echo "<h4>Order ID: $order_id</h4>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Sno.</th>";
                    echo "<th>Item ID</th>";
                    echo "<th>Item Name</th>";
                    echo "<th>Category</th>";
                    echo "<th>Description</th>";
                    echo "<th>Quantity</th>";
                    echo "<th>Price</th>";
                    echo "<th>Unit</th>";
                    echo "<th>Remarks</th>";
                    echo "<th>Date and Time</th>";
                    echo "<th>Actions</th>"; // Added Column
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $item_query = "SELECT od.*, i.category, i.description, i.Unit as unit, i.Remarks as remarks, od.date_and_time FROM order_details od JOIN items i ON od.item_id = i.itemId WHERE od.order_id = $order_id";
                    $item_result = mysqli_query($conn, $item_query);
                    $serial_number = 1;
                    $total_price = 0;

                    while ($item = mysqli_fetch_assoc($item_result)) {
                        $item_id = $item['item_id'];
                        $item_name = $item['item_name'];
                        $category = $item['category'];
                        $description = $item['description'];
                        $quantity = $item['quantity'];
                        $unit = $item['unit'];
                        $price = $item['price'];
                        $remarks = $item['remarks'];
                        $date_and_time = $item['date_and_time']; // Added Column
                        $total_price += $price * $quantity;

                        echo "<tr>";
                        echo "<td>$serial_number</td>";
                        echo "<td>$item_id</td>";
                        echo "<td>$item_name</td>";
                        echo "<td>$category</td>";
                        echo "<td>$description</td>";
                        echo "<td>$quantity</td>";
                        echo "<td>" . number_format($price, 2) . "</td>";
                        echo "<td>$unit</td>";
                        echo "<td>$remarks</td>";
                        echo "<td>$date_and_time</td>"; // Added Column
                        echo "<td><a href='item_details.php?item_id=$item_id' class='btn btn-primary'>View Details</a></td>"; // Added Column
                        echo "</tr>";

                        $serial_number++;
                    }

                    echo "<tr>";
                    echo "<td colspan='9' class='text-right total-price'>Total Price</td>";
                    echo "<td class='total-price' colspan='2'>" . number_format($total_price, 2) . "</td>";
                    echo "</tr>";

                    echo "</tbody>";
                    echo "</table>";
                }
            }
            ?>
        </div>

        <!-- Rejected Orders Section -->
        <h2 class="section-title">Rejected Orders</h2>
        <div class="table-container">
            <?php
            $query = "SELECT * FROM orders WHERE user_id = $user_id AND status = 0";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 0) {
                echo "<div class='no-orders'>No rejected orders.</div>";
            } else {
                while ($order = mysqli_fetch_assoc($result)) {
                    $order_id = $order['order_id'];
                    echo "<h4>Order ID: $order_id</h4>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Sno.</th>";
                    echo "<th>Item ID</th>";
                    echo "<th>Item Name</th>";
                    echo "<th>Category</th>";
                    echo "<th>Description</th>";
                    echo "<th>Quantity</th>";
                    echo "<th>Price</th>";
                    echo "<th>Unit</th>";
                    echo "<th>Remarks</th>";
                    echo "<th>Date and Time</th>"; 
                    echo "<th>Actions</th>"; // Added Column
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $item_query = "SELECT od.*, i.category, i.description, i.Unit as unit, i.Remarks as remarks, od.date_and_time FROM order_details od JOIN items i ON od.item_id = i.itemId WHERE od.order_id = $order_id";
                    $item_result = mysqli_query($conn, $item_query);
                    $serial_number = 1;
                    $total_price = 0;

                    while ($item = mysqli_fetch_assoc($item_result)) {
                        $item_id = $item['item_id'];
                        $item_name = $item['item_name'];
                        $category = $item['category'];
                        $description = $item['description'];
                        $quantity = $item['quantity'];
                        $unit = $item['unit'];
                        $price = $item['price'];
                        $remarks = $item['remarks'];
                        $date_and_time = $item['date_and_time']; // Added Column
                        $total_price += $price * $quantity;

                        echo "<tr>";
                        echo "<td>$serial_number</td>";
                        echo "<td>$item_id</td>";
                        echo "<td>$item_name</td>";
                        echo "<td>$category</td>";
                        echo "<td>$description</td>";
                        echo "<td>$quantity</td>";
                        echo "<td>" . number_format($price, 2) . "</td>";
                        echo "<td>$unit</td>";
                        echo "<td>$remarks</td>";
                        echo "<td>$date_and_time</td>"; // Added Column
                        echo "<td><a href='item_details.php?item_id=$item_id' class='btn btn-primary'>View Details</a></td>"; // Added Column
                        echo "</tr>";

                        $serial_number++;
                    }

                    echo "<tr>";
                    echo "<td colspan='9' class='text-right total-price'>Total Price</td>";
                    echo "<td class='total-price' colspan='2'>" . number_format($total_price, 2) . "</td>";
                    echo "</tr>";

                    echo "</tbody>";
                    echo "</table>";
                }
            }
            ?>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="jquery-3.3.1.slim.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
</body>
</html>
