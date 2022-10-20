<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/izin_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Jurnal Umum';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_izin.php");
    $tmpIzin = new c_izin;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpIzin->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpIzin->edit($_POST);
    }
//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpIzin->upload($_POST);
    }
//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpIzin->delete($_GET["kodeTransaksi"]);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=view/izin_list&pesan=" . $pesan);
    exit;
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () { 
        $(".select2").select2();
        $(".timepicker").timepicker({
            showInputs: false
        });
        $("#txtdatepicker_0").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        $('#txtdatepicker_0').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        });
    });
</script>
<!-- Include script untuk function auto complete -->
<SCRIPT language="JavaScript" TYPE="text/javascript">
    function omodal() {
        $("#myModal").modal({backdrop: 'static'});
        $("#txtUpdate1").val();
        $('#btnUpdate').click(function(){
            if($("#txtUpdate1").val()== ''){
                alert('Description Cannot Empty!');
                $("#txtUpdate1").focus();
                return false;
            }else{
                $("#txtUpdate").val($("#txtUpdate1").val());
            }
        });
    }
    function myFunction(tcounter) {
        var x = document.getElementById("txtJenis_"+tcounter).value;
        if (x.match(/Cuti.*/)) {
            $("#txtKet_"+tcounter).val(x);
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else if(x == 'Izin Tidak Masuk'){
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else if(x == 'Izin Sakit'){
            $("#txtKet_"+tcounter).val('Sakit');
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else if(x == 'Izin Menikah'){
            $("#txtKet_"+tcounter).val('Menikah');
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else{
            $("#txtKet_"+tcounter).val('');
        }
    }
    function addJurnal(){    
        var tcounter = $("#jumAddJurnal").val();
        var ttable = document.getElementById("kendali");
        var trow = document.createElement("TR");
        trow.setAttribute("id", "trid_"+tcounter);

        //Kolom 1 Checkbox
        var td = document.createElement("TD");
        td.setAttribute("align","center");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_'+tcounter+'" id="chkAddJurnal_'+tcounter+'" value="1" checked /></div>';
        trow.appendChild(td);

        //Kolom 2 jenis
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><select onchange="myFunction('+tcounter+')" class="form-control" name="txtJenis_'+tcounter+'" id="txtJenis_'+tcounter+'"><option value="Dinas">Dinas</option><option value="Izin Tidak Masuk">Izin Tidak Masuk</option><option value="Izin 1/2 Hari">Izin 1/2 Hari</option><option value="Izin Meninggalkan Pekerjaan">Izin Meninggalkan Pekerjaan</option><option value="Izin Terlambat">Izin Terlambat</option><option value="Izin Sakit">Izin Sakit</option><option value="Izin Menikah">Izin Menikah</option><option value="Izin Keluarga Meninggal">Izin Keluarga Meninggal</option><option value="Cuti Tahunan">Cuti Tahunan</option><option value="Cuti Melahirkan">Cuti Melahirkan</option></select></div>';
        trow.appendChild(td);

        //Kolom 3 tanggal
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><div class="input-group date"><input type="text" class="form-control pull-right" name="txtdatepicker_'+tcounter+'" id="txtdatepicker_'+tcounter+'"></div></div>';
        trow.appendChild(td);

        //Kolom 4 awal
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="bootstrap-timepicker"><div class="form-group"><input type="text" class="form-control timepicker" name="txtAwal_'+tcounter+'" id="txtAwal_'+tcounter+'"></div></div>';
        trow.appendChild(td);

        //Kolom 5 akhir
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="bootstrap-timepicker"><div class="form-group"><input type="text" class="form-control timepicker" name="txtAkhir_'+tcounter+'" id="txtAkhir_'+tcounter+'"></div></div>';
        trow.appendChild(td);

        //Kolom 6 ket
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtKet_'+tcounter+'" id="txtKet_'+tcounter+'"></div>';
        trow.appendChild(td);

        ttable.appendChild(trow);
        $(".timepicker").timepicker({
            showInputs: false
        });
        $('#txtdatepicker_'+tcounter).datepicker({
          autoclose: true
        });
        $("#jumAddJurnal").val(parseInt($("#jumAddJurnal").val())+1);
    }

</SCRIPT>

<section class="content-header">
    <h1>
        GENERAL ENTRIES
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">General Entries</li>
    </ol>
</section>

<form action="index2.php?page=view/izin_detail" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off">
    <?php
        if ($_GET["mode"] == "edit") {
            $nik = secureParam($_GET["nik"], $dbLink);
            $q = "SELECT * FROM `aki_izin` z left join aki_tabel_master m on z.nik=m.nik ";
            $q.= " WHERE 1=1 and md5(z.nik)='".$nik."' order by m.nik";
            $rsTemp = mysql_query($q, $dbLink);
            if ($dataKaryawan = mysql_fetch_array($rsTemp)) {
                echo "<input type='hidden' id='nik' name='nik' value='" . $dataKaryawan["nik"] . "'>";
            } 
            echo "<input type='hidden' name='txtMode' value='Edit'>";
        }else{
            echo "<input type='hidden' name='txtMode' value='Add'>";
        }
    ?>
<form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
    <section class="content">
    <div class="row">
        <div class="col-md-2">
            <a href="<?php echo $_SERVER["PHP_SELF"].'?page=view/izin_list'; ?>" class="btn btn-primary btn-block margin-bottom">Back</a>
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
        </div>
        <div class="col-md-10">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add New </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="form-group col-md-6">
                <select class="form-control select2" name="cbonik" id="cbonik">
                    <?php
                    $selected = "";
                    $q = 'SELECT * FROM aki_tabel_master m left join `aki_golongan_kerja` g on m.nik=g.nik where m.status="Aktif" ORDER BY m.nik ASC';
                    $sql_nik = mysql_query($q,$dbLink);
                    if ($_GET["mode"] == "edit") {
                        echo '<option value="'.$dataKaryawan["nik"].'" selected>'.$dataKaryawan["nik"].' - '.$dataKaryawan["kname"].'</option>';
                        while($rs_nik = mysql_fetch_assoc($sql_nik)){ 
                            echo '<option value="'.$rs_nik['nik'].'">'.$rs_nik['nik'].' - '.$rs_nik['kname'].'</option>';
                        }  
                    }else{
                        echo '<option value="">NIK</option>';
                        while($rs_nik = mysql_fetch_assoc($sql_nik)){ 
                            echo '<option value="'.$rs_nik['nik'].'">'.$rs_nik['nik'].' - '.$rs_nik['kname'].'</option>';
                        }  
                    }
                    ?>
                </select>
              </div>
              <br><br><br>
              <div class="form-group">
                <table class="table table-bordered table-striped table-hover"  >
                    <thead>
                        <tr>
                           <th style="width: 1%"><i class='fa fa-edit'></i></th>
                           <th style="width: 15%">Jenis Izin</th>
                           <th style="width: 10%">Tanggal</th>
                           <th style="width: 10%">Jam Awal</th>
                           <th style="width: 10%">Jam Akhir</th>
                           <th style="width: 30%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="kendali">
                        <?php 
                            if ($_GET['mode']=='edit'){
                                $q = 'SELECT * FROM aki_izin where no="'.$_GET["no"].'" ORDER BY tanggal ASC';
                                $sql_izin = mysql_query($q,$dbLink);
                                while($rs_izin = mysql_fetch_assoc($sql_izin)){ 
                                    echo '<td><div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_0" id="chkAddJurnal_0" value="1" checked /></div></td>';
                                    echo '<td><div class="form-group"><select class="form-control" name="txtJenis_0" id="txtJenis_0"><option value="'.$rs_izin['jenis'].'" selected>'.$rs_izin['jenis'].'</option><option value="Dinas">Dinas</option><option value="Izin Tidak Masuk">Izin Tidak Masuk</option><option value="Izin 1/2 Hari">Izin 1/2 Hari</option><option value="Izin Meninggalkan Pekerjaan">Izin Meninggalkan Pekerjaan</option><option value="Izin Terlambat">Izin Terlambat</option><option value="Izin Sakit">Izin Sakit</option><option value="Izin Menikah">Izin Menikah</option><option value="Izin Keluarga Meninggal">Izin Keluarga Meninggal</option><option value="Cuti Tahunan">Cuti Tahunan</option><option value="Cuti Melahirkan">Cuti Melahirkan</option></select></div></td>';
                                    echo '<td><div class="form-group"><div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" name="txtdatepicker_0" id="txtdatepicker_0" value="'.date("m/d/Y", strtotime($rs_izin['tanggal'])).'"></div></div></td>';
                                    echo '<td><div class="bootstrap-timepicker"><div class="form-group"><input type="text" class="form-control timepicker" name="txtAwal_0" id="txtAwal_0" value="'.date("h:i a", strtotime($rs_izin['start'])).'"></div></div></td>';
                                    echo '<td><div class="bootstrap-timepicker"><div class="form-group"><input type="text" class="form-control timepicker" name="txtAkhir_0" id="txtAkhir_0" value="'.date("h:i a", strtotime($rs_izin['end'])).'"></div></div></td>';
                                    echo '<td><div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtKet_0" id="txtKet_0" value="'.$rs_izin['keterangan'].'"></div></td><input type="hidden" class="form-control"name="txtNo_0" id="txtNo_0" value="'.$rs_izin['no'].'">';
                                }  
                            }
                        ?>
                    </tbody>
                </table>
                <?php 
                    if ($_GET['mode']!='edit'){
                        echo '<center><button type="button" class="btn btn-success" onclick="javascript:addJurnal()">Add Detail</button></center> ';
                    }
                ?>
                
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="pull-right">
                    <?php 
                        if ($_GET['mode']=='edit'){
                            echo '<input type="button" class="btn btn-primary" onclick="omodal()" value="Save">';
                        }else{
                            echo '<input type="submit" class="btn btn-primary" value="Save">';
                        }
                    ?>
                </div>
                <input type="hidden" value="0" id="jumAddJurnal" name="jumAddJurnal"/>
                <a href="index.php?page=html/izin_list">
                    <button type="button" class="btn btn-default ">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>    
                </a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /. box -->
        </div>
    </div>
    </section>  

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upadate Description</h4>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" id="txtUpdate1"></textarea>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Save"  id="btnUpdate">
                </div>
            </div>
        </div>
    </div> 
</form>