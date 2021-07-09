<?php
//Persiapan Import Excel ke Mysql

//panggil Koneksi Database
include( 'config.php' );
global $dbLink;
// Panggil Library Excel Reader
include "excel_reader2.php";

//upload file xls
$target = basename($_FILES['filepegawai']['name']);
move_uploaded_file($_FILES['filepegawai']['tmp_name'], $target);

//beri permision agar file xls dapat dibaca
chmod($_FILES['filepegawai']['name'], 0777);

//mengambil isi file xls
$data = new Spreadsheet_Excel_Reader($_FILES['filepegawai']['name'], false);
//menghitung jumlah baris data yang ada
$jumlah_baris = $data->rowcount($sheet_index = 0);

//Jumlah default data yang berhasil di import
$berhasil = 0;
for($i = 7; $i <= $jumlah_baris; $i++)
{
	//menangkap data dan memasukkan ke variabel sesuai dengan kolomnya masing2
	$nik = $data->val($i, 2);
	$tanggal = $data->val($i, 7);
	$scan1 = $data->val($i, 8);
	$scan2 = $data->val($i, 9);
	$scan3 = $data->val($i, 10);
	$scan4 = $data->val($i, 11);
	$scan5 = $data->val($i, 12);
	$scan6 = $data->val($i, 13);

	//buat pengujian jika nama,alamat & telp tidak kosong
	if($nik != "" && $tanggal != "" && $scan1 != ""){
		//persiapkan insert data ke database
		$fixtanggal = date("y-m-d", strtotime($tanggal));
		$q="INSERT INTO aki_absensi (`nik`, `tanggal`, `scan1`, `scan2`, `scan3`, `scan4`, `scan5`, `scan6`)  VALUES ('$nik', '$fixtanggal', '$scan1', '$scan2', '$scan3', '$scan4', '$scan5', '$scan6')";
		mysql_query( $q,$dbLink);
		$berhasil++;
	}
}

//hapus kembali file .xls yang di upload tadi
unlink($_FILES['filepegawai']['name']);

//alihkan halaman ke index.php
header("location:index.php?berhasil=$berhasil");

?>