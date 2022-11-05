<?php
/* ==================================================
  //=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/jamkerja_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan User';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90 ) {
    unset($_SESSION['my']);
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
?>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () { 
        $(".timepicker").timepicker({
            showInputs: false
        });
        $("#myModal").modal({backdrop: 'static'});
        $('#btnClose').click(function(){
            location.href='index.php';
        });
        $('#btnSave').click(function(){
            $("#modal-pass").modal({backdrop: 'static'});
        });
        $('#btnSubmit').click(function(){
            var password = $('#txtPass').val();
            $.post("function/ajax_function.php",{ fungsi: "cekpass", kodeUser:"sdm",pass:password } ,function(data)
            {
                if(data=='yes') {
                $.post("function/ajax_function.php",{ fungsi: "updatejamkerja",jmasuk:$('#masuk').val(),jistirahat1:$('#istirahat1').val(),jistirahat2:$('#istirahat2').val(), jpulang:$('#pulang').val(),jsabtu:$('#sabtu').val()} ,function(data)
                {
                    if(data=='yes') {
                        toastr.success('Sukses Update Jam Kerja . . . .')
                        $("#modal-pass").modal('hide');
                    }else{
                        toastr.error('Gagal Update Jam Kerja . . . .')
                    }
                });
                }else{
                    toastr.error('Gagal !!!<br> Password SDM Level 1 Salah . . . .')
                    $("#txtPass").focus();
                }
            });
                    
        });
    });
</script>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="btnClose">&times;</button>
                <h4 class="modal-title">Set Jam Kerja</h4>
            </div>
            <div class="modal-body">
                
                <div class="modal-header" style="background-color: #fafafa;">
                    <?php
                    $q = "SELECT * FROM `aki_jamkerja` WHERE aktif='1'";
                    $rs = new MySQLPagedResultSet($q, $recordPerPage, $dbLink);
                    ?>
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th class="sort-alpha">Jam Masuk</th>
                                <th class="sort-alpha">Istirahat 1</th>
                                <th class="sort-alpha">Istirahat 2</th>
                                <th class="sort-alpha">Jam Pulang</th>
                                <th class="sort-alpha">Jam Pulang Sabtu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter = 1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo '<td>'. $rowCounter . '</td>';
                                echo '<td><div class="bootstrap-timepicker"><input type="text" class="form-control timepicker" id="masuk" name="masuk" value="'. date("h:i a", strtotime($query_data["masuk"])) . '"></div></td>';
                                echo '<td><div class="bootstrap-timepicker"><input type="text" class="form-control timepicker" id="istirahat1" name="istirahat1" value="'. date("h:i a", strtotime($query_data["istirahat1"])) . '"></div></td>';
                                echo '<td><div class="bootstrap-timepicker"><input type="text" class="form-control timepicker" id="istirahat2" name="istirahat2" value="'. date("h:i a", strtotime($query_data["istirahat2"])) . '"></div></td>';
                                echo '<td><div class="bootstrap-timepicker"><input type="text" class="form-control timepicker" id="pulang" name="pulang" value="'. date("h:i a", strtotime($query_data["pulang"])) . '"></div></td>';
                                echo '<td><div class="bootstrap-timepicker"><input type="text" class="form-control timepicker" id="sabtu" name="sabtu" value="'. date("h:i a", strtotime($query_data["sabtu"])) . '" ></div></td>';
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='10' align='center'>Maaf, data tidak ditemukan</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                    
                </div>
            </div>
            <div class="modal-footer">
            <?php
                echo '<button type="button" class="btn btn-primary" id="btnSave">Save changes</button>';
            ?>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="modal-pass">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h4 class="modal-title">Input Password SDM Level 1 </h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <div class="input-group-addon">
                        <label class="control-label" for="txtTglTransaksi">Password</label>
                    </div>
                    <input type="password" name="txtPass" id="txtPass" class="form-control">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSubmit">Save changes</button>
            </div>
        </div>
    </div>
</div>