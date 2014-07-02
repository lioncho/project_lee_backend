<?php

class Attachfile extends CI_Model
{
	private $table_name	= 'attach_files';

	function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
	}

	function file_by_id($id)
	{
		$this->db->order_by("created_at","desc");
		$this->db->where("id",$id);
		$query = $this->db->get($this->table_name);
		$dat = $query->result();
		return $dat[0];
	}
	
	function create_file($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');

		if ($this->db->insert($this->table_name, $data)) {
			$_id = $this->db->insert_id();
			return $_id;
		}
		return NULL;
	}
	
	function update_file($data, $id)
	{
		$data['updated_at'] = date('Y-m-d H:i:s');

		if ($this->db->update($this->table_name, $data, array('id' => $id))) {
			return $id;
		}
		return NULL;
	}

	function delete_file($id){
		$this->db->where('id', $id);
		$query = $this->db->delete($this->table_name); 
		return $query;
	}
}
?>