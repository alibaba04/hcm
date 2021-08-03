<?php
require_once( 'config.php' );
global $dbLink;
$id_nik = $_GET['id_nik'];
$sql = "SELECT * FROM aki_tabel_master m left join `aki_golongan_kerja` g on m.nik=g.nik WHERE `m.nik` = '$id_nik'";
$query = mysql_query($sql,$dbLink);
$data = array();
while($row =mysql_fetch_assoc($query)){
$data[] = array("nik" => $row['nik'], "kname" => $row['kname'], "phone" => $row['phone'], "phone" => $row['phone'], "gol_kerja" => $row['gol_kerja'], "jabatan" => $row['jabatan'], "departemen" => $row['departemen'], "divisi" => $row['divisi'], "direktorat" => $row['direktorat'], "unit" => $row['unit']);
}
echo json_encode($data);?>