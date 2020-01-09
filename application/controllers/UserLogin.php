<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserLogin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
         $_ENV['url'] = '127.0.0.1';
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
        $client = new \GuzzleHttp\Client();
        $this->load->library('session');
        $data['user'] = array(
            'username' => $username = $this->input->post('username'),
            'password' => $password = $this->input->post('password'),
        );

        //$login_data = $this->DataModel->get_data(array('username' => $username), 'user');
        //$login_pass = $login_data->row();

        $GET_REQUEST = $client->request('GET', $_ENV['url'] . '/PAW-Rest/api/validation?api_key=' . $_SESSION['api_key'] . '&username=' . $username);

        $login_pass = json_decode($GET_REQUEST->getBody(), true);

        if($login_pass['username'] === null)
        {
            $this->session->set_flashdata('data_invalid', 'username or password is not correct');
            $this->session->set_userdata('flashdata', $data);
            redirect('UserLogin');
            return;
        }
        else if($login_pass['password'] != md5($password))
        {
            $this->session->set_flashdata('data_invalid','username or password is not correct');
            $this->session->set_userdata('flashdata', $data);
            redirect('UserLogin');
            return;
        }

        $_SESSION['user'] = $username;
        $_SESSION['pass'] = $password;
        redirect('User/dashboard');

    }

}
