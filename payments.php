<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";

$orders = $conn->query("SELECT MaDH, TongTien FROM donhang ORDER BY MaDH DESC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $madh = intval($_POST["MaDH"] ?? 0);
    $sotien = floatval($_POST["SoTien"] ?? 0);
    $pt = trim($_POST["PhuongThucTT"] ?? "");

    if ($madh <= 0 || $sotien <= 0) {
        $message = "<p class='error'>Vui lòng chọn đơn hàng và số tiền > 0.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO thanhtoan (SoTien, PhuongThucTT, MaDH) VALUES (?,?,?)");
        $stmt->bind_param("dsi", $sotien, $pt, $madh);
        if ($stmt->execute()) {
            $message = "<p class='message'>Thêm thanh toán thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$list = $conn->query("SELECT t.*, d.TongTien FROM thanhtoan t LEFT JOIN donhang d ON t.MaDH = d.MaDH ORDER BY t.MaTT DESC");

render_header("Quản lý thanh toán", "payments.php");

echo $message;

echo '<h2>Thêm thanh toán</h2>';
echo '<form method="post">';
echo '<label>Đơn hàng:</label>';
echo '<select name="MaDH" required>';
echo '<option value="">-- Chọn đơn hàng --</option>';
while ($d = $orders->fetch_assoc()) {
    echo '<option value="' . $d["MaDH"] . '">ĐH #' . $d["MaDH"] . ' (' . number_format($d["TongTien"],0,',','.') . ' đ)</option>';
}
echo '</select>';
echo '<label>Số tiền thanh toán:</label>';
echo '<input type="number" step="0.01" name="SoTien" required>';
echo '<label>Phương thức:</label>';
echo '<input type="text" name="PhuongThucTT" placeholder="Tiền mặt, chuyển khoản...">';
echo '<input type="submit" value="Thêm thanh toán">';
echo '</form>';

echo '<h2>Lịch sử thanh toán</h2>';
echo '<table>';
echo '<tr><th>Mã TT</th><th>Đơn hàng</th><th>Ngày TT</th><th>Số tiền</th><th>Phương thức</th></tr>';
while ($t = $list->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $t["MaTT"] . '</td>';
    echo '<td>#' . $t["MaDH"] . '</td>';
    echo '<td>' . $t["NgayTT"] . '</td>';
    echo '<td>' . number_format($t["SoTien"],0,',','.') . ' đ</td>';
    echo '<td>' . htmlspecialchars($t["PhuongThucTT"]) . '</td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>