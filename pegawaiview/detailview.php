<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_agenda"; // Ganti dengan nama database yang sesuai

$koneksi = new mysqli($servername, $username, $password, $database);

// Check connection
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

if (isset($_GET['id_agenda']) && isset($_GET['action']) && $_GET['action'] == 'view') {
    // Mendapatkan ID Agenda dari parameter URL
    $id_agenda = $_GET['id_agenda'];

    // Query untuk mendapatkan informasi Agenda berdasarkan ID
    $query = "SELECT * FROM agenda WHERE id_agenda = $id_agenda";
    $result = $koneksi->query($query);

    if (!$result) {
        echo "Error: " . $koneksi->error;
        exit;
    }

    $agenda = $result->fetch_assoc();

    // Memeriksa apakah file undangan ada
    $file_undangan = '../assets/uploads/' . $agenda['file_undangan'];
    if (file_exists($file_undangan)) {
        // Mendapatkan jenis file berdasarkan ekstensinya
        $file_extension = strtolower(pathinfo($file_undangan, PATHINFO_EXTENSION));

        // Menentukan jenis konten berdasarkan ekstensi file
        switch ($file_extension) {
            case "pdf":
                header('Content-Type: application/pdf');
                break;
            case "jpg":
            case "jpeg":
                header('Content-Type: image/jpeg');
                break;
                // Tambahkan jenis konten lainnya sesuai kebutuhan
            default:
                header('Content-Type: application/octet-stream');
                break;
        }

        // Menampilkan file undangan
        readfile($file_undangan);
    } else {
        echo "File undangan tidak ditemukan.";
    }
} else {
    echo "Invalid request.";
}