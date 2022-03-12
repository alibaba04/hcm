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
                <input type="text" class="form-control" name="snik" id="snik" placeholder="NIK ...."
                <?php
                if ($_GET["pesan"] != "") {

                            echo $_GET["pesan"];
                        }
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
                        if (isset($_GET["stanggal"])){
                            $tgl = secureParam($_GET["stanggal"], $dbLink);
                        }else{
                            $tgl = "";
                        }
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
                        if (isset($_GET["stype"])){
                            $stype = secureParam($_GET["stype"], $dbLink);
                        }else{
                            $stype = "";
                        }
                        //Set Filter berdasarkan query string
                        $filter="";
                        if ($kname)
                            $filter = $filter . " AND m.kname LIKE '%" . $kname . "%'";
                        if ($nik)
                            $filter = $filter . " AND z.nik LIKE '%" . $nik . "%'";
                        if ($tgl)
                            $filter = $filter . " AND z.tanggal LIKE '%" .date("Y-m-d", strtotime($tgl)) . "%'";
                        if ($smonth)
                            $filter = $filter . " AND  month(z.tanggal) LIKE '%" . $smonth . "%'";
                        if ($syear)
                            $filter = $filter . " AND  year(z.tanggal) LIKE '%" . $syear . "%'";
                        if ($stype)
                            $filter = $filter . " AND  z.jenis LIKE '%" . $stype . "%'";
                        //database
                        $q = "SELECT * ";
                        $q.= "FROM `aki_izin` z left join aki_tabel_master m on m.nik=z.nik";
                        $q.= " WHERE 1=1 and z.aktif=1 " . $filter." order by z.tanggal desc";
                        $rs = new MySQLPagedResultSet($q, 100, $dbLink);
                        echo $rs->getPageNav($_SERVER['QUERY_STRING']);
                    ?></ul>
                </div>
            </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
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
                        echo "<tbody>
                        <tr>
                            <td><button type='button' class='btn btn-primary' onclick=\"if(confirm('Apakah anda yakin akan menghapus data ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kode=" . ($query_data["no"]) . "'}\" style='cursor:pointer;'>";
                            echo '<i class="fa fa-trash"></i></button></td><td class="mailbox-star">'.$query_data['nik'].'</td>
                            <td class="mailbox-name"><a href="'.$_SERVER["PHP_SELF"].'?page=view/izin_detail&mode=edit&no='.$query_data['no'].'&nik='.md5($query_data['nik']).'"><b>'.$query_data['kname'].'</b></a></td>
                            <td class="mailbox-date">'.date("d F Y", strtotime($query_data["tanggal"])).'</td>';
                        if ($query_data["start"] =='07:30:00' && $query_data["end"] =='16:00:00') {
                            if ($query_data["jenis"] =='Cuti') {
                                echo '<td class="mailbox-attachment" colspan="2"> - Cuti</td>';
                            }else{
                                echo '<td class="mailbox-attachment" colspan="2"> - Satu Hari</td>';
                            }
                            
                        }else{
                            echo '<td class="mailbox-attachment">'.$query_data["start"].'</td>';
                            echo '<td class="mailbox-attachment">'.$query_data["end"].'</td>';
                        }
                        echo '<td class="mailbox-subject"><b>'.$query_data['jenis'].'</b>  -  '.$query_data['keterangan'].'</td></tr>
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
