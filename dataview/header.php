<header class="header bayangan body-pd" id="header">
        <div class="header_toggle"> <i class='bx bx-menu bx' id="header-toggle"></i> </div>
        
        <div class="header_img"><img src="<?php echo "../". $_SESSION['foto']; ?>" width="10%"> </div>
    </header>
    <div class="l-navbar show" id="nav-bar">
        <nav class="nav">
            <div> <a href="#" class="nav_logo"> <i class='bx bxs-user-circle nav_logo-icon'></i> <span class="nav_logo-name">Admint</span> </a>
                <div class="nav_list"> 
                    <a href="?page=agenda" class="nav_link <?php if($page=='agenda'){ echo "active"; }?>"> <i class='bx bx-notepad nav_icon'></i> 
                    <span class="nav_name">Data Agenda</span> </a> 
                    <a href="?page=pegawai" class="nav_link <?php if($page=='pegawai'){ echo "active"; }?>"> <i class='bx bxs-user-detail nav_icon'></i> 
                    <span class="nav_name">Data Pegawai</span> </a>
                    <a href="?page=jabatan" class="nav_link <?php if($page=='jabatan'){ echo "active"; }?>"> <i class='bx bx-spreadsheet nav_icon'></i> 
                    <span class="nav_name">Level Jabatan</span> </a> 
                    <a href="?page=hasil" class="nav_link <?php if($page=='hasil'){ echo "active"; }?>"> <i class='bx bx-calendar-check nav_icon'></i> 
                    <span class="nav_name">Hasil Rapat</span> </a></div>
            </div> <a href="../logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">SignOut</span> </a>
        </nav>
    </div>
    <!--Container Main start-->
    <div class="height-100 bg-whitec"><br>
        <div class="container-fluid">