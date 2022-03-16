<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);
    $pdf=new FPDF();
    $pdf->AddPage('L');
    $month = $_GET['month'];
    $years = $_GET['years'];

    $tgl1 = $years.'-'.((int)$month-1).'-26';
    $tgl2 = $years.'-'.$month.'-25';
    $pdf->SetFont('Helvetica', '', 14);
    $pdf->Cell(0, 7, "DATA ABSENSI ".date("d F Y", strtotime($tgl1))." s/d ".date("d F Y", strtotime($tgl2)), 0, 1, 'C');
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
    $pdf->SetMargins(6,6,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 6.5); 
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(15,5,'No',1,0,'C',1);
    $pdf->Cell(35,5,'Nama',1,0,'C',1);
    $cday = cal_days_in_month(CAL_GREGORIAN,6,2021);
    for ($i=26; $i <= $cday; $i++) { 
        $pdf->Cell(5,5,$i,1,0,'C',1);
    }
    for ($i=1; $i < 26; $i++) { 
        $pdf->Cell(5,5,$i,1,0,'C',1);
    }

    $pdf->SetFillColor(122, 120, 116);
    $pdf->Cell(5,5,' ',1,0,'C',1);
    $pdf->SetFillColor(184, 153, 88);
    $pdf->Cell(5,5,'HK',1,0,'C',1);
    $pdf->Cell(5,5,'D',1,0,'C',1);
    $pdf->Cell(5,5,'A',1,0,'C',1);
    $pdf->Cell(5,5,'SH',1,0,'C',1);
    $pdf->Cell(6,5,'IMP',1,0,'C',1);
    $pdf->Cell(5,5,'TL',1,0,'C',1);
    $pdf->Cell(5,5,'S',1,0,'C',1);
    $pdf->Cell(6,5,'NKH',1,0,'C',1);
    $pdf->Cell(6,5,'MGL',1,0,'C',1);
    $pdf->Cell(5,5,'CT',1,0,'C',1);
    $pdf->Cell(5,5,'CM',1,0,'C',1);
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(6,5,'UM',1,0,'C',1);
    $pdf->Cell(6,5,'Tran',1,0,'C',1);
    $pdf->Cell(6,5,'Kom',1,1,'C',1);
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
    $q = "SELECT m.nik,m.kname,t.um,t.transport,t.komunikasi,t.fungsional FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik left join aki_tunjangan t on m.nik=t.nik where m.status='Aktif '" . $filter." order by m.nik";
    $result=mysqli_query($dbLink,$q);
    $no=1;
    $pdf->SetFont('helvetica', '', 6.5);
    while ($lap = mysqli_fetch_array($result)) {
        $pdf->Cell(15,4,$lap["nik"],1,0,'C',0);
        $pdf->Cell(35,4,$lap["kname"],1,0,'L',0);
        $cday = cal_days_in_month(CAL_GREGORIAN,6,2021);
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
        for ($i=26; $i <= $cday; $i++) { 
            if (empty($absen[$lap['nik']][$i])) {
                if (date('l', strtotime($years.'-'.((int)$month-1).'-'.$i)) == 'Sunday') {
                    $pdf->SetTextColor(220,50,50);
                    $pdf->Cell(5,4,'M',1,0,'C',0);
                    $pdf->SetTextColor(0);
                }else{
                    if (empty($absen2[$lap['nik']][$i])) {
                        if (empty($absen3[$i])) {
                            $pdf->Cell(5,4,'',1,0,'C',0);
                        }else{
                            $pdf->SetFillColor(204, 54, 51);
                            $pdf->Cell(5,4,$absen3[$i],1,0,'C',1);
                        }
                    }else{
                        if ($absen2[$lap['nik']][$i] == 'Izin Meninggalkan Pekerjaan') {
                            $pdf->SetFillColor(240, 127, 127);
                            $pdf->Cell(5,4,"IMP",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalImp++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Terlambat') {
                            $pdf->SetFillColor(133, 106, 98);
                            $pdf->Cell(5,4,"TL",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalTl++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Sakit') {
                            $pdf->SetFillColor(157, 196, 245);
                            $pdf->Cell(5,4,"S",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalS++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Menikah') {
                            $pdf->SetFillColor(237, 69, 74);
                            $pdf->Cell(5,4,"NKH",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalNkh++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Keluarga Meninggal') {
                            $pdf->SetFillColor(225, 250, 0);
                            $pdf->Cell(5,4,"MGL",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalMgl++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Izin 1/2 Hari'){
                            $pdf->SetFillColor(244,176,131);
                            $pdf->Cell(5,4,"SH",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalSh++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Izin Tidak Masuk'){
                            $pdf->SetFillColor(220,50,50);
                            $pdf->Cell(5,4,"A",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalA++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Dinas'){
                            $pdf->SetFillColor(37, 250, 0);
                            $pdf->Cell(5,4,"D",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalD++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Cuti Tahunan'){
                            $pdf->SetFillColor(235, 240, 189);
                            $pdf->Cell(5,4,"CT",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalCt++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Cuti Melahirkan'){
                            $pdf->SetFillColor(227, 14, 202);
                            $pdf->Cell(5,4,"CM",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalCm++;
                            }
                        }
                    }
                }
            }else{
                $pdf->Cell(5,4,$absen[$lap['nik']][$i],1,0,'C',0);
                if ($lap['nik']) {
                    $totalAbsen++;
                }
            }
        }
        for ($i=1; $i < 26; $i++) { 
            if (empty($absen[$lap['nik']][$i])) {
                if (date('l', strtotime($years.'-'.$month.'-'.$i)) == 'Sunday') {
                    $pdf->SetTextColor(220,50,50);
                    $pdf->Cell(5,4,'M',1,0,'C',0);
                    $pdf->SetTextColor(0);
                }else{
                    if (empty($absen2[$lap['nik']][$i])) {
                        if (empty($absen3[$i])) {
                            $pdf->Cell(5,4,'',1,0,'C',0);
                        }else{
                            $pdf->SetFillColor(204, 54, 51);
                            $pdf->Cell(5,4,$absen3[$i],1,0,'C',1);
                        }
                    }else{
                        if ($absen2[$lap['nik']][$i] == 'Izin Meninggalkan Pekerjaan') {
                            $pdf->SetFillColor(240, 127, 127);
                            $pdf->Cell(5,4,"IMP",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalImp++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Terlambat') {
                            $pdf->SetFillColor(133, 106, 98);
                            $pdf->Cell(5,4,"TL",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalTl++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Sakit') {
                            $pdf->SetFillColor(157, 196, 245);
                            $pdf->Cell(5,4,"S",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalS++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Menikah') {
                            $pdf->SetFillColor(237, 69, 74);
                            $pdf->Cell(5,4,"NKH",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalNkh++;
                            }
                        } if ($absen2[$lap['nik']][$i] == 'Izin Keluarga Meninggal') {
                            $pdf->SetFillColor(225, 250, 0);
                            $pdf->Cell(5,4,"MGL",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalMgl++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Izin 1/2 Hari'){
                            $pdf->SetFillColor(244,176,131);
                            $pdf->Cell(5,4,"SH",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalSh++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Izin Tidak Masuk'){
                            $pdf->SetFillColor(220,50,50);
                            $pdf->Cell(5,4,"A",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalA++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Dinas'){
                            $pdf->SetFillColor(37, 250, 0);
                            $pdf->Cell(5,4,"D",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalD++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Cuti Tahunan'){
                            $pdf->SetFillColor(235, 240, 189);
                            $pdf->Cell(5,4,"CT",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalCt++;
                            }
                        } if($absen2[$lap['nik']][$i] == 'Cuti Melahirkan'){
                            $pdf->SetFillColor(227, 14, 202);
                            $pdf->Cell(5,4,"CM",1,0,'C',1);
                            if ($lap['nik']) {
                                $totalCm++;
                            }
                        }
                    }
                }
            }else{
                $pdf->Cell(5,4,$absen[$lap['nik']][$i],1,0,'C',0);
                if ($lap['nik']) {
                    $totalAbsen++;
                }
            }
        }
        $pdf->SetFillColor(122, 120, 116);
        $pdf->Cell(5,4,'',1,0,'C',1);
        if ($totalAbsen == 0) {
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalAbsen,1,0,'C',0);
        }
        $pdf->SetFillColor(247, 207, 121);
        if ($totalD == 0) {
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalD,1,0,'C',0);
        }
        if ($totalA == 0) {
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalA,1,0,'C',0);
        }
        if ($totalSh == 0) {
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalSh,1,0,'C',0);
        }
        if ($totalImp == 0) {
            $pdf->Cell(6,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(6,4,$totalImp,1,0,'C',0);
        }
        if ($totalTl == 0) {
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalTl,1,0,'C',0);
        }
        if ($totalS == 0) {
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalS,1,0,'C',0);
        }
        if ($totalNkh == 0) {
            $pdf->Cell(6,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(6,4,$totalNkh,1,0,'C',0);
        }
        if ($totalMgl == 0) {
            $pdf->Cell(6,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(6,4,$totalMgl,1,0,'C',0);
        }
        if ($totalCt == 0) {
            $pdf->SetFillColor(247, 207, 121);
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalCt,1,0,'C',0);
        }
        if ($totalCm == 0) {
            $pdf->SetFillColor(247, 207, 121);
            $pdf->Cell(5,4,'0',1,0,'C',1);
        }else{
            $pdf->Cell(5,4,$totalCm,1,0,'C',0);
        }
        if ($lap['um']==1) {
            $pdf->Cell(6,4,(int)$totalAbsen+(int)$totalCt,1,0,'C',0);
        }else{
            $pdf->Cell(6,4,'0',1,0,'C',0);
        }
        if ($lap['transport']==1) {
            $trans = (int)$absenT[$lap['nik']]['tunjangan']+(int)$totalAbsen;
            $pdf->Cell(6,4,$trans,1,0,'C',0);
        }else{
            $pdf->Cell(6,4,'0',1,0,'C',0);
        }
        if ($lap['komunikasi']==1) {
            $komn = (int)$absenK[$lap['nik']]['tunjangan']+(int)$totalAbsen;
            $pdf->Cell(6,4,$komn,1,0,'C',0);
        }else{
            $pdf->Cell(6,4,'0',1,0,'C',0);
        }
        
        
        $pdf->Cell(5,4,'',0,1,'C',0);
        $no++;
    }
    //output file PDF
    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>