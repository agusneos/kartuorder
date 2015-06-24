<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welding extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('transaksi/checksheet/m_welding','record');
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
            $this->load->view('transaksi/checksheet/v_welding');      
        }
    } 
    
    function create()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $img                = addslashes($_POST['img']);
        $welding_lot        = addslashes($_POST['welding_lot']);
        $welding_sub        = addslashes($_POST['welding_sub']);
        $welding_date    = addslashes($_POST['welding_date']);
        
        $data   = $this->record->create($welding_lot, $welding_sub, $welding_date);
        
        $query = $this->record->selectLastOrdcard();        
        foreach ($query->result() as $value)
        {
            $image  = $this->record->createImg($value->welding_id, $img);
        }        

        if($data && $image)
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }     
    
    function update($welding_id=null)
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $welding_lot        = addslashes($_POST['welding_lot']);
        $welding_sub        = addslashes($_POST['welding_sub']);
        $welding_date    = addslashes($_POST['welding_date']);
        
        if($this->record->update($welding_id, $welding_lot, $welding_sub, $welding_date))
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

        $welding_id = addslashes($_POST['welding_id']);
        
        $data   = $this->record->delete($welding_id);
        $image  = $this->record->deleteImg($welding_id);
        if($data && $image)
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
    
    function viewImage()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $welding_id = addslashes($_POST['welding_id']);
        $query = $this->record->viewImage($welding_id);
        
        if($query)
        {
            foreach ($query->result() as $data)
            {
                echo json_encode(array('success'=>true,'img'=>$data->img));
            }
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
        
    }
    
    function getDateWelding()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $query = $this->record->getDatePacking();        
        foreach ($query->result() as $data)
        {
            echo json_encode(array('sesdate'=>$data->sesdate));
        }
    }
    
    function getCustItem()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $welding_lot = addslashes($_POST['welding_lot']); 
        $query = $this->record->getCustItem($welding_lot);
        foreach ($query->result() as $data)
        {
            echo json_encode(array('customer'=>$data->cust_name,'barang'=>$data->item_name));
        }
    }
    
    function updateSesdate()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $sesdate = addslashes($_POST['sesdate']);
        if($this->record->updateSesdate($sesdate))
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
    
    function cekLot()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $welding_lot = addslashes($_POST['welding_lot']); 
        $result = $this->record->cekLot($welding_lot);
        if($result->num_rows() == 0)
        {            
            echo json_encode(array('success'=>false));
        }
        else
        {
            echo json_encode(array('success'=>true));
        }
    }
    
    function updateAfter()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $welding_before = addslashes($_POST['aa']);
        $create_date    = addslashes($_POST['bb']);
        $welding_after  = addslashes($_POST['cc']);
        if($this->record->updateAfter($welding_before, $create_date, $welding_after))
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
    
    function updateBetween()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $welding_before     = addslashes($_POST['dd']);
        $after_create_date  = addslashes($_POST['ee']);
        $before_create_date = addslashes($_POST['ff']);
        $welding_after      = addslashes($_POST['gg']);
        if($this->record->updateBetween($welding_before, $after_create_date, $before_create_date, $welding_after))
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
    
    function check()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $check_date     = addslashes($_POST['check_date']);
        echo $this->record->check($check_date);  
        
    }
                
}

/* End of file ordercard.php */
/* Location: ./application/controllers/transaksi/ordercard.php */