<?php
require_once "config.php";
$u = "kebojuara";
$p = password_hash("admin", PASSWORD_BCRYPT);
$f = "Kebo Juara";
$r = "admin";
$stmt = $conn->prepare("INSERT INTO users (username,password,fullname,role,created_at) VALUES (?,?,?,?,NOW())");
$stmt->bind_param("ssss", $u, $p, $f, $r);
$stmt->execute();
echo "OK";
