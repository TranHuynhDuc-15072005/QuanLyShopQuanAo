<?php
require_once "auth.php";

function render_header($title, $activePage) {
    echo '<!DOCTYPE html>';
    echo '<html lang="vi">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<title>' . htmlspecialchars($title) . '</title>';
    echo '<link rel="stylesheet" href="style.css">';
    echo '</head>';
    echo '<body>';
    echo '<div class="wrapper">';
    echo '<h1>Hệ thống quản lý shop quần áo</h1>';

    // Thanh trạng thái đăng nhập
    $user = current_user();
    echo '<div class="status-bar">';
    if ($user) {
        echo 'Xin chào, <b>' . htmlspecialchars($user["TenKH"]) . '</b> | ';
        echo '<a href="logout.php">Đăng xuất</a>';
    } else {
        echo '<a href="login.php">Đăng nhập</a> | ';
        echo '<a href="register.php">Đăng ký</a>';
    }
    echo '</div>';

    // Menu chính (không có Tổng quan)
    $items = [
        "categories.php" => "Danh mục",
        "products.php"   => "Sản phẩm",
        "promotions.php" => "Khuyến mãi",
        "customers.php"  => "Khách hàng",
        "employees.php"  => "Nhân viên",
        "orders.php"     => "Đơn hàng",
        "payments.php"   => "Thanh toán",
        "requests.php"   => "Phiếu yêu cầu"
    ];

    echo '<nav>';
    foreach ($items as $file => $label) {
        $cls = ($file === $activePage) ? 'active' : '';
        echo '<a href="' . $file . '" class="' . $cls . '">' . htmlspecialchars($label) . '</a>';
    }
    echo '</nav>';
}

function render_footer() {
    echo '</div>';
    echo '</body>';
    echo '</html>';
}
?>