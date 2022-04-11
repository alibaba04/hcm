<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/dinas_list";
error_reporting( error_reporting() & ~E_NOTICE );
//Periksa hak user pada modul/menu ini
$judulMenu = 'Data dinas';
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

    require_once("./class/c_dinas.php");
    $tmpdinas = new c_dinas;

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpdinas->delete($_GET["nodinas"]);
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
        }/*else if(res2 == 'pesan=Sukses'){
            $("#myPesan").modal({backdrop: 'static'});
        }*/
        $("#stanggal").daterangepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
    });
</script>
<section class="content-header">
    <h1>
        Data dinas
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Dinas</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <div class="col-md-3">
            <a href="<?php echo $_SERVER["PHP_SELF"].'?page=view/dinas_detail&mode=add'; ?>" class="btn btn-primary btn-block margin-bottom">Add</a>
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
                if (isset($_GET["tanggal"])) {
                    echo("value='" . $_GET["tgl"] . "'");
                }
                ?>
                onKeyPress="return handleEnter(this, event)" placeholder="Range Date">
                <span class="input-group-btn">
                    <button type="Submit" class="btn btn-primary pull-flat"><i class="fa fa-search"></i></button></span>
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
                            $filter = $filter . " AND d.nodinas LIKE '%" . $sno . "%'";
                        if ($tgl1 && $tgl2)
                            $filter = $filter . " AND d.tgl_pengajuan BETWEEN '" . tgl_mysql($tgl1) . "' AND '" . tgl_mysql($tgl2) . "'  ";
                        //database
                        $q = "SELECT * FROM `aki_dinas` d where d.aktif=1 ".$filter." order by d.tgl_pengajuan asc";
                        $rs = new MySQLPagedResultSet($q, 100, $dbLink);
                        echo $rs->getPageNav($_SERVER['QUERY_STRING']);
                    ?></ul>
                </div>
            </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                            <th style="width: 3%">Report</th>
                            <th style="width: 25%">No</th>
                            <th style="width: 15%">Date</th>
                            <th style="width: 52%" colspan="3">Desc</th>
                    </thead><tbody>
                    <?php
                    
                //Paging
                    
                    $rowCounter=1;
                    $totDebet = 0; $totKredit = 0;
                    while ($query_data = $rs->fetchArray()) {
                        echo "<tr >";
                        if ($query_data["report"]==0) {
                            echo "<td><center>-</center></td>";
                        }else{
                            /*echo "<td><center><button type='button' class='btn btn-primary' onclick=\"if(confirm('Laporan sudah diterima ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kode=" . ($query_data["report"]) . "'}\" style='cursor:pointer;'><i class='fa fa-check'></i></button></center></td>";*/
                            echo "<td href='".$_SERVER["PHP_SELF"]."?page=view/dinas_detail&mode=edit&nodinas=".md5($query_data['nodinas'])."'><center><i class='fa fa-check'></i></center></td>";
                        }
                            echo "<td class='mailbox-name link' href='".$_SERVER["PHP_SELF"]."?page=view/dinas_detail&mode=edit&nodinas=".md5($query_data['nodinas'])."'><b>".$query_data["nodinas"]."</b></td>";
                            echo "<td class='mailbox-date link' href='".$_SERVER["PHP_SELF"]."?page=view/dinas_detail&mode=edit&nodinas=".md5($query_data['nodinas'])."'>".date("d F Y", strtotime($query_data["tgl_pengajuan"]))."</td>";
                            echo "<td class='mailbox-subject link' href='".$_SERVER["PHP_SELF"]."?page=view/dinas_detail&mode=edit&nodinas=".md5($query_data['nodinas'])."'>";
                                if (strlen($query_data['ket'])>=20) {
                                     echo substr($query_data['ket'], 0, 30)." . . . .";
                                 }else{
                                    echo $query_data['ket'];
                                 }
                            echo "</td><td class='pull-right'><center><button type='button' class='btn btn-default' onclick=\"location.href='pdf/pdf_lapdinas.php?nodinas=".md5($query_data["nodinas"])."'\" style='cursor:pointer;'><i class='fa fa-print'></i></button></center></td>";
                            echo "<td class='pull-right'><center><button type='button' class='btn btn-default' onclick=\"if(confirm('Laporan sudah diterima ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&nodinas=" . ($query_data["nodinas"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i></button></center></td></tr>";
                            
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
