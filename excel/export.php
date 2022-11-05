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
	$month = $_GET['month'];
	$years = $_GET['years'];
	$tgl1 = $years.'-'.((int)$month-1).'-26';
	$tgl2 = $years.'-'.$month.'-25';
	$tanggal = date("Y-m-d h", time());
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Data_".date(" F Y", strtotime($tgl2)).".xls");
	?>
 
	<center>
		<h1>Export Data Absensi <?php echo date("d F Y", strtotime($tgl1))." s/d ".date("d F Y", strtotime($tgl2)); ?><br/></h1>
	</center>
 
	<table border="1" class="table">
		<thead class="thead-dark">
			<th style='background: palegreen;'>NIK</th>
			<th style='background: palegreen;'>Name</th>
			<?php 
				$tgl2b = $years.'-'.$month.'-26';
				$period = new DatePeriod(
					new DateTime(date("Y-m-d", strtotime($tgl1))),
					new DateInterval('P1D'),
					new DateTime(date("Y-m-d", strtotime($tgl2b)))
				);
				foreach ($period as $key => $value) {
					echo "<th style='background: palegreen;'>".$value->format('d')."</th>" ;
				}
			?>
			<th style='background: seagreen ;'>HK</th>
			<th style='background: seagreen ;'>D</th>
			<th style='background: seagreen ;'>A</th>
			<th style='background: seagreen ;'>SH</th>
			<th style='background: seagreen ;'>IMP</th>
			<th style='background: seagreen ;'>TL</th>
			<th style='background: seagreen ;'>S</th>
			<th style='background: seagreen ;'>NKH</th>
			<th style='background: seagreen ;'>MGL</th>
			<th style='background: seagreen ;'>CT</th>
			<th style='background: seagreen ;'>CM</th>
		</tr></thead>
		<?php 
		// koneksi database
		$dbLink = mysqli_connect("localhost","u8364183_marketing","PVMMA0Akp4;(","u8364183_hcm");

		$qjamkerja = "SELECT * FROM `aki_jamkerja` WHERE aktif='1'";
		$rjamkerja=mysqli_query($dbLink,$qjamkerja);
		$jmasuk='';$jistirahat1='';$jistirahat2='';$jpulang='';$jsabtu='';
		while ($labjamkerja = mysqli_fetch_array($rjamkerja)) {
			$jmasuk=$labjamkerja['masuk'];$jistirahat1=$labjamkerja['istirahat1'];$jistirahat2=$labjamkerja['istirahat2'];$jpulang=$labjamkerja['pulang'];$jsabtu=$labjamkerja['sabtu'];
		}
 		
 		$qabsen = "SELECT nik,DAYNAME(tanggal) dayname,day(tanggal) as day,month(tanggal) as month,year(tanggal) as year,(CASE WHEN (scan1)<time( '".$jmasuk."' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > if(DAYNAME(tanggal)='Saturday','".$jsabtu."','".$jpulang."') THEN (1) END) AS masuk FROM `aki_absensi` where tanggal BETWEEN '".$tgl1."' and '".$tgl2."' order by nik,month,day";
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
			if ($month ==01) {
				$cday = cal_days_in_month(CAL_GREGORIAN,($month+11),$years);
			}else{
				$cday = cal_days_in_month(CAL_GREGORIAN,($month-1),$years);
			}
			$totalImp=0;
			$totalTl=0;
			$totalS=0;
			$totalNkh=0;
			$totalMgl=0;
			$totalSh=0;
			$totalA=0;
			$totalD=0;
			$totalCt=0;
			$totalCm=0;
			$totalAbsen=0;
			echo "<tr><td>'".$lap['nik']."</td>";
			echo "<td>".$lap['kname']."</td>";
			for ($i=26; $i <= $cday; $i++) { 
				if (empty($absen[$lap['nik']][$i])) {
					if (date('l', strtotime($years.'-'.((int)$month-1).'-'.$i)) == 'Sunday') {
						echo "<td style='color:white;background:red'><center>M</center></td>";
					}else{
						if (empty($absen2[$lap['nik']][$i])) {
							if (empty($absen3[$i])) {
								echo "<td></td>";
							}else{
								echo "<td>".$absen3[$i]."</td>";
							}
						}else{
							if ($absen2[$lap['nik']][$i] == 'Izin Meninggalkan Pekerjaan') {
								echo "<td>IMP</td>";
								if ($lap['nik']) {
									$totalImp++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Terlambat') {
								echo "<td>TL</td>";
								if ($lap['nik']) {
									$totalTl++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Sakit') {
								echo "<td>S</td>";
								if ($lap['nik']) {
									$totalS++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Menikah') {
								echo "<td>NKH</td>";
								if ($lap['nik']) {
									$totalNkh++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Keluarga Meninggal') {
								echo "<td>MGL</td>";
								if ($lap['nik']) {
									$totalMgl++;
								}
							} if($absen2[$lap['nik']][$i] == 'Izin 1/2 Hari'){
								echo "<td>SH</td>";
								if ($lap['nik']) {
									$totalSh++;
								}
							} if($absen2[$lap['nik']][$i] == 'Izin Tidak Masuk'){
								echo "<td>A</td>";
								if ($lap['nik']) {
									$totalA++;
								}
							} if($absen2[$lap['nik']][$i] == 'Dinas'){
								echo "<td>D</td>";
								if ($lap['nik']) {
									$totalD++;
								}
							} if($absen2[$lap['nik']][$i] == 'Cuti Tahunan'){
								echo "<td>CT</td>";
								if ($lap['nik']) {
									$totalCt++;
								}
							} if($absen2[$lap['nik']][$i] == 'Cuti Melahirkan'){
								echo "<td>CM</td>";
								if ($lap['nik']) {
									$totalCm++;
								}
							}
						}
					}
				}else{
					echo "<td>".$absen[$lap['nik']][$i]."</td>";
				}
			}
			for ($i=1; $i < 26; $i++) { 
				if (empty($absen[$lap['nik']][$i])) {
					if (date('l', strtotime($years.'-'.$month.'-'.$i)) == 'Sunday') {
						echo "<td style='color:white;background:red'><center>M</center></td>";
					}else{
						if (empty($absen2[$lap['nik']][$i])) {
							if (empty($absen3[$i])) {
								echo "<td></td>";
							}else{
								echo "<td>".$absen3[$i]."</td>";
							}
						}else{
							if ($absen2[$lap['nik']][$i] == 'Izin Meninggalkan Pekerjaan') {
								echo "<td>IMP</td>";
								if ($lap['nik']) {
									$totalImp++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Terlambat') {
								echo "<td>TL</td>";
								if ($lap['nik']) {
									$totalTl++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Sakit') {
								echo "<td>S</td>";
								if ($lap['nik']) {
									$totalS++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Menikah') {
								echo "<td>NKH</td>";
								if ($lap['nik']) {
									$totalNkh++;
								}
							} if ($absen2[$lap['nik']][$i] == 'Izin Keluarga Meninggal') {
								echo "<td>MGL</td>";
								if ($lap['nik']) {
									$totalMgl++;
								}
							} if($absen2[$lap['nik']][$i] == 'Izin 1/2 Hari'){
								echo "<td>SH</td>";
								if ($lap['nik']) {
									$totalSh++;
								}
							} if($absen2[$lap['nik']][$i] == 'Izin Tidak Masuk'){
								echo "<td>A</td>";
								if ($lap['nik']) {
									$totalA++;
								}
							} if($absen2[$lap['nik']][$i] == 'Dinas'){
								echo "<td>D</td>";
								if ($lap['nik']) {
									$totalD++;
								}
							} if($absen2[$lap['nik']][$i] == 'Cuti Tahunan'){
								echo "<td>CT</td>";
								if ($lap['nik']) {
									$totalCt++;
								}
							} if($absen2[$lap['nik']][$i] == 'Cuti Melahirkan'){
								echo "<td>CM</td>";
								if ($lap['nik']) {
									$totalCm++;
								}
							}
						}
					}
				}else{
					echo "<td>".$absen[$lap['nik']][$i]."</td>";
					if ($lap['nik']) {
						$totalAbsen++;
					}
				}
			}
			if ($totalAbsen == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalAbsen."</td>";
			}
			if ($totalD == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalD."</td>";
			}
			if ($totalA == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalA."</td>";
			}
			if ($totalSh == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalSh."</td>";
			}
			if ($totalImp == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalImp."</td>";
			}
			if ($totalTl == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalTl."</td>";
			}
			if ($totalS == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalS."</td>";
			}
			if ($totalNkh == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalNkh."</td>";
			}
			if ($totalMgl == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalMgl."</td>";
			}
			if ($totalCt == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalCt."</td>";
			}
			if ($totalCm == 0) {
				echo "<td style='background: MediumSpringGreen;'></td>";
			}else{
				echo "<td style='background: MediumSpringGreen;'>".$totalCm."</td>";
			}
			echo "</tr>";
			$i++; $no++;
		}
		?>
	</table>
</body>
</html>




