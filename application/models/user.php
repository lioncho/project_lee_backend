<?php
class User extends CI_Model
{
	private $table_name			= 'users';
	private $table_name_token	= 'user_tokens';

	function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
	}
	
	function get_or_create($data)
	{
		$data["username"] = preg_replace("/[^+0-9]*/s", "", $data["username"]);

		$this->db->where("username", $data["username"]);
		$val = $this->db->get($this->table_name);
		$result = $val->result();
		if (count($result) > 0) {
			$data['modified'] = date('Y-m-d H:i:s');
			$this->db->update($this->table_name, $data, array('id' => $result[0]->id));
			$_id = $result[0]->id;
		}
		else 
		{
			$data['created'] = date('Y-m-d H:i:s');
			if ($this->db->insert($this->table_name, $data)) {
				$_id = $this->db->insert_id();
			}
		}
		return $_id;
	}
	
	function create_token($id, $ip) 
	{
		$data["user_id"] = $id;
		$data["access_token"] = $this->get_unique_token();
		$data["device"]  = "unknown";
		$data["enabled"] = 1;
		$data['created'] = date('Y-m-d H:i:s');
		if ($this->db->insert($this->table_name_token, $data)) {

			$rdata['last_ip'] = $ip; 
			$rdata['last_login'] = date('Y-m-d H:i:s'); 
			$rdata['modified'] = date('Y-m-d H:i:s');
			$this->db->update($this->table_name, $rdata, array('id' => $id));

			return $data["access_token"];
		}
		
		return NULL;
	}
	
	function update_token($data) 
	{
		$data['modified'] = date('Y-m-d H:i:s');
		if ($this->db->update($this->table_name_token, $data, array('access_token' => $data["access_token"]))) {
			return $data["access_token"];
		}
		return NULL;
	}
	
	function get_token($accessToken) {
		$this->db->from($this->table_name_token);
		$this->db->join($this->table_name, "users.id = user_tokens.user_id");
		$this->db->select("users.language, user_tokens.*");
		$this->db->where("access_token", $accessToken["access_token"]);
		$val = $this->db->get();
		$result = $val->result();
		return count($result) > 0 ? $result[0] : NULL;
	}
	
	function get_unique_token() 
	{
		$unique = "";
		while (true) {
			$unique = uniqid('', true);
			$this->db->where("access_token", $unique);
			$val = $this->db->get($this->table_name_token);
			$result = $val->result();
		
			if (!count($result)) break;
		}
		
		return $unique;
	}
}