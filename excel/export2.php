<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
 
	}
	a{
		background: blue;
		color: #fff;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 2px;
	}
	</style>
 
	<?php
	error_reporting(error_reporting() & ~E_NOTICE);
	$month = $_GET['month'];
	$years = $_GET['years'];
	$tgl1 = $years.'-'.((int)$month-1).'-26';
	$tgl2 = $years.'-'.$month.'-25';
	$tanggal = date("Y-m-d h", time());
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Data_Absensi_".$years.".xls");
	?>
 
	<center>
		<h1>Export Data Absensi <?php echo $years; ?><br/></h1>
	</center>
 
	<table border="1" class="table">
		<thead class="thead-dark">
			<tr>
				<th style='background: palegreen;'>NIK</th>
				<th style='background: palegreen;'>Name</th>
				<?php 
				for ($i = 1; $i <= 12; ) {
					$dateObj   = DateTime::createFromFormat('!m', $i);
					$monthName = $dateObj->format('F'); 
					echo "<th style='background: palegreen;'>".$monthName."</th>";
					$i++;
				} 
				?>
			</tr>
		</thead>
		<tbody>
			<?php 
			$dbLink = mysqli_connect("localhost","u8364183_marketing","PVMMA0Akp4;(","u8364183_hcm");
			$qjamkerja = "SELECT * FROM `aki_jamkerja` WHERE aktif='1'";
			$rjamkerja=mysqli_query($dbLink,$qjamkerja);
			$jmasuk='';$jistirahat1='';$jistirahat2='';$jpulang='';$jsabtu='';
			while ($labjamkerja = mysqli_fetch_array($rjamkerja)) {
				$jmasuk=$labjamkerja['masuk'];$jistirahat1=$labjamkerja['istirahat1'];$jistirahat2=$labjamkerja['istirahat2'];$jpulang=$labjamkerja['pulang'];$jsabtu=$labjamkerja['sabtu'];
			}
			$qabsen = "SELECT nik,day(tanggal) as day,month(tanggal) as month,year(tanggal) as year,(CASE WHEN (scan1)<time( '".$jmasuk."' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > if(DAYNAME(tanggal)='Saturday','".$jsabtu."','".$jpulang."') THEN 1 END) AS masuk FROM `aki_absensi` where year(tanggal)=".$years." and day(tanggal) BETWEEN 1 and 25 order by nik,month";
			$result=mysqli_query($dbLink,$qabsen);
			$absen=array();
			while ($labsen = mysqli_fetch_array($result)) {
				if ($labsen['masuk'] != null) {
					$absen[$labsen['nik']][$labsen['month']]+=$labsen['masuk'];
				}
			}

			$qabsen = "SELECT nik,day(tanggal) as day,month(tanggal) as month,year(tanggal) as year,(CASE WHEN (scan1)<time( '".$jmasuk."' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > if(DAYNAME(tanggal)='Saturday','".$jsabtu."','".$jpulang."') THEN 1 END) AS masuk FROM `aki_absensi` where year(tanggal)=".$years." and day(tanggal) BETWEEN 26 and 31 order by nik,month";
			$result=mysqli_query($dbLink,$qabsen);
			$absen2=array();
			while ($labsen = mysqli_fetch_array($result)) {
				if ($labsen['masuk'] != null) {
					$absen2[$labsen['nik']][$labsen['month']]+=$labsen['masuk'];
				}
			}
			$filter="";
			if (isset($_GET["skname"])){
				$kname = ($_GET["skname"]);
			}else{
				$kname = "";
			}
			if (isset($_GET["snik"])){
				$nik = ($_GET["snik"]);
			}else{
				$nik = "";
			}
			if (isset($_GET["gol"])){
				$gol = ($_GET["gol"]);
			}else{
				$gol = "";
			}
			if ($kname)
				$filter = $filter . " AND kname LIKE '%" . $kname . "%'";
			if ($nik)
				$filter = $filter . " AND nik LIKE '%" . $nik . "%'";
			if ($gol)
				$filter = $filter . " AND g.gol_kerja='" . $gol . "'";
			$q = "SELECT * FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik where m.status='Aktif '" . $filter." order by m.nik";
			$result=mysqli_query($dbLink,$q);
			$no=1;
			while ($lap = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td><center>".$no."</center></td>";
				echo "<td><center>".$lap['kname']."</center></td>";
				for ($i = 1; $i <= 12; ) {
					if ($lap["nik"]) {
						$jml = (int)$absen[$lap["nik"]][$i]+(int)$absen2[$lap["nik"]][$i-1];
						echo "<td><center>".$jml."</center></td>";
					}
					$i++;
				} 
				$no++;
				echo "</tr>";
			}
			?>
		</tbody>
		
	</table>
</body>
</html>




