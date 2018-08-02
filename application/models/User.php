<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Model{
    function __construct() {

    }
    /*
     * get rows from the users table
     */
    function getRows($params = array(),$table){
        $columns = isset($params['columns']) ? $params['columns']:'*';
        $this->db->select($columns);
        $this->db->from($table);
        
        //fetch data by conditions
        if(array_key_exists("conditions",$params)){
            foreach ($params['conditions'] as $key => $value) {
                $this->db->where($key,$value);
            }
        }
        
        if(array_key_exists("id",$params)){
            $this->db->where('id',$params['id']);
            $query = $this->db->get();
            $result = $query->row_array();
        }else{
            //set start and limit
            if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit'],$params['start']);
            }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit']);
            }
            $query = $this->db->get();
            if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
                $result = $query->num_rows();
            }elseif(array_key_exists("returnType",$params) && $params['returnType'] == 'single'){
                $result = ($query->num_rows() > 0)?$query->row_array():FALSE;
            }else{
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }
        
        //return fetched data
        return $result;
    }
    
    /*
     * Insert user information
     */
    public function insert($data = array(),$table) {
        //insert user data to users table
        $insert = $this->db->insert($table, $data);
        
        //return the status
        if($insert){
            return $this->db->insert_id();;
        }else{
            return false;
        }
    }
}