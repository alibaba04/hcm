<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/salary_list";
error_reporting( error_reporting() & ~E_NOTICE );
error_reporting(E_ERROR | E_PARSE);
//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Izin';
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
    $(document).ready(function () { 
        $(".select2").select2();
        $("#stanggal").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
    });
</script>
<section class="content-header">
    <h1>
        Report
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Izin</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <div class="col-md-3">
          <form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" name="snik" id="snik" placeholder="NIK ...."
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
                <input type="text" class="form-control" name="skname" id="skname" placeholder="Name ...."
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
            <p>- or -</p>
            <div class="input-group input-group-sm">
                <select name="year" id="year" class="form-control select2">
                    <option value="">Select</option>
                    <?php
                    for ($i = 0; $i < 12; ) {
                        $date_str = date('Y', strtotime($i++." years"));
                        echo "<option value=".$date_str .">".$date_str ."</option>";
                    } ?>
                </select>
                
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
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
        </div>
        </form>
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#activity" data-toggle="tab">Activity</a></li>
                    <li><a href="#izin" data-toggle="tab">Izin</a></li>
                    <li><a href="#izinstngah" data-toggle="tab">Setengah Hari</a></li>
                    <li><a href="#cuti" data-toggle="tab">Cuti</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            <div class="pull-right">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.pull-right -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td class="mailbox-star">NIK</td>
                                        <td class="mailbox-name">Name</td>
                                        <td class="mailbox-name">Jan</td>
                                        <td class="mailbox-name">Feb</td>
                                        <td class="mailbox-name">Mar</td>
                                        <td class="mailbox-name">Apr</td>
                                        <td class="mailbox-name">Mei</td>
                                        <td class="mailbox-name">Jun</td>
                                        <td class="mailbox-name">Jul</td>
                                        <td class="mailbox-name">Ags</td>
                                        <td class="mailbox-name">Sep</td>
                                        <td class="mailbox-name">Okt</td>
                                        <td class="mailbox-name">Nov</td>
                                        <td class="mailbox-name">Des</td>
                                        <td class="mailbox-date">Years</td>
                                    </tr>
                                </thead>
                                <?php
                                if (isset($_GET["skname"])){
                                    $kname = secureParam($_GET["skname"], $dbLink);
                                }else{
                                    $kname = "";
                                }
                                if (isset($_GET["snik"])){
                                    $nik = secureParam($_GET["snik"], $dbLink);
                                }else{
                                    $nik = "";
                                }
                                if (isset($_GET["year"])){
                                    $year = secureParam($_GET["year"], $dbLink);
                                }else{
                                    $year = date("Y");
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
                                $filter="";
                                if ($kname)
                                    $filter = $filter . " AND kname LIKE '%" . $kname . "%'";
                                if ($nik)
                                    $filter = $filter . " AND nik LIKE '%" . $nik . "%'";
                                if ($year)
                                    $filter1 = $filter . " AND year(tanggal)='" . $year . "'";
                                if ($gol)
                                    $filter = $filter . " AND g.gol_kerja='" . $gol . "'";
                                if ($status)
                                    $filter = $filter . " AND m.status='" . $status . "'";
                                $q = "SELECT nik,Year(tanggal) as years,month(tanggal) as month,COUNT(CASE WHEN (scan1)<time( '07:36:00' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6)>if(DAYNAME(tanggal)='Saturday','12:00:00','16:00:00') THEN (scan1) END) AS masuk FROM `aki_absensi` where 1=1 ". $filter1." GROUP by nik,month(tanggal)";
                                $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                                $sumhadir=1;

                                $absen=array();
                                while ($query_data = $rs->fetchArray()) {
                                    $absen[$query_data['nik']][$query_data['month']]=$query_data['masuk'];
                                    $absen['nik']['years']=$query_data['years'];
                                }
                                $qm="SELECT * FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik where m.status='Aktif '" . $filter." order by m.nik";
                                $rs = new MySQLPagedResultSet($qm, 200, $dbLink);
                                while ($query_data = $rs->fetchArray()) {
                                    echo '<tbody>
                                    <tr>
                                    <td class="mailbox-star">'.$query_data['nik'].'</td>
                                    <td class="mailbox-name"><a href="read-mail.html"><b>'.$query_data['kname'].'</b></a></td>';
                                    for ($i=1; $i <= 12; $i++) { 
                                        echo '<td class="mailbox-subject">';
                                        if (empty($absen[$query_data['nik']][$i])) {
                                            echo '0';
                                        }else{
                                            echo '<b>'.$absen[$query_data['nik']][$i];
                                        }echo '</td>';
                                    }
                                    echo '<td class="mailbox-subject"><b>'.$absen['nik']['years'].'</td>';
                                    echo '</tr>
                                    </tbody>';
                                }
                                if (!$rs->getNumPages()) {
                                    echo("<tr class='even'>");
                                    echo ("<td colspan='10' align='center'>No Data Found!</td>");
                                    echo("</tr>");
                                }
                                ?>
                            </table>
                        </div>
                        <div class="box-footer no-padding">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                                <div class="pull-right">
                                    1-50/200
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <!-- /.pull-right -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="izin">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            <div class="pull-right">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.pull-right -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td class="mailbox-star">NIK</td>
                                        <td class="mailbox-name">Name</td>
                                        <td class="mailbox-name">Jan</td>
                                        <td class="mailbox-name">Feb</td>
                                        <td class="mailbox-name">Mar</td>
                                        <td class="mailbox-name">Apr</td>
                                        <td class="mailbox-name">Mei</td>
                                        <td class="mailbox-name">Jun</td>
                                        <td class="mailbox-name">Jul</td>
                                        <td class="mailbox-name">Ags</td>
                                        <td class="mailbox-name">Sep</td>
                                        <td class="mailbox-name">Okt</td>
                                        <td class="mailbox-name">Nov</td>
                                        <td class="mailbox-name">Des</td>
                                        <td class="mailbox-date">Total</td>
                                    </tr>
                                </thead>
                                <?php
                                $absen=array();
                                $q='SELECT nik,Year(tanggal) as years,month(tanggal) as month ,COUNT(CASE WHEN (TIME_TO_SEC(timediff(end, start)))>=30600 THEN (TIME_TO_SEC(timediff(end, start))) END) as time FROM `aki_izin` WHERE jenis!="cuti" '. $filter1.'GROUP by nik,month(tanggal)';
                                $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                                while ($query_data = $rs->fetchArray()) {
                                    $absen[$query_data['nik']][$query_data['month']]=$query_data['time'];
                                    $absen[$query_data['nik']]['years']=$query_data['years'];
                                }
                                $rs = new MySQLPagedResultSet($qm, 200, $dbLink);
                                while ($query_data = $rs->fetchArray()) {
                                    $total=0;
                                    echo '<tbody>
                                    <tr>
                                    <td class="mailbox-star">'.$query_data['nik'].'</td>
                                    <td class="mailbox-name"><a href="read-mail.html"><b>'.$query_data['kname'].'</b></a></td>';
                                    for ($i=1; $i <= 12; $i++) { 
                                        echo '<td class="mailbox-subject">';
                                        if (empty($absen[$query_data['nik']][$i])) {
                                            echo '0';
                                        }else{
                                            echo '<b>'.$absen[$query_data['nik']][$i];
                                        }echo '</td>';
                                        $total+=$absen[$query_data['nik']][$i];
                                    }
                                    echo '<td class="mailbox-subject"><b>'.$total.'</td>';
                                    echo '</tr>
                                    </tbody>';
                                }
                                if (!$rs->getNumPages()) {
                                    echo("<tr class='even'>");
                                    echo ("<td colspan='10' align='center'>No Data Found!</td>");
                                    echo("</tr>");
                                }
                                ?>
                            </table>
                        </div>
                        <div class="box-footer no-padding">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                                <div class="pull-right">
                                    1-50/200
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <!-- /.pull-right -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="izinstngah">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            <div class="pull-right">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.pull-right -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td class="mailbox-star">NIK</td>
                                        <td class="mailbox-name">Name</td>
                                        <td class="mailbox-name">Jan</td>
                                        <td class="mailbox-name">Feb</td>
                                        <td class="mailbox-name">Mar</td>
                                        <td class="mailbox-name">Apr</td>
                                        <td class="mailbox-name">Mei</td>
                                        <td class="mailbox-name">Jun</td>
                                        <td class="mailbox-name">Jul</td>
                                        <td class="mailbox-name">Ags</td>
                                        <td class="mailbox-name">Sep</td>
                                        <td class="mailbox-name">Okt</td>
                                        <td class="mailbox-name">Nov</td>
                                        <td class="mailbox-name">Des</td>
                                        <td class="mailbox-date">Years</td>
                                    </tr>
                                </thead>
                                <?php
                                $absen=array();
                                $q='SELECT nik,Year(tanggal) as years,month(tanggal) as month ,sum(CASE WHEN (TIME_TO_SEC(timediff(end, start)))<30600 THEN (TIME_TO_SEC(timediff(end, start))) END) as time FROM `aki_izin` WHERE 1 '. $filter1.' GROUP by nik,month(tanggal)';
                                $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                                while ($query_data = $rs->fetchArray()) {
                                    $absen[$query_data['nik']][$query_data['month']]=gmdate("H:i:s", $query_data['time']);
                                    $absen[$query_data['nik']]['years']=$query_data['years'];
                                }
                                $rs = new MySQLPagedResultSet($qm, 200, $dbLink);
                                while ($query_data = $rs->fetchArray()) {
                                    echo '<tbody>
                                    <tr>
                                    <td class="mailbox-star">'.$query_data['nik'].'</td>
                                    <td class="mailbox-name"><a href="read-mail.html"><b>'.$query_data['kname'].'</b></a></td>';
                                    for ($i=1; $i <= 12; $i++) { 
                                        echo '<td class="mailbox-subject">';
                                        if (empty($absen[$query_data['nik']][$i])) {
                                            echo '00:00:00';
                                        }else{
                                            echo '<b>'.$absen[$query_data['nik']][$i];
                                        }echo '</td>';
                                    }
                                    echo '<td class="mailbox-subject"><b>'.$absen[$query_data['nik']]['years'].'</td>';
                                    echo '</tr>
                                    </tbody>';
                                }
                                if (!$rs->getNumPages()) {
                                    echo("<tr class='even'>");
                                    echo ("<td colspan='10' align='center'>No Data Found!</td>");
                                    echo("</tr>");
                                }
                                ?>
                            </table>
                        </div>
                        <div class="box-footer no-padding">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                                <div class="pull-right">
                                    1-50/200
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <!-- /.pull-right -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="cuti">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            <div class="pull-right">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.pull-right -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td class="mailbox-star">NIK</td>
                                        <td class="mailbox-name">Name</td>
                                        <td class="mailbox-name">Jan</td>
                                        <td class="mailbox-name">Feb</td>
                                        <td class="mailbox-name">Mar</td>
                                        <td class="mailbox-name">Apr</td>
                                        <td class="mailbox-name">Mei</td>
                                        <td class="mailbox-name">Jun</td>
                                        <td class="mailbox-name">Jul</td>
                                        <td class="mailbox-name">Ags</td>
                                        <td class="mailbox-name">Sep</td>
                                        <td class="mailbox-name">Okt</td>
                                        <td class="mailbox-name">Nov</td>
                                        <td class="mailbox-name">Des</td>
                                        <td class="mailbox-date">Total</td>
                                    </tr>
                                </thead>
                                <?php
                                
                                $q = "SELECT *,Year(tanggal) as years,month(tanggal) as month,COUNT(jenis) as cuti FROM aki_izin where jenis='Cuti' and Year(tanggal)=YEAR(CURDATE()) GROUP BY nik,month(tanggal)";
                                $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                                $absen=array();
                                while ($query_data = $rs->fetchArray()) {
                                    if (empty($query_data['cuti'])) {
                                        $absen[$query_data['nik']][$query_data['month']]=0;
                                    }else{
                                        $absen[$query_data['nik']][$query_data['month']]=$query_data['cuti'];
                                    }
                                    $absen[$query_data['nik']]['years']=$query_data['years'];
                                }
                                $rs = new MySQLPagedResultSet($qm, 200, $dbLink);
                                while ($query_data = $rs->fetchArray()) {
                                    $total=0;
                                    echo '<tbody>
                                    <tr>
                                    <td class="mailbox-star">'.$query_data['nik'].'</td>
                                    <td class="mailbox-name"><a href="read-mail.html"><b>'.$query_data['kname'].'</b></a></td>';
                                    for ($i=1; $i <= 12; $i++) { 
                                        echo '<td class="mailbox-subject">';
                                        if (empty($absen[$query_data['nik']][$i])) {
                                            echo '0';
                                        }else{
                                            echo '<b>'.$absen[$query_data['nik']][$i];
                                        }echo '</td>';
                                        $total+=$absen[$query_data['nik']][$i];
                                    }
                                    echo '<td class="mailbox-subject"><b>'.$total.'</td>';
                                    echo '</tr>
                                    </tbody>';
                                }
                                if (!$rs->getNumPages()) {
                                    echo("<tr class='even'>");
                                    echo ("<td colspan='10' align='center'>No Data Found!</td>");
                                    echo("</tr>");
                                }
                                ?>
                            </table>
                        </div>
                        <div class="box-footer no-padding">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                                <div class="pull-right">
                                    1-50/200
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <!-- /.pull-right -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
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
