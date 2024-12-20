<?php
include '../includes/db.php';
include '../includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user_id = $_SESSION['user_id'];

// Lấy cart_id từ cơ sở dữ liệu hoặc tạo giỏ hàng mới nếu chưa có
$sql_cart = "SELECT cart_id FROM cart WHERE user_id = $user_id";
$result_cart = $conn->query($sql_cart);
// Nếu người dùng chưa có giỏ hàng, tạo giỏ hàng mới
if ($result_cart->num_rows == 0) {
    $sql_create_cart = "INSERT INTO cart (user_id) VALUES ($user_id)";
    if ($conn->query($sql_create_cart) === TRUE) {
        $cart_id = $conn->insert_id;  // Lấy cart_id mới
    } else {
        echo "Lỗi: " . $conn->error;
    }
} else {
    // Nếu người dùng đã có giỏ hàng, lấy cart_id của họ
    $cart_id = $result_cart->fetch_assoc()['cart_id'];
}

// Lấy sản phẩm trong giỏ hàng
$sql = "SELECT c.*, p.product_name, p.price, p.image_url, s.size
        FROM cart_detail c
        JOIN product p ON c.product_id = p.product_id
        JOIN size s ON c.size_id = s.size_id
        WHERE c.cart_id = $cart_id";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Giỏ hàng của bạn</h1>
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Kích thước</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng cộng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0; // Tổng số tiền của giỏ hàng
                    while ($row = $result->fetch_assoc()):
                        $total_price = $row['quantity'] * $row['price'];
                        $grand_total += $total_price;
                    ?>
                    <tr>
                        <td><img src="./assets/images/<?= $row['image_url']; ?>" alt="<?= $row['product_name']; ?>"
                                 class="img-fluid" style="max-width: 120px;"></td>
                        <td><?= $row['product_name']; ?></td>
                        <td><?= $row['size']; ?></td>
                        <td>
                            <form method="POST" action="updatecart" class="d-inline">
                                <input type="hidden" name="cart_id" value="<?= $row['cart_id']; ?>">
                                <input type="hidden" name="product_id" value="<?= $row['product_id']; ?>">
                                <input type="hidden" name="size_id" value="<?= $row['size_id']; ?>">
                                <button type="submit" name="decrease" class="btn btn-outline-secondary btn-sm">-</button>
                                <span class="quantity-display"><?= $row['quantity']; ?></span>
                                <button type="submit" name="increase" class="btn btn-outline-secondary btn-sm">+</button>
                            </form>
                        </td>
                        <td><?= number_format($row['price'], 0, ',', '.'); ?> VNĐ</td>
                        <td><?= number_format($total_price, 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <form method="POST" action="removecart" class="d-inline">
                                <input type="hidden" name="cart_id" value="<?= $row['cart_id']; ?>">
                                <input type="hidden" name="product_id" value="<?= $row['product_id']; ?>">
                                <input type="hidden" name="size_id" value="<?= $row['size_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Tổng cộng:</strong></td>
                        <td><strong><?= number_format($grand_total, 0, ',', '.'); ?> VNĐ</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <a href="checkout" class="btn btn-success btn-lg">Thanh toán</a>
        </div>
    <?php else: ?>
        <div class="alert alert-light text-center">
            <h4 class="alert-heading">Giỏ hàng trống!</h4>
            <p>Giỏ hàng của bạn hiện tại không có sản phẩm. Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm.</p>
            <hr>
            <p class="mb-0"><a href="home" class="btn btn-outline-dark">Hãy chọn sản phẩm</a> ngay bây giờ!</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
