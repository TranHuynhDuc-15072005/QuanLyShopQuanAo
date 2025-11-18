<?php
require_once "auth.php";

if (current_user()) {
    header("Location: categories.php");
} else {
    header("Location: login.php");
}
exit;
?>