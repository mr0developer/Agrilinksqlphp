<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    $_SESSION['message'] = "Please login as a buyer to access the order chat!";
    header("Location: Login/error.php");
    exit();
}

// Get cart items and their farmers
$buyer_id = $_SESSION['id'];
$sql = "SELECT DISTINCT f.fid, f.fname, f.fmobile, f.femail 
        FROM mycart c 
        JOIN fproduct p ON c.pid = p.pid 
        JOIN farmer f ON p.fid = f.fid 
        WHERE c.bid = $buyer_id";
$result = mysqli_query($conn, $sql);

// Create order for each farmer
while ($farmer = mysqli_fetch_assoc($result)) {
    $farmer_id = $farmer['fid'];
    
    // Get products from this farmer in cart
    $products_sql = "SELECT p.*, c.quantity 
                    FROM mycart c 
                    JOIN fproduct p ON c.pid = p.pid 
                    WHERE c.bid = $buyer_id AND p.fid = $farmer_id";
    $products_result = mysqli_query($conn, $products_sql);
    
    // Create order
    $order_sql = "INSERT INTO orders (buyer_id, farmer_id, status, created_at) 
                  VALUES ($buyer_id, $farmer_id, 'pending', NOW())";
    mysqli_query($conn, $order_sql);
    $order_id = mysqli_insert_id($conn);
    
    // Add products to order
    while ($product = mysqli_fetch_assoc($products_result)) {
        $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES ($order_id, {$product['pid']}, {$product['quantity']}, {$product['price']})";
        mysqli_query($conn, $order_item_sql);
    }
    
    // Clear cart items for this farmer
    $clear_cart_sql = "DELETE c FROM mycart c 
                      JOIN fproduct p ON c.pid = p.pid 
                      WHERE c.bid = $buyer_id AND p.fid = $farmer_id";
    mysqli_query($conn, $clear_cart_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Chat - Agrilink</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="bootstrap\js\bootstrap.min.js"></script>
		<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="login.css"/>
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
		<link rel="stylesheet" href="indexfooter.css" />
    <style>
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chat-header {
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: #f9f9f9;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
            position: relative;
        }
        .buyer-message {
            background-color: #e3f2fd;
            margin-left: auto;
            text-align: right;
        }
        .farmer-message {
            background-color: #f5f5f5;
            margin-right: auto;
        }
        .message-sender {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        .message-content {
            word-wrap: break-word;
        }
        .message-time {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
        .chat-input {
            padding: 15px;
            background: white;
            border-top: 1px solid #ddd;
        }
        .farmer-info {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <?php require 'menu.php'; ?>

    <div class="container">
        <div class="chat-container">
            <div class="chat-header">
                <h3>Order Chat</h3>
            </div>
            
            <?php
            // Get the first farmer's details
            mysqli_data_seek($result, 0);
            $farmer = mysqli_fetch_assoc($result);
            
            if ($farmer) {
            ?>
            <div class="farmer-info">
                <h4>Farmer: <?php echo htmlspecialchars($farmer['fname']); ?></h4>
                <p>Contact: <?php echo htmlspecialchars($farmer['fmobile']); ?></p>
                <p>Email: <?php echo htmlspecialchars($farmer['femail']); ?></p>
            </div>

            <div class="chat-messages" id="chat-messages">
                <!-- Chat messages will be loaded here -->
            </div>

            <div class="chat-input">
                <form id="message-form">
                    <input type="hidden" id="farmer_id" value="<?php echo $farmer['fid']; ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" id="message-input" placeholder="Type your message...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php } else { ?>
            <div class="alert alert-info">
                <h4>No Active Orders</h4>
                <p>You don't have any active orders to chat about. Please add items to your cart and place an order first.</p>
                <a href="myCart.php" class="btn btn-primary">View Cart</a>
            </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const farmerId = $('#farmer_id').val();
            
            // Function to load messages
            function loadMessages() {
                $.get('get_messages.php?farmer_id=' + farmerId, function(data) {
                    $('#chat-messages').html(data);
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                });
            }

            // Load messages initially and every 3 seconds
            loadMessages();
            setInterval(loadMessages, 3000);

            // Handle message submission
            $('#message-form').submit(function(e) {
                e.preventDefault();
                const message = $('#message-input').val();
                if (message.trim() !== '') {
                    $.post('send_message.php?farmer_id=' + farmerId, { message: message }, function(response) {
                        if (response.status === 'success') {
                            $('#message-input').val('');
                            loadMessages();
                        } else {
                            alert(response.message || 'Error sending message. Please try again.');
                        }
                    }, 'json');
                }
            });
        });
    </script>
</body>
</html> 