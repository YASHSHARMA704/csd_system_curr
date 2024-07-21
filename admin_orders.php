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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['action']) && isset($_POST['order_id'])) {
            $order_id = intval($_POST['order_id']);
            $action = $_POST['action'];

            if ($action == 'approve') {
                $update_query = "UPDATE orders SET status = 2 WHERE order_id = $order_id"; // Assuming status 2 means approved
            } elseif ($action == 'reject') {
                $update_query = "UPDATE orders SET status = 3 WHERE order_id = $order_id"; // Assuming status 3 means rejected
            }

            if (mysqli_query($conn, $update_query)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
            }
            exit();
        }
    }
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

            .btn-action {
                margin: 0 5px;
            }

            td.d-flex {
        border: none; /* Remove any border around the td */
        outline: none; /* Remove any outline */
        box-shadow: none; /* Remove any shadow */
        gap:4px;
        padding: 0; /* Remove any default padding */
        margin: 0; /* Remove any default margin */
    }

    /* Remove any border, outline, or shadow from buttons */
    td.d-flex button {
        border: none; /* Remove border from buttons */
        outline: none; /* Remove outline from buttons */
        box-shadow: none; /* Remove any shadow from buttons */
        margin: 0; /* Ensure no extra margin around buttons */
    }
            
        </style>
    </head>
    <body>

        <!-- navbar -->
        <?php include 'navbar.php'; ?>

        <div class="container">
            <!-- Current Orders Section -->
            <h2 class="section-title">Current Orders</h2>
            <div class="table-container">
                <?php
                $query = "SELECT * FROM orders WHERE status = 1";
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

                        $item_query = "SELECT od.*, i.category, i.description, i.Unit as unit, i.Remarks as remarks, i.stock_quantity, od.date_and_time 
                                    FROM order_details od 
                                    JOIN items i ON od.item_id = i.itemId 
                                    WHERE od.order_id = $order_id";
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
                            $date_and_time = $item['date_and_time'];
                            $stock_quantity = $item['stock_quantity'];
                            $total_price += $price * $quantity;

                            echo "<tr id='item-$item_id'>";
                            echo "<td>$serial_number</td>";
                            echo "<td>$item_id</td>";
                            echo "<td>$item_name</td>";
                            echo "<td>$category</td>";
                            echo "<td>$description</td>";
                            echo "<td class='item-quantity'>$quantity</td>";
                            echo "<td class='item-price'>" . number_format($price, 2) . "</td>";
                            echo "<td>$unit</td>";
                            echo "<td>$remarks</td>";
                            echo "<td>$date_and_time</td>";
                            echo "<td class='d-flex mt-2 gap-2'>
                                    <button class='btn btn-sm btn-primary btn-update' 
                                            data-item-id='$item_id' 
                                            data-quantity='$quantity' 
                                            data-stock-quantity='$stock_quantity'>Update</button>
                                    <button class='btn btn-sm btn-danger btn-delete' 
                                            data-item-id='$item_id'>Delete</button>
                                </td>";
                            echo "</tr>";

                            $serial_number++;
                        }

                        echo "<tr id='total-price-row'>";
                        echo "<td colspan='9' class='text-right total-price'>Total Price</td>";
                        echo "<td class='total-price' colspan='2' id='total-price'>" . number_format($total_price, 2) . "</td>";
                        echo "</tr>";

                        echo "</tbody>";
                        echo "</table>";
                        echo "<div class='text-right'>
                                <button class='btn btn-success btn-action btn-approve' data-order-id='$order_id'>Approve Order</button>
                                <button class='btn btn-danger btn-action btn-reject' data-order-id='$order_id'>Reject Order</button>
                            </div>";
                    }
                }
                ?>
            </div>
        </div>

        <!-- Update Quantity Modal -->
        <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Quantity</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="updateForm">
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" step="0.01" required>
                            </div>
                            <input type="hidden" id="updateItemId" name="item_id">
                            <input type="hidden" id="updateStockQuantity" name="stock_quantity">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery and Bootstrap JS -->
        <script src="jquery-3.3.1.slim.min.js"></script>
        <script src="popper.min.js"></script>
        <script src="bootstrap.min.js"></script>

        <script>
        $(document).ready(function() {
            // Function to update the total price
            function updateTotalPrice() {
                var totalPrice = 0;
                $('.item-quantity').each(function() {
                    var quantity = parseFloat($(this).text());
                    var price = parseFloat($(this).closest('tr').find('.item-price').text());
                    totalPrice += quantity * price;
                });
                $('#total-price').text(totalPrice.toFixed(2));
            }

            // Update modal button click handler
            $('.btn-update').on('click', function() {
                var itemId = $(this).data('item-id');
                var quantity = $(this).data('quantity');
                var stockQuantity = $(this).data('stock-quantity');
                $('#updateItemId').val(itemId);
                $('#quantity').val(quantity);
                $('#updateStockQuantity').val(stockQuantity);
                $('#quantity').attr('max', stockQuantity);
                $('#updateModal').modal('show');
            });

            // Update form submit handler
            $('#updateForm').on('submit', function(e) {
                e.preventDefault();
                var itemId = $('#updateItemId').val();
                var newQuantity = parseFloat($('#quantity').val());
                var stockQuantity = parseFloat($('#updateStockQuantity').val());

                if (newQuantity < 1 || newQuantity > stockQuantity) {
                    alert('Quantity must be between 1 and ' + stockQuantity);
                    return;
                }

                $('#item-' + itemId + ' .item-quantity').text(newQuantity);
                updateTotalPrice();
                $('#updateModal').modal('hide');
            });

            // Delete button click handler
            $('.btn-delete').on('click', function() {
                var itemId = $(this).data('item-id');
                $('#item-' + itemId).remove();
                updateTotalPrice();
            });

            // Approve button click handler
            $('.btn-approve').on('click', function() {
                var orderId = $(this).data('order-id');
                $.post('', { action: 'approve', order_id: orderId }, function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        alert('Order approved successfully.');
                        location.reload();
                    } else {
                        alert('Failed to approve order: ' + data.error);
                    }
                });
            });

            // Reject button click handler
            $('.btn-reject').on('click', function() {
                var orderId = $(this).data('order-id');
                $.post('', { action: 'reject', order_id: orderId }, function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        alert('Order rejected successfully.');
                        location.reload();
                    } else {
                        alert('Failed to reject order: ' + data.error);
                    }
                });
            });
        });
    </script>


    </body>
    </html>
