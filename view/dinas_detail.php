<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/dinas_detail";
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
    require_once("./class/c_dinas.php");
    $tmpdinas = new c_dinas;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpdinas->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpdinas->edit($_POST);
    }
//Jika Mode Upload
    if ($_POST["txtMode"] == "Report") {
        $pesan = $tmpdinas->sreport($_POST);
    }
//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpdinas->delete($_GET["kodeTransaksi"]);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=view/dinas_list&pesan=" . $pesan);
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
        $('#txtdateout').datepicker({ format: 'dd-mm-yyyy', autoclose:true });
        $("#txtdatein").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        $("#txtdatehome").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
       
        $(".select2").select2();
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
    function reprtmodal() {
        $("#reprtmodal").modal({backdrop: 'static'});
        $("#txtMode").val('Report');
    }
    function myFunction(tcounter) {
        var x = document.getElementById("txtJenis_"+tcounter).value;
        if (x.match(/Cuti.*/)) {
            $("#txtKet_"+tcounter).val(x);
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else if(x == 'dinas Tidak Masuk'){
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else if(x == 'dinas Sakit'){
            $("#txtKet_"+tcounter).val('Sakit');
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else if(x == 'dinas Menodinasah'){
            $("#txtKet_"+tcounter).val('Menodinasah');
            $("#txtAwal_"+tcounter).val('07:30 AM');
            $("#txtAkhir_"+tcounter).val('04:00 PM');
        }else{
            $("#txtKet_"+tcounter).val('');
        }
    }
    function getnik(tcounter) {
       $.post("function/ajax_function.php",{ fungsi: "ambilnik"} ,function(data){
            for(var i=0; i<179; ++i) {
                var x = document.getElementById("txtnik_"+tcounter);
                var option = document.createElement("option");
                option.text = data[i].text;
                option.value = data[i].val;
                x.add(option);
            }
        },'json'); 
    }
    function getjobs(tcounter) {
        var nik = $("#txtnik_"+tcounter).val();
       $.post("function/ajax_function.php",{ fungsi: "getjobs",nik} ,function(data){
           document.getElementById("txtJobs_"+tcounter).value = data.jobs;
        },'json');
    }
    function testt(){
        alert('f');
    }

    function addJurnal(){         
        var tcounter = $("#jumAddJurnal").val();
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
        td.innerHTML+='<div class="form-group"><select class="form-control select2" name="txtnik_'+tcounter+'" id="txtnik_'+tcounter+'" onchange="getjobs('+tcounter+')"><option><center>Select NIK</center></option></select></div>';
        trow.appendChild(td);

        //Kolom 2 jobs
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtJobs_'+tcounter+'" id="txtJobs_'+tcounter+'" required></div>';
        trow.appendChild(td);

        //Kolom 3 ket
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtKet_'+tcounter+'" id="txtKet_'+tcounter+'" value="-" required></div>';
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

<form action="index2.php?page=view/dinas_detail" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off">
    <?php
        if ($_GET["mode"] == "edit") {
            $nodinas = secureParam($_GET["nodinas"], $dbLink);
            $q = "SELECT * FROM `aki_dinas`d WHERE 1=1 and md5(d.nodinas)='".($_GET["nodinas"])."'";
            $rsTemp = mysql_query($q, $dbLink);
            if ($dataDinas = mysql_fetch_array($rsTemp)) {
                echo "<input type='hidden' id='nodinas' name='nodinas' value='" . $dataDinas["nodinas"] . "'>";
            } 
            echo "<input type='hidden' name='txtMode' id='txtMode' value='Edit'>";
        }else{
            $q = "SELECT * FROM aki_dinas where id=( SELECT max(id) FROM aki_dinas )";
            $rsTemp = mysql_query($q, $dbLink);
            $tglTransaksi = date("Y-m-d");
            $tglTr = substr($tglTransaksi, 0,4);
            $bulan = bulanRomawi(substr($tglTransaksi,5,2));
            if ($kode_ = mysql_fetch_array($rsTemp)) {
                $urut = "";
                $no = "";
                if ($kode_['nodinas'] != ''){
                    $urut = substr($kode_['nodinas'],0, 4);
                    $tahun = substr($kode_['nodinas'],-4);
                    $kode = (int)$urut + 1;
                    if (strlen($kode)==1) {
                        $kode = '00'.$kode;
                    }else if (strlen($kode)==2){
                        $kode = '0'.$kode;
                    }
                    if ($tglTr != $tahun) {
                        $kode = '001';
                    }
                    if ($kode_['aktif']==99) {
                        $no = '001'.'/S-SDM/PTAKI/'.$bulan.'/'.$tglTr;
                    }else{
                        $no = $kode.'/S-SDM/PTAKI/'.$bulan.'/'.$tglTr;
                    }
                }
            }else{
                $no = '001'.'/S-SDM/PTAKI/'.$bulan.'/'.$tglTr;
            }
            echo "<input type='hidden' name='txtMode' value='Add'>";
        }
    ?>
<form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
    <section class="content">
    <div class="row">
        <div class="col-md-5">
            
            <input type="hidden" name="page" value="<?php echo $curPage; ?>">
            <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Header </h3>
                  <?php 
                    if ($_GET["mode"] == "edit") {
                        echo '<input type="button" class="btn btn-success pull-right" onclick="reprtmodal()" value="Report">';
                    }
                  ?>
                </div>
                <div class="box-body">
                    <div class="form-group" >
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-arrow-right"></i> No</div>
                            <input name="txtnodinas" id="txtnodinas" maxlength="30" class="form-control" readonly value="<?php if($_GET["mode"]=='edit'){ echo $dataDinas["nodinas"]; }else{echo $no;}?>" placeholder="Nomor otomatis dibuat">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i> Out</div>
                            <input type="text" class="form-control pull-right" name="txtdateout" id="txtdateout" value="<?php if($_GET["mode"]=='edit'){ echo date('d-m-Y', strtotime($dataDinas["tgl_berangkat"])); }?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i> In &nbsp&nbsp</div>
                            <input type="text" class="form-control pull-right" name="txtdatein" id="txtdatein" value="<?php if($_GET["mode"]=='edit'){ echo date('d-m-Y', strtotime($dataDinas["tgl_selesai"])); }?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-map"></i> To &nbsp</div>
                            <textarea class="form-control" name="txtaddress" id="txtaddress" required><?php if($_GET["mode"]=='edit'){ echo $dataDinas["alamat"]; }?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-commenting"></i> Desc &nbsp</div>
                            <textarea class="form-control" name="txtket" id="txtket" required><?php if($_GET["mode"]=='edit'){ echo $dataDinas["ket"]; }?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-car"></i>&nbsp&nbsp</div>
                            <select class="form-control" name="txtTransport" id="txtTransport">
                                <?php  
                                    if ($_GET["mode"] == "edit") {
                                        echo '<option value="'.$dataDinas["transport"].'">'.$dataDinas["transport"].'</option>';
                                    }
                                ?>
                                <option value="Kendaraan Dinas">Kendaraan Dinas</option>
                                <option value="Kendaraan Umum">Kendaraan Umum</option>
                                <option value="Kendaraan Pribadi">Kendaraan Pribadi</option>
                            </select>
                            <input name="txtJkendaraan" id="txtJkendaraan" class="form-control" placeholder="Jenis Kendaraan" value="<?php if($_GET["mode"]=='edit'){ echo $dataDinas["jenis_transport"]; }?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
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
                                $q = 'SELECT dd.*,m.kname,g.jabatan FROM aki_ddinas dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nodinas)="'.$_GET["nodinas"].'" order by dd.nik ';
                                $sqldinas = mysql_query($q,$dbLink);
                                $iJurnal = 0;
                                while($rs_dinas = mysql_fetch_assoc($sqldinas)){ 
                                    echo '<tr><td><div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_' . $iJurnal . '" id="chkAddJurnal_' . $iJurnal . '" value="1" checked /></div></td>';
                                    $q = "SELECT m.nik,kname,g.* FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik where tanggal_nonaktif='0000-00-00' order by m.nik";
                                        $nikoption = mysql_query($q, $dbLink);

                                    echo '<td align="center" valign="top" width=><div class="form-group"><select class="form-control select2" name="txtnik_' . $iJurnal . '" id="txtnik_' . $iJurnal . '" onchange="getjobs('.$iJurnal.')">
                                       <option value="'.$rs_dinas['nik'].'">'.$rs_dinas['nik'].' - '.$rs_dinas['kname'].'</option>';
                                       while ($dnik = mysql_fetch_array($nikoption)) {
                                            echo '<option value="'.$dnik['nik'].'">'.$dnik['nik'].' - '.$dnik['kname'].'</option>';
                                        }
                                    echo '</select></div></td><td><div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtJobs_' . $iJurnal . '" id="txtJobs_' . $iJurnal . '" value="'.$rs_dinas['jabatan'].'" disabled></div></td>';
                                    echo '<td><div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtKet_' . $iJurnal . '" id="txtKet_' . $iJurnal . '" value="'.$rs_dinas['ket'].'"></div></td></tr>';
                                    $iJurnal++;
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
                <input type="hidden" value="<?php if ($_GET['mode']=='edit'){echo $iJurnal;}else{echo 0;} ?>" id="jumAddJurnal" name="jumAddJurnal"/>
                <a href="index.php?page=html/dinas_list">
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
    <div class="modal fade" id="reprtmodal" role="dialog">
        <div class="modal-dialog modal-sm">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Aktual Pulang</h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control pull-right" name="txtdatehome" id="txtdatehome" value="<?php if($_GET["mode"]=='edit'){ 
                        if($dataDinas["tgl_pulang"]!='0000-00-00'){echo date('d-m-Y', strtotime($dataDinas["tgl_pulang"]));} }?>" required>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Save"  id="btnUpdate">
                </div>
            </div>
        </div>
    </div> 
</form>