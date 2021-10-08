<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_libur
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtdatepicker"]=='' )
		{
			$this->strResults.="Tanggal harus diisi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
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
			$this->strResults="Gagal Tambah Izin - ".$this->strResults;
			return $this->strResults;
		}
		$nik = secureParam($params["cbonik"],$dbLink);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$keterangan = secureParam($params["txtket"], $dbLink);
			$date = secureParam($params["txtdatepicker"], $dbLink);
			$tanggal =  date("y-m-d", strtotime($date));
			$id = rand(100,100000);
			$q = "INSERT INTO `aki_libur`(`id`, `tanggal`, `keterangan`, `user`)  VALUES ";
			$q.= "('".$id."',  '".$tanggal."', '".$keterangan."', '".$pembuat."');";
			if (!mysql_query( $q, $dbLink))
				throw new Exception($jumData.'Gagal tambah data izin.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Izin";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data - ".$e->getMessage().'<br/>';
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
			$this->strResults="Gagal Edit Izin - ".$this->strResults;
			return $this->strResults;
		}
		$nik = secureParam($params["cbonik"],$dbLink);
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$keterangan = secureParam($params["txtket"], $dbLink);
			$date = secureParam($params["txtdatepicker"], $dbLink);
			$tanggal =  date("Y-m-d", strtotime($date));
			$id = secureParam($params["txtid"], $dbLink);
			$q = "UPDATE `aki_libur` SET `tanggal`='".$tanggal."',`keterangan`='".$keterangan."' WHERE id=".$id;
			if (!mysql_query( $q, $dbLink))
				throw new Exception($jumData.'Gagal ubah data izin.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults=$tanggal."Sukses Ubah Data Izin";
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
			
			$q = "UPDATE `aki_izin` SET `aktif`=0 WHERE no='".$no."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data izin.');
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
