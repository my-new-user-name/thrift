<?php
session_start();
include('db.php');

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Check if the product_id is set in the URL
if (!isset($_GET['product_id'])) {
    header("Location: upload_history.php");
    exit();
}

$product_id = intval($_GET['product_id']);

// Fetch product details (Photo, Item, Price, Address, Details, Uploaded On)
$product_sql = "SELECT item, price, address, details, photo, created_at FROM products WHERE id = ?";
$stmt = $conn->prepare($product_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();
$stmt->close();

// Fetch comments for the product
$comments_sql = "SELECT u.username, c.comment, c.created_at 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 WHERE c.product_id = ? 
                 ORDER BY c.created_at DESC";
$stmt = $conn->prepare($comments_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$comments_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments for <?php echo htmlspecialchars($product['item']); ?></title>
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
</head>
<body>
    <div class="container">
         <div class="heads">
              <a href="product_history_uploaded.php" class="btn"><i class='bx bx-arrow-back'></i></a>
              <h1><?php echo htmlspecialchars($product['item']); ?>'s Post</h1>
         </div>


        <!-- Product Details Section -->
        <div class="product-details">
            <p><strong>Item:</strong> <?php echo htmlspecialchars($product['item']); ?></p>
            <p><strong>Price:</strong> ₱<?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($product['address']); ?></p>
            <p><strong>Details:</strong> <?php echo htmlspecialchars($product['details']); ?></p>

            <img src="<?php echo htmlspecialchars($product['photo']); ?>" width="80" height="80" alt="Product Image">
        </div>

        <!-- Comments Section for this Product -->
        <div class="comments-section">
    <h2>Comments</h2>
    <?php
    if ($comments_result->num_rows > 0) {
        while ($row = $comments_result->fetch_assoc()) {
            echo "<div class='comment-wrapper'>";
                echo "<div class='comment-box'>";
                    echo "<p><strong>" . htmlspecialchars($row['username']) . "</strong></p>";
                    echo "<div class='comment-text'>" . htmlspecialchars($row['comment']) . "</div>";
                echo "</div>"; // end comment-box
                echo "<div class='comment-time'>Posted at: " . $row['created_at'] . "</div>";

            echo "</div><br>";
        }
    } else {
        echo "<p>No comments found for this product.</p>";
    }
    ?>
</div>

    </div>

<style>
    .container {
    max-width: 400px;
    margin: 30px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
    background-color: #fdfdfd;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #333;
}

.heads {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.heads .btn {
    position: absolute;
    left: 0;
    padding: 10px 18px;
    background-color: #fff;
    color: black;
    text-decoration: none;
    border-radius: 6px;
    font-size: 19px;
    transition: background-color 0.3s;
}

.heads .btn:hover {
    background-color: #fff;
}

.heads h1 {
    margin: 0;
    font-size: 24px;
    color: #333;
}


.product-details {
    background-color: #f0f8ff;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.product-details img {
    border-radius: 8px;
    margin-bottom: 10px;
}

.product-details p {
    margin: 6px 0;
}

.comments-section {
    background-color: #fff;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.comment-box {
    background-color: #f0f2f5;
    padding: 10px 15px;
    border-radius: 18px;
    margin-bottom: 5px;
    width: fit-content;
    max-width: 80%;
}

.comment-text {
    font-size: 14px;
    color: #050505;
}

.comment-time {
    font-size: 12px;
    color: #65676b;
    margin-left: 15px;
    margin-top: 5px;
    text-decoration: none; /* <-- extra safe, no underline */
}



</style>
</body>
</html>
