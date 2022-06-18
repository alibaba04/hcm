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
	header("Content-Disposition: attachment; filename=Data_DINAS_".$years.".xls");
	?>
 
	<center>
		<h1>Export Data DINAS <?php echo $years; ?><br/></h1>
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
			$qt='SELECT dd.nik,d.tgl_berangkat,month(tgl_berangkat) as month,COUNT(dd.nik) as jml FROM `aki_dinas` d left join aki_ddinas dd on d.nodinas=dd.nodinas WHERE aktif=1 and year(tgl_berangkat)="'.$years.'" GROUP by month,nik';
			$result=mysqli_query($dbLink,$qt);
			$absen=array();
			while ($labsen = mysqli_fetch_array($result)) {
				if ($labsen['nik']) {
					$absen[$labsen['nik']][$labsen['month']]=$labsen['jml'];
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
						$jml = (int)$absen[$lap["nik"]][$i];
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




