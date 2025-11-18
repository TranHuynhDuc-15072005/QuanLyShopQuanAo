<?php
require_once "db.php";

// Thông tin tài khoản admin muốn tạo
$adminEmail = "admin@shop.com";
$adminPass  = "admin123";
$adminName  = "Admin";

$sqlCheck = $conn->prepare("SELECT MaKH FROM khachhang WHERE Email = ?");
$sqlCheck->bind_param("s", $adminEmail);
$sqlCheck->execute();
$sqlCheck->store_result();

if ($sqlCheck->num_rows > 0) {
    echo "Tài khoản admin đã tồn tại, không cần tạo thêm.";
} else {
    $hash = password_hash($adminPass, PASSWORD_DEFAULT);

    $sqlInsert = $conn->prepare(
        "INSERT INTO khachhang (TenKH, DiaChi, MatKhau, Email, SDT)
         VALUES (?, '', ?, ?, '')"
    );
    $sqlInsert->bind_param("sss", $adminName, $hash, $adminEmail);

    if ($sqlInsert->execute()) {
        echo "Tạo tài khoản admin thành công.<br>";
        echo "Email: " . $adminEmail . "<br>";
        echo "Mật khẩu: " . $adminPass;
    } else {
        echo "Lỗi khi tạo admin: " . $conn->error;
    }
    $sqlInsert->close();
}

$sqlCheck->close();
$conn->close();
?>
