<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";

$khach = $conn->query("SELECT MaKH, TenKH FROM khachhang ORDER BY TenKH");
$nhanvien = $conn->query("SELECT MaNV, TenNV FROM nhanvien ORDER BY TenNV");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $makh = intval($_POST["MaKH"] ?? 0);
    $manv = intval($_POST["MaNV"] ?? 0);

    if ($makh <= 0 || $manv <= 0) {
        $message = "<p class='error'>Vui lòng chọn khách hàng và nhân viên.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO donhang (MaKH, MaNV) VALUES (?,?)");
        $stmt->bind_param("ii", $makh, $manv);
        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $message = "<p class='message'>Tạo đơn hàng #$newId thành công. <a href="order_detail.php?MaDH=$newId">Thêm chi tiết</a></p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$sql = "SELECT d.*, k.TenKH, n.TenNV
        FROM donhang d
        LEFT JOIN khachhang k ON d.MaKH = k.MaKH
        LEFT JOIN nhanvien n ON d.MaNV = n.MaNV
        ORDER BY d.MaDH DESC";
$orders = $conn->query($sql);

render_header("Quản lý đơn hàng", "orders.php");

echo $message;

echo '<h2>Tạo đơn hàng mới</h2>';
echo '<form method="post">';
echo '<label>Khách hàng:</label>';
echo '<select name="MaKH" required>';
echo '<option value="">-- Chọn khách --</option>';
while ($k = $khach->fetch_assoc()) {
    echo '<option value="' . $k["MaKH"] . '">' . htmlspecialchars($k["TenKH"]) . '</option>';
}
echo '</select>';
echo '<label>Nhân viên phụ trách:</label>';
echo '<select name="MaNV" required>';
echo '<option value="">-- Chọn nhân viên --</option>';
while ($n = $nhanvien->fetch_assoc()) {
    echo '<option value="' . $n["MaNV"] . '">' . htmlspecialchars($n["TenNV"]) . '</option>';
}
echo '</select>';
echo '<input type="submit" value="Tạo đơn hàng">';
echo '</form>';

echo '<h2>Danh sách đơn hàng</h2>';
echo '<table>';
echo '<tr><th>Mã ĐH</th><th>Khách hàng</th><th>Nhân viên</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Chi tiết</th></tr>';
while ($d = $orders->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $d["MaDH"] . '</td>';
    echo '<td>' . htmlspecialchars($d["TenKH"]) . '</td>';
    echo '<td>' . htmlspecialchars($d["TenNV"]) . '</td>';
    echo '<td>' . $d["NgayDat"] . '</td>';
    echo '<td>' . number_format($d["TongTien"],0,',','.') . ' đ</td>';
    echo '<td><a href="order_detail.php?MaDH=' . $d["MaDH"] . '">Xem / thêm</a></td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>