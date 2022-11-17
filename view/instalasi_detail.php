<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/instalasi_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Intalasi';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_instalasi.php");
    $tmpinstalasi = new c_instalasi;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpinstalasi->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpinstalasi->edit($_POST);
    }
//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpinstalasi->delete($_GET["kodeTransaksi"]);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=view/instalasi_list&pesan=" . $pesan);
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
        $('#txtselesai').datepicker({ format: 'dd-mm-yyyy', autoclose:true });
        $("#txtberangkat").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
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
           document.getElementById("txtUnit_"+tcounter).value = data.jobs;
        },'json');
    }

    function addJurnal(){         
        var tcounter = $("#jumTim").val();
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
        td.innerHTML+='<div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtUnit_'+tcounter+'" id="txtUnit_'+tcounter+'" value="-" required></div>';
        trow.appendChild(td);

        //Kolom 3 ket
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtJobs_'+tcounter+'" id="txtJobs_'+tcounter+'" required></div>';
        trow.appendChild(td);

        ttable.appendChild(trow);
        $(".select2").select2();
        $(".timepicker").timepicker({
            showInputs: false
        });
        $('#txtdatepicker_'+tcounter).datepicker({
          autoclose: true
        });
        $("#jumTim").val(parseInt($("#jumTim").val())+1);
    }

</SCRIPT>

<section class="content-header">
    <h1>
        Data Instalasi
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Data Instalasi</li>
    </ol>
</section>

<form action="index2.php?page=view/instalasi_detail" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off">
    <?php
        if ($_GET["mode"] == "edit") {
            $nosurat = secureParam($_GET["nosurat"], $dbLink);
            $q = "SELECT * FROM `aki_instalasi`d WHERE 1=1 and md5(d.nosurat)='".($_GET["nosurat"])."'";
            $rsTemp = mysql_query($q, $dbLink);
            if ($dataInstalasi = mysql_fetch_array($rsTemp)) {
                echo "<input type='hidden' id='nosurat' name='nosurat' value='" . $dataInstalasi["nosurat"] . "'>";
            } 
            echo "<input type='hidden' name='txtMode' id='txtMode' value='Edit'>";
        }else{
            $q = "SELECT * FROM aki_instalasi where id=( SELECT max(id) FROM aki_instalasi )";
            $rsTemp = mysql_query($q, $dbLink);
            $tglTransaksi = date("Y-m-d");
            $tglTr = substr($tglTransaksi, 0,4);
            $bulan = bulanRomawi(substr($tglTransaksi,5,2));
            if ($kode_ = mysql_fetch_array($rsTemp)) {
                $urut = "";
                $no = "";
                if ($kode_['nosurat'] != ''){
                    $urut = substr($kode_['nosurat'],0, 4);
                    $tahun = substr($kode_['nosurat'],-4);
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
                </div>
                <div class="box-body">
                    <div class="form-group" >
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-arrow-right"></i> No</div>
                            <input name="txtnosurat" id="txtnosurat" maxlength="30" class="form-control" value="<?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["nosurat"]; }else{echo $no;}?>" placeholder="Nomor otomatis dibuat">
                        </div><br>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-arrow-right"></i> Sales</div>
                            <input name="txtsales" id="txtsales" maxlength="30" class="form-control" value="<?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["sales"]; }?>" placeholder="Nama Sales">
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Proyek </h3>
                </div>
                <div class="box-body">
                    <div class="form-group" style="padding-left: 0px;">
                        <div class="form-group"><label class=" control-label">No SPK</label><input type="text" class="form-control" id="txtnospk" name="txtnospk" required value="<?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["nospk"]; }?>"></div>
                        <div class="form-group"><label class=" control-label">Nama Proyek</label><input type="text" class="form-control" id="txtnamaproyek" name="txtnamaproyek" required value="<?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["proyek"]; }?>"></div>
                        <div class="form-group"><label class=" control-label">Alamat</label><textarea class="form-control" name="txtaddress" id="txtaddress" required><?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["alamat"]; }?></textarea></div>
                        <div class="form-group"><label class=" control-label">Ukuran Kubah</label><textarea class="form-control" name="txtspek" id="txtspek" required><?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["spek"]; }?></textarea></div>
                        <div class="form-group"><label class=" control-label">Plafon </label><input type="text" class="form-control" id="txtplafon" name="txtplafon" value="<?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["plafon"]; }?>"></div>
                        <div class="form-group"><label class=" control-label">Jenis Pemasangan </label><input type="text" class="form-control" id="txtjenisp" name="txtjenisp" value="<?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["jpemasangan"]; }?>"></div>
                        <div class="form-group"><label class=" control-label">No HP PJ </label><input type="text" class="form-control" id="txtnohp" name="txtnohp" value="<?php if($_GET["mode"]=='edit'){ echo $dataInstalasi["nohp"]; }?>"></div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class=" control-label">Tanggal Berangkat</label><input type="text" class="form-control pull-right" name="txtberangkat" id="txtberangkat" value="<?php if($_GET["mode"]=='edit'){ echo date('d-m-Y', strtotime($dataInstalasi["tgl_selesai"])); }?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class=" control-label">Tanggal Selesai</label><input type="text" class="form-control pull-right" name="txtselesai" id="txtselesai" value="<?php if($_GET["mode"]=='edit'){ echo date('d-m-Y', strtotime($dataInstalasi["tgl_selesai"])); }?>" required>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Tim Pemasang </h3>
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
                           <th style="width: 20%">Unit</th>
                           <th style="width: 30%">Jabatan Dinas</th>
                        </tr>
                    </thead>
                    <tbody id="kendali">
                        <?php 
                            if ($_GET['mode']=='edit'){
                                $q = 'SELECT dd.*,m.kname,g.jabatan FROM aki_dinstalasi dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nosurat)="'.$_GET["nosurat"].'" order by dd.nik ';
                                $sqltim = mysql_query($q,$dbLink);
                                $iTim = 0;
                                while($rs_dinas = mysql_fetch_assoc($sqltim)){ 
                                    echo '<tr><td><div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_' . $iTim . '" id="chkAddJurnal_' . $iTim . '" value="1" checked /></div></td>';
                                    $q = "SELECT m.nik,kname,g.* FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik where tanggal_nonaktif='0000-00-00' order by m.nik";
                                        $nikoption = mysql_query($q, $dbLink);

                                    echo '<td align="center" valign="top" width=><div class="form-group"><select class="form-control select2" name="txtnik_' . $iTim . '" id="txtnik_' . $iTim . '" onchange="getjobs('.$iTim.')">
                                       <option value="'.$rs_dinas['nik'].'">'.$rs_dinas['nik'].' - '.$rs_dinas['kname'].'</option>';
                                       while ($dnik = mysql_fetch_array($nikoption)) {
                                            echo '<option value="'.$dnik['nik'].'">'.$dnik['nik'].' - '.$dnik['kname'].'</option>';
                                        }
                                    echo '</select></div></td><td><div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtUnit_' . $iTim . '" id="txtUnit_' . $iTim . '" value="'.$rs_dinas['unit'].'" ></div></td>';
                                    echo '<td><div class="form-group"><input type="text" class="form-control" placeholder="Enter ..." name="txtJobs_' . $iTim . '" id="txtJobs_' . $iTim . '" value="'.$rs_dinas['jobs'].'"></div></td></tr>';
                                    $iTim++;
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
                <input type="hidden" value="<?php if ($_GET['mode']=='edit'){echo $iTim;}else{echo 0;} ?>" id="jumTim" name="jumTim"/>
                <a href="index.php?page=html/instalasi_list">
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