<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_dinas
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnodinas"]=='' )
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
		$nodinas = secureParam($params["txtnodinas"],$dbLink);
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
			$q = "UPDATE `aki_dinas` SET `tgl_pulang`='".tgl_mysql($tglPulang)."',`report`=1 WHERE nodinas='".$nodinas."'";
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

	function validateDelete($kode) 
	{
		global $dbLink;
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="Nama Rekening tidak ditemukan!<br/>";
			$temp=FALSE;
		}

		//cari ID inisiasi di tabel penyusunan
		$rsTemp=mysql_query("SELECT id_transaksi, kode_rekening FROM aki_tabel_transaksi WHERE md5(id_transaksi) = '".$kode."'", $dbLink);
                $rows = mysql_num_rows($rsTemp);
                if($rows==0)
		{
			$temp=TRUE;
		} 
		else
        {
            $this->strResults.="Data Transaksi Jurnal masih terpakai dalam Salah satu tabel!<br />";
            $temp=FALSE;
        }
		
		return $temp;
	}
	
	function add(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah dinas - ".$this->strResults;
			return $this->strResults;
		}
		$nodinas = secureParam($params["txtnodinas"],$dbLink);
        $tglPengajuan = date("Y-m-d");
        $tglBerangkat = secureParam($params["txtdateout"],$dbLink);
        $tglSelesai = secureParam($params["txtdatein"],$dbLink);
        $alamat = secureParam($params["txtaddress"],$dbLink);
        $ket = secureParam($params["txtket"],$dbLink);
        $transport = secureParam($params["txtTransport"],$dbLink);
        $jenisTransport = secureParam($params["txtJkendaraan"],$dbLink);
        date_default_timezone_set("Asia/Jakarta");
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
			$q = "INSERT INTO `aki_dinas`(`nodinas`, `tgl_pengajuan`,`tgl_berangkat`, `tgl_selesai`, `alamat`, `ket`, `transport`, `jenis_transport`, `screate`, `user`, `aktif`) ";
			$q.= "VALUES ('".$nodinas."','".$tgl."','".tgl_mysql($tglBerangkat)."','".tgl_mysql($tglSelesai)."', '".$alamat."', '".$ket."','".$transport."', '".$jenisTransport."','".$tgl."', '".$pembuat."',1 )";
			
			if (!mysql_query($q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');
			//insert ke tabel transaksi sebanyak jumAddJurnal
			$jumData = $params["jumAddJurnal"];
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $nik = secureParam($params["txtnik_" . $j], $dbLink);
                    $jobs = secureParam($params["txtJobs_" . $j], $dbLink);
                    $ket = secureParam($params["txtKet_" . $j], $dbLink);
                   
                    $q2 = "INSERT INTO `aki_ddinas`(`nodinas`, `nik`,`jobs`, `ket`)VALUES ";
					$q2.= "('".$nodinas."','".$nik."','".$jobs."','".$ket."')";
					
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
		$nodinas = secureParam($params["txtnodinas"],$dbLink);
        $tglPengajuan = date("Y-m-d");
        $tglBerangkat = secureParam($params["txtdateout"],$dbLink);
        $tglSelesai = secureParam($params["txtdatein"],$dbLink);
        $alamat = secureParam($params["txtaddress"],$dbLink);
        $ket = secureParam($params["txtket"],$dbLink);
        $transport = secureParam($params["txtTransport"],$dbLink);
        $jenisTransport = secureParam($params["txtJkendaraan"],$dbLink);
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

            $q = "UPDATE `aki_dinas` SET `tgl_berangkat`='".tgl_mysql($tglBerangkat)."',`tgl_selesai`='".tgl_mysql($tglSelesai)."',`alamat`='".$alamat."' ,`ket`='".$ket."' ,`transport`='".$transport."' ,`jenis_transport`='".$jenisTransport."' WHERE nodinas='".$nodinas."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal edit data dinas.');

			$jumData = $params["jumAddJurnal"];
			for ($j = 0; $j <= $jumData ; $j++){
				$q3 = "DELETE FROM `aki_ddinas` WHERE nodinas='".$nodinas."'";
				if (!mysql_query( $q3, $dbLink))
					throw new Exception('Gagal edit data dinas.');
			}
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $nik = secureParam($params["txtnik_" . $j], $dbLink);
                    $jobs = secureParam($params["txtJobs_" . $j], $dbLink);
                    $ket = secureParam($params["txtKet_" . $j], $dbLink);
                   
                    $q2 = "INSERT INTO `aki_ddinas`(`nodinas`, `nik`,`jobs`, `ket`)VALUES ";
					$q2.= "('".$nodinas."','".$nik."','".$jobs."','".$ket."')";
					
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('Gagal tambah data ddinas.');
				}
			}

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:s");
			$pesan = $params["txtUpdate1"];
			$ket = "Pesan : ".$pesan." -has change, No Dinas : ".$nodinas.", datetime: ".$tgl;
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
	
	function delete($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Jurnal Umum - ".$this->strResults;
			return $this->strResults;
		}

		$no = secureParam($kode, $dbLink);
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
			$pesan = $params["txtUpdate"];
			$ket = "Pesan : ".$pesan." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal tambah report. ');
			
			$q = "UPDATE `aki_dinas` SET `aktif`=0 WHERE no='".$no."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data dinas.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults=$no."Sukses Hapus Data Jurnal Umum ";
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
