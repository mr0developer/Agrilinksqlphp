<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id']) || $_SESSION['Category'] != 0) { // Only buyers can rate
    header("Location: error.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Get order details
$sql = "SELECT o.*, p.product, p.pid, f.fid as farmer_id 
        FROM orders o 
        JOIN fproduct p ON o.product_id = p.pid 
        JOIN farmer f ON o.farmer_id = f.fid 
        WHERE o.order_id = $order_id AND o.buyer_id = " . $_SESSION['id'];
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header("Location: error.php");
    exit();
}

// Check if already rated
$sql = "SELECT * FROM product_ratings WHERE order_id = $order_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    header("Location: error.php?msg=already_rated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rate Product - <?php echo $order['product']; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .rating-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .rating-stars {
            font-size: 2em;
            color: #ffd700;
            cursor: pointer;
        }
        .rating-stars i {
            margin: 0 5px;
        }
        .rating-stars i:hover,
        .rating-stars i.active {
            color: #ffd700;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="rating-container">
            <h2>Rate Product: <?php echo $order['product']; ?></h2>
            <form id="rating-form">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input type="hidden" name="product_id" value="<?php echo $order['pid']; ?>">
                <input type="hidden" name="farmer_id" value="<?php echo $order['farmer_id']; ?>">
                <input type="hidden" name="rating" id="rating-value" value="0">
                
                <div class="rating-stars">
                    <i class="far fa-star" data-rating="1"></i>
                    <i class="far fa-star" data-rating="2"></i>
                    <i class="far fa-star" data-rating="3"></i>
                    <i class="far fa-star" data-rating="4"></i>
                    <i class="far fa-star" data-rating="5"></i>
                </div>
                
                <textarea name="review" placeholder="Write your review (optional)" rows="4"></textarea>
                
                <button type="submit">Submit Rating</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle star rating
            $('.rating-stars i').hover(
                function() {
                    const rating = $(this).data('rating');
                    $('.rating-stars i').removeClass('fas').addClass('far');
                    $('.rating-stars i').slice(0, rating).removeClass('far').addClass('fas');
                },
                function() {
                    const currentRating = $('#rating-value').val();
                    $('.rating-stars i').removeClass('fas').addClass('far');
                    $('.rating-stars i').slice(0, currentRating).removeClass('far').addClass('fas');
                }
            );

            $('.rating-stars i').click(function() {
                const rating = $(this).data('rating');
                $('#rating-value').val(rating);
                $('.rating-stars i').removeClass('fas').addClass('far');
                $('.rating-stars i').slice(0, rating).removeClass('far').addClass('fas');
            });

            // Handle form submission
            $('#rating-form').on('submit', function(e) {
                e.preventDefault();
                
                if ($('#rating-value').val() == 0) {
                    alert('Please select a rating');
                    return;
                }

                $.ajax({
                    url: 'submit_rating.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response === 'success') {
                            alert('Thank you for your rating!');
                            window.location.href = 'profileView.php';
                        } else {
                            alert('Error submitting rating. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html> 