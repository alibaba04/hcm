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

    $pdf->Cell(0, 7, "DATA CUTI ".$years, 0, 1, 'C');
    $qt='SELECT nik,Year(tanggal) as years,month(tanggal) as month ,jenis,COUNT(nik) as jml FROM `aki_izin`WHERE aktif=1 and jenis like "Cuti%" and year(tanggal)='.$years.' GROUP by month,nik,jenis';
    $result=mysqli_query($dbLink,$qt);
    $absen=array();
    while ($labsen = mysqli_fetch_array($result)) {
        if ($labsen['nik']) {
            $absen[$labsen['nik']][$labsen['month']][$labsen['jenis']]=$labsen['jml'];
        }
    }
    $pdf->SetMargins(8,10,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 8); 
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(6,9,'No',1,0,'C',1);
    $pdf->Cell(50,9,'Name',1,0,'C',1);
    for ($i = 1; $i <= 12; ) {
        $dateObj   = DateTime::createFromFormat('!m', $i);
        $monthName = $dateObj->format('F'); 
        $pdf->Cell(19,5,$monthName,1,0,'C',1);
        $i++;
    } 
    $pdf->Cell(1,5,'',0,1,'C',0);
    $pdf->Cell(56,4,'',0,0,'C',0);
    for ($i = 1; $i <= 12; ) {
        $pdf->Cell(9.5,4,'CT',1,0,'C',1);
        $pdf->Cell(9.5,4,'CM',1,0,'C',1);
        $i++;
    } 
    $pdf->Cell(1,4,'',0,1,'C',0);
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
    $pdf->SetFont('helvetica', '', 8);
    while ($lap = mysqli_fetch_array($result)) {
        if ($no % 2 == 0) {
            $pdf->SetFillColor(223, 231, 242);
        }else{
            $pdf->SetFillColor(255, 255, 255);
        }
        $pdf->Cell(6,5,$no,1,0,'C',1);
        $pdf->Cell(50,5,substr($lap["kname"],0,31),1,0,'L',1);
        for ($i = 1; $i <= 12; ) {
            if ($lap["nik"]) {
                $CT = (int)$absen[$lap["nik"]][$i]['Cuti Tahunan'];
                $CM = (int)$absen[$lap["nik"]][$i]['Cuti Melahirkan'];
                $pdf->Cell(9.5,5,$CT,1,0,'C',1);
                $pdf->Cell(9.5,5,$CM,1,0,'C',1);
            }
            $i++;
        } 
        $pdf->Cell(1,5,'',0,1,'C',0);
        $no++;
    }
    $pdf->Output('Report_cuti_'.$month.'/'.$years.'.pdf', 'I'); //download file pdf
?>