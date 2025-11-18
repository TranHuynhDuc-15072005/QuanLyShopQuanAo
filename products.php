<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ten  = trim($_POST["TenSP"] ?? "");
    $mota = trim($_POST["MoTaSP"] ?? "");
    $gia  = floatval($_POST["GiaBanSP"] ?? 0);
    $madm = intval($_POST["MaDM"] ?? 0);
    $makm = $_POST["MaKM"] !== "" ? intval($_POST["MaKM"]) : null;

    if ($ten === "" || $gia <= 0) {
        $message = "<p class='error'>Tên sản phẩm và giá phải hợp lệ.</p>";
    } else {
        if ($makm === null) {
            $stmt = $conn->prepare("INSERT INTO sanpham (TenSP, MoTaSP, GiaBanSP, MaDM, MaKM) VALUES (?,?,?, ?, NULL)");
            $stmt->bind_param("ssdi", $ten, $mota, $gia, $madm);
        } else {
            $stmt = $conn->prepare("INSERT INTO sanpham (TenSP, MoTaSP, GiaBanSP, MaDM, MaKM) VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssdii", $ten, $mota, $gia, $madm, $makm);
        }
        if ($stmt->execute()) {
            $message = "<p class='message'>Thêm sản phẩm thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$categories = $conn->query("SELECT * FROM danhmuc ORDER BY TenDM ASC");
$promos     = $conn->query("SELECT * FROM khuyenmai ORDER BY TenKM ASC");

$sql = "SELECT s.*, d.TenDM, k.TenKM
        FROM sanpham s
        LEFT JOIN danhmuc d ON s.MaDM = d.MaDM
        LEFT JOIN khuyenmai k ON s.MaKM = k.MaKM
        ORDER BY MaSP DESC";
$products = $conn->query($sql);

render_header("Quản lý sản phẩm", "products.php");

echo $message;

echo '<h2>Thêm sản phẩm mới</h2>';
echo '<form method="post">';
echo '<label>Tên sản phẩm:</label>';
echo '<input type="text" name="TenSP" required>';
echo '<label>Mô tả:</label>';
echo '<input type="text" name="MoTaSP">';
echo '<label>Giá bán:</label>';
echo '<input type="number" step="0.01" name="GiaBanSP" required>';
echo '<label>Danh mục:</label>';
echo '<select name="MaDM">';
while ($dm = $categories->fetch_assoc()) {
    echo '<option value="' . $dm["MaDM"] . '">' . htmlspecialchars($dm["TenDM"]) . '</option>';
}
echo '</select>';
echo '<label>Khuyến mãi (nếu có):</label>';
echo '<select name="MaKM">';
echo '<option value="">-- Không áp dụng --</option>';
while ($km = $promos->fetch_assoc()) {
    echo '<option value="' . $km["MaKM"] . '">' . htmlspecialchars($km["TenKM"]) . '</option>';
}
echo '</select>';
echo '<input type="submit" value="Thêm sản phẩm">';
echo '</form>';

echo '<h2>Danh sách sản phẩm</h2>';
echo '<table>';
echo '<tr><th>Mã SP</th><th>Tên SP</th><th>Danh mục</th><th>Khuyến mãi</th><th>Giá bán</th><th>Mô tả</th></tr>';
while ($sp = $products->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $sp["MaSP"] . '</td>';
    echo '<td>' . htmlspecialchars($sp["TenSP"]) . '</td>';
    echo '<td>' . htmlspecialchars($sp["TenDM"]) . '</td>';
    echo '<td>' . htmlspecialchars($sp["TenKM"]) . '</td>';
    echo '<td>' . number_format($sp["GiaBanSP"],0,',','.') . ' đ</td>';
    echo '<td>' . htmlspecialchars($sp["MoTaSP"]) . '</td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>