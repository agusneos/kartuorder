<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class M_ordercard extends CI_Model
{    
    static $table           = 'ordcard';
    static $po              = 'po';
    static $item            = 'item';
    static $cust            = 'cust';
    static $image           = 'image_ordcard';
    static $sesdate         = 'session_date';
    static $po_cust_item    = 'po_cust_item';
     
    public function __construct() {
        parent::__construct();
      //  $this->load->helper('database'); // Digunakan untuk memunculkan data Enum
    }

    function index()
    {
        $page   = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows   = isset($_POST['rows']) ? intval($_POST['rows']) : 50;
        $offset = ($page-1)*$rows;      
        $sort   = isset($_POST['sort']) ? strval($_POST['sort']) : 'ordcard_id';
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
        $this->db->join(self::$po, self::$table.'.ordcard_lot='.self::$po.'.lot_no', 'left')
                 ->join(self::$item, self::$po.'.po_item='.self::$item.'.item_id', 'left')
                 ->join(self::$cust, self::$po.'.po_cust='.self::$cust.'.cust_id', 'left');
        $this->db->from(self::$table);
        $total  = $this->db->count_all_results();
        
        $this->db->where($cond, NULL, FALSE);
        $this->db->join(self::$po, self::$table.'.ordcard_lot='.self::$po.'.lot_no', 'left')
                 ->join(self::$item, self::$po.'.po_item='.self::$item.'.item_id', 'left')
                 ->join(self::$cust, self::$po.'.po_cust='.self::$cust.'.cust_id', 'left');
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
        $img = $this->input->post('img',true);
        return $this->db->insert(self::$table,array(
            'ordcard_packing'=>$this->input->post('ordcard_packing',true),
            'ordcard_lot'=>$this->input->post('ordcard_lot',true),
            'ordcard_sub'=>$this->input->post('ordcard_sub',true)
        ));
    }
    
    function selectLastOrdcard()
    {
        $this->db->select_max('ordcard_id');
        return $this->db->get(self::$table);
    }
    
    function createImg($id, $img)
    {
        return $this->db->insert(self::$image,array(
            'id'=>$id,
            'img'=>$img
        ));
    }      
    
    function update($ordcard_id)
    {
        $this->db->where('ordcard_id', $ordcard_id);
        return $this->db->update(self::$table,array(
            'ordcard_packing'=>$this->input->post('ordcard_packing',true),
            'ordcard_lot'=>$this->input->post('ordcard_lot',true),
            'ordcard_sub'=>$this->input->post('ordcard_sub',true)
        ));
    }
    
    function delete($ordcard_id)
    {
        return $this->db->delete(self::$table, array('ordcard_id' => $ordcard_id)); 
    }
    
    function deleteImg($ordcard_id)
    {
        return $this->db->delete(self::$image, array('id' => $ordcard_id)); 
    }
    
    function viewImage($image)
    {        
        $this->db->select('img');
        $this->db->where('id', $image);
        return $this->db->get(self::$image);        
    }
    
    function getLot()
    {    
        $this->db->order_by('lot_no', 'asc');
        $query  = $this->db->get(self::$po);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }       
        return json_encode($data);
    }
    
    function getDatePacking()
    {        
        $this->db->select('sesdate');
        return $this->db->get(self::$sesdate);        
    }
       
    function updateSesdate($sesdate)
    {
        return $this->db->update(self::$sesdate,array(
            'sesdate'=>$sesdate
        ));
    }
    
    function cekLot($ordcard_lot)
    {
        $this->db->select('lot_no');
        $this->db->where('lot_no',$ordcard_lot);
        return $this->db->get(self::$po);        
    }
    
    function getCustItem($ordcard_lot)
    {
        $this->db->select('cust_name, item_name');
        $this->db->where('lot_no',$ordcard_lot);
        return $this->db->get(self::$po_cust_item);        
    }
    
    function updateAfter($packing_before, $create_date, $packing_after)
    {
        $this->db->where('ordcard_packing', $packing_before)
                 ->where('ordcard_upload >= "'.$create_date.'"');
        return $this->db->update(self::$table,array(
            'ordcard_packing'=>$packing_after
        ));
    }
    
    function updateBetween($packing_before, $after_create_date, $before_create_date, $packing_after)
    {
        $this->db->where('ordcard_packing', $packing_before)
                 ->where('ordcard_upload BETWEEN "'.$after_create_date.'" AND "'.$before_create_date.'"');
        return $this->db->update(self::$table,array(
            'ordcard_packing'=>$packing_after
        ));
    }
}

/* End of file m_ordcard.php */
/* Location: ./application/models/master/m_ordcard.php */