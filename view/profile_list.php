  <?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/profile_list";

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
<!-- Include script date di bawah jika ada field tanggal -->
<!-- End of Script Tanggal -->

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
    if (isset($_GET["month"])){
        $month = secureParam($_GET["month"], $dbLink);
        echo "<input type='hidden' id='month' name='month' value='" . $month . "'>";
    }
?>
<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-md-3">

      <!-- Profile Image -->
      <div class="box box-primary">
        <div class="box-body box-profile">
          <img class="profile-user-img img-responsive img-circle" src="dist/img/logo-qoobah.png" alt="User profile picture">

          <h3 class="profile-username text-center"><?php echo $dataKaryawan["kname"];?></h3>

          <p class="text-muted text-center"><?php echo $dataKaryawan["jabatan"];?></p>

          <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <b>NIK</b> <p class="pull-right"><?php echo $dataKaryawan["nik"];?></p>
            </li>
            <li class="list-group-item">
              <b>Phone</b> <p class="pull-right"><?php 
              if($dataKaryawan["phone2"]==''){
                echo $dataKaryawan["phone"];
              }else{
                echo $dataKaryawan["phone"].' / '.$dataKaryawan["phone2"];
              }?></p>
            </li>
            <li class="list-group-item">
              <b>Email</b> <p class="pull-right"><?php echo $dataKaryawan["email"];?></p>
            </li>
            <li class="list-group-item">
            <?php 
                if ($dataKaryawan["status"]=='Aktif') {
                    echo '<b>Active</b> <p class="pull-right">'.date("d F Y", strtotime($dataKaryawan["tanggal_aktif"])).' ('.$dataKaryawan["status_ikatan"].')</p>';
                }else{
                    echo '<b>Not Active</b> <p class="pull-right">'.date("d F Y", strtotime($dataKaryawan["tanggal_nonaktif"])).'</p>';
                }
            ?>
            </li>
          </ul>
        </div>
        <div class="box-body">
          <strong><i class="fa fa-pencil margin-r-5"></i> Position</strong>
          <p>
            <span class="label label-danger"><?php echo $dataKaryawan["gol_kerja"];?></span>
            <span class="label label-primary"><?php echo $dataKaryawan["divisi"];?></span>
            <span class="label label-info"><?php echo $dataKaryawan["departemen"];?></span>
            <span class="label label-success"><?php echo $dataKaryawan["jabatan"];?></span>
            <span class="label label-warning"><?php echo $dataKaryawan["direktorat"];?></span>
          </p>
          <hr>
          <strong><i class="fa fa-book margin-r-5"></i> Education</strong>
          <p class="text-muted">
            <?php echo $dataKaryawan["pendidikan"].', '.$dataKaryawan["jurusan"].' from '.$dataKaryawan["universitas"].', tahun '.$dataKaryawan["tahun_lulus"];?>
          </p>
          <hr>
          <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>
          <p class="text-muted"><?php echo $dataKaryawan["alamat"];?></p>
          <hr>
          <strong><i class="fa fa-book margin-r-5"></i> Guarantee</strong>
          <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <b>Program BPJSTK</b> <p class="pull-right"><?php echo $dataKaryawan["program_bpjstk"];?></p>
            </li>
            <li class="list-group-item">
              <b>BPJS Kesehatan</b> <p class="pull-right"><?php echo $dataKaryawan["bpjs_kes"];?></p>
            </li>
            <li class="list-group-item">
              <b>BPJSTK</b> <p class="pull-right"><?php echo $dataKaryawan["bpjstk"];?></p>
            </li>
          </ul>
        </div>
        <!-- /.box-body -->
      </div>
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

<script>
  $(document).ready(function () {
    var nik = $('#nik').val();
    var url = "fetch-event.php?nik="+nik;
    var calendar = $('#calendar').fullCalendar({
        editable: true,
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay'
        },
        buttonText: {
          today: 'today',
          month: 'month',
          week: 'week',
          day: 'day'
        },
        events: url,
        displayEventTime: false,
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },
    });
    var month = $('#month').val();
    if (month!='') {
      calendar.fullCalendar( 'gotoDate', month );
    }
    
});
</script>