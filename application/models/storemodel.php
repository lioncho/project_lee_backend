<?php
class StoreModel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
	}
	
	function signin($param)
	{
		$this->db->where("username", $param["username"]);
		$this->db->where("password", $param["password"]);
		$this->db->where("enabled", "1");
		$val = $this->db->get("store");
		$result = $val->result();
		
		if (count($result) > 0) return $result[0];
		else return NULL;
		
	}
	
	function get($param)
	{
		$this->db->from("store");
		$this->db->join('store_contents_'.$param["lang_code"], 'store_contents_'.$param["lang_code"].'.id = store.id');
		$this->db->where("store.username", $param["username"]);
		$this->db->where("store.password", $param["password"]);
		$this->db->where("store.enabled", "1");
		
		$val = $this->db->get();
		$result = $val->result();
		
		if (count($result) > 0) return $result[0];
		else return NULL;
		
	}
	
	function get_by_id($id, $lang)
	{
		$this->db->from("store");
		$this->db->join('store_contents_'.$lang, 'store_contents_'.$lang.'.id = store.id');
		$this->db->where("store.id", $id);
		$this->db->where("store.enabled", "1");
		
		$val = $this->db->get();
		$result = $val->result();
		
		if (count($result) > 0) return $result[0];
		else return NULL;
		
	}
	
	function already_join($userid) 
	{
		$this->db->where("username", $userid);
		$this->db->where("enabled", "1");
		$val = $this->db->get("store");
		$result = $val->result();
		
		return count($result);
	}
	
	function already_join_contents($userid, $region) 
	{
		$this->db->where("id", $userid);
		$val = $this->db->get("store_contents_".$region);
		$result = $val->result();
		
		return count($result);
	}
	
	function create($param) 
	{
		$param["activated"] = 0;
		$param['created'] = date('Y-m-d H:i:s');
		if ($this->db->insert("store", $param)) {
			$_id = $this->db->insert_id();
		}
		
		return $_id;
	}
	
	function create_manager($param)
	{
		$data1["type"] 			= "V";
		$data1["username"] 		= $param["username"];
		$data1["password"] 		= $param["password"];
		$data1["display_name"] 	= $param["display_name"];
		
		$data1["activated"] 	= 0;
		$data1["enabled"] 		= 1;
		$data1['created'] 		= date('Y-m-d H:i:s');
		
		if ($this->db->insert("store", $data1)) {
			$_id = $this->db->insert_id();
			
			$data2["id"] 			= $_id;
			$data2["address"]		= $param["address"];
			$data2["phone"]			= $param["phone"];
			$data2["email"]			= $param["email"];
			$data2["website"]		= $param["website"];
			$data2["member_type"]	= $param["member_type"];
		
			$this->db->insert("store_vender", $data2);	
		}
		
		return $_id;
	}
		
	function create_detail($param, $region) 
	{
		if ($this->already_join_contents($param["id"], $region)) {
			$param['modified'] = date('Y-m-d H:i:s');
			if ($this->db->update("store_contents_".$region, $param, array('id' => $param["id"]))) {
				$_id = $param["id"];
			}
		}
		else {
			$param['created'] = date('Y-m-d H:i:s');
			if ($this->db->insert("store_contents_".$region, $param)) {
				$_id = $param["id"];
				$this->db->update("store", array(
					'enabled' => '1', 
					'activated' => '1'
				), array(
					'id' => $param["id"]
				));
			}
		}
		
		return $_id;
	}
	
	function update($id, $param) 
	{
		$param['modified'] = date('Y-m-d H:i:s');
		if ($this->db->update("store", $param, array('id' => $id))) {
			$_id = $id;
		}
		
		return $_id;
	}

	function getPosition($lang, $param)
	{
		$tbn = "store_contents_".$lang;

		$this->db->from("store");
		$this->db->join($tbn, $tbn.'.id = store.id');
		$this->db->where("store.enabled", "1");
		$this->db->group_by($tbn.".address_top");
		$this->db->select($tbn.'.address_top, count(*) count');

		$val = $this->db->get();
		return $val->result();
	}
}