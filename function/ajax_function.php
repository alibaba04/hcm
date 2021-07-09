<?php
global $passSalt;
require_once('../config.php' );
require_once('../function/secureParam.php');
//require_once('../function/mysql.php');

switch ($_POST['fungsi']) {
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
        $result = mysql_query("SELECT * FROM `aki_absensi` WHERE md5(nik)='" . secureParamAjax($_POST['nik']. "'", $dbLink);
        if (mysql_num_rows($result)) {
           echo "yes";
        } else {
           echo "no";
        }
   break;
}
?>
