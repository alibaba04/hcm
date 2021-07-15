<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/izin_list";
error_reporting( error_reporting() & ~E_NOTICE );
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
        $("#stanggal").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
    });
</script>
<section class="content-header">
    <h1>
        Data Izin
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
            <a href="<?php echo $_SERVER["PHP_SELF"].'?page=view/profile_detail'; ?>" class="btn btn-primary btn-block margin-bottom">ADD</a>
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
                <input name="stanggal" id="stanggal" maxlength="30" class="form-control" 
                value="<?php ?>" placeholder="Tanggal" onKeyPress="return handleEnter(this, event)">
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
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Inbox</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
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
                    $q.= "FROM `aki_izin` z left join aki_tabel_master m on m.nik=z.nik";
                    $q.= " WHERE 1=1 " . $filter." order by m.nik";
                //Paging
                    $rs = new MySQLPagedResultSet($q, 500, $dbLink);
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
                        echo '<tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td class="mailbox-star">'.$query_data['nik'].'</td>
                            <td class="mailbox-name"><a href="read-mail.html"><b>'.$query_data['kname'].'</b></a></td>
                            <td class="mailbox-subject"><b>'.$query_data['jenis'].'</b> - '.$query_data['keterangan'].'
                        </td>
                            <td class="mailbox-attachment"></td>
                            <td class="mailbox-date">'.date("d F Y", strtotime($query_data["tanggal"])).'</td>
                        </tr>
                        </tbody>';
                        $rowCounter++;
                    }
                    if (!$rs->getNumPages()) {
                        echo("<tr class='even'>");
                        echo ("<td colspan='10' align='center'>No Data Found!</td>");
                        echo("</tr>");
                    }
                    ?>
                  
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
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
          <!-- /. box -->
        </div>
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
