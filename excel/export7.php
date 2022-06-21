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
		<h1>Export DATA REKAP ABSENSI <?php echo $years; ?><br/></h1>
	</center>
 
	<table border="1" class="table">
		<thead class="thead-dark">
			<tr>
				<th style='background: palegreen;'>No</th>
				<th style='background: palegreen;'>NIK</th>
				<th style='background: palegreen;'>Name</th>
				<th style='background: palegreen;'>Bulan</th>
				<th style='background: palegreen;'>Masuk</th>
				<th style='background: palegreen;'>Istirahat1</th>
				<th style='background: palegreen;'>Istirahat2</th>
				<th style='background: palegreen;'>Pulang</th>
				<th style='background: palegreen;'>Result</th>
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
				if ($lap["jabatan"] == 'Kerumahtanggaan') {
					if ($lap["scan1"]<'06:30:00') {
						$absenR[$lap['nik']]['masuk']+=1;
					}
				}else{
					if ($lap["scan1"]<'07:30:00') {
						$absenR[$lap['nik']]['masuk']+=1;
					}
				}

				if ($lap["gol_kerja"] == 'Manajemen') {
					if ($lap["jabatan"] == 'Kerumahtanggaan'){
						if ($lap["dayname"] != 'Saturday') {
							if ($lap["dayname"] == 'Friday') {
								if ($lap["scan2"]<'11:00:00' || $lap["scan2"] > '12:30:00') {
								}else{
									$absenR[$lap['nik']]['istirahat1']+=1;
								}
							}else{
								if ($lap["scan2"]<'12:00:00' || $lap["scan2"] > '12:30:00') {
								}else{
									$absenR[$lap['nik']]['istirahat1']+=1;
								}
							}
							if ($lap["scan3"]=='00:00:00' || $lap["scan3"]<'12:30:00'|| $lap["scan3"] > '13:00:00') {
							}else{
								$absenR[$lap['nik']]['istirahat2']+=1;
							}
							if ($lap["scan4"]<'17:00:00') {
							}else{
								$absenR[$lap['nik']]['pulang']+=1;
							}
						}else{
							if ($lap["mpulang"]<'17:00:00' && $lap["dayname"] != 'Saturday') {
							}else{
								$absenR[$lap['nik']]['pulang']+=1;
							}
						}
					}else{
						if ($lap["mpulang"]<'16:00:00' && $lap["dayname"] != 'Saturday') {
						}else{
							$absenR[$lap['nik']]['pulang']+=1;
						}
					}

				}else if ($lap["gol_kerja"] == 'Produksi'){
					if ($lap["dayname"] != 'Saturday') {
						if ($lap["dayname"] == 'Friday') {
							if ($lap["scan2"]<'11:00:00' || $lap["scan2"] > '12:30:00') {
							}else{
								$absenR[$lap['nik']]['istirahat1']+=1;
							}
						}else{
							if ($lap["scan2"]<'12:00:00' || $lap["scan2"] > '12:30:00') {
							}else{
								$absenR[$lap['nik']]['istirahat1']+=1;
							}
						}
						if ($lap["scan3"]=='00:00:00' || $lap["scan3"]<'12:30:00'|| $lap["scan3"] > '13:00:00') {
						}else{
							$absenR[$lap['nik']]['istirahat2']+=1;
						}
						if ($lap["scan4"]<'16:00:00') {
						}else{
							$absenR[$lap['nik']]['pulang']+=1;
						}

					}else{
						$absenR[$lap['nik']]['istirahat1']+=1;
						$absenR[$lap['nik']]['istirahat2']+=1;
						if ($lap["mpulang"]<'16:00:00' && $lap["dayname"] != 'Saturday') {
						}else{
							$absenR[$lap['nik']]['pulang']+=1;
						}
					}
				}
				$no++;
			}
			$q2 = "SELECT m.nik,m.kname,g.jabatan,g.gol_kerja,DAYNAME(tanggal) dayname,MONTHNAME(tanggal) as month,DAYNAME(tanggal) dayname,year(tanggal) as year,ab.tanggal FROM `aki_absensi` ab RIGHT join aki_tabel_master m on ab.nik=m.nik left join aki_golongan_kerja g on m.nik=g.nik where tanggal BETWEEN '".$tgl1."' and '".$tgl2."'" . $filter." group by m.nik";
			$result2=mysqli_query($dbLink,$q2);
			$no2=1;
			while ($lap2 = mysqli_fetch_array($result2)) {
				echo "<tr>";
				echo "<td><center>".$no."</center></td>";
				echo "<td><center>".$lap2['nik']."</center></td>";
				echo "<td><center>".$lap2['kname']."</center></td>";
				echo "<td><center>".$lap2['month']."</center></td>";
				echo "<td><center>".$absenR[$lap2["nik"]]['masuk']."</center></td>";
				echo "<td><center>".$absenR[$lap2["nik"]]['istirahat1']."</center></td>";
				echo "<td><center>".$absenR[$lap2["nik"]]['istirahat2']."</center></td>";
				echo "<td><center>".$absenR[$lap2["nik"]]['pulang']."</center></td>";
				if ($lap2["gol_kerja"] == 'Manajemen') {
					$resulta = min($absenR[$lap2["nik"]]['masuk'],$absenR[$lap2["nik"]]['pulang']);
				}else{
					$resulta = min($absenR[$lap2["nik"]]['masuk'],$absenR[$lap2["nik"]]['istirahat1'],$absenR[$lap2["nik"]]['istirahat2'],$absenR[$lap2["nik"]]['pulang']);
				}
				echo "<td><center>".$resulta."</center></td>";
				$no2++;
			}
			?>
		</tbody>
		
	</table>
</body>
</html>




