<?php
include './includes/db.php';

// Số lượng sản phẩm hiển thị mỗi trang
$items_per_page = 9;

// Xác định trang hiện tại, nếu không có trang sẽ mặc định là 1
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Tính OFFSET
$offset = ($current_page - 1) * $items_per_page;

// Truy vấn để lấy sản phẩm theo phân trang
$sql = "SELECT * FROM product LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);

// Lấy tổng số sản phẩm để tính số trang
$total_sql = "SELECT COUNT(*) AS total FROM product";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $items_per_page);
?>

<div class="container mt-4">
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="assets/images/<?= $row['image_url']; ?>" class="card-img-top" alt="<?= $row['product_name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['product_name']; ?></h5>
                        <!-- Loại bỏ mô tả -->
                        <!-- <p class="card-text"><?= $row['description']; ?></p> -->
                        <p class="card-text"><strong><?= number_format($row['price'], 0, ',', '.'); ?> VNĐ</strong></p>
                        <a href="product_detail?id=<?= $row['product_id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Phân trang -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=1" aria-label="First">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Các nút trang -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $total_pages ?>" aria-label="Last">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>