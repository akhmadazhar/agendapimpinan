<?php
$conn = mysqli_connect("localhost", "root", "", "db_agenda");
function registrasi($data)
{
    global $conn;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);

    $result = mysqli_query($conn, "select username from users where username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
                alert('username sudah terdaftar')
            </script>";
        return false;
    }

    if ($password != $password2) {
        echo "<script>
                alert('konfirmasi password tidak sesuai!')
                </script>";
        return false;
    }

    $NAMAFILE = $_FILES['gambar']['name'];
    $NAMATMP = $_FILES['gambar']['tmp_name'];
    $FOLDER = "image/" . $NAMAFILE;

    $password = password_hash($password, PASSWORD_DEFAULT);
    if (move_uploaded_file($NAMATMP, $FOLDER)) {
        mysqli_query($conn, "insert into users values('', '$username', '$password', '$FOLDER')");
    } else {
        echo "PILIH FILE";
    }

    return mysqli_affected_rows($conn);
}


function tambahagenda($data)
{
    global $conn;
    $file_undangan = uploadFileUndangan();
    $id = date("mYdhis");
    $nik_pegawai = $data['nik_pegawai'];
    $judul = $data['judul'];
    $deskripsi = $data['deskripsi'];
    $tanggal = $data['tanggal'];
    $lokasi = $data['lokasi'];
    $status = "Diajukan";
    $pesan = $data['pesan'];
    date_default_timezone_set("Asia/Makassar");
    $timestamp = date("Y-m-d - H:i");

    // Pengecekan apakah file undangan berhasil diunggah
    if ($file_undangan !== null) {
        // Hitung selisih hari antara tanggal sekarang dan tanggal input
        $selisih_hari = ceil((strtotime($tanggal) - time()) / (60 * 60 * 24));

        if ($selisih_hari >= 3) {
            $status = "Selesai";
            // Tambahkan 3 hari ke tanggal selesai
            $tanggal_selesai = date("Y-m-d");
            $kesimpulan = "";
        } else {
            $status = "Diajukan";
            $tanggal_selesai = null; // Tidak ada tanggal selesai jika status masih "Diajukan"
            $kesimpulan = "";
        }


        mysqli_query($conn, "INSERT INTO agenda (id_agenda, nik_pegawai, judul, deskripsi, tanggal, lokasi, file_undangan, status, timestamp) VALUES ('$id', '$nik_pegawai', '$judul', '$deskripsi', '$tanggal', '$lokasi', '$file_undangan', '$status', '$timestamp')");

        mysqli_query($conn, "insert into permohonan values(NULL, '$id', '$nik_pegawai', '$pesan')");
        mysqli_query($conn, "insert into undangan values(NULL, '$id', '$nik_pegawai')");

        mysqli_query($conn, "INSERT INTO hasil (id_agenda, tanggal_selesai, kesimpulan) VALUES ('$id', '$tanggal_selesai', '$kesimpulan')");

        return mysqli_affected_rows($conn);
    } else {
        // File undangan gagal diunggah, Anda dapat menangani kesalahan di sini
        echo "File undangan gagal diunggah.";
        return 0; // Atau return sesuai kebutuhan Anda jika terjadi kesalahan
    }
}

function uploadFileUndangan()
{
    $targetDir = "../assets/uploads/";

    // Buat direktori jika belum ada
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    } // Direktori untuk menyimpan file undangan (pastikan direktori ini sudah ada)
    $targetFile = $targetDir . basename($_FILES["file_undangan"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is a valid image or PDF
    if ($fileType != "pdf" && !getimagesize($_FILES["file_undangan"]["tmp_name"])) {
        echo "File harus berupa gambar atau PDF.";
        $uploadOk = 0;
    }

    // Generate nama file yang unik
    $uniqueFileName = md5(uniqid(rand(), true)) . '.' . $fileType;
    $targetFile = $targetDir . $uniqueFileName;

    // Check file size
    if ($_FILES["file_undangan"]["size"] > 5000000) {
        echo "Ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Upload file jika semua valid
    if ($uploadOk == 1) {
        move_uploaded_file($_FILES["file_undangan"]["tmp_name"], $targetFile);
        return $targetFile;
    } else {
        return null; // Mengembalikan null jika upload gagal
    }
}
function deleteagenda($data)
{
    global $conn;

    $id = $data['id_agenda'];
    mysqli_query($conn, "delete from agenda where id_agenda='$id'");

    return mysqli_affected_rows($conn);
}
function editagenda($data)
{
    global $conn;

    $id = $data['id_agenda'];
    $nik_pegawai = $data['nik_pegawai'];
    $judul = $data['judul'];
    $deskripsi = $data['deskripsi'];
    $tanggal = $data['tanggal'];

    mysqli_query($conn, "update agenda set nik_pegawai='$nik_pegawai', judul='$judul', deskripsi='$deskripsi', tanggal='$tanggal'  where id_agenda='$id'");

    return mysqli_affected_rows($conn);
}

function tambahpegawai($data)
{
    global $conn;
    $nik = $data['nik'];
    $nama = $data['nama'];
    $email = $data['email'];
    $id_jabatan = $data['id_jabatan'];
    $username = $data['username'];
    $password = $data['password'];

    $NAMAFILE = $_FILES['gambar']['name'];
    $NAMATMP = $_FILES['gambar']['tmp_name'];
    $FOLDER = "../image/" . $NAMAFILE;

    if (move_uploaded_file($NAMATMP, $FOLDER)) {
        mysqli_query($conn, "insert into pegawai values(NULL, '$nik', '$nama', '$email', '$id_jabatan', '$username', '$password', 'image/$NAMAFILE')");
    } else {
        echo "PILIH FILE";
    }


    return mysqli_affected_rows($conn);
}
function deletepegawai($data)
{
    global $conn;

    $id = $data['id'];
    mysqli_query($conn, "delete from pegawai where id='$id'");

    return mysqli_affected_rows($conn);
}
function editpegawai($data)
{
    global $conn;

    $id = $data['id'];
    $nik = $data['nik'];
    $nama = $data['nama'];
    $email = $data['email'];
    $id_jabatan = $data['id_jabatan'];
    $username = $data['username'];
    $password = $data['password'];

    $NAMAFILE = $_FILES['gambar']['name'];
    $NAMATMP = $_FILES['gambar']['tmp_name'];
    $FOLDER = "../image/" . $NAMAFILE;

    if (move_uploaded_file($NAMATMP, $FOLDER)) {
        mysqli_query($conn, "update pegawai set nik='$nik', nama='$nama', email='$email', id_jabatan='$id_jabatan', username='$username', password='$password', foto='image/$NAMAFILE' where id='$id'");
    } else {
        mysqli_query($conn, "update pegawai set nik='$nik', nama='$nama', email='$email', id_jabatan='$id_jabatan', username='$username', password='$password' where id='$id'");
    }
    return mysqli_affected_rows($conn);
}

function tambahjabatan($data)
{
    global $conn;
    $id = $data['id_jabatan'];
    $nama_jabatan = $data['nama_jabatan'];
    $level = $data['level'];

    mysqli_query($conn, "insert into jabatan values('$id', '$nama_jabatan', '$level')");

    return mysqli_affected_rows($conn);
}
function deletejabatan($data)
{
    global $conn;

    $id = $data['id_jabatan'];
    mysqli_query($conn, "delete from jabatan where id_jabatan='$id'");

    return mysqli_affected_rows($conn);
}
function editjabatan($data)
{
    global $conn;

    $id = $data['id_jabatan2'];
    $id_jabatan = $data['id_jabatan'];
    $nama_jabatan = $data['nama_jabatan'];
    $level = $data['level'];

    mysqli_query($conn, "update jabatan set id_jabatan='$id_jabatan', nama_jabatan='$nama_jabatan', level='$level' where id_jabatan='$id'");

    return mysqli_affected_rows($conn);
}

function deleteAjuan($data)
{
    global $conn;

    $id = $data['id'];

    $data = mysqli_query($conn, "select * from permohonan where id='$id'");
    $result = mysqli_fetch_assoc($data);
    $id_agenda = $result['id_agenda'];
    mysqli_query($conn, "update agenda set status='Ditolak' where id_agenda='$id_agenda'");
    mysqli_query($conn, "delete from permohonan where id='$id'");

    return mysqli_affected_rows($conn);
}
function verifikasiAjuan($data)
{
    global $conn;

    $id_agenda = $data['id_agenda'];
    $id_permohonan = $data['id_permohonan'];

    mysqli_query($conn, "update agenda set status='Dilaksanakan' where id_agenda='$id_agenda'");
    mysqli_query($conn, "delete from permohonan where id='$id_permohonan'");

    return mysqli_affected_rows($conn);
}

function disposisikan($data)
{
    global $conn;

    $id_agenda = $data['id_agenda'];
    $id_permohonan = $data['id_permohonan'];

    $nik_pegawai = $data['nik_pegawai'];
    $nik_perwakilan = $data['nik_perwakilan'];
    $catatan = $data['catatan'];

    date_default_timezone_set("Asia/Makassar");
    $timestamp = date(("Y-m-d - H:i"));

    $data1 = mysqli_query($conn, "select * from pegawai where nik = '$nik_pegawai'");
    $hasil1 = mysqli_fetch_assoc($data1);
    $pegawai_before = $hasil1['nama'];
    $data2 = mysqli_query($conn, "select * from pegawai where nik = '$nik_perwakilan'");
    $hasil2 = mysqli_fetch_assoc($data2);
    $pegawai_after = $hasil2['nama'];

    mysqli_query($conn, "update agenda set nik_pegawai='$nik_perwakilan' where id_agenda='$id_agenda'");
    mysqli_query($conn, "insert into permohonan values(NULL, '$id_agenda', '$nik_perwakilan', '$catatan')");
    mysqli_query($conn, "insert into disposisi values(NULL, '$id_agenda', '$pegawai_before', '$pegawai_after', '$catatan', '$timestamp')");
    mysqli_query($conn, "insert into undangan values(NULL, '$id_agenda', '$nik_perwakilan')");
    mysqli_query($conn, "delete from permohonan where id='$id_permohonan'");

    return mysqli_affected_rows($conn);
}

function selesaiRapat($data)
{
    global $conn;

    $kesimpulan = $data['kesimpulan'];
    $id_agenda = $data['id_agenda'];
    $timestamp = date(("Y-m-d"));

    mysqli_query($conn, "insert into hasil values(NULL, '$id_agenda', '$timestamp', '$kesimpulan')");
    mysqli_query($conn, "update agenda set status='Selesai' where id_agenda='$id_agenda'");

    return mysqli_affected_rows($conn);
}
