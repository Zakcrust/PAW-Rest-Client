<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserData extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
    }

    public function edit($id)
    {
        $data['query']  = $this->DataModel->get_data(array('id' => $id), 'user_studio');
        $data['user'] = $data['query']->row();

        $this->load->view('useredit', $data);
    }

    public function delete($id)
    {
        $this->DataModel->hapus_data(array('id' => $id), 'user_studio');

        redirect('AdminMain/user');
    }

    public function update()
    {
        $id = $this->input->post('id');
        $data = array(
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'pass' => md5($this->input->post('password'))
        );
        $this->DataModel->update_data(array('id' => $id), $data, 'user_studio');
        redirect('AdminMain/user');
    }
}
