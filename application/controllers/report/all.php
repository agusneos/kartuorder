<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class All extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('report/m_all','record');
    }
    
    function index()
    {
        $auth = new Auth();

        $auth->restrict();
        //$auth->cek_menu(14);
        $this->load->view('report/all/v_dialog_all.php');
    }

    function get_all()
    {
        $auth       = new Auth();
         // mencegah user yang belum login untuk mengakses halaman ini
        $auth->restrict();
        
        if (isset($_GET['grid']))
        {
            echo $this->record->get_all($lot, $sublot);      
        }
        else 
        {
            $this->load->view('report/all/v_get_all.php'); 
        }
    }
        
}

/*
* End of file saldo_supplier.php
* Location: ./application/controllers/report/saldo_supplier.php
*/