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
        $(".timepicker").timepicker({
            showInputs: false
        });
        $('#txtdatepicker_0').datepicker({
          autoclose: true
        });
        $('#txtdateout').datepicker({
          autoclose: true
        });
        $('#txtdatein').datepicker({
          autoclose: true
        });
    });
</script>
<script type="text/javascript" charset="utf-8">
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
        }else if(x == 'Izin Menodinasah'){
            $("#txtKet_"+tcounter).val('Menodinasah');
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else{
            $("#txtKet_"+tcounter).val('');
        }
    }
    function getnik(tcounter) {
       $.post("function/ajax_function.php",{ fungsi: "ambilnik"} ,function(data){
            getjobs(data[0].val,tcounter);
            for(var i=0; i<178; ++i) {
                var x = document.getElementById("txtnik_"+tcounter);
                var option = document.createElement("option");
                option.text = data[i].text;
                option.value = data[i].val;
                x.add(option);
            }
        },'json'); 
    }
    function getjobs(nik,tcounter) {
       $.post("function/ajax_function.php",{ fungsi: "getjobs",nik} ,function(data){
           document.getElementById("txtJobs_"+tcounter).value = data.jobs;
        },'json'); 
    }

    function addJurnal(){   
        var tcounter = $("#jumAddJurnal").val();
        var e = document.getElementById("'txtnik_"+tcounter+"'");
        var getnikselect = e.options[e.selectedIndex].value;
        getnik(tcounter);
        var ttable = document.getElementById("kendali");
        var trow = document.createElement("TR");
        trow.setAttribute("id", "trid_"+tcounter);

        //Kolom 1 Checkbox
        var td = document.createElement("TD");
        td.setAttribute("align","center");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_'+tcounter+'" id="chkAddJurnal_'+tcounter+'" value="1" checked /></div>';
        trow.appendChild(td);

        //Kolom 1 nik
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><select class="form-control select2" name="txtnik_'+tcounter+'" id="txtnik_'+tcounter+'" onchange="getjobs('+getnikselect+','+tcounter+')"></div></select></div>';
        trow.appendChild(td);

        //Kolom 2 jobs
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtJobs_'+tcounter+'" id="txtJobs_'+tcounter+'"></div>';
        trow.appendChild(td);

        //Kolom 3 ket
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtKet_'+tcounter+'" id="txtKet_'+tcounter+'"></div>';
        trow.appendChild(td);

        ttable.appendChild(trow);
        $(".select2").select2();
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
            $nodinas = secureParam($_GET["nodinas"], $dbLink);
            $q = "SELECT * FROM `aki_dinas`d left join aki_ddinas dd on d.nodinas=dd.nodinas WHERE 1=1 and d.nodinas='".$_GET["nodinas"]."' order by dd.nodinas";
            $rsTemp = mysql_query($q, $dbLink);
            if ($dataKaryawan = mysql_fetch_array($rsTemp)) {
                echo "<input type='hidden' id='nodinas' name='nodinas' value='" . $dataKaryawan["nodinas"] . "'>";
            } 
            echo "<input type='hidden' name='txtMode' value='Edit'>";
        }else{
            $q = "SELECT * FROM aki_dinas where id=( SELECT max(id) FROM aki_dinas )";
            $rsTemp = mysql_query($q, $dbLink);
            $tglTransaksi = date("Y-m-d");
            if ($kode_ = mysql_fetch_array($rsTemp)) {
                $urut = "";
                $no = "";
                $tglTr = substr($tglTransaksi, 0,4);
                $bulan = bulanRomawi(substr($tglTransaksi,5,2));
                if ($kode_['nodinas'] != ''){
                    $urut = substr($kode_['nodinas'],0, 4);
                    $tahun = substr($kode_['nodinas'],-4);
                    $kode = (int)$urut + 1;
                    if (strlen($kode)==1) {
                        $kode = '000'.$kode;
                    }else if (strlen($kode)==2){
                        $kode = '00'.$kode;
                    }else if (strlen($kode)==3){
                        $kode = '0'.$kode;
                    }
                    if ($tglTr != $tahun) {
                        $kode = '0001';
                    }
                    if ($kode_['aktif']==99) {
                        $no = '0001'.'/SPH-MS/PTAKI/'.$bulan.'/'.$tglTr;
                    }else{
                        $no = $kode.'/SPH-MS/PTAKI/'.$bulan.'/'.$tglTr;
                    }

                }else{
                    $no = '0001'.'/SPH-MS/PTAKI/'.$bulan.'/'.$tglTr;
                }

            }
            echo "<input type='hidden' name='txtMode' value='Add'>";
        }
    ?>
<form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
    <section class="content">
    <div class="row">
        <div class="col-md-4">
            <a href="<?php echo $_SERVER["PHP_SELF"].'?page=view/izin_list'; ?>" class="btn btn-primary btn-block margin-bottom">Back</a>
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
            <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Header </h3>
                </div>
                <div class="box-body">
                    <div class="form-group" >
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-arrow-right"></i> No</div>
                            <input name="txtnoSph" id="txtnoSph" maxlength="30" class="form-control" readonly value="<?php if($_GET["mode"]=='edit'){ echo $dataSph["noSph"]; }else{echo $no;}?>" placeholder="Nomor otomatis dibuat">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i> Out</div>
                            <input type="text" class="form-control pull-right" name="txtdateout" id="txtdateout">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i> In &nbsp&nbsp</div>
                            <input type="text" class="form-control pull-right" name="txtdatein" id="txtdatein">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-map"></i> To &nbsp</div>
                            <textarea class="form-control" name="txtaddress" id="txtaddress"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-commenting"></i> Desc &nbsp</div>
                            <textarea class="form-control" name="txtaddress" id="txtaddress"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add New </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="form-group">
                <table class="table table-bordered table-striped table-hover"  >
                    <thead>
                        <style type="text/css">
                             .select2 span{
                                width: 100%;
                            }
                        </style>
                        <tr>
                           <th style="width: 1%"><i class='fa fa-edit'></i></th>
                           <th style="">NIK</th>
                           <th style="width: 25%">Job</th>
                           <th style="width: 30%">Description</th>
                        </tr>
                    </thead>
                    <tbody id="kendali">
                        <?php 
                            if ($_GET['mode']=='edit'){
                                $q = 'SELECT * FROM aki_izin where no="'.$_GET["no"].'" ORDER BY tanggal ASC';
                                $sql_izin = mysql_query($q,$dbLink);
                                while($rs_izin = mysql_fetch_assoc($sql_izin)){ 
                                    echo '<td><div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_0" id="chkAddJurnal_0" value="1" checked /></div></td>';
                                    echo '<td><div class="form-group"><select class="form-control" name="txtJenis_0" id="txtJenis_0"><option value="'.$rs_izin['jenis'].'" selected>'.$rs_izin['jenis'].'</option><option value="Dinas">Dinas</option><option value="Izin Tidak Masuk">Izin Tidak Masuk</option><option value="Izin 1/2 Hari">Izin 1/2 Hari</option><option value="Izin Meninggalkan Pekerjaan">Izin Meninggalkan Pekerjaan</option><option value="Izin Terlambat">Izin Terlambat</option><option value="Izin Sakit">Izin Sakit</option><option value="Izin Menodinasah">Izin Menodinasah</option><option value="Izin Keluarga Meninggal">Izin Keluarga Meninggal</option><option value="Cuti Tahunan">Cuti Tahunan</option><option value="Cuti Melahirkan">Cuti Melahirkan</option></select></div></td>';
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
                    //if ($_GET['mode']!='edit'){
                        echo '<center><button type="button" class="btn btn-success" onclick="javascript:addJurnal()">Add Detail</button></center> ';
                   // }
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