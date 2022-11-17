    <?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);

    $pdf=new FPDF('P','mm','F4');

    // SURAT surat //
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);
    $pdf->SetLineWidth(0.6);
    $pdf->SetMargins(8,6,0,0);
    $pdf->image('../dist/img/kop.jpg',0,0,210,327);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 14);
    $pdf->Ln(28);

    $q= "SELECT * FROM `aki_instalasi` Where md5(nosurat)='".$_GET["nosurat"]."'";
    $rs = mysql_query($q, $dbLink);
    $hasil = mysql_fetch_array($rs);
    $pdf->Cell(199,6,'SURAT PERINTAH KERJA PEMASANGAN',0,1,'C',0);
    $pdf->SetFont('Calibri', '', 11.5);
    $pdf->Cell(199,6,'Nomor : '.$hasil['nosurat'],0,1,'C',0);
    $pdf->SetMargins(13,6,0,0);
    $pdf->Ln(8);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(199,6,'Melaksanakan pemasangan proyek kubah masjid, maka yang bertanda tangan di bawah ini : ',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 12);
    $pdf->Cell(25,6,'Nama ',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->Cell(199,6,': Andik Nur Setiawan',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 12);
    $pdf->Cell(25,6,'Jabatan ',0,0,'L',0);
    $pdf->Cell(199,6,': Direktur Utama ',0,1,'L',0);
    $pdf->Ln(2);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(199,6,'Memberikan perintah kepada nama-nama berikut :',0,1,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(2,6,'',0,0,'L',0);
    $pdf->Cell(10,6,'No. ',0,0,'L',0);
    $pdf->Cell(80,6,'Nama',0,0,'L',0);
    $pdf->Cell(199,6,'Jabatan',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);

    $qt='SELECT dd.*,m.kname,g.jabatan FROM aki_dinstalasi dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nosurat)="'.$_GET["nosurat"].'" order by dd.nik';
    $result=mysqli_query($dbLink,$qt);
    $no=1;
    while ($dsurat = mysqli_fetch_array($result)) {
        $pdf->Cell(2,6,'',0,0,'L',0);
        $pdf->Cell(10,6,$no.".",0,0,'L',0);
        $pdf->Cell(80,6,$dsurat['kname'],0,0,'L',0);
        $pdf->Cell(199,6,$dsurat['jobs'],0,1,'L',0);
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
    $pdf->Ln(1);
    $pdf->MultiCell(180,6,'Untuk menyelesaikan pemasangan kubah masjid pada :',0,'J',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Nama Proyek',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['proyek'],0,'J',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Nomor Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['nospk'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Alamat Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['alamat'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Nomor HP PJ Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['nohp'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Ukuran Kubah',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['spek'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    
    $pdf->Ln(3);
    $pdf->Cell(199,6,'Surat Tugas Kerja Pemasangan terhitung sejak',0,1,'L',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Berangkat',0,0,'L',0);

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
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Selesai',0,0,'L',0);
    $pdf->Cell(150,6,": ".$dayList[date('D', strtotime($hasil['tgl_selesai']))].", ".date('d F Y', strtotime($hasil['tgl_selesai'])),0,1,'L',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Lama Hari',0,0,'L',0);
    
    $diff = abs(strtotime($hasil['tgl_selesai']) - strtotime($hasil['tgl_berangkat']));
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

    $pdf->Cell(150,6,": ".(floor($days)+1)." Hari",0,1,'L',0);
    $pdf->Ln(3);
    $pdf->MultiCell(180,6,'Demikian surat perintah kerja pemasangan ini dibuat untuk dipergunakan sebagaimana mestinya dan dilaksanakan dengan penuh tanggung jawab.',0,'J',0);
    $pdf->Ln(3);

    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'Kediri, '.$dayList[date('D', strtotime($hasil['tgl_by']))].", ".date('d F Y', strtotime($hasil['tgl_buat'])),0,1,'L',0);
    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'PT. Anugerah Kubah Indonesia',0,1,'L',0);
    $pdf->Ln(18);
    $pdf->SetFont('Calibri', 'BU', 11);
    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'ANDIK NUR SETIAWAN',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,5,'Direktur Utama',0,1,'L',0);
    $pdf->SetTextColor(180);
    $pdf->SetFont('Calibri', '', 9);
    date_default_timezone_set("Asia/Jakarta");
    $tgl = date("d/m/Y H:i:s a");
    $pdf->Cell(185,6,'Printed by SDM Dept. '.$tgl,0,1,'L',0);
    $pdf->SetTextColor(0);
    $pdf->SetLineWidth(0.2);
    $pdf->MultiCell(180,6,'NB : Apabila terjadi kendala pemasangan hingga melewati tanggal selesai, maka surat perintah kerja ini otomatis diperpanjang dengan mendapatkan persetujuan berupa tanda tangan dari Panitia Pembangunan Masjid yang berwenang.','LTR','J',0);
    $pdf->SetFont('Calibri', 'B', 9);
    $pdf->Cell(10,6,'','L',0,'C',0);
    $pdf->Cell(50,6,'Tanggal Selesai Pemasangan :',0,0,'L',0);
    $pdf->Cell(70,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'Mengetahui,','R',1,'L',0);
    $pdf->Cell(180,10,'','LR',1,'C',0);
    $pdf->SetFont('Calibri', '', 9);
    $pdf->Cell(10,6,'','LB',0,'C',0);
    $pdf->Cell(50,6,'______________________','B',0,'L',0);
    $pdf->Cell(70,6,'','B',0,'C',0);
    $pdf->Cell(50,6,'______________________','RB',1,'L',0);

    // LAPORAN //
    $pdf->AddPage();
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->SetMargins(5,0,0,0);
    $pdf->Ln(2);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //
    $pdf->kopsdm2($hasil['nospk']);

    $pdf->SetMargins(9,0,0,0);
    $pdf->Ln(0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(155,6,'PEMASANGAN PROYEK',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(36,6,'NO. URUT : '.substr($hasil['nosurat'],0,3),0,1,'R',0);
    $pdf->Cell(192,6,'Nomor Surat Perintah Kerja Pemasangan : '.$hasil['nosurat'],0,1,'L',0);
    $pdf->Ln(3);

    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Nama Proyek',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['proyek'],0,'J',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Nomor Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['nospk'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Alamat Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['alamat'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Nomor HP PJ Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['nohp'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Ukuran Kubah',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['spek'],0,'J',0);

    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Berangkat',0,0,'L',0);
    $pdf->Cell(150,6,": ".$dayList[date('D', strtotime($hasil['tgl_berangkat']))].", ".date('d F Y', strtotime($hasil['tgl_berangkat'])),0,1,'L',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Target Tanggal Selesai',0,0,'L',0);
    $pdf->Cell(150,6,": ".$dayList[date('D', strtotime($hasil['tgl_selesai']))].", ".date('d F Y', strtotime($hasil['tgl_selesai'])),0,1,'L',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Selesai',0,0,'L',0);
    $pdf->Cell(150,6,": ______________________",0,1,'L',0);

    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(38,6,'NAMA TIM PEMASANGAN',0,1,'L',0);
    $pdf->Cell(2,6,'',0,0,'L',0);
    $pdf->Cell(10,6,'No. ',0,0,'L',0);
    $pdf->Cell(80,6,'Nama',0,0,'L',0);
    $pdf->Cell(199,6,'Jabatan',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);

    $qt='SELECT dd.*,m.kname,g.jabatan FROM aki_dinstalasi dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nosurat)="'.$_GET["nosurat"].'" order by dd.nik';
    $result=mysqli_query($dbLink,$qt);
    $no=1;
    while ($dsurat = mysqli_fetch_array($result)) {
        $pdf->Cell(2,6,'',0,0,'L',0);
        $pdf->Cell(10,6,$no.".",0,0,'L',0);
        $pdf->Cell(80,6,$dsurat['kname'],0,0,'L',0);
        $pdf->Cell(199,6,$dsurat['jobs'],0,1,'L',0);
        $no++;
    }
    if ($no<13) {
        for ($no; $no <= 13; $no++) { 
            $pdf->Cell(2,6,'',0,0,'L',0);
            $pdf->Cell(10,6,$no.".",0,0,'L',0);
            $pdf->Cell(80,6,"-",0,0,'L',0);
            $pdf->Cell(199,6,"-",0,1,'L',0);
        }
    }

    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(34,5,'LAPORAN PEMASANGAN  ',0,0,'L',0);
    $pdf->Cell(110,5,'*Arsip Untuk SDM',0,1,'R',0);
    $pdf->SetFillColor(255,255,0);
    $pdf->Cell(6,6,'No',1,0,'C',1);
    $pdf->Cell(30,6,'Tanggal',1,0,'L',1);
    $pdf->Cell(28,6,'Jam Kerja',1,0,'L',1);
    $pdf->Cell(68,6,'Rincian Pekerjaan',1,0,'L',1);
    $pdf->Cell(30,6,'Jam Lembur',1,0,'L',1);
    $pdf->Cell(30,6,'TTD Panitia',1,1,'L',1);
    $pdf->SetFont('Calibri', '', 10);
    for ($i=0; $i < 3; $i++) { 
        $pdf->Cell(6,30,'',1,0,'C',0);
        $pdf->Cell(30,30,'',1,0,'L',0);
        $pdf->Cell(28,30,'',1,0,'L',0);
        for ($j=0; $j < 5; $j++) { 
            if ($j>0) {
                $pdf->Cell(64,6,' ',0,0,'L',0);
            }
            if ($j!=4) {
                $pdf->Cell(68,6,' ',1,1,'L',0);
            }else{
                $pdf->Cell(68,6,' ',1,0,'L',0);
            }
        }
        $pdf->Ln(-24);
        $pdf->Cell(132,6,' ',0,0,'L',0);
        $pdf->Cell(30,30,'',1,0,'L',0);
        $pdf->Cell(30,30,'',1,1,'L',0);
    }

    $pdf->SetMargins(153,6,0,0);
    $pdf->Ln(-190);
    $pdf->SetFillColor(128, 232, 155);
    $pdf->Cell(48,5,'Laporan Diperiksa Oleh',1,1,'C',1);
    $pdf->Cell(48,20,'','LR',1,'L',0);
    $pdf->Cell(48,5,'(______________________)','LR',1,'C',0);
    $pdf->Cell(48,5,'Staf Ekspedisi & Instalasi','LBR',1,'C',0);

    for ($k=0; $k < 3; $k++) { 
        $pdf->AddPage();
        $pdf->SetMargins(5,0,0,0);
        if ($k>0) {
            $pdf->Ln(6);
        }
        $pdf->SetLineWidth(0.6);
        $pdf->Rect(5, 8, 200, 314, 'D');
        $pdf->SetLineWidth(0);
        $pdf->Ln(2);
        $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
        $pdf->SetFont('Calibri', 'B', 11);

        // KOP SURAT //
        $pdf->kopsdm2($hasil['nospk']);

        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->SetMargins(8,0,0,0);
        $pdf->Ln(8);
        $pdf->SetFillColor(255,255,0);
        $pdf->Cell(6,6,'No',1,0,'C',1);
        $pdf->Cell(30,6,'Tanggal',1,0,'L',1);
        $pdf->Cell(28,6,'Jam Kerja',1,0,'L',1);
        $pdf->Cell(68,6,'Rincian Pekerjaan',1,0,'L',1);
        $pdf->Cell(30,6,'Jam Lembur',1,0,'L',1);
        $pdf->Cell(30,6,'TTD Panitia',1,1,'L',1);
        $pdf->SetFont('Calibri', '', 10);
        for ($i=0; $i < 8; $i++) { 
            $pdf->Cell(6,30,'',1,0,'C',0);
            $pdf->Cell(30,30,'',1,0,'L',0);
            $pdf->Cell(28,30,'',1,0,'L',0);
            for ($j=0; $j < 5; $j++) { 
                if ($j>0) {
                    $pdf->Cell(64,6,' ',0,0,'L',0);
                }
                if ($j!=4) {
                    $pdf->Cell(68,6,' ',1,1,'L',0);
                }else{
                    $pdf->Cell(68,6,' ',1,0,'L',0);
                }
            }
            $pdf->Ln(-24);
            $pdf->Cell(132,6,' ',0,0,'L',0);
            $pdf->Cell(30,30,'',1,0,'L',0);
            $pdf->Cell(30,30,'',1,1,'L',0);
        }
    }

    $pdf->AddPage();
    $pdf->Ln(6);
    $pdf->SetMargins(5,0,0,0);
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->Ln(2);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->SetFont('Calibri', 'B', 11);

// KOP SURAT //                                                 
    $pdf->kopsdm2($hasil['nospk']);

    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->Cell(5,6,'',0,0,'L',0);
    $pdf->Cell(155,6,'KENDALA TEKNIS PEMASANGAN DAN SOLUSI',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(36,6,'*Arsip untuk Operasional.',0,1,'R',0);
    $pdf->Ln(3);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(38,6,'Nama Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->MultiCell(140,5,$hasil['proyek'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(38,6,'Nomor Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['nospk'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Alamat Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['alamat'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Nomor HP PJ Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['nohp'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Ukuran Kubah',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['spek'],0,'J',0);

    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Berangkat',0,0,'L',0);
    $pdf->Cell(150,6,": ".$dayList[date('D', strtotime($hasil['tgl_berangkat']))].", ".date('d F Y', strtotime($hasil['tgl_berangkat'])),0,1,'L',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Selesai',0,0,'L',0);
    $pdf->Cell(150,6,": ______________________",0,1,'L',0);

    $pdf->SetMargins(8,0,0,0);
    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(2,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'NAMA TIM PEMASANGAN',0,1,'L',0);
    $pdf->Cell(2,6,'',0,0,'L',0);
    $pdf->Cell(10,6,'No. ',0,0,'L',0);
    $pdf->Cell(80,6,'Nama',0,0,'L',0);
    $pdf->Cell(199,6,'Jabatan',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);

    $qt='SELECT dd.*,m.kname,g.jabatan FROM aki_dinstalasi dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nosurat)="'.$_GET["nosurat"].'" order by dd.nik';
    $result=mysqli_query($dbLink,$qt);
    $no=1;
    while ($dsurat = mysqli_fetch_array($result)) {
        $pdf->Cell(2,6,'',0,0,'L',0);
        $pdf->Cell(10,6,$no.".",0,0,'L',0);
        $pdf->Cell(80,6,$dsurat['kname'],0,0,'L',0);
        $pdf->Cell(199,6,$dsurat['jobs'],0,1,'L',0);
        $no++;
    }
    if ($no<11) {
        for ($no; $no <= 11; $no++) { 
            $pdf->Cell(2,6,'',0,0,'L',0);
            $pdf->Cell(10,6,$no.".",0,0,'L',0);
            $pdf->Cell(80,6,"-",0,0,'L',0);
            $pdf->Cell(199,6,"-",0,1,'L',0);
        }
    }

    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetFillColor(255,255,0);
    $pdf->Cell(13,10,'No',1,0,'C',1);
    $pdf->Cell(80,10,'Kendala Teknis',1,0,'L',1);
    $pdf->Cell(80,10,'Solusi',1,0,'L',1);
    $pdf->SetMargins(181,5,0,0);
    $pdf->Cell(20,5,'Paraf','LTR',1,'C',1);
    $pdf->SetMargins(8,5,0,0);
    $pdf->Cell(20,5,'Ketua Tim','LBR',1,'C',1);
    $pdf->SetFont('Calibri', '', 10);
    for ($i=0; $i < 14; $i++) { 
        $pdf->Cell(13,8,'',1,0,'C',0);
        $pdf->Cell(80,8,'',1,0,'L',0);
        $pdf->Cell(80,8,'',1,0,'L',0);
        $pdf->Cell(20,8,'',1,1,'L',0);
    }

    $pdf->AddPage();
    $pdf->Ln(1);
    $pdf->SetMargins(5,0,0,0);
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->Ln(2);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //                                                 
    $pdf->kopsdm2($hasil['nospk']);

    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetMargins(8,5,0,0);
    $pdf->Ln(5);
    $pdf->SetFillColor(255,255,0);
    $pdf->Cell(13,10,'No',1,0,'C',1);
    $pdf->Cell(80,10,'Kendala Teknis',1,0,'L',1);
    $pdf->Cell(80,10,'Solusi',1,0,'L',1);
    $pdf->SetMargins(181,5,0,0);
    $pdf->Cell(20,5,'Paraf','LTR',1,'C',1);
    $pdf->SetMargins(8,5,0,0);
    $pdf->Cell(20,5,'Ketua Tim','LBR',1,'C',1);
    $pdf->SetFont('Calibri', '', 10);
    for ($i=0; $i < 31; $i++) { 
        $pdf->Cell(13,8,'',1,0,'C',0);
        $pdf->Cell(80,8,'',1,0,'L',0);
        $pdf->Cell(80,8,'',1,0,'L',0);
        $pdf->Cell(20,8,'',1,1,'L',0);
    }

    $pdf->AddPage();
    $pdf->Ln(1);
    $pdf->SetMargins(5,0,0,0);
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->Ln(2);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //                                                 
    $pdf->kopsdm2($hasil['nospk']);

    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->Cell(5,6,'',0,0,'L',0);
    $pdf->Cell(155,6,'LAPORAN KEUANGAN PEMASANGAN',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(36,6,'*Arsip untuk Dept. Keuangan.',0,1,'R',0);
    $pdf->Ln(3);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(38,6,'Nama Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->MultiCell(140,5,$hasil['proyek'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(38,6,'Alamat Proyek',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['alamat'],0,'J',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Ukuran Kubah',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['spek'],0,'J',0);

    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Berangkat',0,0,'L',0);
    $pdf->Cell(150,6,": ".$dayList[date('D', strtotime($hasil['tgl_berangkat']))].", ".date('d F Y', strtotime($hasil['tgl_berangkat'])),0,1,'L',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Selesai',0,0,'L',0);
    $pdf->Cell(150,6,": ______________________",0,1,'L',0);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Total Hari Pemasangan',0,0,'L',0);
    $pdf->Cell(150,6,": ______________________",0,1,'L',0);

    $pdf->SetMargins(10,0,0,0);
    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(38,6,'NAMA TIM PEMASANGAN',0,1,'L',0);
    $pdf->Cell(2,6,'',0,0,'L',0);
    $pdf->Cell(10,6,'No. ',0,0,'L',0);
    $pdf->Cell(80,6,'Nama',0,0,'L',0);
    $pdf->Cell(199,6,'Jabatan',0,1,'L',0);

    $qt='SELECT dd.*,m.kname,g.jabatan FROM aki_dinstalasi dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nosurat)="'.$_GET["nosurat"].'" order by dd.nik';
    $result=mysqli_query($dbLink,$qt);
    $no=1;
    $pdf->SetFont('Calibri', '', 10);
    while ($dsurat = mysqli_fetch_array($result)) {
        $pdf->Cell(2,6,'',0,0,'L',0);
        $pdf->Cell(10,6,$no.".",0,0,'L',0);
        $pdf->Cell(80,6,$dsurat['kname'],0,0,'L',0);
        $pdf->Cell(199,6,$dsurat['jobs'],0,1,'L',0);
        $no++;
    }
    if ($no<12) {
        for ($no; $no <= 12; $no++) { 
            $pdf->Cell(2,6,'',0,0,'L',0);
            $pdf->Cell(10,6,$no.".",0,0,'L',0);
            $pdf->Cell(80,6,"-",0,0,'L',0);
            $pdf->Cell(199,6,"-",0,1,'L',0);
        }
    }
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(31,8,'JUMLAH UANG PP ',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 8);
    $pdf->Cell(38,8,'(*diisi oleh Dept. Keuangan) ',0,1,'L',0);
    $pdf->Cell(38,10,'__________________________________________________________________',0,1,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(31,6,'JUMLAH UANG PP ',0,0,'L',0);

    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetMargins(8,5,0,0);
    $pdf->Ln(5);
    $pdf->SetFillColor(255,255,0);
    $pdf->Cell(8,10,'No',1,0,'C',1);
    $pdf->Cell(34,10,'Tanggal',1,0,'C',1);
    $pdf->Cell(34,10,'Pemasukan',1,0,'C',1);
    $pdf->Cell(34,10,'Pengeluaran',1,0,'C',1);
    $pdf->Cell(34,10,'Sisa Saldo',1,0,'C',1);
    $pdf->Cell(50,10,'Keterangan',1,1,'C',1);
    $pdf->SetFont('Calibri', '', 10);
    for ($i=0; $i < 9; $i++) { 
        $pdf->Cell(8,10,'',1,0,'C',0);
        $pdf->Cell(34,10,'',1,0,'C',0);
        $pdf->Cell(34,10,'',1,0,'C',0);
        $pdf->Cell(34,10,'',1,0,'C',0);
        $pdf->Cell(34,10,' ',1,0,'C',0);
        $pdf->Cell(50,10,'',1,1,'C',0);
    }

    $pdf->AddPage();
    $pdf->Ln(1);
    $pdf->SetMargins(5,0,0,0);
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->Ln(2);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //                                                 
    $pdf->kopsdm2($hasil['nospk']);

    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetMargins(8,5,0,0);
    $pdf->Ln(5);
    $pdf->SetFillColor(255,255,0);
    $pdf->Cell(8,10,'No',1,0,'C',1);
    $pdf->Cell(34,10,'Tanggal',1,0,'C',1);
    $pdf->Cell(34,10,'Pemasukan',1,0,'C',1);
    $pdf->Cell(34,10,'Pengeluaran',1,0,'C',1);
    $pdf->Cell(34,10,'Sisa Saldo',1,0,'C',1);
    $pdf->Cell(50,10,'Keterangan',1,1,'C',1);
    $pdf->SetFont('Calibri', '', 10);
    for ($i=0; $i < 20; $i++) { 
        $pdf->Cell(8,10,'',1,0,'C',0);
        $pdf->Cell(34,10,'',1,0,'C',0);
        $pdf->Cell(34,10,'',1,0,'C',0);
        $pdf->Cell(34,10,'',1,0,'C',0);
        $pdf->Cell(34,10,' ',1,0,'C',0);
        $pdf->Cell(50,10,'',1,1,'C',0);
    }

    $pdf->Ln(1);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(144,10,'UANG SAKU',1,0,'L',0);
    $pdf->Cell(50,10,'',1,1,'C',0);
    $pdf->SetFillColor(235, 150, 50);
    $pdf->Cell(144,10,'TOTAL PENGELUARAN',1,0,'L',1);
    $pdf->Cell(50,10,'',1,1,'C',0);
    $pdf->SetFillColor(235, 235, 50);
    $pdf->Cell(144,10,'SISA UANG SAKU',1,0,'L',1);
    $pdf->Cell(50,10,'',1,1,'C',0);
    $pdf->Ln(20);
    $pdf->Cell(200,10,'Nama Bendahara Tim : ________________________________________ Ttd : ___________________________',0,1,'C',0);

    $pdf->Output('lapinstalasi.pdf', 'I'); //download file pdf
?>