<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Po extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('master/m_po','record');
    }
    
    function index()
    {
        $auth       = new Auth();
         // mencegah user yang belum login untuk mengakses halaman ini
        $auth->restrict();
        
        if (isset($_GET['grid'])) 
        {
            echo $this->record->index(); 
        }
        else 
        {
            $this->load->view('master/v_po');  
        }
    } 
    
    function create()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        if($this->record->create())
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }     
    
    function update($lot_no=null)
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        if($this->record->update($lot_no))
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
        
    function delete()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $lot_no = addslashes($_POST['lot_no']);
        
        if($this->record->delete($lot_no))
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
    
    function getCust()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        echo $this->record->getCust();
    }   
    
    function getItem()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        echo $this->record->getItem();
    }  
}

/* End of file po.php */
/* Location: ./application/controllers/master/po.php */