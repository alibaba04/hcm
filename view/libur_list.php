<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/izin_list";
error_reporting( error_reporting() & ~E_NOTICE );
//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Izin';
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

    require_once("./class/c_izin.php");
    $tmpIzin = new c_izin;

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpIzin->delete($_GET["kode"]);
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
                <p><?php echo "Warning!!, please text to " . $mailSupport . " for support this error!."; ?></p>
                <p id="pesanErr"></p>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $(".select2").select2();
        var link = window.location.href;
        var res = link.match(/pesan=Gagal/g); 
        if (res == 'pesan=Gagal') {
            $("#myPesan").modal({backdrop: 'static'});
        }
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
            <a href="<?php echo $_SERVER["PHP_SELF"].'?page=view/izin_detail&mode=add'; ?>" class="btn btn-primary btn-block margin-bottom">Add</a>
          <form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
            <div class="input-group input-group-sm">
                <select name="year" id="month" class="form-control select2">
                    <option value="">Select</option>
                    <?php
                    for ($i = 0; $i < 12; ) {
                        $date_str = date('F', strtotime($i++." months"));
                        echo "<option value=".$date_str .">".$date_str ."</option>";
                    } ?>
                </select>
                
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
                        if (isset($_GET["smonth"])){
                            $smonth = secureParam($_GET["smonth"], $dbLink);
                        }else{
                            $smonth = "";
                        }
                        if (isset($_GET["syear"])){
                            $syear = secureParam($_GET["syear"], $dbLink);
                        }else{
                            $syear = "";
                        }
                       
                        //Set Filter berdasarkan query string
                        $filter="";
                        if ($smonth)
                            $filter = $filter . " AND  month(z.tanggal) LIKE '%" . $smonth . "%'";
                        if ($syear)
                            $filter = $filter . " AND  year(z.tanggal) LIKE '%" . $syear . "%'";
                        //database
                        $q = "SELECT * ";
                        $q.= "FROM `aki_libur` ";
                        $q.= " WHERE 1=1 " . $filter;
                        $rs = new MySQLPagedResultSet($q, 100, $dbLink);
                        echo $rs->getPageNav($_SERVER['QUERY_STRING']);
                    ?></ul>
                </div>
            </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                    <thead>
                        <th>`</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                    </thead>
                    <?php
                    
                //Paging
                    
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
                        echo "
                        <tr>
                            <td><button type='button' class='btn btn-primary' onclick=\"if(confirm('Apakah anda yakin akan menghapus data ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kode=" . ($query_data["no"]) . "'}\" style='cursor:pointer;'>";
                            echo '<i class="fa fa-trash"></i></button></td><td class="mailbox-star">'.$query_data['nik'].'</td>
                            <td>'.date("d F Y", strtotime($query_data["tanggal"])).'</td>';
                        echo '<td><b>'.$query_data['keterangan'].'</td></tr>';
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
