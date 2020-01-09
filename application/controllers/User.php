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
		$_ENV['url'] = 'https://balahu.space';
	}

	public function index()
	{
		
		$this->load->library('session');
		
		if (isset($_SESSION['user']))
			redirect('User/dashboard');
		else
		{
			$this->load->view('userregister');
		}
	}

	function register()
	{
		$client = new \GuzzleHttp\Client();
		$this->load->library('session');
		$data = array(
				'username'  => $username = $this->input->post('username'),
				'password'  => $pass = md5($this->input->post('password')),
				'name'		=> $name = $this->input->post('name')
		);

		$GET_REQUEST = $client->request('GET', $_ENV['url'] . '/PAW-Rest/api/validation?api_key='.$_SESSION['api_key'].'&username=' . $username );
		
		$isUserNameTaken = json_decode($GET_REQUEST->getBody(), true);

		if($isUserNameTaken['username'] != null)
		{
			$this->session->set_flashdata('data_invalid', 'username has already taken');
			$this->session->set_flashdata('flashdata', $data);
			redirect('User');
			return;
		}
		else
		{
			$POST_REQUEST = $client->request('POST', $_ENV['url'] .  '/PAW-Rest/api/user', [
				'form_params' => [
					'username' 	=> $username,
					'password' 	=> $pass,
					'name'		=> $name,
					'api_key'	=> $_SESSION['api_key']
				]
			]);

			$_SESSION['user'] = $username;
			$_SESSION['pass'] = $this->input->post('password');
			redirect('User/dashboard');
		}
		
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
		$client = new \GuzzleHttp\Client();
		$GET_REQUEST = $client->request('GET', $_ENV['url'] . '/PAW-Rest/api/validation?api_key=' . $_SESSION['api_key'] . '&username=' . $_SESSION['user']);
		$userdata = json_decode((string) $GET_REQUEST->getBody());
		$temp = explode(" ", $userdata->id);
		$_SESSION['userid'] = end($temp);

		$this->load->view('dashboard');
	}

	public function logout()
	{
		$this->load->library('session');
		if (isset($_SESSION['user']))
		{
			unset($_SESSION['user']);
			unset($_SESSION['pass']);
			unset($_SESSION['userid']);
		}
			

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
		$this->load->library('session');
		/* $user_data_query = $this->DataModel->get_data(array('username' => $_SESSION['user']),'user');
		$user_data = $user_data_query->row(); */
		$this->session->set_flashdata('data_success','data has been submitted');
		$data = array(
				'level' 	=> $this->input->post('level'),
				'game_id' 	=> $this->input->post('game_id'),
				'user_id'	=> $this->input->post('user_id'),
				'score'		=> $this->input->post('score')
		);
		
		$this->DataModel->input_data($data, 'game_level');

		redirect('User/score');
	}

	public function submitleaderboard()
	{
		$game_id 	= $this->input->post('game_id');
		$level		= $this->input->post('level');
		$this->db->order_by('score', 'DESC');
		$data['query'] = $this->DataModel->get_data(array('game_id' => $game_id, 'level' => $level),'game_level');
		$data['leaderboard'] = $data['query']->result();

		$this->load->view('leaderboardview', $data);
	}

	public function leaderboard()
	{
		$this->load->library('session');
		$this->load->view('leaderboard');
	}

	public function profile()
	{
		$this->load->library('session');
		$this->load->view('profile');
	}

	public function updateProfile()
	{
		$this->load->library('session');
		$client = new \GuzzleHttp\Client();
		$base64_img = $this->input->post('current_image');
		if(isset($_FILES['fileToUpload']))
		{
			$errors = array();
			$allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
			$file_name = $_FILES['fileToUpload']['name'];
			$tmp = explode('.', $file_name);
			$file_ext = end($tmp);

			$file_size = $_FILES['fileToUpload']['size'];
			$file_tmp = $_FILES['fileToUpload']['tmp_name'];

			$data = file_get_contents($file_tmp);
			$base64_img = base64_encode($data);
			if(strlen($file_name) <= 0)
			{
				$base64_img = $this->input->post('current_image');
			}
			else if (in_array($file_ext, $allowed_ext) === false) {
				$errors[] = 'Extension not allowed';
				$this->session->set_flashdata('data_invalid', 'Extension not allowed');
				$this->load->view('profile');
				return;
			}
			else if ($file_size > 2097152) {
				$errors[] = 'File size must be under 2mb';
				$this->session->set_flashdata('data_invalid', 'File size must be under 2mb');
				$this->load->view('profile');
				return;
			}
			$this->session->set_flashdata('data_invalid', '');
			
		}

		

		$PUT_REQUEST = $client->request('PUT', $_ENV['url'] .  '/PAW-Rest/api/user' , [
			'form_params' => [
				'id'		=> $this->input->post('id'),
				'username' 	=> $_SESSION['user'],
				'password' 	=> md5($this->input->post('password')),
				'name'		=> $this->input->post('name'),
				'photo'		=> $base64_img,
				'api_key'	=> $_SESSION['api_key']
			]
		]);


		unset($_FILES['fileToUpload']);
		$this->load->view('profile');
	}

	public function data()
	{
		$client = new \GuzzleHttp\Client();
		$GET_REQUEST = $client->request('GET', $_ENV['url'] . '/PAW-Rest/api/data?api_key=' . $_SESSION['api_key'] . '&user_id=' . $_SESSION['userid']);
		$userdata['datauser'] = json_decode($GET_REQUEST->getBody());
		$this->load->view('dataview', $userdata);
	}

	public function dataedit($id)
	{
		$client = new \GuzzleHttp\Client();
		$GET_REQUEST = $client->request('GET', $_ENV['url'] . '/PAW-Rest/api/DataEdit?api_key=' . $_SESSION['api_key'] . '&id=' . $id);
		$data['userdata'] = json_decode($GET_REQUEST->getBody());
		$this->load->view('dataedit', $data);
	}

	public function datadelete($id)
	{
		$client = new \GuzzleHttp\Client();
		$DELETE_REQUEST = $client->request('DELETE', $_ENV['url'] .  '/PAW-Rest/api/data' , [
			'form_params' => [
				'id'			=> $id,
				'api_key'		=> $_SESSION['api_key']
			]
		]);	
		redirect('User/data');
	}

	public function dataadd()
	{
		$this->load->view('datacreate');
	}

	public function datasubmit()
	{
		$client = new \GuzzleHttp\Client();
		$random_text = $this->input->post('random_text');
		$POST_REQUEST = $client->request('POST', $_ENV['url'] .  '/PAW-Rest/api/data' , [
			'form_params' => [
				'user_id'		=> $_SESSION['userid'],
				'random_text' 	=> $random_text,
				'api_key'		=> $_SESSION['api_key']
			]
		]);	

		redirect('User/data');
	}

	public function dataupdate()
	{
		$client = new \GuzzleHttp\Client();

		$id = $this->input->post('id');
		$random_text = $this->input->post('random_text');

		$PUT_REQUEST = $client->request('PUT', $_ENV['url'] .  '/PAW-Rest/api/DataEdit', [
			'form_params' => [
				'id'			=> $id,
				'user_id'		=> $_SESSION['userid'],
				'random_text' 	=> $random_text,
				'api_key'		=> $_SESSION['api_key']
			]
		]);

		redirect('User/data');
	}
}
