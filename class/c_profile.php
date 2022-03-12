<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_profile
{
	var $strResults="";

	function add(&$params) 
	{
		global $dbLink;
		
		$nik = secureParam($params["inputNik"],$dbLink);
		$kname = secureParam($params["inputName"],$dbLink);
		$email = secureParam($params["inputEmail"],$dbLink);
		$addrs = secureParam($params["inputAddress"],$dbLink);
		$sex = secureParam($params["sex"],$dbLink);
		$phone = secureParam($params["inputPhone"],$dbLink);
		$active = secureParam($params["inputActive"],$dbLink);
		$tanggal =  date("y-m-d", strtotime($active));
		$place = secureParam($params["inputPLaceb"],$dbLink);
		$tlahir = secureParam($params["inputDate"],$dbLink);
		$gol = secureParam($params["cbogol"],$dbLink);
		$dep = secureParam($params["inputDep"],$dbLink);
		$divs = secureParam($params["inputDiv"],$dbLink);
		$direct = secureParam($params["inputPhone"],$dbLink);
		$unit = secureParam($params["cboUnit"],$dbLink);
		$lvl =  secureParam($params["inputLevel"],$dbLink);
		$status = secureParam($params["inputStatus"],$dbLink);
		$jabtn = secureParam($params["inputJabatan"],$dbLink);
		$program = secureParam($params["inputProgram"],$dbLink);
		$bpjs_kes = secureParam($params["inputBpjskes"],$dbLink);
		$bpjstk =  secureParam($params["inputBPJSTK"],$dbLink);
		$umroh = secureParam($params["inputUmroh"],$dbLink);
		$qurban = secureParam($params["inputQurban"],$dbLink);
        //$pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$q = "INSERT INTO `aki_tabel_master`(`nik`, `kname`, `email`, `alamat`,`jenis_kelamin`, `phone`, `tanggal_aktif`,`tempat_lahir`, `tanggal_lahir`, `status`) VALUES";
			$q.= "('".$nik."',  '".$kname."', '".$email."', '".$addrs."', '".$sex."', '".$phone."', '".$tanggal."', '".$place."', '".$tlahir ."', 'Aktif');";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal tambah data master.');
			$q2 = "INSERT INTO `aki_golongan_kerja`(`nik`, `gol_kerja`, `jabatan`, `departemen`, `divisi`, `direktorat`, `unit`, `level`, `status_ikatan`) VALUES";
			$q2.= "('".$nik."',  '".$gol."', '".$jabtn."', '".$dep."', '".$divs."', '".$direct."', '".$unit."', '".$lvl."', '".$status ."');";
			if (!mysql_query( $q2, $dbLink))
				throw new Exception('Gagal tambah data master.');
			$q3= "INSERT INTO `aki_tabel_jaminan`(`nik`, `bpjs_kes`, `bpjstk`, `program_bpjstk`) VALUES ('".$nik."','".$bpjs_kes."','".$bpjstk."','".$program."')";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception('Gagal tambah data master.');
			$q4= "INSERT INTO `aki_tabel_benefit`(`nik`, `umroh`, `qurban`) VALUES ('".$nik."','".$umroh."','".$qurban."')";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal tambah data master.');
			
			@mysql_query("COMMIT", $dbLink);
			$this->strResults=$q2."Sukses Tambah Data Master";
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
		
		$nik = secureParam($params["inputNik"],$dbLink);
		$kname = secureParam($params["inputName"],$dbLink);
		$email = secureParam($params["inputEmail"],$dbLink);
		$addrs = secureParam($params["inputAddress"],$dbLink);
		$sex = secureParam($params["sex"],$dbLink);
		$phone = secureParam($params["inputPhone"],$dbLink);
		$active = secureParam($params["inputActive"],$dbLink);
		$tanggal =  date("y-m-d", strtotime($active));
		$place = secureParam($params["inputPLaceb"],$dbLink);
		$tlahir = secureParam($params["inputDate"],$dbLink);
		$tlahir =  date("y-m-d", strtotime($tlahir));
		$gol = secureParam($params["cbogol"],$dbLink);
		$dep = secureParam($params["inputDep"],$dbLink);
		$divs = secureParam($params["inputDiv"],$dbLink);
		$direct = secureParam($params["inputPhone"],$dbLink);
		$unit = secureParam($params["cboUnit"],$dbLink);
		$lvl =  secureParam($params["inputLevel"],$dbLink);
		$status = secureParam($params["inputStatus"],$dbLink);
		$jabtn = secureParam($params["inputJabatan"],$dbLink);
		$program = secureParam($params["inputProgram"],$dbLink);
		$bpjs_kes = secureParam($params["inputBpjskes"],$dbLink);
		$bpjstk =  secureParam($params["inputBPJSTK"],$dbLink);
		$umroh = secureParam($params["inputUmroh"],$dbLink);
		$qurban = secureParam($params["inputQurban"],$dbLink);
        
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
            $q = "UPDATE aki_tabel_master SET `kname`='".$kname."',`jenis_kelamin`='".$sex."',`tempat_lahir`='".$place."',`tanggal_lahir`='".$tlahir."',`alamat`='".$addrs."',`phone`='".$phone."',`email`='".$email."',`no_ktp`='".$no_ktp."',`no_npwp`='".$no_npwp."',`status_martial`='".$status."',`tanggal_aktif`='".$tanggal."',`tanggal_nonaktif`='".$tanggal_nonaktif."' ";
			$q.= " WHERE nik='".$nik."' ";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal mengubah database.');
			$q2 ="UPDATE `aki_golongan_kerja` SET `gol_kerja`='".$gol."',`jabatan`='".$jabtn."',`departemen`='".$dep."',`divisi`='".$divs."',`direktorat`='".$direct."',`unit`='".$unit."',`level`='".$lvl."',`status_ikatan`='".$status."' WHERE nik='".$nik."' ";
			if (!mysql_query( $q2, $dbLink))
				throw new Exception('Gagal mengubah database.');
			$q3 ="UPDATE `aki_tabel_jaminan` SET `bpjs_kes`='".$bpjs_kes."',`bpjstk`='".$bpjstk."',`program_bpjstk`='".$program."' WHERE nik='".$nik."' ";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception('Gagal mengubah database.');
			$q4 ="UPDATE `aki_tabel_benefit` SET `umroh`='".$umroh."',`qurban`='".$qurban."' WHERE nik='".$nik."' ";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal mengubah database.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Profil ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data Profil - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function delete($kodePerkiraan)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kodeSiswa))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Perkiraan - ".$this->strResults;
			return $this->strResults;
		}
		
                $kodeRekening = secureParam($kodePerkiraan,$dbLink);
                $pembatal = $_SESSION["my"]->id;
                		
		$q = "DELETE FROM tabel_master ";
		$q.= "WHERE md5(kode_rekening)='".$kodeRekening."';";
                
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Hapus Data Perkiraan";
		}
		else
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Perkiraan - ".mysql_error();
		}
		return $this->strResults;
	}
}
?>
