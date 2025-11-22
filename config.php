<?php
session_start();
$host = "sqlXXX.infinityfree.com";
$user = "uXXXXXXXXX_user";
$pass = "PASSWORD_DB";
$dbname = "uXXXXXXXXX_newsportal";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database error");
}
$conn->set_charset("utf8mb4");
$base_url = "https://domainmu.infinityfreeapp.com";
