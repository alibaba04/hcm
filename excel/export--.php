<?php
//Menggabungkan dengan file koneksi yang telah kita buat
include '../config_pdf.php';

// Load library phpspreadsheet
require('vendor/autoload.php');
require_once('../function/fungsi_formatdate.php');

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// End load library phpspreadsheet

$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('sikubah.com')
->setLastModifiedBy('sikubah.com')
->setTitle('Office sikubah.com')
->setSubject('Office sikubah.com')
->setDescription('Document for Office sikubah.com')
->setKeywords('Office sikubah.com')
->setCategory('Result file sikubah.com');

$month = $_GET['month'];
$years = $_GET['years'];
$tgl1 = $years.'-'.((int)$month-1).'-26';
$tgl2 = $years.'-'.$month.'-25';
$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'Export Data SPH Tanggal '.date("d F Y", strtotime($tgl1))." s/d ".date("d F Y", strtotime($tgl2)));


//Font Color
$spreadsheet->getActiveSheet()->getStyle('A3:L3')
    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

// Background color
    $spreadsheet->getActiveSheet()->getStyle('A3:L3')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ADD8E6');


// Header Tabel
$spreadsheet->setActiveSheetIndex(0)
->setCellValue('A3', 'NIK')
->setCellValue('B3', 'Nama')
->setCellValue('C3', '26')
->setCellValue('D3', '27')
->setCellValue('E3', '28')
->setCellValue('F3', '29')
->setCellValue('G3', '30')
->setCellValue('H3', '31')
->setCellValue('I3', '1')
->setCellValue('J3', '2')
->setCellValue('K3', '3')
->setCellValue('L3', '4')
->setCellValue('M3', '5')
->setCellValue('N3', '6')
->setCellValue('O3', '7')
->setCellValue('P3', '8')
->setCellValue('Q3', '9')
->setCellValue('R3', '10')
->setCellValue('S3', '11')
->setCellValue('T3', '12')
->setCellValue('U3', '13')
->setCellValue('V3', '14')
->setCellValue('W3', '15')
->setCellValue('X3', '16')
->setCellValue('Y3', '17')
->setCellValue('Z3', '18')
;

$qabsen = "SELECT nik,DAYNAME(tanggal) dayname,day(tanggal) as day,month(tanggal) as month,year(tanggal) as year,(CASE WHEN (scan1)<time( '07:36:00' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > if(DAYNAME(tanggal)='Saturday','12:00:00','16:00:00') THEN (1) END) AS masuk FROM `aki_absensi` where tanggal BETWEEN '".$tgl1."' and '".$tgl2."' order by nik,month,day";
$result=mysqli_query($dbLink,$qabsen);
$absen=array();
while ($labsen = mysqli_fetch_array($result)) {
	$absen[$labsen['nik']][$labsen['day']]=$labsen['masuk'];
	$absen[$labsen['nik']]['hk']=$labsen['masuk'];
}
$absen2=array();
$qizin="SELECT nik,Year(tanggal) as years,month(tanggal) as month ,day(tanggal) as day ,jenis,sum(CASE WHEN (TIME_TO_SEC(timediff(end, start)))<30600 THEN (TIME_TO_SEC(timediff(end, start))) END) as time,count(nik) as jml FROM `aki_izin` WHERE aktif=1 and tanggal BETWEEN '".$tgl1."' and '".$tgl2."' group by nik,tanggal";
$result1=mysqli_query($dbLink,$qizin);
while ($lizin = mysqli_fetch_array($result1)) {
	$absen2[$lizin['nik']][$lizin['day']]=$lizin['jenis'];
}
$absen3=array();
$qlibur="SELECT *,day(tanggal) as day FROM `aki_libur` WHERE tanggal BETWEEN '".$tgl1."' and '".$tgl2."'";
$result2=mysqli_query($dbLink,$qlibur);
while ($libur = mysqli_fetch_array($result2)) {
	$absen3[$libur['day']]='L';
}
$qt='SELECT az.nik,um,transport,komunikasi,Year(tanggal) as years,month(tanggal) as month ,jenis,COUNT(az.nik) as jml FROM `aki_izin`az left join aki_tunjangan t on az.nik=t.nik WHERE aktif=1 and tanggal BETWEEN "'.$tgl1.'" and "'.$tgl2.'" GROUP by jenis,nik';
$rs1=mysqli_query($dbLink,$qt);
$absenT=array();$absenK=array();
while ($query_data = mysqli_fetch_array($rs1)) {
	if ($query_data['jenis'] == 'Izin 1/2 Hari') {
		if($query_data['transport']==1){
			$absenT[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenT[$query_data['nik']]['tunjangan']=0;
		}
		if($query_data['komunikasi']==1){
			$absenK[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenK[$query_data['nik']]['tunjangan']=0;
		}
	}else if ($query_data['jenis'] == 'Izin Meninggalkan Pekerjaan') {
		if($query_data['transport']==1){
			$absenT[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenT[$query_data['nik']]['tunjangan']=0;
		}
		if($query_data['komunikasi']==1){
			$absenK[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenK[$query_data['nik']]['tunjangan']=0;
		}
	}else if ($query_data['jenis'] == 'Izin Terlambat') {
		if($query_data['transport']==1){
			$absenT[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenT[$query_data['nik']]['tunjangan']=0;
		}
		if($query_data['komunikasi']==1){
			$absenK[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenK[$query_data['nik']]['tunjangan']=0;
		}
	}else if ($query_data['jenis'] == 'Izin Sakit') {
		if($query_data['komunikasi']==1){
			$absenK[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenK[$query_data['nik']]['tunjangan']=0;
		}
	}else if ($query_data['jenis'] == 'Izin Menikah') {
		if($query_data['komunikasi']==1){
			$absenK[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenK[$query_data['nik']]['tunjangan']=0;
		}
	}else if ($query_data['jenis'] == 'Izin Keluarga Meninggal') {
		if($query_data['komunikasi']==1){
			$absenK[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenK[$query_data['nik']]['tunjangan']=0;
		}
	}else if ($query_data['jenis'] == 'Cuti Tahunan') {
		if($query_data['transport']==1){
			$absenT[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenT[$query_data['nik']]['tunjangan']=0;
		}
		if($query_data['komunikasi']==1){
			$absenK[$query_data['nik']]['tunjangan']+=$query_data['jml'];
		}else{
			$absenK[$query_data['nik']]['tunjangan']=0;
		}
	}
}

$filter="";
if (isset($_GET["gol"])){
	$gol = ($_GET["gol"]);
}else{
	$gol = "";
}
if ($gol)
	$filter = $filter . " AND g.gol_kerja='" . $gol . "'";
$q = "SELECT m.nik,m.kname,t.um,t.transport,t.komunikasi,t.fungsional FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik left join aki_tunjangan t on m.nik=t.nik where m.status='Aktif '" . $filter." order by m.nik";
$result=mysqli_query($dbLink,$q);
$no=1;$i=4;
while ($lap = mysqli_fetch_array($result)) {
	$cday = cal_days_in_month(CAL_GREGORIAN,6,2021);
    $spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A'.$i, $lap["nik"])
	->setCellValue('B'.$i, $lap["kname"]);
	for ($i=26; $i <= $cday; $i++) { 
		foreach(range('C','Z') as $v){
			if (empty($absen[$lap['nik']][$i])) {
				if (date('l', strtotime($years.'-'.((int)$month-1).'-'.$i)) == 'Sunday') {
					$spreadsheet->setActiveSheetIndex(0)
					->setCellValue($v.$i, 'M');
				}else{
					if (empty($absen2[$lap['nik']][$i])) {
						if (empty($absen3[$i])) {
							$spreadsheet->setActiveSheetIndex(0)
							->setCellValue($v.$i, '');
						}else{
							$spreadsheet->setActiveSheetIndex(0)
							->setCellValue($v.$i, $absen3[$i]);
						}
					}
				}
			}else{
				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue($v.$i, $absen[$lap['nik']][$i]);

			}
		}
	}
	/*->setCellValue('C'.$i, $row['tanggal'])
	->setCellValue('D'.$i, $row['d'])
	->setCellValue('E'.$i, $row['t'])
	->setCellValue('F'.$i, $row['dt'])
	->setCellValue('G'.$i, $kel)
	->setCellValue('H'.$i, $row['kn'])
	->setCellValue('I'.$i, $row['pn'])
	->setCellValue('J'.$i, $row['nama_cust'])
	->setCellValue('K'.$i, $row['nama'])
	->setCellValue('L'.$i, $row['affiliate']);*/
	$i++; $no++;
}
/*$i=4; 
$no=1;
$tgl1 = $_GET["tgl1"];
$tgl2 = $_GET["tgl2"];
$filter = "";
$filter3 = "";
if ($tgl1 && $tgl2)
	$filter = $filter . " AND s.tanggal BETWEEN '" . tgl_mysql($tgl1) . "' AND '" . tgl_mysql($tgl2) . "'  ";
$filter3 = $filter3 . " AND s1.tanggal BETWEEN '" . tgl_mysql($tgl1) . "' AND '" . tgl_mysql($tgl2) . "'  ";
$q = "SELECT s.*,ds.bahan,ds.model,ds.d,ds.t,ds.dt,ds.plafon,ds.harga,ds.harga2,ds.jumlah,ds.ket,ds.transport,u.kodeUser,u.nama,p.name as pn,k.name as kn ";
$q.= "FROM aki_sph s right join aki_dsph ds on s.noSph=ds.noSph left join aki_user u on s.kodeUser=u.kodeUser left join provinsi p on s.provinsi=p.id LEFT join kota k on s.kota=k.id ";
$q.= "WHERE s.aktif=1 " . $filter."group by s.noSph Union All" ;
$q.= " SELECT s1.*,'Kaligrafi' as bahan,'Kaligrafi' as model,ds1.d,ds1.t,'-' as dt,'-' as plafon,ds1.harga,'-' as harga2,'-' as jumlah,'-' as ket,'-' as transport, u1.kodeUser, u1.nama, p1.name as pn, k1.name as kn ";
$q.= "FROM aki_sph s1 right join aki_dkaligrafi ds1 on s1.noSph=ds1.noSph left join aki_user u1 on s1.kodeUser=u1.kodeUser left join provinsi p1 on s1.provinsi=p1.id LEFT join kota k1 on s1.kota=k1.id ";
$q.= "WHERE s1.aktif=1 " . $filter3."group by s1.noSph" ;
$q.= " ORDER BY idSph desc ";*/
/*
$result = $dbLink->prepare($q);
$result->execute();
$res1 = $result->get_result();
while ($row = $res1->fetch_assoc()) {
	$kel='';
	if ($row["plafon"] == 0){
		$kel = 'Full';
	}else if ($row["plafon"] == 1){
		$kel = 'Tanpa Plafon';
	}else{
		$kel = 'Waterproof';
	}
	$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A'.$i, $no)
	->setCellValue('B'.$i, $row['noSph'])
	->setCellValue('C'.$i, $row['tanggal'])
	->setCellValue('D'.$i, $row['d'])
	->setCellValue('E'.$i, $row['t'])
	->setCellValue('F'.$i, $row['dt'])
	->setCellValue('G'.$i, $kel)
	->setCellValue('H'.$i, $row['kn'])
	->setCellValue('I'.$i, $row['pn'])
	->setCellValue('J'.$i, $row['nama_cust'])
	->setCellValue('K'.$i, $row['nama'])
	->setCellValue('L'.$i, $row['affiliate']);
	$i++; $no++;
}*/

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// We'll be outputting an excel file
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Report Excel-'.$month.' .xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$write = IOFactory::createWriter($spreadsheet, 'Xlsx');
$write->save('php://output');

?>
