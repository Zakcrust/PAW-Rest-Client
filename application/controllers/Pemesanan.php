<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Pemesanan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
    }


    public function index()
    {
        $this->load->library('session');
        $data['jadwal'] = $this->DataModel->get_all_data('jadwal_studio');
        $data['pemesanan'] = $this->DataModel->get_all_data('pemesanan_studio');
        $data['jadwal'] = $data['jadwal']->result();
        $data['pemesanan'] = $data['pemesanan']->result();
        /* $filteredData['jadwal'] = [];
        foreach($data['jadwal'] as $jadwal)
        {
            $checker_count = 0;
            foreach($data['pemesanan'] as $pemesanan)
            {
                if($jadwal->id_jadwal != $pemesanan->id_jadwal)
                {
                    echo $jadwal->id_jadwal;
                    echo $pemesanan->id_jadwal;
                    $checker_count = $checker_count + 1;
                }
            }
            die($checker_count);
            if($checker_count == count($data['pemesanan']))
            {
                array_push($filteredData,$jadwal);
            }
        }

        $this->load->view('reserves',$filteredData); */
        $this->load->view('reserves', $data);
        if (!isset($_SESSION['name'])) {
            redirect('Welcome');
        }
    }

    public function pesan()
    {
        $this->load->library('session');
        $name = $_SESSION['name'];
        $user_data_query = $this->DataModel->get_data(array('name' => $name), 'user_studio');
        $user_data = $user_data_query->row();
        $data = array(
            'id_jadwal' => $id_jadwal = $this->input->post("pilih_jadwal"),
            'id_user' => $user_data->id,
            'token' => bin2hex(random_bytes(4)),
            'nama_user' => $this->input->post("nama_lengkap"),
            'durasi' => $this->input->post("durasi")
        );

        $this->DataModel->input_data($data, "pemesanan_studio");
        $this->load->view('report');
    }

    public function pesanan()
    {
        $this->load->library('session');
        $name = $_SESSION['name'];
        $user_data_query = $this->DataModel->get_data(array('name' => $name), 'user_studio');
        $user_data = $user_data_query->row();
        $pesanan_user_query = $this->DataModel->get_data(array('id_user' => $user_data->id), 'pemesanan_studio');
        $pesanan_user['pesanan'] = $pesanan_user_query->result();

        $this->load->view('myreservation', $pesanan_user);
    }

    public function delete($id)
    {
        $this->DataModel->hapus_data(array('id' => $id), 'pemesanan_studio');

        redirect('Pemesanan/pesanan');
    }
    
}
