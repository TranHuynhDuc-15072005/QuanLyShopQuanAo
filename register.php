<?php
require_once "db.php";
require_once "layout.php";

if (current_user()) {
    header("Location: categories.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ten     = trim($_POST["TenKH"] ?? "");
    $email   = trim($_POST["Email"] ?? "");
    $matkhau = trim($_POST["MatKhau"] ?? "");
    $nhapLai = trim($_POST["MatKhau2"] ?? "");
    $diachi  = trim($_POST["DiaChi"] ?? "");
    $sdt     = trim($_POST["SDT"] ?? "");

    if ($ten === "" || $email === "" || $matkhau === "" || $nhapLai === "") {
        $message = "<p class='error'>Vui lòng nhập đầy đủ thông tin bắt buộc.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<p class='error'>Email không hợp lệ.</p>";
    } elseif ($matkhau !== $nhapLai) {
        $message = "<p class='error'>Mật khẩu nhập lại không khớp.</p>";
    } else {
        $stmt = $conn->prepare("SELECT MaKH FROM khachhang WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<p class='error'>Email này đã được sử dụng.</p>";
        } else {
            $hash = password_hash($matkhau, PASSWORD_DEFAULT);
            $stmtInsert = $conn->prepare(
                "INSERT INTO khachhang (TenKH, DiaChi, MatKhau, Email, SDT)
                 VALUES (?,?,?,?,?)"
            );
            $stmtInsert->bind_param("sssss", $ten, $diachi, $hash, $email, $sdt);
            if ($stmtInsert->execute()) {
                $id = $stmtInsert->insert_id;
                $res = $conn->query("SELECT MaKH, TenKH, Email FROM khachhang WHERE MaKH = " . intval($id));
                $kh = $res->fetch_assoc();
                login_user($kh);
                header("Location: categories.php");
                exit;
            } else {
                $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
            }
            $stmtInsert->close();
        }
        $stmt->close();
    }
}

render_header("Đăng ký tài khoản", "categories.php");

echo $message;
echo '<h2>Đăng ký tài khoản</h2>';
echo '<form method="post">';
echo '<label>Họ tên (*):</label>';
echo '<input type="text" name="TenKH" required>';
echo '<label>Email (*):</label>';
echo '<input type="text" name="Email" required>';
echo '<label>Mật khẩu (*):</label>';
echo '<input type="password" name="MatKhau" required>';
echo '<label>Nhập lại mật khẩu (*):</label>';
echo '<input type="password" name="MatKhau2" required>';
echo '<label>Địa chỉ:</label>';
echo '<input type="text" name="DiaChi">';
echo '<label>Số điện thoại:</label>';
echo '<input type="text" name="SDT">';
echo '<input type="submit" value="Đăng ký">';
echo '</form>';

render_footer();
?>