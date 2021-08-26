<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/reportt_list";
error_reporting( error_reporting() & ~E_NOTICE );
error_reporting(E_ERROR | E_PARSE);
//Periksa hak user pada modul/menu ini
$judulMenu = 'Report';
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
    function myFchange() {
        if ($("#txtJenis").val()==2) {
            $("#txtBulan").prop('disabled', true);
        }else if($("#txtJenis").val()==3){
            $("#txtBulan").prop('disabled', true);
        }else{
            $("#txtBulan").prop('disabled', false);
        }
    }
    $(document).ready(function () { 
        $("#myModal").modal({backdrop: 'static'});
        $(".select2").select2();
        $("#stanggal").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        $('#btnClose').click(function(){
            location.href='index.php';
        });
    });
    function topdf() {
        if ($("#txtJenis").val() == 1) {
            location.href='pdf/pdf_absensi.php?&month='+$("#txtBulan").val()+'&years='+$("#txtTahun").val();
        }else if($("#txtJenis").val() == 2){
            location.href='pdf/pdf_absensi2.php?&years='+$("#txtTahun").val();
        }else if($("#txtJenis").val() == 3){
            location.href='pdf/pdf_absensi3.php?&years='+$("#txtTahun").val();
        }else if($("#txtJenis").val() == 4){
            location.href='pdf/pdf_absensi4.php?&years='+$("#txtTahun").val();
        }else if($("#txtJenis").val() == 5){
            location.href='pdf/pdf_absensi5.php?&years='+$("#txtTahun").val();
        }else if($("#txtJenis").val() == 6){
            location.href='pdf/pdf_absensi6.php?&years='+$("#txtTahun").val();
        }
        
    }
</script>
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="btnClose">&times;</button>
                <h4 class="modal-title">Report</h4>
            </div>
            <div class="modal-body">
                <select class="form-control" name="txtJenis" id="txtJenis" onchange="myFchange()">
                    <option value="1">Kehadiran Per Hari</option>
                    <option value="2">Absensi Per Bulan</option>
                    <option value="3">Izin Per Bulan</option>
                    <option value="4">Izin 1/2 Hari Per Bulan</option>
                    <option value="5">Cuti Per Bulan</option>
                    <option value="6">Dinas Per Bulan</option>
                </select><label></label>
                <select class="form-control" name="txtTahun" id="txtTahun">
                    <?php
                        for ($i = 0; $i < 5; ) {
                            $date_str = date('Y', strtotime('-'.$i." years"));
                            echo "<option value=".$date_str .">".$date_str ."</option>";
                            $i++;
                        }
                    ?>
                </select>
                <select class="form-control" name="txtBulan" id="txtBulan">
                    <?php
                        for ($i = 0; $i < 12; ) {
                            $month_val = date('m', strtotime($i." months"));
                            $month_str = date('F', strtotime($i++." months"));
                            echo "<option value=".$month_val .">".$month_str ."</option>";
                        } 
                    ?>
                </select>
            </div>
            <div class="modal-footer">
            <?php
                echo "<button><a style='cursor:pointer;' onclick='topdf()'><i class='fa fa-download'></i>&nbsp;Download</a></button>";
            ?>
            </div>
        </div>
    </div>
</div> 
