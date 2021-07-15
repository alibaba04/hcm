<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/profile_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Profile List';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

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
        $("#txtTglTransaksi").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        $(".select2").select2();
    });
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
    $q.= "FROM aki_tabel_master m left join aki_golongan_kerja g on m.nik=g.nik left join aki_tabel_pendidikan p on m.nik=p.nik left join aki_tabel_jaminan j on m.nik=j.nik";
    $q.= " WHERE 1=1 and md5(m.nik)='".$nik."' order by m.nik";
    $rsTemp = mysql_query($q, $dbLink);
    if ($dataKaryawan = mysql_fetch_array($rsTemp)) {
        echo "<input type='hidden' id='nik' name='nik' value='" . $dataKaryawan["nik"] . "'>";
    } 
?>
<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-md-3">
      <!-- Profile Image -->
    <form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <img class="profile-user-img img-responsive img-circle" src="dist/img/logo-qoobah.png" alt="User profile picture"><br>
          <div class="form-group" >
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-user"></i>
                </div>
                <input type="text" name="txtnik" id="txtnik" class="form-control" placeholder="NIK ...."></div>
          </div>
          <div class="form-group" >
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-address-card"></i>
                </div>
                <input type="text" name="txtnik" id="txtnik" class="form-control" placeholder="Name ...."></div>
          </div>
          <div class="form-group" >
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </div>
                <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txtPhone" id="txtPhone" class="form-control" data-inputmask='"mask": "9999 9999 9999"' data-mask value="<?php  ?>" placeholder="Phone ...."></div>
          </div>
          <div class="form-group" >
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input name="txtTglTransaksi" id="txtTglTransaksi" maxlength="30" class="form-control" 
                value="<?php ?>" placeholder="Active" onKeyPress="return handleEnter(this, event)"></div>
          </div>
          <div class="form-group" >
            <label class="control-label" for="txtTglTransaksi">Position</label>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input name="txtTglTransaksi" id="txtTglTransaksi" maxlength="30" class="form-control" 
                value="<?php ?>" placeholder="Active" onKeyPress="return handleEnter(this, event)"></div>
          </div>
        </div>
      </div>
    </form>
      <!-- /.box -->
    </div>
    <!-- /.col -->
    <div class="col-md-9">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#activity" data-toggle="tab">Activity</a></li>
          <li><a href="#timeline" data-toggle="tab">Timeline</a></li>
          <li><a href="#settings" data-toggle="tab">Settings</a></li>
        </ul>
        <div class="tab-content">
          <div class="active tab-pane" id="activity">
            <div class="box box-primary">
              <div class="box-body no-padding">
                <!-- THE CALENDAR -->
                <div id="calendar"></div>
              </div>
              <!-- /.box-body -->
            </div>
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="timeline">
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="settings">
          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

</section>  