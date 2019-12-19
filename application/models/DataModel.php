<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function tampil_data($table)
    {
        return $this->db->get($table);
    }

    function input_data($data, $table)
    {
        $this->db->insert($table, $data);
    }

    function hapus_data($where, $table)
    {
        $this->db->where($where);
        $this->db->delete($table);
    }

    function get_data($where, $table)
    {   
        return $this->db->get_where($table, $where);
    }

    function get_all_data($table)
    {
        return $this->db->get($table);
    }

    function update_data($where, $data, $table)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
    }
}
