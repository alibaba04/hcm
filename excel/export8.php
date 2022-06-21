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
	header("Content-Disposition: attachment; filename=DATA_REKAP_ABSENSI_".$years.".xls");
	?>
 
	<center>
		<h1>Export DATA REKAP ABSENSI DETAIL <?php echo $years; ?><br/></h1>
	</center>
 
	<table border="1" class="table">
		<thead class="thead-dark">
			<tr>
				<th style='background: palegreen;'>No</th>
				<th style='background: palegreen;'>NIK</th>
				<th style='background: palegreen;'>Name</th>
				<th style='background: palegreen;'>Tanggal</th>
				<th style='background: palegreen;'>Hari</th>
				<th style='background: palegreen;'>Masuk</th>
				<th style='background: palegreen;'>Istirahat1</th>
				<th style='background: palegreen;'>Istirahat2</th>
				<th style='background: palegreen;'>Pulang</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$dbLink = mysqli_connect("localhost","u8364183_marketing","PVMMA0Akp4;(","u8364183_hcm");
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
			$month = $_GET['month'];
			$years = $_GET['years'];
			$tgl1 = $years.'-'.((int)$month-1).'-26';
			$tgl2 = $years.'-'.$month.'-25';
			$q = "SELECT m.kname,g.jabatan,g.gol_kerja,DAYNAME(tanggal) dayname,MONTHNAME(tanggal) as month,DAYNAME(tanggal) dayname,year(tanggal) as year,ab.*,if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) as mpulang FROM `aki_absensi` ab RIGHT join aki_tabel_master m on ab.nik=m.nik left join aki_golongan_kerja g on m.nik=g.nik where tanggal BETWEEN '".$tgl1."' and '".$tgl2."'" . $filter." order by m.nik";
			$result=mysqli_query($dbLink,$q);
			$no=1;
			$absenR=array();
			 
			while ($lap = mysqli_fetch_array($result)) {
				$red=''; $red2=''; $red3=''; $red4=''; 
				if ($no % 2 == 0) {
					echo "<tr style='background: lightblue;'>";
				}else{
					echo "<tr>";
				}
				echo "<td><center>".$no."</center></td>";
				echo "<td><center>".$lap['nik']."</center></td>";
				echo "<td><center>".$lap['kname']."</center></td>";
				echo "<td><center>".$lap['tanggal']."</center></td>";
				echo "<td><center>".$lap['dayname']."</center></td>";
				if ($lap["scan1"]>'07:30:00') {
					$red="style='background: lightblue;'";
				}
				if ($lap["jabatan"] == 'Kerumahtanggaan') {
					if ($lap["scan1"]>'06:30:00') {
						$red="style='background: red;'";
					}
					echo "<td ".$red."><center>".$lap['scan1']."</center></td>";
				}else{
					if ($lap["scan1"]>'07:30:00') {
						$red="style='background: red;'";
					}
					echo "<td ".$red."><center>".$lap['scan1']."</center></td>";
				}
				
				if ($lap["gol_kerja"] == 'Manajemen') {
					if ($lap["jabatan"] == 'Kerumahtanggaan'){
						if ($lap["dayname"] != 'Saturday') {
							if ($lap["dayname"] == 'Friday') {
								if ($lap["scan2"]<'11:00:00' || $lap["scan2"] > '12:00:00') {
									$red2="style='background: red;'";
								}
							}else{
								if ($lap["scan2"]<'12:00:00' || $lap["scan2"] > '12:30:00') {
									$red2="style='background: red;'";
								}
							}
							if ($lap["scan3"]=='00:00:00' || $lap["scan3"]<'12:30:00'|| $lap["scan3"] > '13:00:00') {
								$red3="style='background: red;'";
							}
							if ($lap["scan4"]<'17:00:00') {
								$red4="style='background: red;'";
							}
							echo "<td><center ".$red2.">".$lap['scan2']."</center></td>";
							echo "<td><center ".$red3.">".$lap['scan3']."</center></td>";
							echo "<td><center ".$red4.">".$lap['scan4']."</center></td>";
						}else{
							if ($lap["mpulang"]<'17:00:00' && $lap["dayname"] != 'Saturday') {
								$red4="style='background: red;'";
							}
							echo "<td><center>-</center></td>";
							echo "<td><center>-</center></td>";
							echo "<td ".$red4."><center>".$lap['mpulang']."</center></td>";
						}
					}else{
						if ($lap["mpulang"]<'16:00:00' && $lap["dayname"] != 'Saturday') {
							$red4="style='background: red;'";
						}
						echo "<td><center>-</center></td>";
						echo "<td><center>-</center></td>";
						echo "<td ".$red4."><center>".$lap['mpulang']."</center></td>";
					}
				}else if ($lap["gol_kerja"] == 'Produksi'){
					if ($lap["dayname"] != 'Saturday') {
						if ($lap["dayname"] == 'Friday') {
							if ($lap["scan2"]<'11:00:00' || $lap["scan2"] > '12:00:00') {
								$red2="style='background: red;'";
							}
						}else{
							if ($lap["scan2"]<'12:00:00' || $lap["scan2"] > '12:30:00') {
								$red2="style='background: red;'";
							}
						}
						if ($lap["scan3"]=='00:00:00' || $lap["scan3"]<'12:30:00'|| $lap["scan3"] > '13:00:00') {
							$red3="style='background: red;'";
						}
						if ($lap["scan4"]<'16:00:00') {
							$red4="style='background: red;'";
						}
						echo "<td ".$red2."><center>".$lap['scan2']."</center></td>";
						echo "<td ".$red3."><center>".$lap['scan3']."</center></td>";
						echo "<td ".$red4."><center>".$lap['mpulang']."</center></td>";
					}else{
						echo "<td><center>-</center></td>";
						echo "<td><center>-</center></td>";
						if ($lap["mpulang"]<'16:00:00' && $lap["dayname"] != 'Saturday') {
							$red4="style='background: red;'";
						}
						echo "<td ".$red4."><center>".$lap['mpulang']."</center></td>";
					}
				}else{
					echo "<td><center>-</center></td>";
					echo "<td><center>-</center></td>";
				}
				$no++;
			}
			?>
		</tbody>
		
	</table>
</body>
</html>




