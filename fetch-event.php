<?php
require_once( 'config.php' );
global $dbLink;
$nik = $_GET['nik'];
 $sqlQuery = "SELECT 'Scan 1 ' as 'title', CONCAT(tanggal,' ',scan1) as 'start',CONCAT(tanggal,' ',scan1) as 'end',if(scan1>'07:35:00','#dd4b39','#3a87ad') as 'backgroundColor',scan1 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    $eventArray = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan1']!='00:00:00') {
            array_push($eventArray, $row);
        }
    }
    $sqlQuery = "SELECT 'Scan 2 ' as 'title', CONCAT(tanggal,' ',scan2) as 'start',CONCAT(tanggal,' ',scan2) as 'end',scan2 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan2']!='00:00:00') {
            array_push($eventArray, $row);
        }
    }
    mysqli_free_result($result);
    $sqlQuery = "SELECT 'Scan 3 ' as 'title', CONCAT(tanggal,' ',scan3) as 'start',CONCAT(tanggal,' ',scan3) as 'end',scan3 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan3']!='00:00:00') {
            array_push($eventArray, $row);
        }
    }
    mysqli_free_result($result);
    $sqlQuery = "SELECT 'Scan 4 ' as 'title', CONCAT(tanggal,' ',scan4) as 'start',CONCAT(tanggal,' ',scan4) as 'end',scan4 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan4']!='00:00:00') {
            array_push($eventArray, $row);
        }
    }
    mysqli_free_result($result);$sqlQuery = "SELECT 'Scan 5 ' as 'title', CONCAT(tanggal,' ',scan5) as 'start',CONCAT(tanggal,' ',scan5) as 'end',scan5 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan5']!='00:00:00') {
            array_push($eventArray, $row);
        }
    }
    mysqli_free_result($result);$sqlQuery = "SELECT 'Scan 6 ' as 'title', CONCAT(tanggal,' ',scan6) as 'start',CONCAT(tanggal,' ',scan6) as 'end',scan6 FROM `aki_absensi` WHERE (nik)='".$nik."'";

    $result = mysqli_query($dbLink, $sqlQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['scan6']!='00:00:00') {
            array_push($eventArray, $row);
        }
    }
echo json_encode($eventArray);
?>