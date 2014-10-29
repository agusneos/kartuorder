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
    
    function upload()
    {
        $auth   = new Auth();
        $auth->restrict();
        
        move_uploaded_file($_FILES["fileu"]["tmp_name"],
                "assets/temp_upload/" . $_FILES["fileu"]["name"]);
        $this->load->library('excel_reader');
        $this->excel_reader->setOutputEncoding('CP1251');
        $this->excel_reader->read('assets/temp_upload/' . $_FILES["fileu"]["name"]);
        error_reporting(E_ALL ^ E_NOTICE);
        
        // Get the contents of the first worksheet
        $data = $this->excel_reader->sheets[0];
        
        // jumlah baris
        $baris  = $data['numRows'];
        $ok = 0;
        $ng = 0;
        
        for ($i = 1; $i <= $baris; $i++)
        {
           $po_no   = $data['cells'][$i][1];
           $po_item = $data['cells'][$i][2];
           $po_date = $data['cells'][$i][3];
           $po_cust = $data['cells'][$i][4];
           $po_qty  = $data['cells'][$i][5];
           $po_prod = $data['cells'][$i][6];
           $lot_no  = $data['cells'][$i][7];
           
           $query   = $this->record->upload($po_no, $po_item, $po_date, $po_cust,
                                        $po_qty, $po_prod, $lot_no);
           if ($query)
           {
               $ok++;
           }
           else
           {
               $ng++;
           }
        }
        unlink('assets/temp_upload/' . $_FILES["fileu"]["name"]);
        echo json_encode(array('success'=>true,
                                'total'=>'Total Data: '.($baris),
                                'ok'=>'Data OK: '.$ok,
                                'ng'=>'Data NG: '.$ng));
    }
}

/* End of file po.php */
/* Location: ./application/controllers/master/po.php */