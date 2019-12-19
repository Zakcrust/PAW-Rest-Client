<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JadwalData extends CI_Controller
{
    public function index()
    {
        $this->load->view('jadwaladd');
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
    }

    public function add()
    {
        $data = array(
            'Hari' => $this->input->post('hari'),
            'Jam' => $this->input->post('jam')
        );

        $this->DataModel->input_data($data, 'jadwal_studio');
        redirect('AdminMain/jadwal');
    }

    public function edit($id)
    {
        $data['query']  = $this->DataModel->get_data(array('id_jadwal' => $id), 'jadwal_studio');
        $data['jadwal'] = $data['query']->row();

        $this->load->view('jadwaledit',$data);
    }

    public function delete($id)
    {
        $this->DataModel->hapus_data(array('id_jadwal' => $id), 'jadwal_studio');

        redirect('AdminMain/jadwal');
    }

    public function update()
    {
        $id = $this->input->post('id_jadwal');
        $data = array(
            'Hari' => $this->input->post('hari'),
            'Jam' => $this->input->post('jam')
        );
        $this->DataModel->update_data(array('id_jadwal' => $id),$data,'jadwal_studio');
        redirect('AdminMain/jadwal');
    }
}
