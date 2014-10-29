<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('master/m_customer','record');
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
            $this->load->view('master/v_customer'); 
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
    
    function update($cust_id=null)
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        if($this->record->update($cust_id))
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

        $cust_id = addslashes($_POST['cust_id']);
        
        if($this->record->delete($cust_id))
        {
            echo json_encode(array('success'=>true));
        }
        else
        {
            echo json_encode(array('success'=>false));
        }
    }
    
    function upload()
    {
        $auth   = new Auth();
        $auth->restrict();
        
        move_uploaded_file($_FILES["fileb"]["tmp_name"],
                "assets/temp_upload/" . $_FILES["fileb"]["name"]);
        $this->load->library('excel_reader');
        $this->excel_reader->setOutputEncoding('CP1251');
        $this->excel_reader->read('assets/temp_upload/' . $_FILES["fileb"]["name"]);
        error_reporting(E_ALL ^ E_NOTICE);
        
        // Get the contents of the first worksheet
        $data = $this->excel_reader->sheets[0];
        
        // jumlah baris
        $baris  = $data['numRows'];
        $ok = 0;
        $ng = 0;
        
        for ($i = 1; $i <= $baris; $i++)
        {
           $cust_id   = $data['cells'][$i][1];
           $cust_name = $data['cells'][$i][2];
           
           $query   = $this->record->upload($cust_id, $cust_name);
           if ($query)
           {
               $ok++;
           }
           else
           {
               $ng++;
           }
        }
        unlink('assets/temp_upload/' . $_FILES["fileb"]["name"]);
        echo json_encode(array('success'=> true,
                                'total' => 'Total Data: '.($baris),
                                'ok'    => 'Data OK: '.$ok,
                                'ng'    => 'Data NG: '.$ng));
    }
                
}

/* End of file customer.php */
/* Location: ./application/controllers/master/customer.php */