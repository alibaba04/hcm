<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);

    $pdf=new FPDF('P','mm','F4');

    // SURAT DINAS //
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);
    $pdf->SetLineWidth(0.6);
    $pdf->SetMargins(8,6,0,0);
    $pdf->image('../dist/img/kop.jpg',0,0,210,327);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 20);
    $pdf->Ln(30);

    $q= "SELECT * FROM `aki_dinas` Where md5(nodinas)='".$_GET["nodinas"]."'";
    $rs = mysql_query($q, $dbLink);
    $hasil = mysql_fetch_array($rs);
    $pdf->Cell(199,6,'SURAT TUGAS DINAS LUAR',0,1,'C',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(199,6,'Nomor : '.$hasil['nodinas'],0,1,'C',0);
    $pdf->SetMargins(13,6,0,0);
    $pdf->Ln(10);
    $pdf->Cell(199,6,'Yang bertanda tangan di bawah ini : ',0,1,'L',0);
    $pdf->Ln(2);
    $pdf->Cell(25,6,'Nama ',0,0,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(199,6,': Andik Nur Setiawan',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(25,6,'Jabatan ',0,0,'L',0);
    $pdf->Cell(199,6,': Direktur Utama ',0,1,'L',0);
    $pdf->Ln(5);
    $pdf->Cell(199,6,'Memberikan perintah kepada nama-nama berikut :',0,1,'L',0);
    $pdf->Ln(3);
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
    
    $pdf->Ln(5);
    $pdf->MultiCell(180,6,'Untuk melakukan tugas yang diberikan oleh PT. Anugerah Kubah Indonesia dengan rincian sebagai berikut',0,'J',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Ln(3);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Lokasi Tujuan',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['alamat'],0,'J',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Keperluan',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(140,5,$hasil['ket'],0,'J',0);
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
    $pdf->Ln(5);
    $pdf->Cell(199,6,'Surat Tugas Dinas Luar ini terhitung sejak',0,1,'L',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Ln(3);
    $pdf->Cell(7,6,'',0,0,'L',0);
    $pdf->Cell(38,6,'Tanggal Berangkat',0,0,'L',0);
    $pdf->SetFont('Calibri', '', 11);

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

    $pdf->Cell(150,6,": ".(floor($days)+1)." Hari",0,1,'L',0);
    $pdf->Ln(5);
    $pdf->MultiCell(180,6,'Demikian surat tugas dinas luar ini dibuat untuk dipergunakan sebagaimana mestinya dan dilaksanakan dengan penuh tanggung jawab.',0,'J',0);
    $pdf->Ln(10);

    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'Kediri, '.date('d F Y', strtotime($hasil['tgl_pengajuan'])),0,1,'L',0);
    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'PT. Anugerah Kubah Indonesia',0,1,'L',0);
    $pdf->Ln(25);
    $pdf->SetFont('Calibri', 'BU', 11);
    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,6,'ANDIK NUR SETIAWAN',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(125,6,'',0,0,'C',0);
    $pdf->Cell(50,5,'Direktur Utama',0,1,'L',0);
    $pdf->SetTextColor(180);
    $pdf->SetY(308);
    $pdf->SetFont('Calibri', '', 9);
    date_default_timezone_set("Asia/Jakarta");
    $tgl = date("d/m/Y H:i:s a");
    $pdf->Cell(185,6,'Printed by SDM Dept. '.$tgl,0,1,'C',0);
    $pdf->SetTextColor(0);
    // LAPORAN //
    $pdf->AddPage();
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->SetMargins(5,0,0,0);
    $pdf->Ln(2);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //
    $pdf->kopsdm();

    $pdf->SetMargins(9,0,0,0);
    $pdf->Ln(0);
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->Cell(36,9,'LAPORAN PERJALANAN DINAS',0,1,'L',0);
    $pdf->Ln(5);
    $pdf->Cell(192,6,'NO. SURAT : '.$hasil['nodinas'],0,1,'R',0);

    $dayList = array(
    'Sun' => 'Minggu',
    'Mon' => 'Senin',
    'Tue' => 'Selasa',
    'Wed' => 'Rabu',
    'Thu' => 'Kamis',
    'Fri' => 'Jumat',
    'Sat' => 'Sabtu'
    );
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Departemen',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $qt="SELECT dd.*,g.departemen FROM `aki_ddinas` dd left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(nodinas)='".$_GET["nodinas"]."' GROUP by departemen";
    $resultd=mysqli_query($dbLink,$qt);
    $adept=array();
    while ($dept = mysqli_fetch_array($resultd)) {
        array_push($adept,$dept['departemen']);
    }
    $pdf->Cell(34,5,implode(", ",$adept),0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Tanggal Berangkat',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(100,5,$dayList[date('D', strtotime($hasil['tgl_berangkat']))].", ".date('d F Y', strtotime($hasil['tgl_berangkat'])),0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Alamat Tujuan',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->MultiCell(140,5,$hasil['alamat'],0,'J',0);
    if (strlen($hasil['alamat'])<79) {
        $pdf->Cell(4,5,'',0,1,'R',0);
    }
    $pdf->Cell(34,5,'Keperluan',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->MultiCell(140,5,$hasil['ket'],0,'J',0);
    if (strlen($hasil['ket'])<79) {
        $pdf->Cell(4,5,'',0,1,'R',0);
    }
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Tanggal Selesai',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(100,5,$dayList[date('D', strtotime($hasil['tgl_selesai']))].", ".date('d F Y', strtotime($hasil['tgl_selesai'])),0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Lama Hari',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);

    $diff = abs(strtotime($hasil['tgl_selesai']) - strtotime($hasil['tgl_berangkat']));
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    $pdf->Cell(100,5,(floor($days)+1)." Hari",0,1,'L',0);
    $pdf->Cell(34,5,'Transportasi',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->Cell(100,5,$hasil['transport'],0,1,'L',0);
    $pdf->Cell(34,5,'Jenis Kendaraan',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->Cell(100,5,$hasil['jenis_transport'],0,1,'L',0);
    $pdf->Ln(5);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(100,5,'NAMA YANG BERTUGAS',0,1,'L',0);
    $qt='SELECT dd.*,m.kname,g.jabatan FROM aki_ddinas dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nodinas)="'.$_GET["nodinas"].'" order by dd.nik';
    $result=mysqli_query($dbLink,$qt);
    $no=1;
    $pdf->SetFont('Calibri', '', 11);
    $arrddinas=array();
    while ($ddinas = mysqli_fetch_array($result)) {
        $arrddinas[$no]['kname']=$ddinas['kname'];
        $arrddinas[$no]['jobs']=$ddinas['jobs'];
        $no++;
    }
    $i=1;
    for ($i; $i <= 6; $i++) { 
        $pdf->Cell(6,5,$i.".",0,0,'L',0);
        if (empty($arrddinas[$i]['kname'])) {
            $pdf->Cell(60,5,"-",0,0,'L',0);
            $pdf->Cell(30,5,"-",0,1,'L',0);
        }else{
            $pdf->Cell(60,5,$arrddinas[$i]['kname'],0,0,'L',0);
            $pdf->Cell(30,5,$arrddinas[$i]['jobs'],0,1,'L',0);
        }
    }

    $pdf->SetMargins(115,6,0,0);
    $pdf->Ln(-30);
    for ($i; $i <= 12; $i++) { 
        $pdf->Cell(6,5,$i.".",0,0,'L',0);
        if (empty($arrddinas[$i]['kname'])) {
            $pdf->Cell(60,5,"-",0,0,'L',0);
            $pdf->Cell(30,5,"-",0,1,'L',0);
        }else{
            $pdf->Cell(60,5,$arrddinas[$i]['kname'],0,0,'L',0);
            $pdf->Cell(30,5,$arrddinas[$i]['jobs'],0,1,'L',0);
        }
    }
    $pdf->SetMargins(8,6,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(34,5,'LAPORAN PERJALANAN    (Untuk SDM)',0,0,'L',0);
    $pdf->SetFont('Calibri', 'i', 9);
    $pdf->Cell(158,5,'**Kosongi jika menggunakan Kendaraan Umum',0,1,'R',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetFillColor(247,202,172);
    $pdf->Cell(6,6,'No',1,0,'C',1);
    $pdf->Cell(30,6,'Hari, Tgl',1,0,'L',1);
    $pdf->Cell(28,6,'Waktu',1,0,'L',1);
    $pdf->Cell(68,6,'Rincian Kegiatan',1,0,'L',1);
    $pdf->Cell(30,6,'Driver**',1,0,'L',1);
    $pdf->Cell(30,6,'Keterangan',1,1,'L',1);
    $pdf->SetFont('Calibri', '', 10);
    for ($i=0; $i < 16; $i++) { 
        $pdf->Cell(6,6,'',1,0,'C',0);
        $pdf->Cell(30,6,'',1,0,'L',0);
        $pdf->Cell(28,6,'',1,0,'L',0);
        $pdf->Cell(68,6,' ',1,0,'L',0);
        $pdf->Cell(30,6,'',1,0,'L',0);
        $pdf->Cell(30,6,'',1,1,'L',0);
    }
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Ln(5);
    $pdf->Cell(35,5,'Laporan diserahkan tanggal : ___________________________________',0,1,'L',0);
    $pdf->Cell(35,5,'Yang Bertugas',0,1,'L',0);
    $pdf->Ln(23);
    $pdf->Cell(3,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',0,'L',0);
    $pdf->Cell(15,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',0,'L',0);
    $pdf->Cell(15,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',0,'L',0);
    $pdf->Cell(15,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',1,'L',0);

    // page 2 //
    $pdf->AddPage();
    $pdf->SetMargins(5,0,0,0);
    $pdf->Ln(2);
    $pdf->SetLineWidth(0.6);
    $pdf->SetAutoPageBreak(false);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //
    $pdf->kopsdm();

    $pdf->SetMargins(9,6,0,0);
    $pdf->Ln(7);
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->Cell(34,5,'LAPORAN PERJALANAN    (Untuk SDM)',0,0,'L',0);
    $pdf->SetFont('Calibri', 'i', 9);
    $pdf->Cell(158,5,'**Kosongi jika menggunakan Kendaraan Umum',0,1,'R',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Ln(1);
    $pdf->SetFillColor(156,194,229);
    $pdf->Cell(8,6,'No',1,0,'C',1);
    $pdf->Cell(30,6,'Hari, Tgl',1,0,'L',1);
    $pdf->Cell(28,6,'Waktu',1,0,'L',1);
    $pdf->Cell(68,6,'Rincian Kegiatan',1,0,'L',1);
    $pdf->Cell(30,6,'Driver**',1,0,'L',1);
    $pdf->Cell(30,6,'Keterangan',1,1,'L',1);
    $pdf->SetFont('Calibri', '', 10);
    for ($i=0; $i < 35; $i++) { 
        $pdf->Cell(8,6,'',1,0,'C',0);
        $pdf->Cell(30,6,'',1,0,'L',0);
        $pdf->Cell(28,6,'',1,0,'L',0);
        $pdf->Cell(68,6,' ',1,0,'L',0);
        $pdf->Cell(30,6,'',1,0,'L',0);
        $pdf->Cell(30,6,'',1,1,'L',0);
    }
    $pdf->Ln(5);

    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Laporan diserahkan tanggal : ___________________________________',0,1,'L',0);
    $pdf->Cell(34,5,'Yang Bertugas',0,1,'L',0);
    $pdf->Ln(25);
    $pdf->Cell(4,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',0,'L',0);
    $pdf->Cell(15,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',0,'L',0);
    $pdf->Cell(15,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',0,'L',0);
    $pdf->Cell(15,5,'',0,0,'L',0);
    $pdf->Cell(35,5,'','B',1,'L',0);


    // page 3 //
    $pdf->AddPage();
    $pdf->SetMargins(5,0,0,0);
    $pdf->Ln(2);
    $pdf->SetLineWidth(0.6);
    $pdf->SetAutoPageBreak(false);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //
    $pdf->kopsdm();

    $pdf->SetMargins(9,0,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->Cell(36,6,'LAPORAN KEUANGAN PERJADIN',0,0,'L',0);
    $pdf->Cell(155,6,'*Arsip untuk Dept. Keuangan',0,1,'R',0);
    $pdf->Ln(3);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(192,5,'No. Surat Tugas : '.$hasil['nodinas'],0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Departemen',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $qt="SELECT dd.*,g.departemen FROM `aki_ddinas` dd left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(nodinas)='".$_GET["nodinas"]."' GROUP by departemen";
    $resultd=mysqli_query($dbLink,$qt);
    $adept=array();
    while ($dept = mysqli_fetch_array($resultd)) {
        array_push($adept,$dept['departemen']);
    }
    $pdf->Cell(100,5,implode(", ",$adept),0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Tanggal Berangkat',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(100,5,$dayList[date('D', strtotime($hasil['tgl_berangkat']))].", ".date('d F Y', strtotime($hasil['tgl_berangkat'])),0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Alamat Tujuan',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->MultiCell(140,5,$hasil['alamat'],0,'J',0);
    if (strlen($hasil['alamat'])<79) {
        $pdf->Cell(4,5,'',0,1,'R',0);
    }
    $pdf->Cell(34,5,'Keperluan',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->MultiCell(140,5,$hasil['ket'],0,'J',0);
    if (strlen($hasil['ket'])<79) {
        $pdf->Cell(4,5,'',0,1,'R',0);
    }
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Tanggal Selesai',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(100,5,$dayList[date('D', strtotime($hasil['tgl_selesai']))].", ".date('d F Y', strtotime($hasil['tgl_selesai'])),0,1,'L',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(34,5,'Lama Hari',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $diff = abs(strtotime($hasil['tgl_selesai']) - strtotime($hasil['tgl_berangkat']));
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    $pdf->Cell(100,5,(floor($days)+1)." Hari",0,1,'L',0);
    $pdf->Cell(34,5,'Transportasi',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->Cell(100,5,$hasil['transport'],0,1,'L',0);
    $pdf->Cell(34,5,'Jenis Kendaraan',0,0,'L',0);
    $pdf->Cell(4,5,':',0,0,'R',0);
    $pdf->Cell(100,5,$hasil['jenis_transport'],0,1,'L',0);

    $pdf->Ln(5);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->Cell(100,5,'NAMA YANG BERTUGAS',0,1,'L',0);
    $qt='SELECT dd.*,m.kname,g.jabatan FROM aki_ddinas dd left join `aki_tabel_master` m on dd.nik=m.nik left join aki_golongan_kerja g on dd.nik=g.nik WHERE md5(dd.nodinas)="'.$_GET["nodinas"].'" order by dd.nik';
    $result=mysqli_query($dbLink,$qt);
    $no=1;
    $pdf->SetFont('Calibri', '', 11);
    $arrddinas=array();
    while ($ddinas = mysqli_fetch_array($result)) {
        $arrddinas[$no]['kname']=$ddinas['kname'];
        $arrddinas[$no]['jobs']=$ddinas['jobs'];
        $no++;
    }
    $i=1;
    $pdf->Ln(3);
    for ($i; $i <= 6; $i++) { 
        $pdf->Cell(6,5,$i.".",0,0,'L',0);
        if (empty($arrddinas[$i]['kname'])) {
            $pdf->Cell(60,5,"-",0,0,'L',0);
            $pdf->Cell(30,5,"-",0,1,'L',0);
        }else{
            $pdf->Cell(60,5,$arrddinas[$i]['kname'],0,0,'L',0);
            $pdf->Cell(30,5,$arrddinas[$i]['jobs'],0,1,'L',0);
        }
    }

    $pdf->SetMargins(115,6,0,0);
    $pdf->Ln(-30);
    for ($i; $i <= 12; $i++) { 
        $pdf->Cell(6,5,$i.".",0,0,'L',0);
        if (empty($arrddinas[$i]['kname'])) {
            $pdf->Cell(60,5,"-",0,0,'L',0);
            $pdf->Cell(30,5,"-",0,1,'L',0);
        }else{
            $pdf->Cell(60,5,$arrddinas[$i]['kname'],0,0,'L',0);
            $pdf->Cell(30,5,$arrddinas[$i]['jobs'],0,1,'L',0);
        }
    }

    $pdf->SetMargins(11,6,0,0);
    $pdf->Ln(5);
    $pdf->SetFont('Calibri', 'b', 11);
    $pdf->SetFillColor(247,202,172);
    $pdf->Cell(8,6,'No',1,0,'C',1);
    $pdf->Cell(35,6,'Hari, Tgl',1,0,'C',1);
    $pdf->Cell(68,6,'Rincian Pengeluaran',1,0,'C',1);
    $pdf->Cell(30,6,'Total',1,1,'C',1);
    for ($i=0; $i < 17; $i++) { 
        $pdf->Cell(8,7,'',1,0,'C',0);
        $pdf->Cell(35,7,'',1,0,'C',0);
        $pdf->Cell(68,7,' ',1,0,'C',0);
        $pdf->Cell(30,7,'',1,1,'C',0);
    }
    $pdf->Cell(111,6,'TOTAL PENGELUARAN',1,0,'L',1);
    $pdf->Cell(30,6,'',1,1,'C',1);
    $pdf->Ln(5);
    $pdf->Cell(6,6,'',0,0,'C',0);
    $pdf->Cell(35,6,'',0,0,'C',0);
    $pdf->SetFillColor(255,217,102);
    $pdf->Cell(70,6,'UANG SAKU',1,0,'L',1);
    $pdf->Cell(30,6,'',1,1,'C',1);
    $pdf->Cell(6,6,'',0,0,'C',0);
    $pdf->Cell(35,6,'',0,0,'C',0);
    $pdf->SetFillColor(146,208,80);
    $pdf->Cell(70,6,'SISA UANG SAKU',1,0,'L',1);
    $pdf->Cell(30,6,'',1,1,'C',1);


    $pdf->SetMargins(153,6,0,0);
    $pdf->Ln(-106);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(48,5,'','LTR',1,'L',0);
    $pdf->Cell(48,5,'  Tanggal:_______________','LR',1,'L',0);
    $pdf->Cell(48,5,'  Dilaporkan oleh:','LR',1,'L',0);
    $pdf->Cell(48,18,'','LR',1,'L',0);
    $pdf->Cell(48,5,'  ______________________','LR',1,'L',0);
    $pdf->Cell(48,5,'','LBR',1,'L',0);
    $pdf->Ln(3);
    $pdf->Cell(48,5,'','LTR',1,'L',0);
    $pdf->Cell(48,5,'  Tanggal:_______________','LR',1,'L',0);
    $pdf->Cell(48,5,'  Laporan diterima oleh:','LR',1,'L',0);
    $pdf->Cell(48,18,'','LR',1,'L',0);
    $pdf->Cell(48,5,'  ______________________','LR',1,'L',0);
    $pdf->Cell(48,5,'','LBR',1,'L',0);


    // page 4 //
    $pdf->AddPage();
    $pdf->SetMargins(5,0,0,0);
    $pdf->Ln(2);
    $pdf->SetLineWidth(0.6);
    $pdf->SetAutoPageBreak(false);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //
    $pdf->kopsdm();

    $pdf->SetMargins(9,6,0,0);
    $pdf->Ln(7);
    $pdf->SetFont('Calibri', 'b', 11);
    $pdf->SetFillColor(214, 210, 176);
    $pdf->Cell(8,7,'No',1,0,'C',1);
    $pdf->Cell(35,7,'Hari, Tgl',1,0,'C',1);
    $pdf->Cell(68,7,'Rincian Pengeluaran',1,0,'C',1);
    $pdf->Cell(30,7,'Total',1,1,'C',1);
    for ($i=0; $i < 35; $i++) { 
        $pdf->Cell(8,6,'',1,0,'C',0);
        $pdf->Cell(35,6,'',1,0,'C',0);
        $pdf->Cell(68,6,' ',1,0,'C',0);
        $pdf->Cell(30,6,'',1,1,'C',0);
    }
    $pdf->Cell(111,6,'TOTAL PENGELUARAN',1,0,'L',1);
    $pdf->Cell(30,6,'',1,1,'C',1);
    $pdf->Ln(5);
    $pdf->Cell(6,6,'',0,0,'C',0);
    $pdf->Cell(35,6,'',0,0,'C',0);
    $pdf->SetFillColor(255,217,102);
    $pdf->Cell(70,6,'UANG SAKU',1,0,'L',1);
    $pdf->Cell(30,6,'',1,1,'C',1);
    $pdf->Cell(6,6,'',0,0,'C',0);
    $pdf->Cell(35,6,'',0,0,'C',0);
    $pdf->SetFillColor(146,208,80);
    $pdf->Cell(70,6,'SISA UANG SAKU',1,0,'L',1);
    $pdf->Cell(30,6,'',1,1,'C',1);


    $pdf->SetMargins(153,6,0,0);
    $pdf->Ln(-106);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Cell(48,5,'','LTR',1,'L',0);
    $pdf->Cell(48,5,'  Tanggal:_______________','LR',1,'L',0);
    $pdf->Cell(48,5,'  Dilaporkan oleh:','LR',1,'L',0);
    $pdf->Cell(48,18,'','LR',1,'L',0);
    $pdf->Cell(48,5,'  ______________________','LR',1,'L',0);
    $pdf->Cell(48,5,'','LBR',1,'L',0);
    $pdf->Ln(3);
    $pdf->Cell(48,5,'','LTR',1,'L',0);
    $pdf->Cell(48,5,'  Tanggal:_______________','LR',1,'L',0);
    $pdf->Cell(48,5,'  Laporan diterima oleh:','LR',1,'L',0);
    $pdf->Cell(48,18,'','LR',1,'L',0);
    $pdf->Cell(48,5,'  ______________________','LR',1,'L',0);
    $pdf->Cell(48,5,'','LBR',1,'L',0);

    // page 5 //
    $pdf->AddPage();
    $pdf->SetMargins(5,0,0,0);
    $pdf->Ln(2);
    $pdf->SetLineWidth(0.6);
    $pdf->SetAutoPageBreak(false);
    $pdf->Rect(5, 8, 200, 314, 'D');
    $pdf->SetLineWidth(0);
    $pdf->image('../dist/img/qoobah2.png',6,6,30,30);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 11);

    // KOP SURAT //
    $pdf->kopsdm();
    

    $pdf->Output('lapdinas.pdf', 'I'); //download file pdf
?>