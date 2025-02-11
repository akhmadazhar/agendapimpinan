<?php ob_start();
$page = "agenda";
include 'header.php';

require '../function.php';
if (isset($_GET["id_agenda"])) {

    if (deleteagenda($_GET) > 0)
        header("location:?page=agenda");
    else
        echo mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Document</title>
</head>

<body>
    <?php
    $username = $_SESSION["user"];

    $data1 = mysqli_query($conn, "select *, jabatan.nama_jabatan from pegawai inner join jabatan on pegawai.id_jabatan = jabatan.id_jabatan where username = '$username'");
    $result = mysqli_fetch_assoc($data1);
    ?>
    <div class="d-grid gap-2">
        <button onclick="show()" class="btn btn-secondary btn-sm"><i class='bx bx-calendar nav_icon'></i> </button>
    </div><br>
    <div class="filter-buttons">
        <button class="btn btn-primary btn-sm" onclick="filterStatus('Selesai')">Tampilkan Selesai</button>
        <button class="btn btn-warning btn-sm" onclick="filterStatus('Diajukan')">Tampilkan Diajukan</button>
        <button class="btn btn-secondary btn-sm" onclick="filterStatus('')">Tampilkan Semua</button>
    </div>

    <div class="table-responsive" id="the-table">
        <table id="example" class="table table-striped border-light-subtle">
            <thead>
                <tr>
                    <th>ID AGENDA</th>
                    <th>JUDUL</th>
                    <th>DESKRIPSI</th>
                    <th>LIST TANGGAL</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $user = $result['nik'];

                //$dataa = mysqli_query($conn, "select *, pegawai.nama from agenda INNER JOIN pegawai ON agenda.nik_pegawai = pegawai.nik where nik_pegawai='$user' and status='Dilaksanakan'");
                $data = mysqli_query($conn, "select *, agenda.*, pegawai.nama from undangan INNER JOIN agenda ON undangan.id_agenda = agenda.id_agenda INNER JOIN pegawai ON undangan.nik_pegawai = pegawai.nik where undangan.nik_pegawai='$user' and agenda.nik_pegawai!='$user' and status!='Selesai' or undangan.nik_pegawai='$user' and agenda.nik_pegawai='$user' and status='Dilaksanakan'");
                while ($row = mysqli_fetch_array($data)) { ?>
                    <tr>
                        <td><?php echo $row['id_agenda']; ?></td>
                        <td><?php echo $row['judul']; ?></td>
                        <td><?php echo $row['deskripsi']; ?></td>
                        <td><?php echo $row['tanggal']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><a href="?page=detailagenda&id_agenda=<?php echo $row['id_agenda']; ?>" class="btn btn-sm btn-primary">Detail</a>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>

    <div id="the-calendar">
        <div class="card-body lg-6">
            <div id="calendar"></div>
        </div>
    </div>
</body>

<script>
    function filterStatus(status) {
        var table = $('#example').DataTable();

        // Reset semua filter sebelumnya
        table.columns().search('').draw();

        if (status !== '') {
            table.column(4).search(status).draw();
        }
    }

    let mode = 1;
    $("#the-table").show();
    $("#the-calendar").hide();

    function show() {

        if (mode == 1) {
            $("#the-calendar").show();
            $("#the-table").hide();
            mode = 2;
        } else {
            $("#the-calendar").hide();
            $("#the-table").show();
            mode = 1;
        }
    }

    $(document).ready(function() {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'month',
            editable: false,
            events: [
                <?php
                $data = mysqli_query($conn, "SELECT *, agenda.*, pegawai.nama FROM undangan 
                INNER JOIN agenda ON undangan.id_agenda = agenda.id_agenda 
                INNER JOIN pegawai ON undangan.nik_pegawai = pegawai.nik 
                WHERE (undangan.nik_pegawai = '$user' AND agenda.nik_pegawai != '$user') OR (undangan.nik_pegawai = '$user' AND agenda.nik_pegawai = '$user')
            ");

                while ($k = mysqli_fetch_array($data)) {
                    $title = $k['judul'];
                    $start = $k['tanggal'];
                    $status = $k['status'];

                    // Tentukan warna berdasarkan status
                    switch ($status) {
                        case 'Selesai':
                            $color = 'green';
                            break;
                        case 'Diajukan':
                            $color = 'red';
                            break;
                        default:
                            $color = 'blue';
                            break;
                    }

                ?> {
                        title: '<?php echo $title; ?>',
                        start: '<?php echo $start; ?>',
                        url: "?page=detailagenda&id_agenda=<?php echo $k['id_agenda']; ?>",
                        color: '<?php echo $color; ?>'
                    },
                <?php } ?>
            ],
        });
    });
</script>

</html>