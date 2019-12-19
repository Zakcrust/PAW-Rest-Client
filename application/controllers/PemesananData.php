<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PemesananData extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
    }

    public function add()
    {
        $data = array(
            'id_jadwal' => $this->input->post('id_jadwal'),
            'id_user' => $this->input->post('id_user'),
            'token' => $this->input->post('token'),
            'nama_user' => $this->input->post('nama_user'),
            'durasi' => $this->input->post('durasi')
        );

        $this->DataModel->input_data($data,'pemesanan_studio');

        redirect('AdminMain/pemesanan');
    }

    public function edit($id)
    {
        $data['query']  = $this->DataModel->get_data(array('id' => $id),'pemesanan_studio');
        $data['pemesanan'] = $data['query']->row();

        $this->load->view('pemesananedit', $data);
    }

    public function delete($id)
    {
        $this->DataModel->hapus_data(array('id' => $id), 'pemesanan_studio');

        redirect('AdminMain/pemesanan');
    }

    public function update()
    {
        $id = $this->input->post('id');
        $data = array(
            'id_jadwal' => $this->input->post('id_jadwal'),
            'id_user' => $this->input->post('id_user'),
            'token' => $this->input->post('token'),
            'nama_user' => $this->input->post('nama_user'),
            'durasi' => $this->input->post('durasi')
        );

        $this->DataModel->update_data(array('id' => $id), $data, 'pemesanan_studio');
        redirect('AdminMain/pemesanan');
    }

    public function cetak($id)
    {
        $pesanan_user_query = $this->DataModel->get_data(array('id' => $id), 'pemesanan_studio');
        $pesanan_user['pesanan'] = $pesanan_user_query->row();

        $this->load->view('cetak', $pesanan_user);
    }
}
