<?php
$dbLink = mysqli_connect("localhost","u8364183_marketing","PVMMA0Akp4;(","u8364183_hcm");
$nik = $_GET['nik'];
 $sqlQuery = "SELECT *,'Scan 1 ' as 'title',CONCAT(tanggal,' ',scan1) as 'start',if(scan6='00:00:00',if(scan5='00:00:00',if(scan4='00:00:00',if(scan3='00:00:00',CONCAT(tanggal,' ',scan2),CONCAT(tanggal,' ',scan3)),CONCAT(tanggal,' ',scan4)),CONCAT(tanggal,' ',scan5)),CONCAT(tanggal,' ',scan6)) as 'end',if(scan1>'09:35:00','#dd4b39','#3a87ad') as 'backgroundColor',CONCAT(tanggal,' ','17:00:00') as 'pulang',CONCAT(tanggal,' ','12:00:00') as 'break1',CONCAT(tanggal,' ','13:00:00') as 'break2',if(scan2 <'12:00:00',CONCAT(tanggal,' ',scan2),if(scan3 <'12:00:00',CONCAT(tanggal,' ',scan3),if(scan4 <'12:00:00',CONCAT(tanggal,' ',scan4),if(scan5 <'12:00:00',CONCAT(tanggal,' ',scan5),CONCAT(tanggal,' ',scan6))))) as istirahat,if(scan2 like '12%',CONCAT(tanggal,' ',scan2),if(scan3 like '12%',CONCAT(tanggal,' ',scan3),if(scan4 like '12%',CONCAT(tanggal,' ',scan4),if(scan5 like '12%',CONCAT(tanggal,' ',scan5),scan6)))) as istirahat1, if(scan6 like '12%',CONCAT(tanggal,' ',scan6),if(scan5 like '12%',CONCAT(tanggal,' ',scan5),if(scan4 like '12%',CONCAT(tanggal,' ',scan4),if(scan3 like '12%',CONCAT(tanggal,' ',scan3),scan2)))) as istirahat2 FROM `aki_absensi` WHERE (nik)='".$nik."'";


     $qjamkerja = "SELECT * FROM `aki_jamkerja` WHERE aktif='1'";
     $rjamkerja=mysqli_query($dbLink,$qjamkerja);
     $jmasuk='';$jistirahat1='';$jistirahat2='';$jpulang='';$jsabtu='';
     while ($labjamkerja = mysqli_fetch_array($rjamkerja)) {
        $jmasuk=$labjamkerja['masuk'];$jistirahat1=$labjamkerja['istirahat1'];$jistirahat2=$labjamkerja['istirahat2'];$jpulang=$labjamkerja['pulang'];$jsabtu=$labjamkerja['sabtu'];
    }

    $result = mysqli_query($dbLink, $sqlQuery);
    $eventArray = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan1']!='00:00:00') {
            $data =array();
            if ($row['scan1'] >$jmasuk) {
                $data['backgroundColor']='#dd4b39';
            }else{
                $data['backgroundColor']='#3a87ad';
            }
            $data['title']='Masuk';
            $data['start']=$row['start'];
            array_push($eventArray, $data);
        }
        /*if (strpos($row['istirahat'], '07:') === false && strpos($row['istirahat'], '00:') === false) {
            $data['title']='Istirahat';
            $data['backgroundColor']='#dd4b39';
            $data['start']=$row['istirahat'];
            array_push($eventArray, $data);
        }*/
        /*if($row['scan6']!='00:00:00'){
            $data['title']='Istirahat';
            $data['backgroundColor']='#3a87ad';
            $data['start']=$row['istirahat1'];
            array_push($eventArray, $data);
        }*/

        $data =array();
       
        if ($row['end'] <$row['pulang']) {
            $dt = strtotime($row['tanggal']);
            if (date("l", $dt)=='Saturday') {
                $data['backgroundColor']='#3a87ad';
            }else{
                $data['backgroundColor']='#dd4b39';
            }
        }else{
            $data['backgroundColor']='#3a87ad';
        }
        if($row['end']!=$row['tanggal'].' 00:00:00'){
            $data['title']= 'Pulang';
            $data['start']= $row['end'];
        }else{
            $data['title']= 'Tidak Absen Pulang';
            $data['start']= $row['tanggal'].' '.$jpulang;
        }
        
        array_push($eventArray, $data);
    }
    $sqlQuery = "SELECT * FROM `aki_izin` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        $data =array();
        $data['backgroundColor']='#18A87B';
        $data['title']=$row['keterangan'];
        $data['start']=$row['tanggal'].' '.$row['start'];
        $data['end']=$row['tanggal'].' '.$row['end'];
        array_push($eventArray, $data);
    }

    //tambah from aki_dinas u
    mysqli_free_result($result);$sqlQuery = "SELECT * FROM `aki_libur` WHERE 1";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        $data =array();
        $data['backgroundColor']='#fc030f';
        $data['title']=$row['keterangan'];
        $data['start']=$row['tanggal'].' 00:00:00';
        $data['end']=$row['tanggal'].' 00:00:00';
        array_push($eventArray, $data);
    }
    mysqli_free_result($result);
    $sqlQuery = "SELECT 'Scan 3 ' as 'title', CONCAT(tanggal,' ',scan3) as 'start',CONCAT(tanggal,' ',scan3) as 'end',scan3 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan3']!='00:00:00') {
            //array_push($eventArray, $row);
        }
    }
    mysqli_free_result($result);
    $sqlQuery = "SELECT 'Scan 4 ' as 'title', CONCAT(tanggal,' ',scan4) as 'start',CONCAT(tanggal,' ',scan4) as 'end',scan4 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan4']!='00:00:00') {
            //array_push($eventArray, $row);
        }
    }
    mysqli_free_result($result);$sqlQuery = "SELECT 'Scan 5 ' as 'title', CONCAT(tanggal,' ',scan5) as 'start',CONCAT(tanggal,' ',scan5) as 'end',scan5 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan5']!='00:00:00') {
            //array_push($eventArray, $row);
        }
    }
    mysqli_free_result($result);$sqlQuery = "SELECT 'Scan 6 ' as 'title', CONCAT(tanggal,' ',scan6) as 'start',CONCAT(tanggal,' ',scan6) as 'end',scan6 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan6']!='00:00:00') {
            //array_push($eventArray, $row);
        }
    }
echo json_encode($eventArray);
?>