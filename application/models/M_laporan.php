<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan extends CI_Model {

	public function surat_masuk($b, $t, $q)
	{
		# code...
		return $this->db->query("
			SELECT MonthDate.Date, COUNT(id_suma) AS Total
			FROM ( ".$q.") AS MonthDate
			LEFT JOIN tbl_suma AS T1
			ON MonthDate.Date = DAY(T1.tgl_suma)
			AND MONTH(T1.tgl_suma) = ".$b." AND YEAR(T1.tgl_suma) = ".$t."
			GROUP BY MonthDate.Date
			");
	}

	public function surat_keluar($b, $t, $q)
	{
		# code...
		return $this->db->query("
			SELECT MonthDate.Date, COUNT(id_sulur) AS Total
			FROM ( ".$q.") AS MonthDate
			LEFT JOIN tbl_surat_keluar AS T1
			ON MonthDate.Date = DAY(T1.tanggal_sulur)
			AND MONTH(T1.tanggal_sulur) = ".$b." AND YEAR(T1.tanggal_sulur) = ".$t."
			GROUP BY MonthDate.Date
			");
	}
	public function get_surat_keluar_bulan($bulan,$tahun)
	{
		// return $this->db->query("
		// 	SELECT * FROM tbl_surat_keluar WHERE YEAR(tanggal_sulur) = $tahun AND MONTH(tanggal_sulur) = $bulan");
		$this->db->select("a.id_sulur, a.nomer_sulur, a.kode_sulur, a.kepada_sulur, a.perihal_sulur, DATE_FORMAT(a.tanggal_sulur,'%d-%m-%Y') as tanggal_sulur, a.pengolah_sulur, b.nama_bidang");
		$this->db->from('tbl_surat_keluar'.' a');
		$this->db->join('tbl_bidang b', 'a.pengolah_sulur = b.id_bidang', 'left');
		$this->db->where("YEAR(tanggal_sulur) = $tahun AND MONTH(tanggal_sulur) = $bulan");
		$this->db->order_by('a.tanggal_sulur', 'desc');
		return $this->db->get();
	}
	public function get_surat_masuk_bulan($bulan,$tahun)
		{
			// return $this->db->query("
			// 	SELECT * FROM tbl_surat_keluar WHERE YEAR(tanggal_sulur) = $tahun AND MONTH(tanggal_sulur) = $bulan");
			$this->db->select("a.id_suma, a.no_suma, a.pengirim_suma, a.perihal_suma,DATE_FORMAT(a.tgl_suma,'%d-%m-%Y') as tgl_suma, DATE_FORMAT(a.diterima_suma,'%d-%m-%Y') as diterima_suma, a.disposisi, a.bidang, b.nama_bidang");
			$this->db->from('tbl_suma'.' a');
			$this->db->join('tbl_bidang b', 'a.bidang = b.id_bidang', 'left');
			$this->db->where("YEAR(tgl_suma) = $tahun AND MONTH(tgl_suma) = $bulan");
			$this->db->order_by('a.tgl_suma', 'desc');
			return $this->db->get();
		}
	public function get_surat_keluar_semua()
	{
		$this->db->select("a.id_sulur, a.nomer_sulur, a.kode_sulur, a.kepada_sulur, a.perihal_sulur, DATE_FORMAT(a.tanggal_sulur,'%d-%m-%Y') as tanggal_sulur, a.pengolah_sulur, b.nama_bidang");
			$this->db->from('tbl_surat_keluar'.' a');
			$this->db->join('tbl_bidang b', 'a.pengolah_sulur = b.id_bidang', 'left');
			// $this->db->where("YEAR(tanggal_sulur) = $tahun AND MONTH(tanggal_sulur) = $bulan");
			$this->db->order_by('a.tanggal_sulur', 'desc');
			return $this->db->get();
	}
	public function get_surat_masuk_semua()
	{
		$this->db->select("a.id_suma, a.no_suma, a.pengirim_suma, a.perihal_suma,DATE_FORMAT(a.tgl_suma,'%d-%m-%Y') as tgl_suma, DATE_FORMAT(a.diterima_suma,'%d-%m-%Y') as diterima_suma, a.disposisi, a.bidang, b.nama_bidang");
		$this->db->from('tbl_suma'.' a');
		$this->db->join('tbl_bidang b', 'a.bidang = b.id_bidang', 'left');
		// $this->db->where($value);
		$this->db->order_by('a.tgl_suma', 'desc');
		return $this->db->get();
	}

}

/* End of file M_laporan.php */
/* Location: ./application/models/M_laporan.php */