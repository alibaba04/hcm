<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/libur_list";
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

    require_once("./class/c_libur.php");
    $tmpLibur = new c_libur;
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpLibur->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpLibur->edit($_POST);
    }
//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpLibur->upload($_POST);
    }
//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpLibur->delete($_GET["kode"]);
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
        var link = window.location.href;
        var res = link.match(/pesan=Gagal/g); 
        if (res == 'pesan=Gagal') {
            $("#myPesan").modal({backdrop: 'static'});
        }
        var mEdit = link.match(/mode=Edit/g);
        if (mEdit == 'mode=Edit') {
            $("#myModal").modal({backdrop: 'static'});
        } 
        $("#txtdatepicker").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        $('#btnAdd').click(function(){
            $("#myModal").modal({backdrop: 'static'});
        });
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
            <a href="#" class="btn btn-primary btn-block margin-bottom" id="btnAdd">Add</a>
          <form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
            <div class="input-group input-group-sm">
                <select name="month" id="month" class="form-control select2">
                    <option value="">Select</option>
                    <?php
                    for ($i = 0; $i < 12; ) {
                        $date_val = date('m', strtotime($i." months"));
                        $date_str = date('F', strtotime($i++." months"));
                        echo "<option value=".$date_val .">".$date_str ."</option>";
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
           </form>
        </div>
        <form action="index2.php?page=view/libur_list" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off">
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
                    <ul class="pagination pagination-sm inline">
                    <?php 
                        if ($_GET["mode"] == "Edit") {
                            $q = "SELECT * FROM `aki_libur` WHERE 1=1 and id=".$_GET["kode"];
                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataId = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' id='txtid' name='txtid' value='" . $dataId["id"] . "'>";
                            } 
                            echo "<input type='hidden' name='txtMode' value='Edit'>";
                        }else{
                            echo "<input type='hidden' name='txtMode' value='Add'>";
                        }
                        if (isset($_GET["month"])){
                            $smonth = secureParam($_GET["month"], $dbLink);
                        }else{
                            $smonth = "";
                        }
                        if (isset($_GET["year"])){
                            $syear = secureParam($_GET["year"], $dbLink);
                        }else{
                            $syear = "";
                        }
                        $filter="";
                        if ($smonth)
                            $filter = $filter . " AND  month(tanggal) = '" . $smonth . "'";
                        if ($syear)
                            $filter = $filter . " AND  year(tanggal) = '" . $syear . "'";
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
                        <td>Aksi</td>
                        <td>Tanggal</td>
                        <td>Keterangan</td>
                        <td>User</td>
                    </thead>
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
                        echo "
                        <tr>
                            <td><button type='button' class='btn btn-primary' onclick=\"if(confirm('Apakah anda yakin akan menghapus data ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kode=" . ($query_data["id"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i></button> ";
                        echo "<button type='button' onclick=location.href='".$_SERVER["PHP_SELF"]."?page=view/libur_list&mode=Edit&kode=" . ($query_data["id"]) . "' class='btn btn-primary' style='cursor:pointer;'><i class='fa fa-pencil'></i></button></td>";

                        echo '<td><b>'.date("d F Y", strtotime($query_data["tanggal"])).'</td>';
                        echo '<td><b>'.$query_data['keterangan'].'</td>';
                        echo '<td>'.$query_data['user'].'</td></tr>';
                        $rowCounter++;
                    }
                    if (!$rs->getNumPages()) {
                        echo("<tr class='even'>");
                        echo ("<td colspan='10' align='center'>No Data Found!</td>");
                        echo("</tr>");
                    }
                    ?>
                </table>
              </div>
            </div>
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
        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <div class="input-group date">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" name="txtdatepicker" id="txtdatepicker" value="<?php if($_GET["mode"] == "Edit"){echo date("d-m-Y", strtotime($dataId["tanggal"]));}?>">
                            </div>
                        </div>
                        <div class="form-group">
                          <label>Keterangan</label>
                          <textarea class="form-control" rows="3" placeholder="Enter ..." name="txtket" id="txtket"><?php if($_GET["mode"] == "Edit"){echo $dataId["keterangan"];}?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="upload" class="btn btn-primary" value="Add" >
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</form>