<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class M_all extends CI_Model
{    
    static $barel           = 'barel';          static $image_barel             = 'image_barel';
    static $bor             = 'bor';            static $image_bor               = 'image_bor';
    static $champer         = 'champer';        static $image_champer           = 'image_champer';
    static $cutting         = 'cutting';        static $image_cutting           = 'image_cutting';
    static $heading         = 'heading';        static $image_heading           = 'image_heading';
    static $ordcard         = 'ordcard';        static $image_ordcard           = 'image_ordcard';
    static $rolling         = 'rolling';        static $image_rolling           = 'image_rolling';
    static $sloting         = 'sloting';        static $image_sloting           = 'image_sloting';
    static $stamping        = 'stamping';       static $image_stamping          = 'image_stamping';
    static $straightening   = 'straightening';  static $image_straightening     = 'image_straightening';
    static $trimming        = 'trimming';       static $image_trimming          = 'image_trimming';
    static $turret          = 'turret';         static $image_turret            = 'image_turret';
    static $washing         = 'washing';        static $image_washing           = 'image_washing';
    static $welding         = 'welding';        static $image_welding           = 'image_welding';
    
    public function __construct() {
        parent::__construct();
        
    }
    
    function get_all($lot, $sublot)
    {
        $query  = $this->db->query('
            SELECT "barel" AS "tabel", barel_id AS "id", barel_upload AS "upload", barel_date AS "date", barel_lot AS "lot", barel_sub AS "sub" FROM barel WHERE barel_lot='.$lot.'AND barel_sub='.$sublot.'
            UNION ALL
            SELECT "bor" AS "tabel", bor_id AS "id", bor_upload AS "upload", bor_date AS "date", bor_lot AS "lot", bor_sub AS "sub" FROM bor WHERE bor_lot='.$lot.'AND bor_sub='.$sublot
            );
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }       
        return json_encode($data);
    }       
    
}

/*
 * End of file m_total_supplier.php
 * Location: ./models/report/m_total_supplier.php
 */