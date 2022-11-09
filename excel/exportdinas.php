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
	require_once ("../function/fungsi_formatdate.php");
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Data_Detail_DINAS.xls");
	?>
 
	<center>
		<h1>Export Data DINAS</h1>
	</center>
 
	<table border="1" class="table">
		<thead class="thead-dark">
			<tr>
				<th style='background: palegreen;'>No</th>
				<th style='background: palegreen;'>No. Surat</th>
				<th style='background: palegreen;'>Tanggal Pengajuan</th>
				<th style='background: palegreen;'>Tanggal Berangkat</th>
				<th style='background: palegreen;'>Alamat Tujuan</th>
				<th style='background: palegreen;'>Keperluan</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$dbLink = mysqli_connect("localhost","u8364183_marketing","PVMMA0Akp4;(","u8364183_hcm");
			$q = "SELECT d.nodinas, d.tgl_pengajuan, d.tgl_berangkat, d.alamat, d.ket, g.departemen, COUNT(dd.nik) as jml FROM `aki_dinas` d left join aki_ddinas dd on d.nodinas=dd.nodinas left join aki_golongan_kerja g on dd.nik=g.nik WHERE aktif=1 GROUP by d.nodinas ";
			$result=mysqli_query($dbLink,$q);
			$no=1;
			while ($lap = mysqli_fetch_array($result)) {
				
				echo "<tr>";
				echo "<td><center>".$no."</center></td>";
				echo "<td><center>".$lap['nodinas']."</center></td>";
				echo "<td><center>".date("d F Y", strtotime($lap['tgl_pengajuan']))."</center></td>";
				echo "<td><center>".date("d F Y", strtotime($lap['tgl_berangkat']))."</center></td>";
				echo "<td>".$lap['alamat']."</td>";
				echo "<td>".$lap['ket']."</td>";
				echo "</tr>";
				$no++;
			}
			?>
		</tbody>
		
	</table>
</body>
</html>




