<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('DataModel');
	}

	public function index()
	{
		$this->load->library('session');
		//session_start();
		if (isset($_SESSION['user']))
			redirect('Main');
		else
		{
			$this->load->view('userregister');
		}
	}

	function register()
	{
		$this->load->library('session');
		$data = array(
				'unx_id' => uniqid(),
				'username'  => $name = $this->input->post('name'),
				'password'  => $pass = md5($this->input->post('password')),
		);

		$isUserNameTaken = $this->DataModel->get_data(array('username' => $name), 'user');
		$isUserNameTaken = $isUserNameTaken->row();

		if($isUserNameTaken->username != null)
		{
			$this->session->set_flashdata('data_invalid', 'username has already taken');
			$this->session->set_flashdata('flashdata', $data);
			redirect('User');
			return;
		}
		$this->DataModel->input_data($data, 'user');

		$_SESSION['user'] = $name; 
		

		redirect('User/dashboard');
	}

	public function admin()
	{
		if(isset($_SESSION['admin']))
		{
			redirect('AdminMain');
		}
		$this->load->view('adminlogin');
	}

	public function dashboard()
	{
		$this->load->view('dashboard');
	}

	public function logout()
	{
		$this->load->library('session');
		if (isset($_SESSION['user']))
			unset($_SESSION['user']);
		redirect('User');
	}

	public function score()
	{
		$this->load->library('session');
		$user_data_query = $this->DataModel->get_data(array('username' => $_SESSION['user']), 'user');
		$user_data['user'] = $user_data_query->row();

		$this->load->view('submitscore', $user_data);
	}

	public function submitscore()
	{
		/* $user_data_query = $this->DataModel->get_data(array('username' => $_SESSION['user']),'user');
		$user_data = $user_data_query->row(); */

		$data = array(
				'level' 	=> $this->input->post('level'),
				'game_id' 	=> $this->input->post('game_id'),
				'user_id'	=> $this->input->post('user_id'),
				'score'		=> $this->input->post('score')
		);
		
		$this->DataModel->input_data($data, 'game_level');

		redirect('User/score');
	}
}
