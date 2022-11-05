<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_jamkerja
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["jmasuk"]=='' )
		{
			$this->strResults.="Jam Masuk harus diisi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}
	
	function edit(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Edit Jam Kerja - ".$this->strResults;
			return $this->strResults;
		}
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			//report
			$rsTemp=mysql_query("SELECT * FROM `aki_jamkerja` WHERE 1", $dbLink);
			$temp = mysql_fetch_array($rsTemp);
			$tempmasuk  = $temp['masuk'];
			$tempist1  = $temp['istirahat1'];
			$tempist2  = $temp['istirahat2'];
			$temppulang  = $temp['pulang'];
			$tempsabtu  = $temp['sabtu'];

            $masuk = date("H:i:s", strtotime(secureParam($params["jmasuk"], $dbLink)));
            $istirahat1 = date("H:i:s", strtotime(secureParam($params["jistirahat1"], $dbLink)));
            $istirahat2 = date("H:i:s", strtotime(secureParam($params["jistirahat2"], $dbLink)));
            $pulang = date("H:i:s", strtotime(secureParam($params["jpulang"], $dbLink)));
            $sabtu = date("H:i:s", strtotime(secureParam($params["jsabtu"], $dbLink)));

            $q = "UPDATE `aki_jamkerja` SET `masuk`='".$masuk."',`istirahat1`='".$istirahat1."',`istirahat2`='".$istirahat2."' ,`pulang`='".$pulang."' ,`sabtu`='".$sabtu."' WHERE id='1'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal edit data jam kerja.');

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:s");
			$ket = "Pesan : ".$pesan." -has change, jam kerja m : ".$tempmasuk.", i1 : ".$tempist1.", i2 : ".$tempist2.", p : ".$temppulang.", s: ".$tempsabtu.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception($q4.'Gagal ubah transaksi jurnal umum. ');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Jurnal Umum ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
}
?>
