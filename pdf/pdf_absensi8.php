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
    $q = "SELECT m.kname,g.jabatan,g.gol_kerja,DAYNAME(tanggal) dayname,MONTHNAME(tanggal) as month,DAYNAME(tanggal) dayname,year(tanggal) as year,ab.*,if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',scan2,scan3),scan4),scan5),scan6) as mpulang FROM `aki_absensi` ab RIGHT join aki_tabel_master m on ab.nik=m.nik left join aki_golongan_kerja g on m.nik=g.nik where tanggal BETWEEN '".$tgl1."' and '".$tgl2."'" . $filter." order by m.nik";
    $result=mysqli_query($dbLink,$q);
    $no=1;
    $pdf->SetFont('helvetica', '', 7);
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
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 7, "DATA REKAP ABSENSI ".$years, 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 8); 
    $pdf->SetFillColor(230, 172, 48);
    $pdf->Cell(7,6,'No',1,0,'L',1);
    $pdf->Cell(15,6,'NIK',1,0,'C',1);
    $pdf->Cell(50,6,'Nama',1,0,'C',1);
    $pdf->Cell(20,6,'Bulan',1,0,'C',1);
    $pdf->Cell(18,6,'Masuk',1,0,'C',1);
    $pdf->Cell(18,6,'Istirahat1',1,0,'C',1);
    $pdf->Cell(18,6,'Istirahat2',1,0,'C',1);
    $pdf->Cell(18,6,'Pulang',1,0,'C',1);
    $pdf->Cell(20,6,'Result',1,1,'C',1);
    $pdf->SetFillColor(230, 172, 48);
    $q2 = "SELECT m.nik,m.kname,g.jabatan,g.gol_kerja,DAYNAME(tanggal) dayname,MONTHNAME(tanggal) as month,DAYNAME(tanggal) dayname,year(tanggal) as year,ab.tanggal FROM `aki_absensi` ab RIGHT join aki_tabel_master m on ab.nik=m.nik left join aki_golongan_kerja g on m.nik=g.nik where tanggal BETWEEN '".$tgl1."' and '".$tgl2."'" . $filter." group by m.nik";
    $result2=mysqli_query($dbLink,$q2);
    $no2=1;
    $pdf->SetFont('helvetica', '', 7);
    while ($lap2 = mysqli_fetch_array($result2)) {
        if ($no2 % 2 == 0) {
            $pdf->SetFillColor(223, 231, 242);
        }else{
            $pdf->SetFillColor(255, 255, 255);
        }
        $pdf->Cell(7,5,$no2,1,0,'C',0);
        $pdf->Cell(15,5,$lap2["nik"],1,0,'L',0);
        $pdf->Cell(50,5,$lap2["kname"],1,0,'L',0);
        $pdf->Cell(20,5,$lap2["month"],1,0,'C',0);
        $pdf->Cell(18,5,$absenR[$lap2["nik"]]['masuk'],1,0,'C',0);
        $pdf->Cell(18,5,$absenR[$lap2["nik"]]['istirahat1'],1,0,'C',0);
        $pdf->Cell(18,5,$absenR[$lap2["nik"]]['istirahat2'],1,0,'C',0);
        $pdf->Cell(18,5,$absenR[$lap2["nik"]]['pulang'],1,0,'C',0);
        if ($lap2["gol_kerja"] == 'Manajemen') {
            $resulta = min($absenR[$lap2["nik"]]['masuk'],$absenR[$lap2["nik"]]['pulang']);
        }else{
            $resulta = min($absenR[$lap2["nik"]]['masuk'],$absenR[$lap2["nik"]]['istirahat1'],$absenR[$lap2["nik"]]['istirahat2'],$absenR[$lap2["nik"]]['pulang']);
        }
        
        $pdf->SetFillColor(230, 172, 48);
        $pdf->Cell(20,5,($resulta),1,1,'C',1);
        $no2++;
    }
    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>