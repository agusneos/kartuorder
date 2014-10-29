<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class M_po extends CI_Model
{    
    static $table   = 'po';
    static $item    = 'item';
    static $cust    = 'cust';
     
    public function __construct() {
        parent::__construct();
      //  $this->load->helper('database'); // Digunakan untuk memunculkan data Enum
    }

    function index()
    {
        $page   = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows   = isset($_POST['rows']) ? intval($_POST['rows']) : 50;
        $offset = ($page-1)*$rows;      
        $sort   = isset($_POST['sort']) ? strval($_POST['sort']) : 'lot_no';
        $order  = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        
        $filterRules = isset($_POST['filterRules']) ? ($_POST['filterRules']) : '';
	$cond = '1=1';
	if (!empty($filterRules)){
            $filterRules = json_decode($filterRules);
            //print_r ($filterRules);
            foreach($filterRules as $rule){
                $rule = get_object_vars($rule);
                $field = $rule['field'];
                $op = $rule['op'];
                $value = $rule['value'];
                if (!empty($value)){
                    if ($op == 'contains'){
                        $cond .= " and ($field like '%$value%')";
                    } else if ($op == 'beginwith'){
                        $cond .= " and ($field like '$value%')";
                    } else if ($op == 'endwith'){
                        $cond .= " and ($field like '%$value')";
                    } else if ($op == 'equal'){
                        $cond .= " and $field = $value";
                    } else if ($op == 'notequal'){
                        $cond .= " and $field != $value";
                    } else if ($op == 'less'){
                        $cond .= " and $field < $value";
                    } else if ($op == 'lessorequal'){
                        $cond .= " and $field <= $value";
                    } else if ($op == 'greater'){
                        $cond .= " and $field > $value";
                    } else if ($op == 'greaterorequal'){
                        $cond .= " and $field >= $value";
                    } 
                }
            }
	}
        
        $this->db->where($cond, NULL, FALSE);
        $this->db->join(self::$item, self::$table.'.po_item='.self::$item.'.item_id', 'left')
                 ->join(self::$cust, self::$table.'.po_cust='.self::$cust.'.cust_id', 'left');
        $this->db->from(self::$table);
        $total  = $this->db->count_all_results();
        
        $this->db->where($cond, NULL, FALSE);
        $this->db->join(self::$item, self::$table.'.po_item='.self::$item.'.item_id', 'left')
                 ->join(self::$cust, self::$table.'.po_cust='.self::$cust.'.cust_id', 'left');
        $this->db->order_by($sort, $order);
        $this->db->limit($rows, $offset);
        $query  = $this->db->get(self::$table);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }
 
        $result = array();
	$result["total"] = $total;
	$result['rows'] = $data;
        
        return json_encode($result);          
    }   
        
    function create()
    {
        $lot_no = $this->input->post('lot_no',true);
        $this->db->where('lot_no', $lot_no);
        $res = $this->db->get(self::$table);
        
        if($res->num_rows == 0)
        {
            return $this->db->insert(self::$table,array(
                'lot_no'=>$lot_no,
                'po_no'=>$this->input->post('po_no',true),
                'po_item'=>$this->input->post('po_item',true),
                'po_date'=>$this->input->post('po_date',true),
                'po_cust'=>$this->input->post('po_cust',true),
                'po_qty'=>$this->input->post('po_qty',true),
                'po_prod'=>$this->input->post('po_prod',true)
            ));
        }
        else
        {
            return false;
        }        
    }
    
    function update($lot_no)
    {
        $this->db->where('lot_no', $lot_no);
        return $this->db->update(self::$table,array(
            'po_no'=>$this->input->post('po_no',true),
            'po_item'=>$this->input->post('po_item',true),
            'po_date'=>$this->input->post('po_date',true),
            'po_cust'=>$this->input->post('po_cust',true),
            'po_qty'=>$this->input->post('po_qty',true),
            'po_prod'=>$this->input->post('po_prod',true)
        ));
    }
    
    function delete($lot_no)
    {
        return $this->db->delete(self::$table, array('lot_no' => $lot_no)); 
    }
    
    function getCust()
    {    
        $this->db->order_by('cust_name', 'asc');
        $query  = $this->db->get(self::$cust);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }       
        return json_encode($data);
    }
    
    function getItem()
    {    
        $this->db->order_by('item_name', 'asc');
        $query  = $this->db->get(self::$item);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }       
        return json_encode($data);
    }
    
    function upload($po_no, $po_item, $po_date, $po_cust,
                        $po_qty, $po_prod, $lot_no)
    {
        date_default_timezone_set('Asia/Jakarta');
        $po_date = date("Y-m-d",($po_date - 25569)*86400);        
        
        $this->db->where('lot_no', $lot_no);
        $res = $this->db->get(self::$table);
        
        if($res->num_rows == 0)
        {
            return $this->db->insert(self::$table,array(
                'lot_no'=>$lot_no,
                'po_no'=>$po_no,
                'po_item'=>$po_item,
                'po_date'=>$po_date,
                'po_cust'=>$po_cust,
                'po_qty'=>$po_qty,
                'po_prod'=>$po_prod
            ));
        }
        else
        {
            return false;
        }
    }
        
}

/* End of file m_po.php */
/* Location: ./application/models/master/m_po.php */