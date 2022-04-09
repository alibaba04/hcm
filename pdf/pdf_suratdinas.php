<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);

    $pdf=new FPDF();
    $pdf->AddPage();
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(6, 8, 198, 282, 'D');
    $pdf->SetMargins(6,6,0,0);
    $pdf->image('../dist/img/cop-aki.jpg',7,9,196,30);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 18);
    $pdf->Ln(30);
    $pdf->Cell(198,6,'','T',1,'C',0);

    $q= "SELECT * FROM `aki_dinas` Where md5(nodinas)='".$_GET["nodinas"]."'";
    $rs = mysql_query($q, $dbLink);
    $hasil = mysql_fetch_array($rs);
    $pdf->Cell(199,6,'SURAT TUGAS DINAS LUAR',0,1,'C',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(199,6,'Nomor : '.$hasil['nodinas'],0,1,'C',0);
    $pdf->SetMargins(13,6,0,0);
    $pdf->Ln(12);
    $pdf->Cell(199,6,'Yang bertanda tangan di bawah ini : ',0,1,'L',0);
    $pdf->Ln(2);
    $pdf->Cell(25,6,'Nama ',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(199,6,': Andik Nur Setiawan',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(25,6,'Jabatan ',0,0,'L',0);
    $pdf->Cell(199,6,': Direktur Utama ',0,1,'L',0);
    $pdf->Ln(2);
    $pdf->Cell(199,6,'Memberikan perintah kepada nama-nama berikut :',0,1,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(2,6,'',0,0,'L',0);
    $pdf->Cell(10,6,'No. ',0,0,'L',0);
    $pdf->Cell(80,6,'Nama',0,0,'L',0);
    $pdf->Cell(199,6,'Jabatan',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);

    $qt='SELECT dd.*,m.kname,g.jabatan FROM aki_ddinas dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nodinas)="'.$_GET["nodinas"].'" order by dd.nik';
    $result=mysqli_query($dbLink,$qt);
    $no=1;
    while ($ddinas = mysqli_fetch_array($result)) {
        $pdf->Cell(2,6,'',0,0,'L',0);
        $pdf->Cell(10,6,$no.".",0,0,'L',0);
        $pdf->Cell(80,6,$ddinas['kname'],0,0,'L',0);
        $pdf->Cell(199,6,$ddinas['jobs'],0,1,'L',0);
        $no++;
    }
    if ($no<8) {
        for ($no; $no <= 8; $no++) { 
            $pdf->Cell(2,6,'',0,0,'L',0);
            $pdf->Cell(10,6,$no.".",0,0,'L',0);
            $pdf->Cell(80,6,"-",0,0,'L',0);
            $pdf->Cell(199,6,"-",0,1,'L',0);
        }
    }
    
    $pdf->Ln(3);
    $pdf->MultiCell(180,6,'Untuk melakukan tugas yang diberikan oleh PT. Anugerah Kubah Indonesia dengan rincian sebagai berikut',0,'J',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Lokasi Tujuan',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->MultiCell(150,6,": ".$hasil['alamat'],0,'J',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Keperluan',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->MultiCell(150,6,": ".$hasil['ket'],0,'J',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Transportasi',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->MultiCell(150,6,": ".$hasil['transport'],0,'J',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Jenis Kendaraan',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->MultiCell(150,6,": ".$hasil['jenis_transport'],0,'J',0);
    $pdf->Ln(3);
    $pdf->Cell(199,6,'Surat Tugas Dinas Luar ini terhitung sejak',0,1,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Berangkat',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);

    $dayList = array(
    'Sun' => 'Minggu',
    'Mon' => 'Senin',
    'Tue' => 'Selasa',
    'Wed' => 'Rabu',
    'Thu' => 'Kamis',
    'Fri' => 'Jumat',
    'Sat' => 'Sabtu'
    );

    $pdf->Cell(150,6,": ".$dayList[date('D', strtotime($hasil['tgl_berangkat']))].", ".date('d F Y', strtotime($hasil['tgl_berangkat'])),0,1,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Selesai',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(150,6,": ".$dayList[date('D', strtotime($hasil['tgl_selesai']))].", ".date('d F Y', strtotime($hasil['tgl_selesai'])),0,1,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Lama Hari',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    
    $diff = abs(strtotime($hasil['tgl_selesai']) - strtotime($hasil['tgl_berangkat']));
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

    $pdf->Cell(150,6,": ".$days." Hari",0,1,'L',0);
    $pdf->Ln(2);
    $pdf->MultiCell(180,6,'Demikian surat tugas dinas luar ini dibuat untuk dipergunakan sebagaimana mestinya dan dilaksanakan dengan penuh tanggung jawab.',0,'J',0);
    $pdf->Cell(118,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'Kediri, '.date('d F Y', strtotime($hasil['tgl_pengajuan'])),0,1,'L',0);
    $pdf->Cell(118,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'PT. Anugerah Kubah Indonesia',0,1,'L',0);
    $pdf->Ln(25);
    $pdf->SetFont('Calibri', 'BU', 11);
    $pdf->Cell(118,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'ANDIK NUR SETIAWAN',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(118,6,'',0,0,'C',0);
    $pdf->Cell(50,5,'Direktur Utama',0,1,'L',0);

    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>