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

    $pdf->Cell(0, 7,"DATA ABSENSI ".$_GET['month'].'-'.$years, 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 7,"Hari Efektif ".$_GET['day']." Hari.", 0, 1, 'C');
    $qt='SELECT nik,Year(tanggal) as years,month(tanggal) as month ,jenis,COUNT(nik) as jml FROM `aki_izin`WHERE aktif=1 and jenis ="Dinas" and year(tanggal)='.$years.' GROUP by month,nik';
    $result=mysqli_query($dbLink,$qt);
    $absen=array();
    while ($labsen = mysqli_fetch_array($result)) {
        if ($labsen['nik']) {
            $absen[$labsen['nik']][$labsen['month']]=$labsen['jml'];
        }
    }
    $pdf->SetMargins(18,10,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 8); 
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(10,6,'No',1,0,'C',1);
    $pdf->Cell(15,6,'NIK',1,0,'C',1);
    $pdf->Cell(50,6,'Nama',1,0,'C',1);
    $pdf->Cell(30,6,'Bulan',1,0,'C',1);
    $pdf->Cell(30,6,'Kehadiran',1,0,'C',1);
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(5,6,'',1,0,'C',1);
    $pdf->Cell(30,6,'Result',1,0,'C',1);
    
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
    $q = "SELECT m.kname,m.nik,g.gol_kerja,MONTHNAME(tanggal) as month,DAYNAME(tanggal) dayname,year(tanggal) as year,if(gol_kerja='Manajemen', sum(CASE WHEN (scan1)<time( '07:30:00' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > if(DAYNAME(tanggal)='Saturday','12:00:00','16:00:00') THEN (1) else '-'END) ,sum(if(DAYNAME(tanggal)='Saturday',(CASE WHEN (scan1)<time( '07:30:00' ) and if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) > time('12:00:00') then 1 end),(CASE WHEN (scan1)<time('07:30:00') and (scan2)>time('12:00:00') and (scan3)<time( '13:00:00') and (scan4)>time( '16:00:00' ) THEN (1) else '-' END)))) AS masuk,count(ab.nik) as hadir FROM `aki_absensi` ab RIGHT join aki_tabel_master m on ab.nik=m.nik left join aki_golongan_kerja g on m.nik=g.nik where tanggal BETWEEN '".$tgl1."' and '".$tgl2."'" . $filter." group by m.nik order by masuk desc";
    $result=mysqli_query($dbLink,$q);
    $no=1;
    $pdf->SetFont('helvetica', '', 7);
    while ($lap = mysqli_fetch_array($result)) {
        if ($no % 2 == 0) {
            $pdf->SetFillColor(223, 231, 242);
        }else{
            $pdf->SetFillColor(255, 255, 255);
        }
        if ($_GET['day'] == $lap["hadir"] && $_GET['day'] == $lap["masuk"]) {
            $pdf->Cell(10,5,$no,1,0,'C',1);
            $pdf->Cell(15,5,$lap["nik"],1,0,'L',1);
            $pdf->Cell(50,5,$lap["kname"],1,0,'L',1);
            $pdf->Cell(30,5,$lap["month"],1,0,'C',1);
            $pdf->Cell(30,5,$lap["hadir"],1,0,'C',1);
            $pdf->SetFillColor(230, 172, 48);
            $pdf->Cell(5,5,'',1,0,'C',1);
            if ($no % 2 == 0) {
                $pdf->SetFillColor(223, 231, 242);
            }else{
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->Cell(30,5,$lap["masuk"],1,0,'C',1);
            $no++;
            $pdf->Cell(1,5,'',0,1,'C',0);
        }else if($_GET['day'] == 0){
            $pdf->Cell(10,5,$no,1,0,'C',1);
            $pdf->Cell(15,5,$lap["nik"],1,0,'L',1);
            $pdf->Cell(50,5,$lap["kname"],1,0,'L',1);
            $pdf->Cell(30,5,$lap["month"],1,0,'C',1);
            $pdf->Cell(30,5,$lap["hadir"],1,0,'C',1);
            $pdf->SetFillColor(230, 172, 48);
            $pdf->Cell(5,5,'',1,0,'C',1);
            if ($no % 2 == 0) {
                $pdf->SetFillColor(223, 231, 242);
            }else{
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->Cell(30,5,$lap["masuk"],1,0,'C',1);
            $no++;
            $pdf->Cell(1,5,'',0,1,'C',0);
        }
        
        /*$pdf->Cell(20,5,$lap["scan1"],1,0,'C',1);
        if ($lap["gol_kerja"] == 'Manajemen') {
            $pdf->Cell(20,5,'-',1,0,'C',1);
            $pdf->Cell(20,5,'-',1,0,'C',1);
            if ($lap["scan6"] != '00:00:00') {
                $pdf->Cell(20,5,$lap["scan6"],1,0,'C',1);
            }else if($lap["scan5"] != '00:00:00'){
                $pdf->Cell(20,5,$lap["scan5"],1,0,'C',1);
            }else if($lap["scan4"] != '00:00:00'){
                $pdf->Cell(20,5,$lap["scan4"],1,0,'C',1);
            }else if($lap["scan3"] != '00:00:00'){
                $pdf->Cell(20,5,$lap["scan3"],1,0,'C',1);
            }else if($lap["scan2"] != '00:00:00'){
                $pdf->Cell(20,5,$lap["scan2"],1,0,'C',1);
            }else{
                $pdf->Cell(20,5,'-',1,0,'C',1);
            }
        }else if ($lap["gol_kerja"] == 'Produksi'){
            $pdf->Cell(20,5,$lap["scan2"],1,0,'C',1);
            $pdf->Cell(20,5,$lap["scan3"],1,0,'C',1);
            $pdf->Cell(20,5,$lap["scan4"],1,0,'C',1);
        }else{
            $pdf->Cell(20,5,'-',1,0,'C',1);
            $pdf->Cell(20,5,'-',1,0,'C',1);
        }*/
        
        /*if ($lap["gol_kerja"] == 'Manajemen') {
            $pdf->Cell(30,5,$lap["masuk"],1,0,'C',1);
        }else if ($lap["gol_kerja"] == 'Produksi'){
            if ($lap["dayname"] == 'Saturday') {
                $pdf->Cell(30,5,$lap["masuk"],1,0,'C',1);
            }else{
                $pdf->Cell(30,5,$lap["masuk2"],1,0,'C',1);
            }
        }else{
            $pdf->Cell(30,5,'-',1,0,'C',1);
        }*/

        
    }
    /*$q = "SELECT m.kname,m.nik,t.um,t.transport,t.komunikasi,t.fungsional FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik left join aki_tunjangan t on m.nik=t.nik where m.status='Aktif '" . $filter." order by m.nik";
    $result=mysqli_query($dbLink,$q);
    while ($lap = mysqli_fetch_array($result)) {
        $pdf->Cell(6,5,$no,1,0,'C',1);
        $pdf->Cell(50,5,$lap["kname"],1,0,'L',1);

        $pdf->Cell(1,5,'',0,1,'C',0);
        $no++;
    }*/
    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>