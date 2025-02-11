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
    <title>Agenda</title>
</head>

<body>
    <div class="d-grid gap-2">
        <a href="?page=tambahagenda" class="btn btn-outline-secondary btn-sm">Tambah Agenda</a>
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
                    <th>PEGAWAI</th>
                    <th>JUDUL</th>
                    <th>LIST TANGGAL</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $data = mysqli_query($conn, "select *, pegawai.nama from agenda INNER JOIN pegawai ON agenda.nik_pegawai = pegawai.nik");

                while ($row = mysqli_fetch_array($data)) { ?>
                    <tr>
                        <td><?php echo $row['id_agenda']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['judul']; ?></td>
                        <td><?php echo $row['tanggal']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><a href="?page=detailagenda&id_agenda=<?php echo $row['id_agenda']; ?>" class="btn btn-sm btn-primary"><i class='bx bx-detail nav_icon'></i></a>
                            <a href="?page=editagenda&id_agenda=<?php echo $row['id_agenda']; ?>" class="btn btn-sm btn-warning"><i class='bx bx-edit nav_icon'></i></a>
                            <a href="?page=agenda&id_agenda=<?php echo $row['id_agenda']; ?>" class="btn btn-sm btn-danger btn-delet"><i class='bx bx-trash nav_icon'></i></a>
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

    $('.btn-delet').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href')

        Swal.fire({
            title: 'Hapus Agenda?',
            text: "Menghapus agenda ini juga akan menghapus agenda yang sudah dikirim ke pimpinan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Batalkan',
            confirmButtonText: 'Ya Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = href;
            }
        })
    });

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
                $data = mysqli_query($conn, "select * from agenda");
                $warna;
                while ($k = mysqli_fetch_array($data)) {
                    $title = $k['judul'];
                    $start = $k['tanggal'];
                    $warna = $k['status'];
                ?> {
                        title: '<?php echo $title; ?>',
                        start: '<?php echo $start; ?>',
                        url: "?page=detailagenda&id_agenda='<?php echo $k['id_agenda']; ?>'",
                        color: "<?php if ($warna == 'Selesai') {
                                    echo 'green';
                                } else {
                                    echo 'primary';
                                } ?>"
                    },
                <?php } ?>
            ],
        });
    });
</script>

</html>