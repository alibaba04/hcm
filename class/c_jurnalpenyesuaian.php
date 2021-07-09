<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_jurnalpenyesuaian
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtTglTransaksi"]=='' )
		{
			$this->strResults.="Tanggal Transaksi harus diisi!<br/>";
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
			$this->strResults="Gagal Tambah Data Jurnal Umum - ".$this->strResults;
			return $this->strResults;
		}
		// $kodeTransaksi = secureParam($params["txtKodeTransaksi"],$dbLink);
        $tglTransaksi = secureParam($params["txtTglTransaksi"],$dbLink);
        $tglTransaksi = tgl_mysql($tglTransaksi);
        
        $pembuat = $_SESSION["my"]->id;

        //insert ke tabel jurnal umum dulu
        //field tanggal selesai akan diupdate dengan tanggal posting
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$q_kode = mysql_query("SELECT MAX(kode_transaksi) AS kode_transaksi FROM aki_jurnal_umum WHERE tgl_transaksi='".$tglTransaksi."' ", $dbLink);
			$kode = mysql_fetch_array($q_kode);

			$urut = substr($kode['kode_transaksi'], 2);
			$tglKodeTerakhir = substr($kode['kode_transaksi'], 2, 8);
			$tglTr = str_replace("-", "", $tglTransaksi);
			$koderef = '';
			$urutjp = substr($kode['kode_transaksi'], 10, 3);
			if ($tglTr == $tglKodeTerakhir){
				$kode = (int)$urut + 1;
				$kodeTransaksi = "BU".$kode;
				$koderef = "JP".$kode;
			}else{
				$kodeTransaksi = "BU".$tglTr."001";
				$koderef = "JP".$tglTr."001";
			}
			
			$q = "INSERT INTO aki_jurnal_umum(nomor_jurnal, kode_transaksi, tanggal_selesai, tgl_transaksi) ";
			$q.= "VALUES ('NULL', '".$kodeTransaksi."', '0000-00-00', '".$tglTransaksi."');";
			
			if (!mysql_query($q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');

			//insert ke tabel transaksi sebanyak jumAddJurnal
			$jumData = $params["jumAddJurnal"];
			for ($k = 0; $k < $jumData ; $k++){
                    	$pdebet = secureParam($params["txtDebet_" . $k], $dbLink);
                    	$pdebet = str_replace(".", "", $pdebet);
                    	$pkredit = secureParam($params["txtKredit_" . $k], $dbLink);
                    	$pkredit = str_replace(".", "", $pkredit);
                    	$vdebet +=$pdebet;
                    	$vkredit +=$pkredit;
               }
			for ($j = 0; $j < $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
					if ($params["txtKodeRekening_" . $j] == "")
                        throw new Exception("Kode Rekening Harus Diisi !");
                    if ($params["txtKeterangan_" . $j] == "")
                        throw new Exception("Keterangan Harus Diisi !");
                   /* if ($vdebet != $vkredit)
                        throw new Exception("Nominal Debet Dan Kredit Harus Sama !");*/
                    
                    $kodeRekenening = secureParam($params["txtKodeRekening_" . $j], $dbLink);
                    $keterangan = secureParam($params["txtKeterangan_" . $j], $dbLink);
                    $debet = secureParam($params["txtDebet_" . $j], $dbLink);
                    $debet = str_replace(".", "", $debet);
                    $kredit = secureParam($params["txtKredit_" . $j], $dbLink);
                    $kredit = str_replace(".", "", $kredit);

                    $q2 = "INSERT INTO aki_tabel_transaksi(id_transaksi, kode_transaksi, kode_rekening, tanggal_transaksi, jenis_transaksi, keterangan_transaksi, debet, kredit, tanggal_posting, keterangan_posting, last_updater, ref) ";
					$q2.= "VALUES ('NULL',  '".$kodeTransaksi."',  '".$kodeRekenening."', '".$tglTransaksi."', 'Bukti Umum', '".$keterangan."', '".$debet."', '".$kredit."', '0000-00-00', '', '".$pembuat."', '".$koderef."');";

					if (!mysql_query( $q2, $dbLink))
						throw new Exception('Gagal tambah data transaksi jurnal umum.');

				}
			}
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Jurnal Umum ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Jurnal Umum - ".$e->getMessage().'<br/>';
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
			$this->strResults="Gagal Ubah Data Jurnal Umum - ".$this->strResults;
			return $this->strResults;
		}
		
		$kodeTransaksi = secureParam($params["txtKodeTransaksi"],$dbLink);
        $tglTransaksi = secureParam($params["txtTglTransaksi"],$dbLink);
        $tglTransaksi = tgl_mysql($tglTransaksi);
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			//update ke tabel transaksi sebanyak jumEditJurnal
			$jumData = $params["jumEditJurnal"];
			for ($j = 0; $j < $jumData ; $j++){
				if (!empty($params['chkEdit_'.$j])){
					if ($params["txtKodeRekening_" . $j] == "")
                        throw new Exception("Kode Rekening Harus Diisi !");
                    if ($params["txtKeterangan_" . $j] == "")
                        throw new Exception("Keterangan Harus Diisi !");
                    if ($params["txtDebet_" . $j] == "")
                        throw new Exception("Nominal Debet Harus Diisi, Minimal isikan angka 0 (Nol) !");
                    if ($params["txtKredit_" . $j] == "")
                        throw new Exception("Nominal Kredit Harus Diisi, Minimal isikan angka 0 (Nol) !");
                    for ($k=0; $k < $jumData; $k++) { 
                    	$vdebet += $params["txtDebet_".$k];
                    	$vkredit += $params["txtKredit_".$k];
                    }
                    if ($vdebet != $vkredit)
                        throw new Exception("Nominal Debet Dan Kredit Harus Sama !");

                    $idTransaksi = secureParam($params["chkEdit_" . $j], $dbLink);
                    $kodeRekenening = secureParam($params["txtKodeRekening_" . $j], $dbLink);
                    $keterangan = secureParam($params["txtKeterangan_" . $j], $dbLink);
                    $debet = secureParam($params["txtDebet_" . $j], $dbLink);
                    $debet = str_replace(".", "", $debet);
                    $kredit = secureParam($params["txtKredit_" . $j], $dbLink);
                    $kredit = str_replace(".", "", $kredit);

                    $q = "UPDATE aki_tabel_transaksi SET kode_rekening = '".$kodeRekenening."', tanggal_transaksi = '".$tglTransaksi."', keterangan_transaksi='".$keterangan."', debet='".$debet."', kredit= '".$kredit."', last_updater ='".$pembuat."' ";
					$q.= "WHERE id_transaksi='".$idTransaksi."' ;";

					if (!mysql_query( $q, $dbLink))
						throw new Exception('Gagal ubah data transaksi jurnal umum.');

				}
			}
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Jurnal Umum ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data Jurnal Umum - ".$e->getMessage().'<br/>';
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

		$kodeTransaksi = secureParam($kode,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$q = "DELETE FROM aki_tabel_transaksi ";
			$q.= "WHERE md5(kode_transaksi)='".$kodeTransaksi."';";

			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data transaksi jurnal umum.');

			$q1 = "DELETE FROM aki_jurnal_umum ";
			$q1.= "WHERE md5(kode_transaksi)='".$kodeTransaksi."';";

			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data jurnal umum.');

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data Jurnal Umum ";
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
