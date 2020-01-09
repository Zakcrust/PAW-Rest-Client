<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function _uploadImage($file_name)
    {
        $config['allowed_types']        = 'gif|jpg|png';
        $config['file_name']            = $file_name;
        $config['overwrite']            = true;
        $config['max_size']             = 2048; // 1MB
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('fileToUpload')) {
            return $this->upload->data("file_name");
        }

        return $_ENV['default_img'];
    }
}
