<?php
$db_host = 'localhost';
$db_user = '数据库用户名';
$db_pass = '数据库密码';
$db_name = '数据库名称';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?> 