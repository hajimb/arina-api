<?php

class MY_Model extends CI_Model
{

    public $table        = null;
    public $_condition   = array();
    public $_primary_key = null;

    function __construct() {
        parent::__construct($this->table);
    }

    protected function use_table($table) {
        $this->table = $table;
    }

    public function change_table($table) {
        $this->table = $table;
    }

    // For Single Record 

    public function getRows(){
        if(!empty($this->_condition)){
            // print_r($this->_condition);
            $query = $this->db->get_where($this->table, $this->_condition);
            // print($this->db->last_query());
            return $query->result_array();
        }else{
            $query = $this->db->get($this->table);
            return $query->row_array();
        }
    }

    // Select Queries
    function select($tbl = '')
    {
        $query = $this->db->get($tbl);
        return $query->result_array();
    }

    function select_fields( $data='', $single = FALSE, $group_by = '', $order_by = '', $resultArray = false, $status= TRUE)
    {
        if(is_array($data)){
            $this->db->select($data[0],$data[1]);
        }else{
            $this->db->select('*');
        }
        if($status){
            $this->db->where('status',1);
        }
        if($group_by !== ''){
            $this->db->group_by($group_by);
        }
        if($order_by !== ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        $query = $this->db->get($this->table);
        //return $this->db->last_query();
        if ($query) {
            if ($single == TRUE) {
                if($resultArray === false) {
                    return $query->row();
                }elseif($resultArray === true){
                    return $query->row_array();
                }
            } else {
                if($resultArray === false){
                    return $query->result();
                }elseif($resultArray === true){
                    return $query->result_array();
                }
            }
        } else {
            return $this->db->error();
        }
    }
    /**
     * @param string $tbl
     * @param $data
     * @param $where
     * @param bool $single
     * @param string $like
     * @param string $field
     * @param string $value
     * @param string $group_by
     * @param string $order_by
     * @return array
     */
    function select_fields_where( $data, $where, $single = FALSE, $like = '', $field = '', $value = '',$group_by = '',$order_by = '',$array = false)
    {
        if(is_array($data) && isset($data[1])){
            $this->db->select($data[0],$data[1]);
        }else{
            $this->db->select($data);
        }
        $this->db->from($this->table);
        if ($like != '') {
            $this->db->like('LOWER(' . $field . ')', strtolower($value));
            $this->db->like($like);
        }
        $this->db->where($where);
        if($group_by != ''){
            $this->db->group_by($group_by);
        }
        if($order_by !== ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        $query = $this->db->get();
        //return $this->db->last_query();
        if ($query->num_rows() > 0) {
            // query returned results
            if ($single == TRUE) {
                if($array === true){
                    return $query->row_array();
                }else{
                    return $query->row();
                }
            } else {
                if($array === true){
                    return $query->result_array();
                }else{
                    return $query->result();
                }
            }
        } else {
            // query returned no results
            return FALSE;
        }
    }

    function select_fields_where_like($tbl = '', $data='*', $where = '', $single = FALSE, $field, $value,$group_by = '',$order_by = '')
    {

        $this->db->select($data);
        $this->db->from($tbl);
        $this->db->like('LOWER(' . $field . ')', strtolower($value));
        if ($where != '') {
            $this->db->where($where);
        }
        if($group_by != ''){
            $this->db->group_by($group_by);
        }
        if($order_by !== ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        $query = $this->db->get();
        //return $this->db->last_query();
        if ($query->num_rows() > 0) {
        // query returned results
            if ($single == TRUE) {
                return $query->row();
            } else {
                return $query->result();
            }
        } else {
        // query returned no results
            return FALSE;
        }
    }
    function select_fields_where_like_orLikes($tbl = '', $data, $where = '', $single = FALSE, $field, $value, $orLikes = '',$group_by = '')
    {
        $this->db->select($data);
        $this->db->from($tbl);
        $this->db->like('LOWER(' . $field . ')', strtolower($value));
        if($orLikes != '' and is_array($orLikes)){
            foreach($orLikes as $key=>$array){
                $this->db->or_like('LOWER('.$array['field'].')', strtolower($array['value']));
            }
        }
        if ($where != '') {
            $this->db->where($where);
        }
        if($group_by != ''){
            $this->db->group_by($group_by);
        }
        $query = $this->db->get();
        //return $this->db->last_query();
        if ($query->num_rows() > 0) {
        // query returned results
            if ($single == TRUE) {
                return $query->row();
            } else {
                return $query->result();
            }
        } else {
        // query returned no results
            return FALSE;
        }
    }
    function select_fields_where_like_join($tbl = '', $data, $joins = '', $where = '', $single = FALSE, $field = '', $value = '',$group_by='',$order_by = '',$limit = '',$array = false)
    {
        if (is_array($data) and isset($data[1])){
            $this->db->select($data[0],$data[1]);
        }else{
            $this->db->select($data);
        }
        $this->db->from($tbl);
        //Fourth Param is For Escaping, i-e CI should use backTicks on a join or not.
        if ($joins != '') {
            foreach ($joins as $k => $v) {
                $this->db->join($v['table'], $v['condition'], $v['type'],(isset($v['escape']) ? $v['escape'] : TRUE));
            }
        }
        if ($value !== '') {
            $this->db->like('LOWER(' . $field . ')', strtolower($value));
        }
        if ($where != '') {
            $this->db->where($where);
        }
        if($group_by != ''){
            $this->db->group_by($group_by);
        }
        if($order_by != ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        if($limit != ''){
            if(is_array($limit)){
                $this->db->limit($limit[0],$limit[1]);
            }else{
                $this->db->limit($limit);
            }
        }
        $query = $this->db->get();
        if($query === FALSE ){
        //  echo 'Database Error(' . $this->db->_error_number() . ') - ' . $this->db->_error_message();
            return FALSE;
        }
        if ($query->num_rows() !== NULL && $query->num_rows() > 0) {
            if ($single == TRUE) {
                if($array === true){
                    return $query->row_array();
                }else{
                    return $query->row();
                }
            } else {
                if($array === true) {
                    return $query->result_array();
                }else{
                    return $query->result();
                }
            }
        } else {
            return FALSE;
        }
    }

    function get_all_where($where = array(), $limit = 1000000, $offset = 0,$order_by = '') {
        $where_in = get_array_value($where, "where_in");
        if ($where_in) {
            foreach ($where_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
            unset($where["where_in"]);
        }
        if($order_by){
            $this->db->order_by($order_by, 'DESC');
        }
        $query= $this->db->get_where($this->table, $where, $limit, $offset);
        return $query->result_array();
    }
    function select_fields_where_like__orLikes_join($tbl = '', $data, $joins = '', $where = '', $single = FALSE, $field = '', $value = '', $orLikes = '', $group_by='',$order_by = '',$limit = '')
    {
        if (is_array($data) and isset($data[1])){
            $this->db->select($data[0],$data[1]);
        }else{
            $this->db->select($data);
        }
        $this->db->from($tbl);
        if ($joins != '') {
            foreach ($joins as $k => $v) {
                $this->db->join($v['table'], $v['condition'], $v['type']);
            }
        }
        if ($value !== '') {
            $this->db->like('LOWER(' . $field . ')', strtolower($value));
        }
        if($orLikes != '' and is_array($orLikes)){
            foreach($orLikes as $key=>$array){
                $this->db->or_like('LOWER('.$array['field'].')', strtolower($array['value']));
            }
        }
        if ($where != '') {
            $this->db->where($where);
        }
        if($group_by != ''){
            $this->db->group_by($group_by);
        }
        if($order_by != ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        if($limit != ''){
            if(is_array($limit)){
                $this->db->limit($limit[0],$limit[1]);
            }else{
                $this->db->limit($limit);
            }
        }
        $query = $this->db->get();
            // return $this->db->last_query();
            //return $this->db->last_query();
        if ($query->num_rows() > 0) {
            // query returned results
            if ($single == TRUE) {
                return $query->row();
            } else {
                return $query->result();
            }
        } else {
            // query returned no results
            return FALSE;
        }
    }
    
    //Common AutoComplete Queries
    function get_autoComplete($tbl, $data, $field, $value, $where = '', $group_by = false, $limit = '')
    {
        $this->db->select($data);
        $this->db->from($tbl);
        if ($where != '') {
            $this->db->where($where);
        }
        $this->db->like('LOWER(' . $field . ')', strtolower($value));
        if ($group_by == true) {
            $this->db->group_by($field);
        }
        if ($limit != '') {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query->result();
    }
    function get_autoCompleteJoin($PTable, $joins = '', $where = '', $data, $field, $value, $group_by = false)
    {
        $this->db->select($data);
        $this->db->from($PTable);
        if ($joins != "") {
            foreach ($joins as $k => $v) {
                $this->db->join($v['table'], $v['condition'], $v['type']);
            }
        }
        if ($where != '') {
            $this->db->where($where);
        }
        $this->db->like('LOWER(' . $field . ')', strtolower($value));
        if ($group_by == true) {
            $this->db->group_by($field);
        }
        $query = $this->db->get();
//echo $this->db->last_query();
        return $query->result();
    }
    //------------------------ insert record queries -----------------------------------
    // function insert($tbl, $data)
    // {
    //     $this->db->insert($tbl, $data);
    //     return $this->db->insert_id();
    // }

    //Insert Record If Don't Exist Else Update the Record
    // function insert($tbl, $data, $field, $id,$where){
    function insert($data, $id){
        if ( $id > 0 ) {
            $exist =  $this->ifexist($id);
            if($exist > 0){
                return  array('status' => false, 'msg' => ' Already Exists!');
            }else{
                $this->db->where($this->_primary_key ,$id);
                $this->db->update($this->table,$data);
                $affectedRows = $this->db->error();
                if($affectedRows['code']==0){
                    return array('status' => true, 'msg' => ' Successfully Updated!', 'id' => $id);
                }else{
                    // print $this->db->last_query();
                    return array('status' => false, 'msg' => ' Error On Updating!', 'id' => '');
                }

            }
        } else {
            $exist =  $this->ifexist($id);
            // print $this->db->last_query();exit;
            if($exist > 0){
                return array('status' => false, 'msg' => ' Already Exists!', 'id' => '');
            }else{
                $this->db->insert($this->table,$data);
                $insertedID = $this->db->insert_id();
                if($insertedID > 0){
                    return array('status' => true, 'msg' => ' Successfully Added!', 'id' => $insertedID);
                }else{
                    return array('status' => false, 'msg' => ' Error On Adding!', 'id' => '');
                }
            }
        }
    }
    function insert_multiple($tbl, $data)
    {
        $query = $this->db->insert_batch($tbl, $data);
        return $query;
    }

    /**
     * @param $tbl
     * @param $data
     * @return bool
     */
    public function ifexist($id){
        if(isset($this->_condition) && !empty($this->_condition)){
            $this->db->select('*');
            $this->db->from($this->table);
            $this->db->where($this->_condition);
            if($id > 0){
                $this->db->where($this->_primary_key.'!=',$id);
            }
            
            $query = $this->db->get();
            
            return $query->num_rows();
        }else{
            return 0;
        }
    }

    /**
     * @param $tbl
     * @param $data
     * @return bool
     */
    function insert_multiple_ignore_duplicate($tbl, $data){
        $this->db->trans_start();
        foreach ($data as $item) {
            $insert_query = $this->db->insert_string($tbl, $item);
            $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
            $this->db->query($insert_query);
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    function getRowsCondition($column='',$conditiontype='',$value='')
    {

        $this->db->select('*');
        $this->db->from($this->table);
        // $this->db->where($column);
        if(!empty($this->_condition)){
            $this->db->where($this->_condition);

        }
        $query = $this->db->get();
        return $query->result_array();
    }
    //------------------------End of insert record queries -----------------------------------
    // Common Update Queries
    function update($data)
    {
        $this->db->where($this->_condition);
        $this->db->update($this->table, $data);
        $error = $this->db->error();
        if ($error['code']==0) {
            return array('status' => true, 'msg' => 'Successfully Updated!');
        } else {
            return array('status' => fale, 'msg' => 'Erroir On Updating!');
        }
    }

    function update_query($tbl, $field, $id, $data)
    {
        $this->db->where($field, $id);
        $this->db->update($tbl, $data);
        $affectedRows = $this->db->affected_rows();
        if ($affectedRows) {
            return TRUE;
        } else {
            return $this->db->error();
        }
    }

    function delete($temp=TRUE, $id=0)
    {
        // echo 'id' .$id;exit();
        if($id > 0)
        {
            $this->db->where($this->_primary_key,$id);
        }
        if($temp == FALSE){
            $data['deleted'] = 1;
            $this->db->update($this->table,$data);
            
        }else{
            $this->db->delete($this->table);
        }
        // print $this->db->last_query();
        return $this->db->error();
    }
    function update_query_array($tbl, $fields, $data)
    {
        $this->db->where($fields);
        $this->db->update($tbl, $data);
        $afftectedRows = $this->db->affected_rows();
        if ($afftectedRows) {
            return $afftectedRows;
        } else {
            return $this->db->_error_message();
        }
    }
    //Common Update Queries End
    //Common DataTables Queries
    function  select_fields_joined_DTM($data, $PTable, $joins = '', $where = '',$where_in_column ='', $where_in_data = '', $group_by = '', $addColumn = '', $editColumn = '',$unsetColumn = '')
    {
        if(is_array($PTable)){
           
            if(is_array($data)){
                $this->datatables->select($data[0],$data[1]);
            }else{
                $this->datatables->select($data);
            }
            if ($unsetColumn != '') {
                $this->datatables->unset_column($unsetColumn);
            }
            $this->datatables->from($PTable);
            if ($joins != '') {
                foreach ($joins as $k => $v) {
                    $this->datatables->join($v['table'], $v['condition'], $v['type']);
                }
            }
            if ($where != '') {
                if(is_array($where) && array_key_exists("QueryCondition",$where)){
                    $this->datatables->where($where["QueryCondition"],$where[0],$where[1]);
                }else{
                    $this->datatables->where($where);
                }
            }
            if($where_in_data !== ''){
                $this->datatables->where_in($where_in_column,$where_in_data);
            }
            if($group_by != ''){
                $this->datatables->group_by($group_by);
            }
            if ($addColumn != '') {
                foreach($addColumn as $columnKey=>$columnValue ) {
                    if(!is_array($columnValue) && is_string($columnValue)){
                        $this->datatables->add_column($columnKey, $columnValue);
                    }else{
                        $this->datatables->add_column($columnKey, $columnValue[0],$columnValue[1]);
                    }
                }
            }
            if ($editColumn != ''){
                foreach($editColumn as $arrayKey=>$arrayValue){
                    $this->datatables->edit_column($arrayValue[0],$arrayValue[1],$arrayValue[2]);
                }
            }
            $result = $this->datatables->generate();
        }else{
            $result = $this->select_fields_joined_DT($data, $PTable, $joins, $where ,$where_in_column, $where_in_data , $group_by , $addColumn , $editColumn ,$unsetColumn);
        }
        return $result;
    }
    function  select_fields_joined_DT($data, $PTable, $joins = '', $where = '',$where_in_column ='', $where_in_data = '', $group_by = '', $addColumn = '', $editColumn = '',$unsetColumn = '')
    {
        if(is_array($data)){
            $this->datatables->select($data[0],$data[1]);
        }else{
            $this->datatables->select($data);
        }
        if ($unsetColumn != '') {
            $this->datatables->unset_column($unsetColumn);
        }
        $this->datatables->from($PTable);
        if ($joins != '') {
            foreach ($joins as $k => $v) {
                $this->datatables->join($v['table'], $v['condition'], $v['type']);
            }
        }
        if($where != '') {
            if(is_array($where) && array_key_exists("QueryCondition",$where)){
                $this->datatables->where($where["QueryCondition"],$where[0],$where[1]);
            }else{
                $this->datatables->where($where);
            }
        }
        if($where_in_data !== ''){
            $this->datatables->where_in($where_in_column,$where_in_data);
        }
        if($group_by != ''){
            $this->datatables->group_by($group_by);
        }
        if($addColumn != '') {
            foreach($addColumn as $columnKey=>$columnValue ) {
                if(!is_array($columnValue) && is_string($columnValue)){
                    $this->datatables->add_column($columnKey, $columnValue);
                }else{
                    $this->datatables->add_column($columnKey, $columnValue[0],$columnValue[1]);
                }
            }
        }
        if($editColumn != ''){
            foreach($editColumn as $arrayKey=>$arrayValue){
                $this->datatables->edit_column($arrayValue[0],$arrayValue[1],$arrayValue[2]);
            }
        }
        $result = $this->datatables->generate();
        return $result;
    }
    function  select_fields_joined_DTO($data, $PTable, $joins = '', $where = '',$where_in_column ='', $where_in_data = '', $group_by = '',$order_by = '', $addColumn = '', $editColumn = '',$unsetColumn = '')
    {
        if(is_array($data)){
            $this->datatables->select($data[0],$data[1]);
        }else{
            $this->datatables->select($data);
        }
        if ($unsetColumn != '') {
            $this->datatables->unset_column($unsetColumn);
        }
        $this->datatables->from($PTable);
        if ($joins != '') {
            foreach ($joins as $k => $v) {
                $this->datatables->join($v['table'], $v['condition'], $v['type']);
            }
        }
        if($where != '') {
            if(is_array($where) && array_key_exists("QueryCondition",$where)){
                $this->datatables->where($where["QueryCondition"],$where[0],$where[1]);
            }else{
                $this->datatables->where($where);
            }
        }
        if($where_in_data !== ''){
            $this->datatables->where_in($where_in_column,$where_in_data);
        }
        if($order_by != ''){
            $this->datatables->order_by($order_by);
        }
        if($group_by != ''){
            $this->datatables->group_by($group_by);
        }
        if($addColumn != '') {
            foreach($addColumn as $columnKey=>$columnValue ) {
                if(!is_array($columnValue) && is_string($columnValue)){
                    $this->datatables->add_column($columnKey, $columnValue);
                }else{
                    $this->datatables->add_column($columnKey, $columnValue[0],$columnValue[1]);
                }
            }
        }
        if($editColumn != ''){
            foreach($editColumn as $arrayKey=>$arrayValue){
                $this->datatables->edit_column($arrayValue[0],$arrayValue[1],$arrayValue[2]);
            }
        }
        $result = $this->datatables->generate();
        return $result;
    }
    //Importing of File Using Import Library Added To The Application..
    function Import_FileImportLibrary_CSV($table,$data){
        $this->db->insert($table, $data);
    }

    
    // Function for run query
    function get_query_record($query)
    {
        $result=$this->db->query($query);
        if($result->num_rows() > 0)
        {
            return $result;
        }
    }
    // End
    //Function For DropDowns. Updating The Classic Style From Parexons Company.
    function classic_dropdown($table, $option, $key, $value, $where){
        //Lets Put Little Checks On Required Parameters
        if(!isset($table) || empty($table)){
            return false;
        }
        if(!isset($option) || empty($option)){
            return false;
        }
        if(!isset($key) || empty($key)){
            return false;
        }
        if(!isset($value) || empty($value)){
            return false;
        }
        $this->db->select('*');
        $this->db->from($table);
        if($where !== ''){
            $this->db->where($where);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $data[''] = $option;
            foreach($query->result() as $row)
            {
                $data[$row->$key] = $row->$value;
            }
            return $data;
        }
    }
    /// Dynamic Function For Drop Down With Select Option
    public  function dropdown_wd_option($table, $option, $key, $value, $where)
    {
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get($table);
        if($query->num_rows() > 0)
        {
            $data[''] = $option;
            foreach($query->result() as $row)
            {
                $data[$row->$key] = $row->$value;
            }
            return $data;
        }
    }
    public function insert_else_ignore($table,$data)
    {
        $query = $this->db->get_where($table,$data);
        $count = $query->num_rows(); //counting result from query
        if ($count === 0) {
            $this->db->insert($table, $data);
            return true;
        }
    }
    public function get_row_where($table,$data)
    {
        $this->db->where($data);
        $query = $this->db->get($table);
        //echo "<pre> query = "; print_r( $query  ); echo "</pre>";
        if($query->num_rows() > 0) //counting result from query
        {
            return true;
        }
    }
    function get_row_order_desc($tbl = '', $data, $single = FALSE,$order_by = '')
    {
        $this->db->select($data);
        $this->db->from($tbl);
        if($order_by !== ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        $query = $this->db->get();
//return $this->db->last_query();
        if ($query->num_rows() > 0) {
// query returned results
            if ($single == TRUE) {
                return $query->row();
            } else {
                return $query->result();
            }
        } else {
// query returned no results
            return FALSE;
        }
    }
    public function get_record_where($where)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        if($query->num_rows() > 0) //counting result from query
        {
            return $query->row();
        }else{
            return false;
        }
    }
    //End of Common DataTables Queries
}