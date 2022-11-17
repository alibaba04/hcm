<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_instalasi
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnosurat"]=='' )
		{
			$this->strResults.="No Dinas Kosong!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function sreport(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Report dinas - ".$this->strResults;
			return $this->strResults;
		}
		$nosurat = secureParam($params["txtnosurat"],$dbLink);
        $tglPulang = secureParam($params["txtdatehome"],$dbLink);
        $tgl = date("Y-m-d H:i:s");
        $pembuat = $_SESSION["my"]->id;

        //insert ke tabel jurnal umum dulu
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$q = "UPDATE `aki_dinas` SET `tgl_pulang`='".tgl_mysql($tglPulang)."',`report`=1 WHERE nosurat='".$nosurat."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception($q.'Gagal Report.');
			
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Report";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Report - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function add(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Instalasi - ".$this->strResults;
			return $this->strResults;
		}
		$nosurat = secureParam($params["txtnosurat"],$dbLink);
		$nospk = secureParam($params["txtnospk"],$dbLink);
		$proyek = secureParam($params["txtnamaproyek"],$dbLink);
        $tglPengajuan = date("Y-m-d");
        $tglBerangkat = secureParam($params["txtberangkat"],$dbLink);
        $tglSelesai = secureParam($params["txtselesai"],$dbLink);
        $alamat = secureParam($params["txtaddress"],$dbLink);
        $spek = secureParam($params["txtspek"],$dbLink);
        $plafon = secureParam($params["txtplafon"],$dbLink);
        $jpemasangan = secureParam($params["txtjenisp"],$dbLink);
        $nohp = secureParam($params["txtnohp"],$dbLink);
        $sales = secureParam($params["txtsales"],$dbLink);
        date_default_timezone_set("Asia/Jakarta");
        $tgl = date("Y-m-d H:i:s");
        $pembuat = $_SESSION["my"]->id;

        //insert ke tabel 
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$q = "INSERT INTO `aki_instalasi`(`nosurat`, `nospk`, `proyek`, `alamat`, `spek`, `plafon`, `jpemasangan`, `nohp`, `tgl_berangkat`, `tgl_selesai`, `tgl_buat`, `sales`, `kodeUser`) VALUES ";
			$q.= "('".$nosurat."','".$nospk."','".$proyek."','".$alamat."','".$spek."', '".$plafon."', '".$jpemasangan."','".$nohp."', '".tgl_mysql($tglBerangkat)."','".tgl_mysql($tglSelesai)."', '".$tgl."', '".$sales."','".$pembuat."' )";
			
			if (!mysql_query($q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');
			//insert ke tabel transaksi sebanyak jumTim
			$jumData = $params["jumTim"];
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $nik = secureParam($params["txtnik_" . $j], $dbLink);
                    $jobs = secureParam($params["txtJobs_" . $j], $dbLink);
                    $unit = secureParam($params["txtUnit_" . $j], $dbLink);
                   
                    $q2 = "INSERT INTO `aki_dinstalasi`(`nosurat`, `nik`,`jobs`, `unit`)VALUES ";
					$q2.= "('".$nosurat."','".$nik."','".$jobs."','".$unit."')";
					
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('Gagal tambah data ddinas.'.$q2);
				}
			}
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data dinas - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function edit(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Edit dinas - ".$this->strResults;
			return $this->strResults;
		}
		$nosurat = secureParam($params["txtnosurat"],$dbLink);
		$nospk = secureParam($params["txtnospk"],$dbLink);
		$proyek = secureParam($params["txtnamaproyek"],$dbLink);
        $tglPengajuan = date("Y-m-d");
        $tglBerangkat = secureParam($params["txtberangkat"],$dbLink);
        $tglSelesai = secureParam($params["txtselesai"],$dbLink);
        $alamat = secureParam($params["txtaddress"],$dbLink);
        $spek = secureParam($params["txtspek"],$dbLink);
        $plafon = secureParam($params["txtplafon"],$dbLink);
        $jpemasangan = secureParam($params["txtjenisp"],$dbLink);
        $nohp = secureParam($params["txtnohp"],$dbLink);
        $sales = secureParam($params["txtsales"],$dbLink);
        date_default_timezone_set("Asia/Jakarta");
        $tgl = date("Y-m-d H:i:s");
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

            $q = "UPDATE `aki_instalasi` SET `nosurat`='".($nosurat)."',`nospk`='".($nospk)."',`proyek`='".($proyek)."',`alamat`='".($alamat)."',`spek`='".($spek)."',`jpemasangan`='".($jpemasangan)."',`nohp`='".($nohp)."',`tgl_berangkat`='".tgl_mysql($tglBerangkat)."',`tgl_selesai`='".tgl_mysql($tglSelesai)."',`alamat`='".$alamat."' ,`sales`='".$sales."' WHERE nosurat='".$nosurat."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception($q.'Gagal edit data Instalasi.');

			$jumData = $params["jumTim"];
			for ($j = 0; $j <= $jumData ; $j++){
				$q3 = "DELETE FROM `aki_dinstalasi` WHERE nosurat='".$nosurat."'";
				if (!mysql_query( $q3, $dbLink))
					throw new Exception($q3.'Gagal edit data dInstalasi.');
			}
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $nik = secureParam($params["txtnik_" . $j], $dbLink);
                    $jobs = secureParam($params["txtJobs_" . $j], $dbLink);
                    $unit = secureParam($params["txtUnit_" . $j], $dbLink);
                   
                    $q2 = "INSERT INTO `aki_dinstalasi`(`nosurat`, `nik`,`jobs`, `unit`)VALUES ";
					$q2.= "('".$nosurat."','".$nik."','".$jobs."','".$unit."')";
					
					if (!mysql_query( $q2, $dbLink))
						throw new Exception($q2.'Gagal tambah data dInstalasi.');
				}
			}

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:s");
			$pesan = $params["txtUpdate1"];
			$ket = "Pesan : ".$pesan." -has change, No Surat : ".$nosurat.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal Report. ');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Dinas ".$jumData;
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
	
	function delete($nosurat)
	{
		global $dbLink;

		$nosurat = secureParam($nosurat, $dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:s");
			$ket = "Pesan : No Surat ".$nosurat." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal tambah report. ');
			
			$q = "UPDATE `aki_instalasi` SET `aktif`=0 WHERE nosurat='".$nosurat."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data dinas.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults=$no."Sukses Hapus Data";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data Jurnal Umum - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}
}
?>
