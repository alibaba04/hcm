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
				<th style='background: #b3defc;'>No</th>
				<th style='background: #b3defc;'>Tgl. Pengajuan</th>
				<th style='background: #b3defc;'>No Surat</th>
				<th style='background: #b3defc;'>Nama Proyek</th>
				<th style='background: #40E0D0;'>Nomor SPK</th>
				<th style='background: #40E0D0;'>Alamat Proyek</th>
				<th style='background: #40E0D0;'>No HP PJ</th>
				<th style='background: #b3defc;'>Ukuran</th>
				<th style='background: #b3defc;'>Ukuran (Opsional)</th>
				<th style='background: #9FE2BF;'>Tgl. Berangkat</th>
				<th style='background: #9FE2BF;'>Tgl. Selesai</th>
				<th style='background: #b3defc;'>Pulang Pemasangan Aktual</th>
				<th style='background: #b3defc;'>Laporan</th>
				<th style='background: #b3defc;'>Jenis Pemasangan</th>
				<?php
					for ($i=1; $i <=20; $i++) { 
						echo "<th style='background: #dbfffb;'>Nama ".$i."</th>";
						echo "<th style='background: #dbfffb;'>Jabatan ".$i."</th>";
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?php 
			$dbLink = mysqli_connect("localhost","u8364183_marketing","PVMMA0Akp4;(","u8364183_hcm");
			$q = "SELECT d.*, g.departemen, dd.nik, dd.jobs, COUNT(dd.nik) as jml FROM `aki_dinas` d left join aki_ddinas dd on d.nodinas=dd.nodinas left join aki_golongan_kerja g on dd.nik=g.nik WHERE aktif=1 GROUP by d.nodinas ";
			$result=mysqli_query($dbLink,$q);
			$no=1;
			while ($lap = mysqli_fetch_array($result)) {
				$diff = abs(strtotime($lap['tgl_selesai']) - strtotime($lap['tgl_berangkat']));
				$years = floor($diff / (365*60*60*24));
				$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
				$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				echo "<tr>";
				echo "<td><center>".$no."</center></td>";
				echo "<td><center>".$lap['nodinas']."</center></td>";
				echo "<td><center>".date("d F Y", strtotime($lap['tgl_pengajuan']))."</center></td>";
				echo "<td><center>".$lap['departemen']."</center></td>";
				echo "<td><center>".date("d F Y", strtotime($lap['tgl_berangkat']))."</center></td>";
				echo "<td>".$lap['alamat']."</td>";
				echo "<td>".$lap['ket']."</td>";
				echo "<td><center>".(floor($days)+1)." Hari</center></td>";
				echo "<td><center>".date("d F Y", strtotime($lap['tgl_selesai']))."</center></td>";
				echo "<td><center>".date("d F Y", strtotime($lap['tgl_pulang']))."</center></td>";
				echo "<td>"."</td>";
				echo "<td>".$lap['transport']."</td>";
				echo "<td>".$lap['jenis_transport']."</td>";
				$q2 = 'SELECT dd.*,m.kname,g.jabatan FROM aki_ddinas dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE (dd.nodinas)="'.$lap["nodinas"].'" order by dd.nik';
				$result2=mysqli_query($dbLink,$q2);
				while ($lap2 = mysqli_fetch_array($result2)) {
					echo "<td>".$lap2['kname']."</td>";
					echo "<td>".$lap2['jobs']."</td>";
				}
				echo "</tr>";
				$no++;
			}
			?>
		</tbody>
		
	</table>
</body>
</html>




