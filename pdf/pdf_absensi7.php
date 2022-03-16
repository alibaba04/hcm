<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);

    $pdf=new FPDF();
    $pdf->AddPage();
    $years = $_GET['years'];
    $pdf->SetFont('Helvetica', '', 14);

    if ( $pdf->PageNo() !== 1 ) {
        $pdf->SetFont('helvetica', 'B', 9); 
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

    $pdf->Cell(0, 7, "DATA ABSENSI per HARI ".$years, 0, 1, 'C');
    $qt='SELECT nik,Year(tanggal) as years,month(tanggal) as month ,jenis,COUNT(nik) as jml FROM `aki_izin`WHERE aktif=1 and jenis ="Dinas" and year(tanggal)='.$years.' GROUP by month,nik';
    $result=mysqli_query($dbLink,$qt);
    $absen=array();
    while ($labsen = mysqli_fetch_array($result)) {
        if ($labsen['nik']) {
            $absen[$labsen['nik']][$labsen['month']]=$labsen['jml'];
        }
    }
    $pdf->SetMargins(10,10,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 8); 
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(15,6,'NIK',1,0,'C',1);
    $pdf->Cell(50,6,'Nama',1,0,'C',1);
    $pdf->Cell(20,6,'Tanggal',1,0,'C',1);
    $pdf->Cell(20,6,'Hari',1,0,'C',1);
    $pdf->Cell(20,6,'Masuk',1,0,'C',1);
    $pdf->Cell(20,6,'Istirahat1',1,0,'C',1);
    $pdf->Cell(20,6,'Istirahat2',1,0,'C',1);
    $pdf->Cell(20,6,'Pulang',1,0,'C',1);
    $pdf->SetFillColor(230, 172, 48);
    //$pdf->Cell(20,6,'Result',1,0,'C',1);
    
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
    $month = $_GET['month'];
    $years = $_GET['years'];
    $tgl1 = $years.'-'.((int)$month-1).'-26';
    $tgl2 = $years.'-'.$month.'-25';
    $q = "SELECT m.kname,g.gol_kerja,DAYNAME(tanggal) dayname,MONTHNAME(tanggal) as month,DAYNAME(tanggal) dayname,year(tanggal) as year,ab.*,if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) as mpulang FROM `aki_absensi` ab RIGHT join aki_tabel_master m on ab.nik=m.nik left join aki_golongan_kerja g on m.nik=g.nik where tanggal BETWEEN '".$tgl1."' and '".$tgl2."'" . $filter." order by m.nik";
    $result=mysqli_query($dbLink,$q);
    $no=1;
    $pdf->SetFont('helvetica', '', 7);
    while ($lap = mysqli_fetch_array($result)) {
        if ($no % 2 == 0) {
            $pdf->SetFillColor(223, 231, 242);
        }else{
            $pdf->SetFillColor(255, 255, 255);
        }

        $pdf->Cell(15,5,$lap["nik"],1,0,'L',1);
        $pdf->Cell(50,5,$lap["kname"],1,0,'L',1);
        $pdf->Cell(20,5,$lap["tanggal"],1,0,'C',1);
        $pdf->Cell(20,5,$lap["dayname"],1,0,'C',1);
        if ($lap["scan1"]>'07:30:00') {
            $pdf->SetFillColor(255, 0, 0);
        }
        $pdf->Cell(20,5,$lap["scan1"],1,0,'C',1);
        if ($no % 2 == 0) {
            $pdf->SetFillColor(223, 231, 242);
        }else{
            $pdf->SetFillColor(255, 255, 255);
        }
        if ($lap["gol_kerja"] == 'Manajemen') {
            $pdf->Cell(20,5,'-',1,0,'C',1);
            $pdf->Cell(20,5,'-',1,0,'C',1);
            if ($lap["mpulang"]<'16:00:00' && $lap["dayname"] != 'Saturday') {
                $pdf->SetFillColor(255, 0, 0);
            }
            $pdf->Cell(20,5,$lap["mpulang"],1,0,'C',1);
            if ($no % 2 == 0) {
                $pdf->SetFillColor(223, 231, 242);
            }else{
                $pdf->SetFillColor(255, 255, 255);
            }
        }else if ($lap["gol_kerja"] == 'Produksi'){
            if ($lap["dayname"] != 'Saturday') {
                if ($lap["scan2"]<'12:00:00' || $lap["scan2"] > '12:30:00') {
                    $pdf->SetFillColor(255, 0, 0);
                }
                $pdf->Cell(20,5,$lap["scan2"],1,0,'C',1);
                if ($no % 2 == 0) {
                    $pdf->SetFillColor(223, 231, 242);
                }else{
                    $pdf->SetFillColor(255, 255, 255);
                }
                if ($lap["scan3"]=='00:00:00' || $lap["scan3"]<'12:30:00'|| $lap["scan3"] > '13:00:00') {
                    $pdf->SetFillColor(255, 0, 0);
                }
                $pdf->Cell(20,5,$lap["scan3"],1,0,'C',1);
                if ($no % 2 == 0) {
                    $pdf->SetFillColor(223, 231, 242);
                }else{
                    $pdf->SetFillColor(255, 255, 255);
                }
                if ($lap["scan4"]<'16:00:00') {
                    $pdf->SetFillColor(255, 0, 0);
                }
                $pdf->Cell(20,5,$lap["scan4"],1,0,'C',1);
                if ($no % 2 == 0) {
                    $pdf->SetFillColor(223, 231, 242);
                }else{
                    $pdf->SetFillColor(255, 255, 255);
                }
            }else{
                $pdf->Cell(20,5,'-',1,0,'C',1);
                $pdf->Cell(20,5,'-',1,0,'C',1);
                if ($lap["mpulang"]<'16:00:00' && $lap["dayname"] != 'Saturday') {
                    $pdf->SetFillColor(255, 0, 0);
                }
                $pdf->Cell(20,5,$lap["mpulang"],1,0,'C',1);
                if ($no % 2 == 0) {
                    $pdf->SetFillColor(223, 231, 242);
                }else{
                    $pdf->SetFillColor(255, 255, 255);
                }
            }
            
        }else{
            $pdf->Cell(20,5,'-',1,0,'C',1);
            $pdf->Cell(20,5,'-',1,0,'C',1);
        }
        $pdf->Cell(1,5,'',0,1,'C',0);
        $no++;
    }
    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>