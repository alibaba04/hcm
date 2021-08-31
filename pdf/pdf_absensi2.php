<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);

    $pdf=new FPDF();
    $pdf->AddPage('L');
    $years = $_GET['years'];
    $pdf->SetFont('Helvetica', '', 14);

    if ( $pdf->PageNo() !== 1 ) {
        $pdf->SetFont('helvetica', 'B', 8.5); 
        $pdf->SetFillColor(230, 172, 48);
        $pdf->Cell(6,6,'No',1,0,'C',1);
        $pdf->Cell(50,6,'Name',1,0,'C',1);
        for ($i = 1; $i <= 12; ) {
            $dateObj   = DateTime::createFromFormat('!m', $i);
            $monthName = $dateObj->format('F'); 
            $pdf->Cell(18,6,$monthName,1,0,'C',1);
            $i++;
        } 
        $pdf->Cell(1,6,'',0,1,'C',0);
    }

    $pdf->Cell(0, 7, "DATA ABSENSI ".$years, 0, 1, 'C');
    $qabsen = "SELECT nik,day(tanggal) as day,month(tanggal) as month,year(tanggal) as year,(CASE WHEN (scan1)<time( '07:36:00' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > if(DAYNAME(tanggal)='Saturday','12:00:00','16:00:00') THEN 1 END) AS masuk FROM `aki_absensi` where year(tanggal)=".$years." and day(tanggal) BETWEEN 1 and 25 order by nik,month";
    $result=mysqli_query($dbLink,$qabsen);
    $absen=array();
    while ($labsen = mysqli_fetch_array($result)) {
        if ($labsen['masuk'] != null) {
            $absen[$labsen['nik']][$labsen['month']]+=$labsen['masuk'];
        }
    }
    $qabsen = "SELECT nik,day(tanggal) as day,month(tanggal) as month,year(tanggal) as year,(CASE WHEN (scan1)<time( '07:36:00' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > if(DAYNAME(tanggal)='Saturday','12:00:00','16:00:00') THEN 1 END) AS masuk FROM `aki_absensi` where year(tanggal)=".$years." and day(tanggal) BETWEEN 26 and 31 order by nik,month";
    $result=mysqli_query($dbLink,$qabsen);
    $absen2=array();
    while ($labsen = mysqli_fetch_array($result)) {
        if ($labsen['masuk'] != null) {
            $absen2[$labsen['nik']][$labsen['month']]+=$labsen['masuk'];
        }
    }
    $pdf->SetMargins(12,10,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 8.5); 
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(6,6,'No',1,0,'C',1);
    $pdf->Cell(50,6,'Name',1,0,'C',1);
    for ($i = 1; $i <= 12; ) {
        $dateObj   = DateTime::createFromFormat('!m', $i);
        $monthName = $dateObj->format('F'); 
        $pdf->Cell(18,6,$monthName,1,0,'C',1);
        $i++;
    } 
    $pdf->Cell(1,6,'',0,1,'C',0);
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
    $pdf->SetFont('helvetica', '', 7);
    while ($lap = mysqli_fetch_array($result)) {
        if ($no % 2 == 0) {
            $pdf->SetFillColor(223, 231, 242);
        }else{
            $pdf->SetFillColor(255, 255, 255);
        }
        $pdf->Cell(6,5,$no,1,0,'C',1);
        $pdf->Cell(50,5,$lap["kname"],1,0,'L',1);
        for ($i = 1; $i <= 12; ) {
            if ($lap["nik"]) {
                $jml = (int)$absen[$lap["nik"]][$i]+(int)$absen2[$lap["nik"]][$i-1];
                $pdf->Cell(18,5,$jml,1,0,'C',1);
            }
            $i++;
        } 
        $pdf->Cell(1,5,'',0,1,'C',0);
        $no++;
    }
    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>