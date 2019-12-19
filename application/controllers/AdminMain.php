<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminMain extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
    }

    public function index()
    {
        if(!isset($_SESSION['admin']))
            redirect('Welcome/admin');
        $this->load->view('adminmain');
    }

    public function admin()
    {
        if (isset($_SESSION['admin'])) {
            redirect('AdminMain');
        }
        $this->load->view('adminlogin');
    }

    public function user()
    {
        $data['tabel'] = $this->DataModel->get_all_data('user_studio');
        $data['tabel'] = $data['tabel']->result();

        $this->load->view('tableuser',$data);
    }

    public function jadwal()
    {
        $data['tabel'] = $this->DataModel->get_all_data('jadwal_studio');
        $data['tabel'] = $data['tabel']->result();

        $this->load->view('tablejadwal', $data);
    }

    public function pemesanan()
    {
        $data['tabel'] = $this->DataModel->get_all_data('pemesanan_studio');
        $data['tabel'] = $data['tabel']->result();

        $this->load->view('tablepemesanan', $data);
    }

    public function login()
    {
        $this->load->library('session');
        $data = array(
            'user' => $this->input->post('name'),
            'pass' => $this->input->post('password')
        );
        $admin_data = $this->DataModel->get_data(array('user' => $data['user']),'admin_studio');
        $admin_pass = $admin_data->row();
        if($data['user'] != $admin_pass->user || md5($data['pass']) != $admin_pass->pass)
        {
            $this->session->set_flashdata('data_invalid', 'username or password is not correct');
            redirect('Welcome/admin');
        }

        $_SESSION['admin'] = $admin_pass->user;
        $this->load->view('adminmain');
    }

    public function logout()
    {
        if(isset($_SESSION['admin']))
            unset($_SESSION['admin']);

        $this->load->view('adminlogin');
    }

    public function token()
    {
        $this->load->view('tokencheck');
    }

    public function tokencheck()
    {
        $token = $this->input->post('token');
        $pesanan_data = $this->DataModel->get_data(array('token' => $token), 'pemesanan_studio');
        $data['pesanan'] = $pesanan_data->row();

        if($data['pesanan'] == null)
        {
            die('Data tidak ditemukan');
        }
        else
            die('Token Valid');
    }
}
