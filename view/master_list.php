<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/master_list";
error_reporting( error_reporting() & ~E_NOTICE );
//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Master';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_perkiraan.php");
    $tmpPerkiraan = new c_perkiraan();

//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpPerkiraan->add($_POST);
    }

//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpPerkiraan->edit($_POST);
    }

//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpPerkiraan->upload($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpPerkiraan->delete($_GET["nik"]);
    }

//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=$curPage&pesan=" . $pesan);
    exit;
}
?>
<script type="text/javascript" charset="utf-8">
    function delActive() {
        var btnall = document.getElementById("btnall");
        btnall.classList.remove("active");
        var btnmnjemen = document.getElementById("btnmnjemen");
        btnmnjemen.classList.remove("active");
        var btnproduksi = document.getElementById("btnproduksi");
        btnproduksi.classList.remove("active");
    }
    $(document).ready(function () { 
        $('#btnall').click(function(){
            $("#btnall").addClass("active");
        });
        $('#btnmnjemen').click(function(){
            var element = document.getElementById("btnmnjemen");
            element.classList.add("active");
        });
        $('#btnproduksi').click(function(){
            var element = document.getElementById("btnproduksi");
            element.classList.add("active");
        });
        $('#btnaktif').click(function(){
            var element = document.getElementById("btnaktif");
            element.classList.add("active");
        });
        $('#btnnaktif').click(function(){
            var element = document.getElementById("btnnaktif");
            element.classList.add("active");
        });
        $('#btnmutasi').click(function(){
            var element = document.getElementById("btnmutasi");
            element.classList.add("active");
        });
        $('#btnimport').click(function(){
            $("#myModal").modal({backdrop: 'static'});
        });
        
    });
</script>
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content-header">
    <h1>
        Data Master
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Master</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <div class="col-md-3">
          <a href="<?php echo $_SERVER["PHP_SELF"].'?page=view/profile_detail'; ?>" class="btn btn-primary btn-block margin-bottom">ADD</a>
          <a href="#" class="btn btn-primary btn-block margin-bottom" id="btnimport">Import Presence</a>
          <form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" name="nik" id="nik" placeholder="NIK ...."
                <?php
                if (isset($_GET["nik"])) {
                    echo("value='" . $_GET["nik"] . "'");
                }
                ?>
                onKeyPress="return handleEnter(this, event)">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
            <p>- or -</p>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" name="kname" id="kname" placeholder="Name ...."
                <?php
                if (isset($_GET["kname"])) {
                    echo("value='" . $_GET["kname"] . "'");
                }
                ?>
                onKeyPress="return handleEnter(this, event)">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Filter</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked">
                <li  id="btnall"><a href="<?php echo $_SERVER['PHP_SELF'].'?page='.$curPage; ?>" ><i class="fa fa-inbox"></i> All</a></li>
                <li id="btnmnjemen"><a href="<?php echo $_SERVER['PHP_SELF'].'?page='.$curPage.'&gol=Manajemen'; ?>" ><i class="fa fa-inbox"></i> Manajemen</a></li>
                <li id="btnproduksi"><a href="<?php echo $_SERVER['PHP_SELF'].'?page='.$curPage.'&gol=Produksi'; ?>"><i class="fa fa-envelope-o"></i> Produksi</a></li>
                </li>
                <li id="btnaktif"><a href="<?php echo $_SERVER['PHP_SELF'].'?page='.$curPage.'&status=Aktif'; ?>"><i class="fa fa-envelope-o"></i> Aktif</a></li>
                </li>
                <li id="btnnaktif"><a href="<?php echo $_SERVER['PHP_SELF'].'?page='.$curPage.'&status=Non Aktif'; ?>"><i class="fa fa-envelope-o"></i> Tidak Aktif</a></li>
                </li>
                <li id="btnmutasi"><a href="<?php echo $_SERVER['PHP_SELF'].'?page='.$curPage.'&status=Mutasi'; ?>"><i class="fa fa-envelope-o"></i> Mutasi</a></li>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
        </div>
        </form>
        <section class="col-lg-9 connectedSortable">
            <div class="box box-primary">
                <?php
                if (isset($_GET["kname"])){
                    $kname = secureParam($_GET["kname"], $dbLink);
                }else{
                    $kname = "";
                }
                if (isset($_GET["nik"])){
                    $nik = secureParam($_GET["nik"], $dbLink);
                }else{
                    $nik = "";
                }
                if (isset($_GET["gol"])){
                    $gol = secureParam($_GET["gol"], $dbLink);
                }else{
                    $gol = "";
                }
                if (isset($_GET["status"])){
                    $status = secureParam($_GET["status"], $dbLink);
                }else{
                    $status = "";
                }
                //Set Filter berdasarkan query string
                $filter="";
                if ($kname)
                    $filter = $filter . " AND kname LIKE '%" . $kname . "%'";
                if ($nik)
                    $filter = $filter . " AND nik LIKE '%" . $nik . "%'";
                if ($gol)
                    $filter = $filter . " AND g.gol_kerja='" . $gol . "'";
                if ($status)
                    $filter = $filter . " AND m.status='" . $status . "'";
                //database
                $q = "SELECT * ";
                $q.= "FROM aki_tabel_master m left join aki_golongan_kerja g on m.nik=g.nik";
                $q.= " WHERE 1=1 " . $filter." order by m.nik";
                //Paging
                $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?php echo $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                    <!--Cetak PDF dan Export Excel -->
                    <!-- <a href="index2.php?page=<?= $curPage; ?>&mode=lap&tgl1=<?= $tglKirim1; ?>&tgl2=<?= $tglKirim2; ?>" title="Expot Excel"><i class="fa fa-file-excel-o pull-right inline"></i></a><i></i> -->
                    <a href="pdf/pdf_perkiraan.php" title="Cetak PDF CoA"><button type="button" class="btn btn-primary pull-right"><i class="fa fa-print "></i> Print COA</button></a>
                    <!--End Cetak PDF dan Export Excel -->
                </div>

                <div class="box-body">
                    <?php
                    $rowCounter=1;
                    $totDebet = 0; $totKredit = 0;
                    while ($query_data = $rs->fetchArray()) {
                        $gol='';
                        $aktif = '';
                        if ($query_data['status']=='Aktif') {
                            $gol=$query_data['gol_kerja'];
                            $aktif = 'bg-green';
                        }else if($query_data['status']=='Non Aktif'){
                            $gol = "Nonaktif";
                            $aktif = 'bg-green-active';
                        }
                        else{
                            $gol = "Mutasi";
                            $aktif = 'bg-light-green';
                        }
                        echo '<a href="'.$_SERVER["PHP_SELF"].'?page=view/profile_list&nik='.md5($query_data['nik']).'"><div class="col-sm-4 col-xs-12">
                        <div class="info-box with-border">
                        <span class="info-box-icon '.$aktif.'" style="border-radius: 10px;"><img class="img " src="dist/img/logo-qoobah.png" alt="User Avatar"></span>
                        <div class="info-box-content" style="color: black;">
                        <span class="info-box-number">'.$query_data['kname'].'</span>
                        <span class="info-box-text">NIK : '.$query_data['nik'].'</span>
                        </div></div>
                        </div></a>';
                        $rowCounter++;
                    }
                    if (!$rs->getNumPages()) {
                        echo("<tr class='even'>");
                        echo ("<td colspan='10' align='center'>No Data Found!</td>");
                        echo("</tr>");
                    }
                    ?>
                </div> 
            </div>
        </section>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">IMPORT EXCEL</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" enctype="multipart/form-data" action="uploaddata.php">
                            <input type="file" name="filepegawai" required>
                        
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="upload" class="btn btn-primary" value="Import" >
                    </div></form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</section>
