<?php
class CouponModel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
	}
	
	function getList($token, $param)
	{
		$lang = "ko";
		if ($token->language == "zh-TW") $lang = "tw";
		if ($token->language == "zh-CN") $lang = "cn";
		if ($token->language == "ko-KR") $lang = "ko";

		return $this->getTickets($lang, $param);
	}

	function getTickets($lang, $param)
	{
		$this->db->from("tickets");
		$this->db->join('ticket_contents_'.$lang.' tc', 'tc.id = tickets.id');
		$this->db->join('store', 'store.id = tickets.store');
		$this->db->join('store_contents_'.$lang.' sc', 'sc.id = tickets.store');

		if (isset($param["business"])) {
			// Business 지정 시
			$this->db->where("tickets.store in (select store_id from store_business where business_id='".$param["business"]."')");
		}

		if (isset($param["address_top"])) {
			// Business 지정 시
			$this->db->where("tickets.store in (select id from store_contents_".$lang." where address_top like '%".$param["address_top"]."%')");
		}

		if (isset($param["store"])) {
			// Business 지정 시
			$this->db->where("tickets.store = ".$param["store"]);
		}
		
		if (isset($param["query"])) {
			// Business 지정 시
			$this->db->where("tc.title like '%".$param["query"]."%'");
		}

		if (isset($param["type"])) {
			if ($param["type"] == "avail") {
				$this->db->where("close_at >=", date('Y-m-d'));
			}
			else if($param["type"] == "ended") {
				$this->db->where("close_at <=", date('Y-m-d'));
			}
		}
		
		$this->db->order_by("tickets.created", "desc");

		if (isset($param["limit"])) {
			if (isset($param["start"])) {
				$this->db->limit($param["limit"], $param["start"]);
			}
			else {
				$this->db->limit($param["limit"]);
			}
		}
		else {
			$this->db->limit(30);
		}

		$this->db->select("tickets.*, tc.*, ".
			"sc.catchprice, ".
			"sc.company_name, ".
			"store.display_name, ".
			"(select bc.name from business_contents_".$lang." bc, store_business sb where sb.business_id = bc.id and sb.store_id = tickets.store) business_name, ".
			"(select count(*) from user_tickets where ticket_id = tickets.id) downloaded");
		$val = $this->db->get();
		$result = $val->result();
		
		return $result;
	}

	function getTicketsByBest($lang, $param)
	{
		$this->db->from("tickets");
		$this->db->join('ticket_contents_'.$lang.' tc', 'tc.id = tickets.id');
		$this->db->join('store', 'store.id = tickets.store');
		$this->db->join('store_contents_'.$lang.' sc', 'sc.id = tickets.store');
		$this->db->where('tickets.is_best = 1');		
		$this->db->order_by("tickets.best_orderindex", "asc");

		if (isset($param["limit"])) {
			if (isset($param["start"])) {
				$this->db->limit($param["limit"], $param["start"]);
			}
			else {
				$this->db->limit($param["limit"]);
			}
		}
		else {
			$this->db->limit(30);
		}

		$this->db->select("tickets.*, tc.*, ".
			"sc.catchprice, ".
			"sc.company_name, ".
			"store.display_name, ".
			"(select bc.name from business_contents_".$lang." bc, store_business sb where sb.business_id = bc.id and sb.store_id = tickets.store) business_name, ".
			"(select count(*) from user_tickets where ticket_id = tickets.id) downloaded");
		$val = $this->db->get();
		$result = $val->result();
		
		return $result;
	}
	
	function create($param) 
	{
		$param['created'] = date('Y-m-d H:i:s');

		$data1["store"] = $param["store"];
		$data1["password"] = $param["password"];	
		$data1["title"] = $param["title"];
		$data1["start_at"] = $param["start_at"];
		$data1["close_at"] = $param["close_at"];
		$data1["enabled"] = 1;
		$data1["created"] = $param["created"];
		
		if ($this->db->insert("tickets", $data1)) {
			$_id = $this->db->insert_id();
			
			$data2["id"]			= $_id;
			$data2["title"]			= $param["title"];
			$data2["discount"]		= $param["discount"];
//			$data2["description"]	= $param["description"];
			$data2["created"]		= $param["created"];
		
			$this->db->insert("ticket_contents_ko", $data2);	
			$this->db->insert("ticket_contents_cn", $data2);	
			$this->db->insert("ticket_contents_tw", $data2);	
		}
		
		return $_id;
	}
	
	function modify($param, $id) 
	{
		$param['modified'] = date('Y-m-d H:i:s');

		$data1["store"] = $param["store"];
		$data1["password"] = $param["password"];	
		$data1["title"] = $param["title"];
		$data1["start_at"] = $param["start_at"];
		$data1["close_at"] = $param["close_at"];
		$data1["modified"] = $param["modified"];
		
		if ($this->db->update("tickets", $data1, array( "id" => $id ))) {
			
			$data2["title"]			= $param["title"];
			$data2["discount"]		= $param["discount"];
			$data2["description"]	= $param["description"];
			$data2["modified"]		= $param["modified"];
		
			$this->db->update("ticket_contents_".$param["region"], $data2, array( "id" => $id ));	
		}
		
		return $id;
	}
	
	function list_by_mime($id, $lang, $type) 
	{
		$this->db->from("tickets");
		$this->db->join('ticket_contents_'.$lang, 'ticket_contents_'.$lang.'.id = tickets.id');
		$this->db->where("store", $id);
		
		if ($type == "avail") {
			$this->db->where("close_at >=", date('Y-m-d'));
		}
		else if($type == "ended") {
			$this->db->where("close_at <=", date('Y-m-d'));
		}
		
		
		$this->db->order_by("tickets.created", "desc");
		$val = $this->db->get();
		$result = $val->result();
		
		return $result;
	}
	
	function get($id, $lang) 
	{
		$this->db->from("tickets");
		$this->db->join('ticket_contents_'.$lang, 'ticket_contents_'.$lang.'.id = tickets.id');
		$this->db->where("tickets.id", $id);

		$val = $this->db->get();
		$result = $val->result();

		return $result[0];
	}

	function getBusiness($lang, $param)
	{
		$this->db->from("business");
		$this->db->join('business_contents_'.$lang, 'business_contents_'.$lang.'.id = business.id');
		$this->db->where("business.enabled", "1");
		$this->db->order_by("business.order_index", "ASC");
		$this->db->select('*, (select count(*) from store_business where business_id=business.id) count');

		$val = $this->db->get();
		return $val->result();
	}

	function download($token, $id)
	{
		$data["user_id"] = $token->id;
		$data["ticket_id"] = $id;
		$data["usabled"] = "0";
		$data["enabled"] = "1";
		$data["created"] = date('Y-m-d');

		if ($this->db->insert("user_tickets", $data)) {
			$_id = $this->db->insert_id();
		}

		return $_id;
	}

	function hastickets($token, $lang)
	{
		$this->db->from("user_tickets");
		$this->db->join('ticket_contents_'.$lang, 'ticket_contents_'.$lang.'.id = user_tickets.ticket_id');
		$this->db->where("user_tickets.enabled", "1");
		$this->db->order_by("user_tickets.created", "DESC");
		$this->db->select('*');

		$val = $this->db->get();
		return $val->result();

	}
	
}
