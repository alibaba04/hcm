<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/profile_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Profile';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_profile.php");
    $tmpProfile = new c_profile;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpProfile->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpProfile->edit($_POST);
    }
//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpProfile->upload($_POST);
    }
//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpProfile->delete($_GET["kodeTransaksi"]);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=view/master_list&pesan=" . $pesan);
    exit;
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () { 
        $(".datepicker").datepicker({ format: 'dd/mm/yyyy', autoclose:true }); 
        $(".select2").select2();
        if($("#cbogol").val() == 'management'){
          document.getElementById("cboUnit").disabled = true;
          let element = document.getElementById("cboUnit");
            element.value = "-";
        }
        $("#cbogol").change(function(){
          var cbogol = $("#cbogol").val();  
          if(cbogol == 'management'){
            document.getElementById("cboUnit").disabled = true;
            let element = document.getElementById("cboUnit");
            element.value = "-";
          }else{
            document.getElementById("cboUnit").disabled = false;
          }
        });
    });
    $(function(){
      $("#upload_link").on('click', function(e){
        e.preventDefault();
        $("#upload:hidden").trigger('click');
      }); 
    });
    var loadFile = function(event) {
      var output = document.getElementById('output');
      output.src = URL.createObjectURL(event.target.files[0]);
      output.onload = function() {
          URL.revokeObjectURL(output.src) // free memory
        }
      };
</script>
<section class="content-header">
    <h1>
        User Profile
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Examples</a></li>
        <li class="active">User profile</li>
    </ol>
</section>
<?php
    if (isset($_GET["nik"])){
        $nik = secureParam($_GET["nik"], $dbLink);
    }else{
        $nik = "";
    }
    $q = "SELECT * ";
    $q.= "FROM aki_tabel_master m left join aki_golongan_kerja g on m.nik=g.nik left join aki_tabel_pendidikan p on m.nik=p.nik left join aki_tabel_jaminan j on m.nik=j.nik left join aki_tabel_benefit b on m.nik=b.nik";
    $q.= " WHERE 1=1 and md5(m.nik)='".$nik."' order by m.nik";
    $rsTemp = mysql_query($q, $dbLink);
    if ($dataKaryawan = mysql_fetch_array($rsTemp)) {
        echo "<input type='hidden' id='nik' name='nik' value='" . $dataKaryawan["nik"] . "'>";
    } 
     /* if (isset($_GET["pesan"])) {
          echo '<h3 class="box-title">'.$_GET["pesan"].'</h3>';
      }*/
?>

<form action="index2.php?page=view/profile_detail" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off" class="form-horizontal"> 
    <?php
      if (isset($_GET["mode"])) {
        if (($_GET["mode"]) == "Add") {
          echo "<input type='hidden' name='txtMode' value='Add'>";
        }else{
          echo "<input type='hidden' name='txtMode' value='Edit'>";
        }
      }
    ?>
    <section class="content">
    <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-body" >
              <input id="upload" type="file" style="display: none;" onchange="loadFile(event)" accept="image/png, image/gif, image/jpeg" />
              <a id="upload_link"><i class="fa fa-pencil-square-o pull-right"></i></a>
              <img class="profile-user-img img-responsive img-circle" src="dist/img/logo-qoobah.png" alt="User profile picture" id="output" style="width: 100%;min-height: 380px;"><br>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab">Profile</a></li>
              <li><a href="#position" data-toggle="tab">Position</a></li>
              <li><a href="#etc" data-toggle="tab">Etc</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                <?php 
                $q = "SELECT * FROM aki_tabel_master where nik=( SELECT max(nik) FROM aki_tabel_master )";
                $rsTemp = mysql_query($q, $dbLink);
                $tgl = date("Y");
                $nik = "";
                $urut = "";
                if ($kode_ = mysql_fetch_array($rsTemp)) {
                  $urut = substr($kode_['nik'],5, 3);
                  $tahun = substr($tgl,2, 2);
                  $kode = $urut + 1;
                  $nik = '01.'.$tahun.$kode;
                }
                ?>
                <div class="form-group">
                  <label for="inputNik" class="col-sm-2 control-label">NIK</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputNik" name="inputNik" required placeholder="NIK" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["nik"];}else{echo $nik;}  ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputName" class="col-sm-2 control-label">Name</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputName" name="inputName" required placeholder="Name" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["kname"];}  ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPLaceb" class="col-sm-2 control-label">Place of Birth</label>

                  <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputPLaceb" name="inputPLaceb" required placeholder="Place" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["tempat_lahir"];}  ?>">
                  </div>
                  <label for="inputDate" class="col-sm-2 control-label">Date of Birth</label>
                  <div class="col-sm-5">
                    <input type="text" name="inputDate" class="form-control pull-right datepicker" value="<?php if(($_GET["mode"]) == "Edit"){echo date('d/m/Y', strtotime($dataKaryawan["tanggal_lahir"]));} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                  <div class="col-sm-10">
                    <input type="email" class="form-control" id="inputEmail" name="inputEmail" required placeholder="Email" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["email"];}  ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPhone" class="col-sm-2 control-label">Phone</label>

                  <div class="col-sm-10">
                    <input type="tel" required placeholder="8888 8888 8888" pattern="[0-9]{4}[0-9]{4}[0-9]{4}" maxlength="12"  class="form-control" id="inputPhone" name="inputPhone" value="<?php if(($_GET["mode"]) == "Edit"){echo preg_replace('/\D/', '', $dataKaryawan["phone"]);} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputAddress" class="col-sm-2 control-label">Address</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="inputAddress" name="inputAddress" required placeholder="Address"><?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["alamat"];}  ?></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputAddress" class="col-sm-2 control-label"></label>
                  <div class="col-sm-10">
                    <?php
                    $chkd = '';
                    $chkdp = '';
                      if ($dataKaryawan["jenis_kelamin"]=='Lk') {
                         $chkd='checked';
                      }
                      if ($dataKaryawan["jenis_kelamin"]=='Pr') {
                         $chkdp='checked';
                      }
                      echo '<label>
                      <input type="radio" name="sex" class="flat-red" '.$chkdp.' value="Pr">
                      Female
                      </label>
                      <label>
                      <input type="radio" name="sex" class="flat-red" '.$chkd.' value="Lk">
                      Male
                      </label>';
                    ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputActive" class="col-sm-2 control-label">Active</label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <input type="text" name="inputActive" class="form-control pull-right datepicker" value="<?php if(($_GET["mode"]) == "Edit"){echo date('d/m/Y', strtotime($dataKaryawan["tanggal_lahir"]));} ?>" >
                    </div>
                  </div>
                </div>

              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="position">
                <div class="form-group">
                  <label for="inputName" class="col-sm-2 control-label">Golongan</label>

                  <div class="col-sm-10">
                    <select class="form-control" name="cbogol" id="cbogol">
                      <?php
                        if ($_GET['mode'] == 'Edit') {
                          echo '<option value="'.$dataKaryawan["gol_kerja"].'">'.$dataKaryawan["gol_kerja"].'</option>';
                        }
                      ?>
                      <option value="management">Management</option>
                      <option value="operational">Operational</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputJabatan" class="col-sm-2 control-label">Jabatan</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="inputJabatan" required placeholder="" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["jabatan"];} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputDep" class="col-sm-2 control-label">Departemen</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="inputDep" required placeholder="Departemen" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["departemen"];} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputDiv" class="col-sm-2 control-label">Divisi</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="inputDiv" required placeholder="Divisi" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["divisi"];} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputSkills" class="col-sm-2 control-label">Unit</label>

                  <div class="col-sm-10">
                    <select class="form-control" name="cboUnit" id="cboUnit">
                      <?php
                        if ($_GET['mode'] == 'Edit') {
                          echo '<option value="'.$dataKaryawan["unit"].'">'.$dataKaryawan["unit"].'</option>';
                        }
                      ?>
                      <option value="galvalume">Galvalume</option>
                      <option value="malpanel">Mal Panel</option>
                      <option value="cat">Cat</option>
                      <option value="enamel">Enamel</option>
                      <option value="rangka">Rangka</option>
                      <option value="hollow">Hollow</option>
                      <option value="packing">Packing</option>
                      <option value="-">-</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputLevel" class="col-sm-2 control-label">Level</label>

                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="inputLevel" required placeholder="0" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["level"];} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputStatus" class="col-sm-2 control-label">Status</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="inputStatus" required placeholder="Status" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["status_ikatan"];} ?>">
                  </div>
                </div>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="etc">
                <div class="form-group">
                  <label for="inputProgram" class="col-sm-2 control-label">Program BPJS</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputProgram" name="inputProgram" required placeholder="0" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["program_bpjstk"];}else{echo"0";} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputBpjskes" class="col-sm-2 control-label">BPJS Kesehatan</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputBpjskes" name="inputBpjskes" required placeholder=". . . ."  value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["bpjs_kes"];}else{echo"-";} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputBPJSTK" class="col-sm-2 control-label">BPJSTK</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputBPJSTK" name="inputBPJSTK" required placeholder=". . . ." value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["bpjstk"];}else{echo"-";} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputUmroh" class="col-sm-2 control-label">Umroh</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputUmroh" name="inputUmroh" required placeholder="-" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["umroh"];}else{echo"-";} ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputQurban" class="col-sm-2 control-label">Qurban</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputQurban" name="inputQurban" required placeholder="-" value="<?php if(($_GET["mode"]) == "Edit"){echo $dataKaryawan["qurban"];}else{echo"-";} ?>">
                  </div>
                </div>
              </div>
              <!-- /.tab-pane -->
            </div>
            <div class="box-footer">
              <div class="mailbox-controls">
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-danger" id="btnn">Submit</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    </section>  
</form>