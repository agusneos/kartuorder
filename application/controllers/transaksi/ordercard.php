<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ordercard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('transaksi/m_ordercard','record');
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
            $this->load->view('transaksi/v_ordercard');      
        }
    } 
    
    function create()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $img    = addslashes($_POST['img']);
        $data   = $this->record->create();
        
        $query = $this->record->selectLastOrdcard();        
        foreach ($query->result() as $value)
        {
            $image  = $this->record->createImg($value->ordcard_id, $img);
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
    
    function update($ordcard_id=null)
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        if($this->record->update($ordcard_id))
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

        $ordcard_id = addslashes($_POST['ordcard_id']);
        
        $data   = $this->record->delete($ordcard_id);
        $image  = $this->record->deleteImg($ordcard_id);
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

        $ordcard_id = addslashes($_POST['ordcard_id']);
        $query = $this->record->viewImage($ordcard_id);
        
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
    
    function getLot()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        echo $this->record->getLot();
    } 
    
    function getDatePacking()
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

        $ordcard_lot = addslashes($_POST['ordcard_lot']); 
        $query = $this->record->getCustItem($ordcard_lot);
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

        $ordcard_lot = addslashes($_POST['ordcard_lot']); 
        $result = $this->record->cekLot($ordcard_lot);
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
        
        $packing_before = addslashes($_POST['aa']);
        $create_date    = addslashes($_POST['bb']);
        $packing_after  = addslashes($_POST['cc']);
        if($this->record->updateAfter($packing_before, $create_date, $packing_after))
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
        
        $packing_before     = addslashes($_POST['dd']);
        $after_create_date  = addslashes($_POST['ee']);
        $before_create_date = addslashes($_POST['ff']);
        $packing_after      = addslashes($_POST['gg']);
        if($this->record->updateBetween($packing_before, $after_create_date, $before_create_date, $packing_after))
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
                
}

/* End of file ordercard.php */
/* Location: ./application/controllers/transaksi/ordercard.php */