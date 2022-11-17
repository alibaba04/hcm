<?php
global $passSalt;
require_once('../config.php' );
require_once('../function/secureParam.php');
//require_once('../function/mysql.php');

switch ($_POST['fungsi']) {
    case "cekpass":
        $kodeUser = secureParamAjax($_POST['kodeUser'], $dbLink);
        $pass = HASH('SHA512',$passSalt.secureParamAjax($_POST['pass'], $dbLink));
        $result = mysql_query("SELECT kodeUser, nama FROM aki_user WHERE kodeUser='".$kodeUser."' AND  password='".$pass."' AND aktif='Y'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo $pass;
        }
    break;
    case "updatejamkerja":
        $masuk = date("H:i:s", strtotime(secureParamAjax($_POST["jmasuk"], $dbLink)));
        $istirahat1 = date("H:i:s", strtotime(secureParamAjax($_POST["jistirahat1"], $dbLink)));
        $istirahat2 = date("H:i:s", strtotime(secureParamAjax($_POST["jistirahat2"], $dbLink)));
        $pulang = date("H:i:s", strtotime(secureParamAjax($_POST["jpulang"], $dbLink)));
        $sabtu = date("H:i:s", strtotime(secureParamAjax($_POST["jsabtu"], $dbLink)));
        $q = "UPDATE `aki_jamkerja` SET `masuk`='".$masuk."',`istirahat1`='".$istirahat1."',`istirahat2`='".$istirahat2."',`pulang`='".$pulang."',`sabtu`='".$sabtu."' WHERE aktif='1'";
        if (mysql_query($q, $dbLink)) {
            echo "yes";
        } else {
            echo $q;
        }
    break;
    case "checkKodeMenu":

        //echo "yes";

        $result = mysql_query("select kodeMenu FROM aki_menu WHERE kodeMenu ='" . secureParamAjax($_POST['kodeMenu'], $dbLink) . "'", $dbLink);

        if (mysql_num_rows($result)) {
             echo "yes";
        } else {
             echo "no";
        }
        break;

    case "checkKodeGroup":
        $result = mysql_query("select KodeGroup FROM aki_groups WHERE KodeGroup ='" . secureParamAjax($_POST['kodeGroup'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
        break;

    case "checkKodeUser":
        $result = mysql_query("select kodeUser FROM aki_user WHERE kodeUser ='" . secureParamAjax($_POST['kodeUser'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
        break;
    case "getjobs":
        $result = mysql_query("SELECT m.nik,kname,g.* FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik where m.nik ='" . secureParamAjax($_POST['nik'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)>0) {
            while ( $data = mysql_fetch_assoc($result)) {
                if ($data['gol_kerja']=='Manajemen') {
                    echo json_encode(array("nik"=>$data['nik'], "jobs"=>$data['jabatan']));
                }else{
                    echo json_encode(array("nik"=>$data['nik'], "jobs"=>$data['unit']));
                }
                
            } 
            break;
        }
        break;
    case "ambilnik":
        $result = mysql_query("SELECT m.nik,kname,g.* FROM `aki_tabel_master` m left join aki_golongan_kerja g on m.nik=g.nik where tanggal_nonaktif='0000-00-00' order by m.nik", $dbLink);
            if (mysql_num_rows($result)>0) {
                $idx = 0;
                while ( $data = mysql_fetch_assoc($result)) {
                    $output[$idx] = array("val"=>$data['nik'],"text"=>$data['nik'].' - '.$data['kname'],"gol"=>$data['jabatan']);
                    $idx++;
                 } 
                echo json_encode($output);
                break;
            }
    break;
    case "cekpass":
        $kodeUser = secureParamAjax($_POST['kodeUser'], $dbLink);
        $pass = HASH('SHA512',$passSalt.secureParamAjax($_POST['pass'], $dbLink));
        $result = mysql_query("SELECT kodeUser, nama FROM aki_user WHERE kodeUser='".$kodeUser."' AND  password='".$pass."' AND aktif='Y'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
    break;

    case "checkNamaSetting":

        //echo "yes";

        $result = mysql_query("select namaSetting FROM aki_setting WHERE namaSetting ='" . secureParamAjax($_POST['namaSetting'], $dbLink) . "'", $dbLink);

        if (mysql_num_rows($result)) {
             echo "yes";
        } else {
             echo "no";
        }
        break;
    case "getabsesnsi":
        $result = mysql_query("SELECT * FROM `aki_absensi` WHERE md5(nik)='" . secureParamAjax($_POST['nik'], $dbLink). "'", $dbLink);
        if (mysql_num_rows($result)) {
           echo "yes";
        } else {
           echo "no";
        }
   break;
   
   
}
?>
