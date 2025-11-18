<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ten = trim($_POST["TenKM"] ?? "");
    $bd  = $_POST["NgayBatDau"] ?? "";
    $kt  = $_POST["NgayKetThuc"] ?? "";

    if ($ten === "" || $bd === "" || $kt === "") {
        $message = "<p class='error'>Vui lòng nhập đủ thông tin.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO khuyenmai (TenKM, NgayBatDau, NgayKetThuc) VALUES (?,?,?)");
        $stmt->bind_param("sss", $ten, $bd, $kt);
        if ($stmt->execute()) {
            $message = "<p class='message'>Thêm khuyến mãi thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM khuyenmai ORDER BY MaKM DESC");

render_header("Quản lý khuyến mãi", "promotions.php");

echo $message;

echo '<h2>Thêm khuyến mãi</h2>';
echo '<form method="post">';
echo '<label>Tên khuyến mãi:</label>';
echo '<input type="text" name="TenKM" required>';
echo '<label>Ngày bắt đầu:</label>';
echo '<input type="date" name="NgayBatDau" required>';
echo '<label>Ngày kết thúc:</label>';
echo '<input type="date" name="NgayKetThuc" required>';
echo '<input type="submit" value="Thêm khuyến mãi">';
echo '</form>';

echo '<h2>Danh sách khuyến mãi</h2>';
echo '<table>';
echo '<tr><th>Mã KM</th><th>Tên KM</th><th>Ngày bắt đầu</th><th>Ngày kết thúc</th></tr>';
while ($km = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $km["MaKM"] . '</td>';
    echo '<td>' . htmlspecialchars($km["TenKM"]) . '</td>';
    echo '<td>' . $km["NgayBatDau"] . '</td>';
    echo '<td>' . $km["NgayKetThuc"] . '</td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>