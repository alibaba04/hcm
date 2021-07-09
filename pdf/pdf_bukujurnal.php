<?php
    //require_once('../function/fpdf/html_table.php');
    require_once('../function/fpdf/mc_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new PDF_MC_Table();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',14);

    $tglJurnal1 = $_GET['tglJurnal1'];
    $tglJurnal2 = $_GET['tglJurnal2'];
    $date = date_create($tglJurnal1);
    
    $filter = "";
    $html = "";
    if ($tglJurnal1 && $tglJurnal2)
        $filter = $filter . " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($tglJurnal1) . "' AND '" . tgl_mysql($tglJurnal2) . "' ";

    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 7, "LAPORAN TRANSAKSI JURNAL", 0, 1, 'C');
    //ISI
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'b', 12); 
    $pdf->Cell(0, 5, "*Laporan Keuangan, ".(strftime('%A', strtotime($tglJurnal1)))." ".$tglJurnal1."*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);  
    $qsum= "SELECT kode_rekening,awal_debet as debet,awal_kredit as kredit FROM `aki_tabel_master` WHERE 1 UNION all SELECT t.kode_rekening, sum(t.debet) as debet, sum(t.kredit) as kredit FROM aki_tabel_transaksi t WHERE 1=1 and t.aktif=1 AND t.tanggal_transaksi <='".date_format($date,"Y-m-d")."' GROUP by kode_rekening";
    $resultsum=mysqli_query($dbLink,$qsum);
    $saldo = 0;
    $debet = 0;
    $kredit = 0;
    $saldoUSD = 0;
    $saldoPESO = 0;
    while ($lap = mysqli_fetch_array($resultsum)) {
        if ($lap["kode_rekening"]=='1110.001' || $lap["kode_rekening"]<='1120.022' && $lap["kode_rekening"]>='1120.001') {
            $debet +=$lap["debet"];
            $kredit +=$lap["kredit"];
            $saldo = $debet-$kredit;
        }
    }
    $qusd= "SELECT t.kode_transaksi, t.kode_rekening, m.nama_rekening, t.keterangan_transaksi,m.awal_debet,m.awal_kredit, sum(t.debet) as debet, sum(t.kredit) as kredit FROM aki_tabel_transaksi t INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening AND t.kode_rekening= '1110.002' WHERE 1=1 and t.aktif=1 AND t.tanggal_transaksi <= '".date_format($date,"Y-m-d")."' ORDER BY t.kode_transaksi asc,t.tanggal_transaksi,t.no_transaksi,t.keterangan_transaksi,t.debet desc";
    $resultusd=mysqli_query($dbLink,$qusd);
    
    if ($lap = mysqli_fetch_array($resultusd)) {
            $saldoUSD = $lap["awal_debet"]+$lap["debet"]-$lap["kredit"];
    }
    $qpeso= "SELECT t.kode_transaksi, t.kode_rekening, m.nama_rekening, t.keterangan_transaksi,m.awal_debet,m.awal_kredit, sum(t.debet) as debet, sum(t.kredit) as kredit FROM aki_tabel_transaksi t INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening AND t.kode_rekening= '1110.003' WHERE 1=1 and t.aktif=1 AND t.tanggal_transaksi <= '".date_format($date,"Y-m-d")."' ORDER BY t.kode_transaksi asc,t.tanggal_transaksi,t.no_transaksi,t.keterangan_transaksi,t.debet desc";
    $resultpeso=mysqli_query($dbLink,$qpeso);
    
    if ($lap = mysqli_fetch_array($resultpeso)) {
            $saldoPESO = $lap["awal_debet"]+$lap["debet"]-$lap["kredit"];
    }

    $pdf->Cell(0, 5, chr(187).chr(187).' Rp. '.number_format($saldo,0), 0, 1, 'L'); 
    $pdf->Cell(0, 5, chr(187).chr(187).' USD    '.number_format($saldoUSD*0.000069,0), 0, 1, 'L'); 
    $pdf->Cell(0, 5, chr(187).chr(187).' Philippines Peso '.number_format($saldoPESO*0.0034,0), 0, 1, 'L');
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Pemasukan*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12); 
    $qin= "SELECT keterangan_transaksi,(debet) as nominal FROM `aki_tabel_transaksi` WHERE keterangan_transaksi like '%payin%' and debet>1000000  and tanggal_transaksi='".date_format($date,"Y-m-d")."'";
    $resultin=mysqli_query($dbLink,$qin);
        $noin=1;$noout=1;$nopay=1;
    while ($lap = mysqli_fetch_array($resultin)) {
        $ket='';
        if (strpos($lap["keterangan_transaksi"], 'payin') !== FALSE) {
            $tket = explode("ayin",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else if(strpos($lap["keterangan_transaksi"], 'payout') !== FALSE){
            $tket = explode("ayout",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else{
            $ket=$lap["keterangan_transaksi"];
        }
        $pdf->Cell(0, 5, $noin.'. '.$ket.' '.number_format($lap["nominal"],0), 0, 1, 'L'); 
        $noin++;
    }
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Pengeluaran*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12); 
    $qout= "SELECT keterangan_transaksi,(debet) as nominal FROM `aki_tabel_transaksi` WHERE keterangan_transaksi like '%payout%' and debet>1000000 and tanggal_transaksi='".date_format($date,"Y-m-d")."'";
    $resultout=mysqli_query($dbLink,$qout);
    while ($lap = mysqli_fetch_array($resultout)) {
        $ket='';
        if (strpos($lap["keterangan_transaksi"], 'payin') !== FALSE) {
            $tket = explode("ayin",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else if(strpos($lap["keterangan_transaksi"], 'payout') !== FALSE){
            $tket = explode("ayout",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else{
            $ket=$lap["keterangan_transaksi"];
        }
        $pdf->Cell(0, 5, $noout.'. '.$ket.' '.number_format($lap["nominal"],0), 0, 1, 'L');
        $noout++; 
    }
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Rekap Pembayaran, *".(strftime('%A', strtotime($tglJurnal1)))." ".$tglJurnal1."*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12); 
    $qpay= "SELECT keterangan_transaksi,(debet) as nominal FROM `aki_tabel_transaksi` WHERE (keterangan_transaksi like '%pembayaran%' or keterangan_transaksi like '%dp%') and debet>1000000 and tanggal_transaksi='".date_format($date,"Y-m-d")."'";
    $resultpay=mysqli_query($dbLink,$qpay);
    while ($lap = mysqli_fetch_array($resultpay)) {
        $ket='';
        if (strpos($lap["keterangan_transaksi"], 'payin') !== FALSE) {
            $tket = explode("ayin",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else if(strpos($lap["keterangan_transaksi"], 'payout') !== FALSE){
            $tket = explode("ayout",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else{
            $ket=$lap["keterangan_transaksi"];
        }
        $pdf->Cell(0, 5, $nopay.'. '.$ket.' '.number_format($lap["nominal"],0), 0, 1, 'L');
        $nopay++; 
    }
    $pdf->Ln(3);  
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Detail Transaksi*", 0, 1, 'L');
    $pdf->Ln(3);  
    $pdf->SetFont('Arial', '', 10); 
    $pdf->SetFillColor(69, 171, 82);
    $pdf->Cell(19,6,'Kode Akun',1,0,'C',0);
    $pdf->Cell(45,6,'Nama Akun',1,0,'C',0);
    $pdf->Cell(66,6,'Keterangan',1,0,'C',0);
    $pdf->Cell(30,6,'Debet (Rp)',1,0,'C',0);
    $pdf->Cell(30,6,'Kredit (Rp)',1,1,'C',0);
    $pdf->SetWidths(array(19,45,66,30,30));
    $pdf->SetAligns(array('C','L','L','R','R'));
    //database
    $q = "SELECT t.tanggal_transaksi, t.kode_transaksi, t.kode_rekening, m.nama_rekening, m.nama_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
    $q.= "FROM aki_tabel_transaksi t left join aki_tabel_master m on t.kode_rekening=m.kode_rekening ";
    $q.= "WHERE 1=1 ".$filter;
    $q.= " ORDER BY t.tanggal_transaksi, t.id_transaksi ";
    $result=mysqli_query($dbLink,$q);
    $totDebet = 0;
    $totKredit = 0;
    $pdf->SetFillColor(224,235,255);
    while ($lap = mysqli_fetch_array($result)) {
        $ket='';
        if (strpos($lap["keterangan_transaksi"], 'payin') !== FALSE) {
            $tket = explode("ayin",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else if(strpos($lap["keterangan_transaksi"], 'payout') !== FALSE){
            $tket = explode("ayout",$lap["keterangan_transaksi"]);
            $ket=$tket[1];
        }else{
            $ket=$lap["keterangan_transaksi"];
        }
        $pdf->Row(array($lap["kode_rekening"],$lap["nama_rekening"],$ket,number_format($lap["debet"],0),number_format($lap["kredit"],0)));
         $totDebet += $lap["debet"];
         $totKredit += $lap["kredit"];
    }

    //output file PDF
    $pdf->Output('BukuJurnal_'.$tglJurnal1.'.pdf', 'I'); //download file pdf
?>