<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserLogin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
    }
    
    public function index()
    {
        $this->load->library('session');
        $this->load->view('userlogin');

        $check_session = isset($_SESSION['user']);
        if ($check_session)
            redirect('User/dashboard');
    }

    public function SignIn()
    {
        $this->load->library('session');
        $data['user'] = array(
            'username' => $username = $this->input->post('username'),
            'password' => $password = $this->input->post('password'),
        );

        $login_data = $this->DataModel->get_data(array('username' => $username), 'user');
        $login_pass = $login_data->row();

        if($login_pass->username === null)
        {
            $this->session->set_flashdata('data_invalid', 'username or password is not correct');
            $this->session->set_userdata('flashdata', $data);
            redirect('UserLogin');
            return;
        }
        else if($login_pass->password != md5($password))
        {
            $this->session->set_flashdata('data_invalid','username or password is not correct');
            $this->session->set_userdata('flashdata', $data);
            redirect('UserLogin');
            return;
        }

        $_SESSION['user'] = $username;
        redirect('User/dashboard');

    }

    
}
