<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ten    = trim($_POST["TenKH"] ?? "");
    $diachi = trim($_POST["DiaChi"] ?? "");
    $email  = trim($_POST["Email"] ?? "");
    $sdt    = trim($_POST["SDT"] ?? "");
    $matkhau = trim($_POST["MatKhau"] ?? "");

    if ($ten === "") {
        $message = "<p class='error'>Tên khách hàng không được rỗng.</p>";
    } else {
        $hash = $matkhau ? password_hash($matkhau, PASSWORD_DEFAULT) : null;

        if ($hash) {
            $stmt = $conn->prepare("INSERT INTO khachhang (TenKH, DiaChi, MatKhau, Email, SDT) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $ten, $diachi, $hash, $email, $sdt);
        } else {
            $stmt = $conn->prepare("INSERT INTO khachhang (TenKH, DiaChi, Email, SDT) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $ten, $diachi, $email, $sdt);
        }

        if ($stmt->execute()) {
            $message = "<p class='message'>Thêm khách hàng thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM khachhang ORDER BY MaKH DESC");

render_header("Quản lý khách hàng", "customers.php");

echo $message;

echo '<h2>Thêm khách hàng</h2>';
echo '<form method="post">';
echo '<label>Tên khách hàng:</label>';
echo '<input type="text" name="TenKH" required>';
echo '<label>Địa chỉ:</label>';
echo '<input type="text" name="DiaChi">';
echo '<label>Email:</label>';
echo '<input type="text" name="Email">';
echo '<label>Số điện thoại:</label>';
echo '<input type="text" name="SDT">';
echo '<label>Mật khẩu (nếu muốn tạo tài khoản đăng nhập):</label>';
echo '<input type="password" name="MatKhau">';
echo '<input type="submit" value="Thêm khách hàng">';
echo '</form>';

echo '<h2>Danh sách khách hàng</h2>';
echo '<table>';
echo '<tr><th>Mã KH</th><th>Tên KH</th><th>Địa chỉ</th><th>Email</th><th>SDT</th></tr>';
while ($kh = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $kh["MaKH"] . '</td>';
    echo '<td>' . htmlspecialchars($kh["TenKH"]) . '</td>';
    echo '<td>' . htmlspecialchars($kh["DiaChi"]) . '</td>';
    echo '<td>' . htmlspecialchars($kh["Email"]) . '</td>';
    echo '<td>' . htmlspecialchars($kh["SDT"]) . '</td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>