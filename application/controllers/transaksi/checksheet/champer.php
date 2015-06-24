<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Champer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('transaksi/checksheet/m_champer','record');
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
            $this->load->view('transaksi/checksheet/v_champer');      
        }
    } 
    
    function create()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $img                = addslashes($_POST['img']);
        $champer_lot        = addslashes($_POST['champer_lot']);
        $champer_sub        = addslashes($_POST['champer_sub']);
        $champer_date    = addslashes($_POST['champer_date']);
        
        $data   = $this->record->create($champer_lot, $champer_sub, $champer_date);
        
        $query = $this->record->selectLastOrdcard();        
        foreach ($query->result() as $value)
        {
            $image  = $this->record->createImg($value->champer_id, $img);
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
    
    function update($champer_id=null)
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $champer_lot        = addslashes($_POST['champer_lot']);
        $champer_sub        = addslashes($_POST['champer_sub']);
        $champer_date    = addslashes($_POST['champer_date']);
        
        if($this->record->update($champer_id, $champer_lot, $champer_sub, $champer_date))
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

        $champer_id = addslashes($_POST['champer_id']);
        
        $data   = $this->record->delete($champer_id);
        $image  = $this->record->deleteImg($champer_id);
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

        $champer_id = addslashes($_POST['champer_id']);
        $query = $this->record->viewImage($champer_id);
        
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
    
    function getDateChamper()
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

        $champer_lot = addslashes($_POST['champer_lot']); 
        $query = $this->record->getCustItem($champer_lot);
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

        $champer_lot = addslashes($_POST['champer_lot']); 
        $result = $this->record->cekLot($champer_lot);
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
        
        $champer_before = addslashes($_POST['aa']);
        $create_date    = addslashes($_POST['bb']);
        $champer_after  = addslashes($_POST['cc']);
        if($this->record->updateAfter($champer_before, $create_date, $champer_after))
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
        
        $champer_before     = addslashes($_POST['dd']);
        $after_create_date  = addslashes($_POST['ee']);
        $before_create_date = addslashes($_POST['ff']);
        $champer_after      = addslashes($_POST['gg']);
        if($this->record->updateBetween($champer_before, $after_create_date, $before_create_date, $champer_after))
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