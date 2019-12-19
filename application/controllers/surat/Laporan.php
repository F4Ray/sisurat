<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('template');
		$this->load->library('pdf');
        $this->template->authlogin();
		$this->template->set('title',"Laporan");
        $this->_init();
        $this->load->model('M_laporan');
	}

	function _init()
    {
        $this->template->css('assets/themes/adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');
        $this->template->js('assets/themes/adminlte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');
    	// $this->template->js('assets/themes/adminlte/bower_components/chart.js/Chart.js');
    }

	public function index()
	{
		$this->form_validation->set_rules('tgl', 'TGL', 'trim|required|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('jenis', 'jenis', 'trim|required|min_length[1]|max_length[1]|numeric');
		$tgl = $this->input->post("tgl");
		$jenis = $this->input->post("jenis");
		// die();
		$data = array(
			);
		// var_dump($tgl);
		// var_dump($jenis);
		if ($tgl == null) {
			$querykeluar = $this->M_laporan->get_surat_keluar_semua()->result();
			$querymasuk = $this->M_laporan->get_surat_masuk_semua()->result();
			$ketkeluar = 'Laporan Seluruh Surat Keluar';
			$ketmasuk = 'Laporan Seluruh Surat Masuk';
			$data['hasil1'] = $querykeluar;
			$data['hasil2'] = $querymasuk;
			$data['kode'] = "semua";
			$data['ket2'] = $ketkeluar;
			$data['ket3'] = $ketmasuk;
			// echo"zaaan";
			// print_r($this->db->last_query());  
		}elseif ($tgl OR $jenis != null){
			if ($this->form_validation->run() == FALSE) {
				# code...
				$data['kode'] =1;
				echo validation_errors();
			} else {
				$e = explode('-', $tgl);
				$d=cal_days_in_month(CAL_GREGORIAN,$e[0], $e[1]);
				$start = 2;
				$q = "SELECT 1 AS Date UNION ALL ";
				while ($start <= $d) {
					# code...
					if ($start == $d) {
						$q .= "SELECT ".$start;
					}
					else{
						$q .= "SELECT ".$start." UNION ALL ";
					}
					$start++;
				}
				// print_r($q);
				$query = '';
				$ket = '';
				if ($jenis == 2) {
					$query = $this->M_laporan->get_surat_masuk_bulan($e[0],$e[1])->result();
					$ket = 'Laporan Surat Masuk Bulan '.$e[0].' Tahun '.$e[1];
					$data['tipe'] = "masuk";

				}

				if ($jenis == 1) {
					# code...
					// $query = $this->M_laporan->surat_keluar($e[0], $e[1], $q)->result();
					$query = $this->M_laporan->get_surat_keluar_bulan($e[0],$e[1])->result();
					$ket = 'Laporan Surat Keluar Bulan '.$e[0].' Tahun '.$e[1];
					$data['tipe'] = "keluar";
				}
				// var_dump($query);
				// print_r($this->db->last_query());  
				// var_dump($tgl);
				// var_dump($jenis);
				$data['hasil'] = $query;
				$data['b'] = $tgl;
				$data['jenis'] = $jenis;
				$data['kode'] = 2;
				$data['bulanz'] = $e[0]; 
				$data['tahunz'] = $e[1]; 
				// $data['tipe'] = "keluar";
				$data['ket2'] = $ket;
			}
		}
		
		$this->template->adminlte('surat/laporan/view',$data,'surat/laporan/j_view');
	}

	public function excel($jenis, $tgl)
	{
		# code...
		$this->load->library('PHPExcel');
		$data = array();
		$query = '';
		$e = explode('-', $tgl);
		if ($jenis == 2) {	
			$ket = 'Laporan Surat Masuk Bulan '.$e[0].' Tahun '.$e[1];
			$this->load->model('M_masuk');
			$query = $this->M_masuk->lihat(array("DATE_FORMAT(a.tgl_suma,'%m-%Y')"=>$tgl))->result();
			$data['hasil'] = $query;
			$data['ket'] = $ket;
			$this->load->view('surat/laporan/excel_masuk', $data);
		}

		if ($jenis == 1) {
			# code...
			$ket = 'Laporan Surat Keluar Bulan '.$e[0].' Tahun '.$e[1];
			$this->load->model('M_keluar');
			$query = $this->M_keluar->lihat(array("DATE_FORMAT(a.tanggal_sulur,'%m-%Y')"=>$tgl))->result();
			$data['hasil'] = $query;
			$data['ket'] = $ket;
			$this->load->view('surat/laporan/excel_keluar', $data);
		}
	}
	public function print_pdf($jenis,$tgl)
	{
		$e = explode('-', $tgl);

		$pdf = new FPDF('l','mm','A4');
		if ($jenis == 'keluar') {
			$pdf->SetLeftMargin(25);
		}
        // membuat halaman baru
        $pdf->AddPage();
        // setting jenis font yang akan digunakan
        $pdf->SetFont('Arial','B',16);
        // mencetak string 
       	if ($jenis == 'masuk') {
       		$pdf->Cell(268,7,"Laporan Surat Masuk Bulan $e[0] Tahun $e[1]",0,1,'C');
       	}elseif ($jenis == 'keluar') {
       		$pdf->Cell(243,7,"Laporan Surat Keluar Bulan $e[0] Tahun $e[1]",0,1,'C');
       	}
        $pdf->SetFont('Arial','B',12);
        // $pdf->Cell(190,7,"Laporan Surat Masuk Bulan $e[0] Tahun $e[1]",0,1,'C');
        // Memberikan space kebawah agar tidak terlalu rapat
        $pdf->Cell(10,7,'',0,1);
        $pdf->SetFont('Arial','B',10);
        if ($jenis == 'masuk') {
        	$pdf->Cell(40,6,'Nomor Surat',1,0);
	        $pdf->Cell(39,6,'Pengirim Surat',1,0);
	        $pdf->Cell(39,6,'Perihal Surat',1,0);
	        $pdf->Cell(27,6,'Tanggal Surat',1,0);
	        $pdf->Cell(27,6,'Diterima Surat',1,0);
	        $pdf->Cell(68,6,'Disposisi',1,0);
	        $pdf->Cell(36,6,'Nama Bidang',1,1); 
        }elseif ($jenis == 'keluar') {
        	$pdf->Cell(40,6,'Nomor Surat',1,0,'C');
        	$pdf->Cell(39,6,'Kode Surat',1,0);
        	$pdf->Cell(39,6,'Kepada',1,0);
        	$pdf->Cell(35,6,'Perihal',1,0);
        	$pdf->Cell(27,6,'Tanggal Surat',1,0);
        	$pdf->Cell(68,6,'Pengolah Surat',1,1);
        }
        
        $pdf->SetFont('Arial','',10);
        if ($jenis == 'masuk') {
        	$suratnya = $this->M_laporan->get_surat_masuk_bulan($e[0],$e[1])->result();
	        foreach ($suratnya as $hasilnya) {
	            $pdf->Cell(40,6,$hasilnya->no_suma,1,0);
	            $pdf->Cell(39,6,$hasilnya->pengirim_suma,1,0);
	            $pdf->Cell(39,6,$hasilnya->perihal_suma,1,0);
	            $pdf->Cell(27,6,$hasilnya->tgl_suma,1,0);
	            $pdf->Cell(27,6,$hasilnya->diterima_suma,1,0);
	            $pdf->Cell(68,6,$hasilnya->disposisi,1,0);
	            $pdf->Cell(36,6,$hasilnya->nama_bidang,1,1); 
	        }
        }elseif ($jenis == 'keluar') {
        	$suratnya = $this->M_laporan->get_surat_keluar_bulan($e[0],$e[1])->result();
        	foreach ($suratnya as $hasilnya) {
        		$pdf->Cell(40,6,$hasilnya->nomer_sulur,1,0);
	        	$pdf->Cell(39,6,$hasilnya->kode_sulur,1,0);
	        	$pdf->Cell(39,6,$hasilnya->kepada_sulur,1,0);
	        	$pdf->Cell(35,6,$hasilnya->perihal_sulur,1,0);
	        	$pdf->Cell(27,6,$hasilnya->tanggal_sulur,1,0);
	        	$pdf->Cell(68,6,$hasilnya->nama_bidang,1,1);     
        	}
        }
        
        $pdf->Output();
	}
	public function print_pdf_all($jenis)
	{
		// $e = explode('-', $tgl);

		$pdf = new FPDF('l','mm','A4');
		if ($jenis == 'keluar') {
			$pdf->SetLeftMargin(25);
		}
        // membuat halaman baru
        $pdf->AddPage();
        // setting jenis font yang akan digunakan
        $pdf->SetFont('Arial','B',16);
        // mencetak string 
       	if ($jenis == 'masuk') {
       		$pdf->Cell(268,7,"Laporan Seluruh Surat Masuk ",0,1,'C');
       	}elseif ($jenis == 'keluar') {
       		$pdf->Cell(243,7,"Laporan Seluruh Surat Keluar ",0,1,'C');
       	}
        $pdf->SetFont('Arial','B',12);
        // $pdf->Cell(190,7,"Laporan Surat Masuk Bulan $e[0] Tahun $e[1]",0,1,'C');
        // Memberikan space kebawah agar tidak terlalu rapat
        $pdf->Cell(10,7,'',0,1);
        $pdf->SetFont('Arial','B',10);
        if ($jenis == 'masuk') {
        	$pdf->Cell(40,6,'Nomor Surat',1,0);
	        $pdf->Cell(39,6,'Pengirim Surat',1,0);
	        $pdf->Cell(39,6,'Perihal Surat',1,0);
	        $pdf->Cell(27,6,'Tanggal Surat',1,0);
	        $pdf->Cell(27,6,'Diterima Surat',1,0);
	        $pdf->Cell(68,6,'Disposisi',1,0);
	        $pdf->Cell(36,6,'Nama Bidang',1,1); 
        }elseif ($jenis == 'keluar') {
        	$pdf->Cell(40,6,'Nomor Surat',1,0,'C');
        	$pdf->Cell(39,6,'Kode Surat',1,0);
        	$pdf->Cell(39,6,'Kepada',1,0);
        	$pdf->Cell(35,6,'Perihal',1,0);
        	$pdf->Cell(27,6,'Tanggal Surat',1,0);
        	$pdf->Cell(68,6,'Pengolah Surat',1,1);
        }
        
        $pdf->SetFont('Arial','',10);
        if ($jenis == 'masuk') {
        	$suratnya = $this->M_laporan->get_surat_masuk_semua()->result();
	        foreach ($suratnya as $hasilnya) {
	            $pdf->Cell(40,6,$hasilnya->no_suma,1,0);
	            $pdf->Cell(39,6,$hasilnya->pengirim_suma,1,0);
	            $pdf->Cell(39,6,$hasilnya->perihal_suma,1,0);
	            $pdf->Cell(27,6,$hasilnya->tgl_suma,1,0);
	            $pdf->Cell(27,6,$hasilnya->diterima_suma,1,0);
	            $pdf->Cell(68,6,$hasilnya->disposisi,1,0);
	            $pdf->Cell(36,6,$hasilnya->nama_bidang,1,1); 
	        }
        }elseif ($jenis == 'keluar') {
        	$suratnya = $this->M_laporan->get_surat_keluar_semua()->result();
        	foreach ($suratnya as $hasilnya) {
        		$pdf->Cell(40,6,$hasilnya->nomer_sulur,1,0);
	        	$pdf->Cell(39,6,$hasilnya->kode_sulur,1,0);
	        	$pdf->Cell(39,6,$hasilnya->kepada_sulur,1,0);
	        	$pdf->Cell(35,6,$hasilnya->perihal_sulur,1,0);
	        	$pdf->Cell(27,6,$hasilnya->tanggal_sulur,1,0);
	        	$pdf->Cell(68,6,$hasilnya->nama_bidang,1,1);     
        	}
        }
        $pdf->Output();
	}
}

/* End of file Laporan.php */
/* Location: ./application/controllers/surat/Laporan.php */