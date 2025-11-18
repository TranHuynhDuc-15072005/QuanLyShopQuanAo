<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ten = trim($_POST["TenNV"] ?? "");
    $cv  = trim($_POST["ChucVuNV"] ?? "");

    if ($ten === "") {
        $message = "<p class='error'>Tên nhân viên không được rỗng.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO nhanvien (TenNV, ChucVuNV) VALUES (?,?)");
        $stmt->bind_param("ss", $ten, $cv);
        if ($stmt->execute()) {
            $message = "<p class='message'>Thêm nhân viên thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM nhanvien ORDER BY MaNV DESC");

render_header("Quản lý nhân viên", "employees.php");

echo $message;

echo '<h2>Thêm nhân viên</h2>';
echo '<form method="post">';
echo '<label>Tên nhân viên:</label>';
echo '<input type="text" name="TenNV" required>';
echo '<label>Chức vụ:</label>';
echo '<input type="text" name="ChucVuNV">';
echo '<input type="submit" value="Thêm nhân viên">';
echo '</form>';

echo '<h2>Danh sách nhân viên</h2>';
echo '<table>';
echo '<tr><th>Mã NV</th><th>Tên NV</th><th>Chức vụ</th></tr>';
while ($nv = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $nv["MaNV"] . '</td>';
    echo '<td>' . htmlspecialchars($nv["TenNV"]) . '</td>';
    echo '<td>' . htmlspecialchars($nv["ChucVuNV"]) . '</td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>