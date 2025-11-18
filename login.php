<?php
require_once "db.php";
require_once "layout.php";

if (current_user()) {
    header("Location: categories.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email   = trim($_POST["Email"] ?? "");
    $matkhau = trim($_POST["MatKhau"] ?? "");

    if ($email === "" || $matkhau === "") {
        $message = "<p class='error'>Vui lòng nhập email và mật khẩu.</p>";
    } else {
        $stmt = $conn->prepare(
            "SELECT MaKH, TenKH, Email, MatKhau
             FROM khachhang
             WHERE Email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $kh = $result->fetch_assoc();
        $stmt->close();

        if ($kh && password_verify($matkhau, $kh["MatKhau"])) {
            login_user($kh);
            header("Location: categories.php");
            exit;
        } else {
            $message = "<p class='error'>Email hoặc mật khẩu không đúng.</p>";
        }
    }
}

render_header("Đăng nhập", "categories.php");

echo $message;
echo '<h2>Đăng nhập</h2>';
echo '<form method="post">';
echo '<label>Email:</label>';
echo '<input type="text" name="Email" required>';
echo '<label>Mật khẩu:</label>';
echo '<input type="password" name="MatKhau" required>';
echo '<input type="submit" value="Đăng nhập">';
echo '</form>';
echo '<p style="margin-top:10px;">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>';

render_footer();
?>