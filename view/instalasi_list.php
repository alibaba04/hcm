<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/instalasi_list";
error_reporting( error_reporting() & ~E_NOTICE );
//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Instalasi';
$hakUser = getUserPrivilege($curPage);
$pesan='';
if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_instalasi.php");
    $tmpinstalasi = new c_instalasi;

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpinstalasi->delete($_GET["nosurat"]);
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
<div class="modal fade" id="myPesan" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Message</h4>
            </div>
            <div class="modal-body">
                <p><?php 
                        //if (strtoupper(substr($pesan, 0, 5)) == "GAGAL"){
                            echo $_GET["pesan"]."Warning!!, please text to " . $mailSupport . " for support this error!.</p>"; 
                        /*}else{
                            echo "Success!.</p>"; 
                        }*/
                ?>
                <p id="pesanErr"></p>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.link').click(function(){
            window.location = $(this).attr('href');
            return false;
        });
        var link = window.location.href;
        var res = link.match(/pesan=Gagal/g); 
        var res2 = link.match(/pesan=Sukses/g); 
        if (res == 'pesan=Gagal') {
            $("#myPesan").modal({backdrop: 'static'});
        }
        $("#stanggal").daterangepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
    });
</script>
<section class="content-header">
    <h1>
        Data Instalasi
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Instalasi</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <div class="col-md-2">
            <a href="<?php echo $_SERVER["PHP_SELF"].'?page=view/instalasi_detail&mode=add'; ?>" class="btn btn-primary btn-block margin-bottom">Add</a>
            <a href="<?php echo 'excel/exportinstalasi.php'; ?>" class="btn btn-primary btn-block margin-bottom">Export Excel</a>
          <form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
            <div class="input-group input-group">
                <input type="text" class="form-control" name="sno" id="sno" placeholder="No Surat ...."
                onKeyPress="return handleEnter(this, event)">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
            <p>- or -</p>
            <div class="form-group input-group">
                <input type="text" class="form-control" name="stanggal" id="stanggal" 
                <?php
                if (isset($_GET["stanggal"])) {
                    echo("value='" . $_GET["stanggal"] . "'");
                }
                ?>
                onKeyPress="return handleEnter(this, event)" placeholder="Range Date">
                <span class="input-group-btn">
                    <button type="Submit" class="btn btn-primary pull-flat"><i class="fa fa-search"></i></button></span>
            </div>
        </div>
        </form>
        <div class="col-md-10">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Inbox</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <div class="mailbox-controls">
                    <div class="btn-group">
                    </div>
                    <ul class="pagination pagination-sm inline"><?php 
                        $filter = "";
                        if (isset($_GET["sno"])){
                            $sno = secureParam($_GET["sno"], $dbLink);
                        }else{
                            $sno = "";
                        }
                        if (isset($_GET["stanggal"])){
                            $tgl = secureParam($_GET["stanggal"], $dbLink);
                            $tgl = explode(" - ", $tgl);
                            $tgl1 = $tgl[0];
                            $tgl2 = $tgl[1];
                            $tgl=$_GET["tgl"];
                        }else{
                            $tgl1 = "";
                            $tgl2 = "";
                        }
                        if ($sno)
                            $filter = $filter . " AND d.nosurat LIKE '%" . $sno . "%'";
                        if ($tgl1 && $tgl2)
                            $filter = $filter . " AND d.tgl_pengajuan BETWEEN '" . tgl_mysql($tgl1) . "' AND '" . tgl_mysql($tgl2) . "'  ";
                        //database
                        $q = "SELECT * FROM `aki_instalasi` d where d.aktif=1 ".$filter." order by d.tgl_buat asc";
                        $rs = new MySQLPagedResultSet($q, 100, $dbLink);
                        echo $rs->getPageNav($_SERVER['QUERY_STRING']);
                    ?></ul>
                </div>
            </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                            <th style="width: 3%">No</th>
                            <th style="width: 15%">No Surat</th>
                            <th style="width: 15%">No SPK</th>
                            <th style="width: 15%">Nama Proyek</th>
                            <th style="width: 20%">Alamat</th>
                            <th style="width: 10%">Tgl_Berangkat</th>
                            <th style="width: 10%">Tgl_Selesai</th>
                            <th style="width: 10%">Action</th>
                    </thead><tbody>
                    <?php
                    
                //Paging
                    
                    $rowCounter=1;
                    $totDebet = 0; $totKredit = 0;
                    while ($query_data = $rs->fetchArray()) {
                        echo "<tr >";
                            echo "<td><center>".$rowCounter."</center></td>";
                            echo "<td class='mailbox-name link' href='".$_SERVER["PHP_SELF"]."?page=view/instalasi_detail&mode=edit&nosurat=".md5($query_data['nosurat'])."'><b>".$query_data["nosurat"]."</b></td>";
                            echo "<td class='mailbox-date link' href='".$_SERVER["PHP_SELF"]."?page=view/instalasi_detail&mode=edit&nosurat=".md5($query_data['nosurat'])."'>".$query_data["nospk"]."</td>";
                            echo "<td class='mailbox-date link' href='".$_SERVER["PHP_SELF"]."?page=view/instalasi_detail&mode=edit&nosurat=".md5($query_data['nosurat'])."'>".$query_data["proyek"]."</td>";
                            echo "<td class='mailbox-date link' href='".$_SERVER["PHP_SELF"]."?page=view/instalasi_detail&mode=edit&nosurat=".md5($query_data['nosurat'])."'>".$query_data["alamat"]."</td>";
                            echo "<td class='mailbox-date link' href='".$_SERVER["PHP_SELF"]."?page=view/instalasi_detail&mode=edit&nosurat=".md5($query_data['nosurat'])."'>".date("d F Y", strtotime($query_data["tgl_berangkat"]))."</td>";
                            echo "<td class='mailbox-date link' href='".$_SERVER["PHP_SELF"]."?page=view/instalasi_detail&mode=edit&nosurat=".md5($query_data['nosurat'])."'>".date("d F Y", strtotime($query_data["tgl_selesai"]))."</td>";
                            echo "</td><td class='pull-right'><center><button type='button' class='btn btn-default' onclick=\"location.href='pdf/pdf_lapinstalasi.php?nosurat=".md5($query_data["nosurat"])."'\" style='cursor:pointer;'><i class='fa fa-print'></i></button></center></td>";
                            echo "<td class='pull-right'><center><button type='button' class='btn btn-default' onclick=\"if(confirm('Hapus data Instalasi ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&nosurat=" . ($query_data["nosurat"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i></button></center></td></tr>";
                            
                        $rowCounter++;
                    }
                    if (!$rs->getNumPages()) {
                        echo("<tr class='even'>");
                        echo ("<td colspan='10' align='center'>No Data Found!</td>");
                        echo("</tr>");
                    }
                    ?>
                 </tbody> 
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
                <div class="box-body no-padding">
                    <div class="mailbox-controls">
                        <div class="btn-group">
                        </div>
                        <ul class="pagination pagination-sm inline"><?php echo $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                    </div>
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
