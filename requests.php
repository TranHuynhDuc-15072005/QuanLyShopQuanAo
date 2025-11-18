<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";

$khach = $conn->query("SELECT MaKH, TenKH FROM khachhang ORDER BY TenKH");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $makh = intval($_POST["MaKH"] ?? 0);
    $nd   = trim($_POST["NoiDungPYC"] ?? "");

    if ($makh <= 0 || $nd === "") {
        $message = "<p class='error'>Vui lòng chọn khách hàng và nhập nội dung.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO phieuyc (NoiDungPYC, MaKH) VALUES (?,?)");
        $stmt->bind_param("si", $nd, $makh);
        if ($stmt->execute()) {
            $message = "<p class='message'>Thêm phiếu yêu cầu thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$list = $conn->query("SELECT p.*, k.TenKH FROM phieuyc p LEFT JOIN khachhang k ON p.MaKH = k.MaKH ORDER BY p.MaPYC DESC");

render_header("Quản lý phiếu yêu cầu", "requests.php");

echo $message;

echo '<h2>Thêm phiếu yêu cầu</h2>';
echo '<form method="post">';
echo '<label>Khách hàng:</label>';
echo '<select name="MaKH" required>';
echo '<option value="">-- Chọn khách hàng --</option>';
while ($k = $khach->fetch_assoc()) {
    echo '<option value="' . $k["MaKH"] . '">' . htmlspecialchars($k["TenKH"]) . '</option>';
}
echo '</select>';
echo '<label>Nội dung yêu cầu:</label>';
echo '<input type="text" name="NoiDungPYC" required>';
echo '<input type="submit" value="Thêm phiếu yêu cầu">';
echo '</form>';

echo '<h2>Danh sách phiếu yêu cầu</h2>';
echo '<table>';
echo '<tr><th>Mã PYC</th><th>Khách hàng</th><th>Ngày tạo</th><th>Nội dung</th></tr>';
while ($p = $list->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $p["MaPYC"] . '</td>';
    echo '<td>' . htmlspecialchars($p["TenKH"]) . '</td>';
    echo '<td>' . $p["NgayDat"] . '</td>';
    echo '<td>' . htmlspecialchars($p["NoiDungPYC"]) . '</td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>