<?php
require_once "db.php";
require_once "layout.php";
require_login();

$MaDH = intval($_GET["MaDH"] ?? 0);
if ($MaDH <= 0) {
    die("Mã đơn hàng không hợp lệ.");
}

$donRes = $conn->query("SELECT d.*, k.TenKH FROM donhang d LEFT JOIN khachhang k ON d.MaKH = k.MaKH WHERE d.MaDH = $MaDH");
$don = $donRes->fetch_assoc();
if (!$don) {
    die("Không tìm thấy đơn hàng.");
}

$products = $conn->query("SELECT MaSP, TenSP, GiaBanSP FROM sanpham ORDER BY TenSP");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $masp = intval($_POST["MaSP"] ?? 0);
    $sl   = intval($_POST["SoLuong"] ?? 0);
    if ($masp <= 0 || $sl <= 0) {
        $message = "<p class='error'>Vui lòng chọn sản phẩm và số lượng > 0.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO chitietdonhang (MaDH, MaSP, SoLuong)
             VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE SoLuong = SoLuong + VALUES(SoLuong)");
        $stmt->bind_param("iii", $MaDH, $masp, $sl);
        if ($stmt->execute()) {
            $resGia = $conn->query("SELECT GiaBanSP FROM sanpham WHERE MaSP = $masp")->fetch_assoc();
            $tien = $resGia["GiaBanSP"] * $sl;
            $conn->query("UPDATE donhang SET TongTien = TongTien + $tien WHERE MaDH = $MaDH");
            $message = "<p class='message'>Thêm chi tiết đơn hàng thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$sql = "SELECT c.*, s.TenSP, s.GiaBanSP
        FROM chitietdonhang c
        LEFT JOIN sanpham s ON c.MaSP = s.MaSP
        WHERE c.MaDH = $MaDH";
$ct = $conn->query($sql);

render_header("Chi tiết đơn hàng", "orders.php");

echo '<p>Khách hàng: <b>' . htmlspecialchars($don["TenKH"]) . '</b></p>';
echo '<p>Ngày đặt: ' . $don["NgayDat"] . '</p>';
echo '<p>Tổng tiền hiện tại: <b>' . number_format($don["TongTien"],0,',','.') . ' đ</b></p>';

echo $message;

echo '<h2>Thêm sản phẩm vào đơn</h2>';
echo '<form method="post">';
echo '<label>Sản phẩm:</label>';
echo '<select name="MaSP" required>';
echo '<option value="">-- Chọn sản phẩm --</option>';
while ($p = $products->fetch_assoc()) {
    echo '<option value="' . $p["MaSP"] . '">' . htmlspecialchars($p["TenSP"]) . ' (' . number_format($p["GiaBanSP"],0,',','.') . ' đ)</option>';
}
echo '</select>';
echo '<label>Số lượng:</label>';
echo '<input type="number" name="SoLuong" value="1" min="1">';
echo '<input type="submit" value="Thêm vào đơn">';
echo '</form>';

echo '<h2>Danh sách sản phẩm trong đơn</h2>';
echo '<table>';
echo '<tr><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th></tr>';
while ($row = $ct->fetch_assoc()) {
    $thanhtien = $row["GiaBanSP"] * $row["SoLuong"];
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row["TenSP"]) . '</td>';
    echo '<td>' . number_format($row["GiaBanSP"],0,',','.') . ' đ</td>';
    echo '<td>' . $row["SoLuong"] . '</td>';
    echo '<td>' . number_format($thanhtien,0,',','.') . ' đ</td>';
    echo '</tr>';
}
echo '</table>';

echo '<p style="margin-top:10px;"><a href="orders.php">← Quay lại danh sách đơn</a></p>';

render_footer();
?>