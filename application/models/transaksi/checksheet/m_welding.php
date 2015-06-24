<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class M_welding extends CI_Model
{    
    static $table           = 'welding';
    static $po              = 'po';
    static $item            = 'item';
    static $cust            = 'cust';
    static $image           = 'image_welding';
    static $sesdate         = 'session_date_check_sheet_check_sheet';
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
        $sort   = isset($_POST['sort']) ? strval($_POST['sort']) : 'welding_id';
        $order  = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
        
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
        $this->db->join(self::$po, self::$table.'.welding_lot='.self::$po.'.lot_no', 'left')
                 ->join(self::$item, self::$po.'.po_item='.self::$item.'.item_id', 'left')
                 ->join(self::$cust, self::$po.'.po_cust='.self::$cust.'.cust_id', 'left');
        $total  = $this->db->count_all_results(self::$table);
        
        $this->db->where($cond, NULL, FALSE);
        $this->db->join(self::$po, self::$table.'.welding_lot='.self::$po.'.lot_no', 'left')
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
        
    function create($welding_lot, $welding_sub, $welding_date)
    {
        return $this->db->insert(self::$table,array(
            'welding_lot'       => $welding_lot,
            'welding_sub'       => $welding_sub,
            'welding_date'   => $welding_date
        ));
    }
    
    function selectLastOrdcard()
    {
        $this->db->select_max('welding_id');
        return $this->db->get(self::$table);
    }
    
    function createImg($id, $img)
    {
        return $this->db->insert(self::$image,array(
            'id'    => $id,
            'img'   => $img
        ));
    }      
    
    function update($welding_id, $welding_lot, $welding_sub, $welding_date)
    {
        $this->db->where('welding_id', $welding_id);
        return $this->db->update(self::$table,array(
            'welding_lot'       => $welding_lot,
            'welding_sub'       => $welding_sub,
            'welding_date'   => $welding_date
        ));
    }
    
    function delete($welding_id)
    {
        return $this->db->delete(self::$table, array('welding_id' => $welding_id)); 
    }
    
    function deleteImg($welding_id)
    {
        return $this->db->delete(self::$image, array('id' => $welding_id)); 
    }
    
    function viewImage($image)
    {        
        $this->db->select('img');
        $this->db->where('id', $image);
        return $this->db->get(self::$image);        
    }
    
    function getDatePacking()
    {        
        $this->db->select('sesdate');
        return $this->db->get(self::$sesdate);        
    }
       
    function updateSesdate($sesdate)
    {
        return $this->db->update(self::$sesdate,array(
            'sesdate'   => $sesdate
        ));
    }
    
    function cekLot($welding_lot)
    {
        $this->db->select('lot_no');
        $this->db->where('lot_no',$welding_lot);
        return $this->db->get(self::$po);        
    }
    
    function getCustItem($welding_lot)
    {
        $this->db->select('cust_name, item_name');
        $this->db->where('lot_no',$welding_lot);
        return $this->db->get(self::$po_cust_item);        
    }
    
    function updateAfter($welding_before, $create_date, $welding_after)
    {
        $this->db->where('welding_date', $welding_before)
                 ->where('welding_upload >= "'.$create_date.'"');
        return $this->db->update(self::$table,array(
            'welding_date'   => $welding_after
        ));
    }
    
    function updateBetween($welding_before, $after_create_date, $before_create_date, $welding_after)
    {
        $this->db->where('welding_date', $welding_before)
                 ->where('welding_upload BETWEEN "'.$after_create_date.'" AND "'.$before_create_date.'"');
        return $this->db->update(self::$table,array(
            'welding_date'   => $welding_after
        ));
    }
    
    function check($check_date)
    {
        $this->db->where('welding_date', $check_date);
        $total =  $this->db->count_all_results(self::$table);
        
        $result = array();
        $result['success'] = true;
        $result['tgl'] = $check_date;
        $result['total'] = $total;
        return json_encode($result);
    }
}

/* End of file m_welding.php */
/* Location: ./application/models/master/m_welding.php */