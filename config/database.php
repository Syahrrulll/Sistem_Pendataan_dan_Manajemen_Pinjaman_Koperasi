<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'koperasi_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function query($sql) {
    global $conn;
    return $conn->query($sql);
}

function escape($data) {
    global $conn;
    return $conn->real_escape_string($data);
}
?>