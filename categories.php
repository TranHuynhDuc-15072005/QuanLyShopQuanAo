<?php
require_once "db.php";
require_once "layout.php";
require_login();

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ten = trim($_POST["TenDM"] ?? "");
    if ($ten === "") {
        $message = "<p class='error'>Tên danh mục không được rỗng.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO danhmuc (TenDM) VALUES (?)");
        $stmt->bind_param("s", $ten);
        if ($stmt->execute()) {
            $message = "<p class='message'>Thêm danh mục thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM danhmuc ORDER BY MaDM DESC");

render_header("Quản lý danh mục", "categories.php");

echo $message;
echo '<h2>Thêm danh mục mới</h2>';
echo '<form method="post">';
echo '<label>Tên danh mục:</label>';
echo '<input type="text" name="TenDM" required>';
echo '<input type="submit" value="Thêm">';
echo '</form>';

echo '<h2>Danh sách danh mục</h2>';
echo '<table>';
echo '<tr><th>Mã DM</th><th>Tên danh mục</th></tr>';
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $row["MaDM"] . '</td>';
    echo '<td>' . htmlspecialchars($row["TenDM"]) . '</td>';
    echo '</tr>';
}
echo '</table>';

render_footer();
?>