<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupon extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->model('User');
		$this->load->model('CouponModel');
		
		$param = $this->input->get(NULL, TRUE);
		$token = $this->User->get_token($param);
		
		if (!$token) {
			$result["success"] = FALSE;
			$result["message"] = "access_denined : token validate failure";
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		else {
			$coupons = $this->CouponModel->getList($token, $param);

			$result["success"] = TRUE;
			$result["data"] = $coupons;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
	}
	
	public function by_store($id)
	{
		$this->load->model('User');
		$this->load->model('CouponModel');
		
		$param = $this->input->get_post(NULL, TRUE);

		if (isset($param["access_token"]))
			$token = $this->User->get_token($param);
		else
			$token = NULL;

		$lang = "ko";
		if (isset($param["lang"])) {
			$lang = $param["lang"];
		}
		else if($token) {
			$lang = "ko";
			if ($token->language == "zh-TW") $lang = "tw";
			if ($token->language == "zh-CN") $lang = "cn";
			if ($token->language == "ko-KR") $lang = "ko";
		}

		$param["store"] = $id;
		
		$coupons = $this->CouponModel->getTickets($lang, $param);

		$result["success"] = TRUE;
		$result["data"] = $coupons;
	
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function by_best()
	{
		$this->load->model('User');
		$this->load->model('CouponModel');
		
		$param = $this->input->get_post(NULL, TRUE);
		if (isset($param["access_token"]))
			$token = $this->User->get_token($param);
		else
			$token = NULL;
		

		if (isset($param["lang"])) {
			$lang = $param["lang"];
		}
		else if($token) {
			$lang = "ko";
			if ($token->language == "zh-TW") $lang = "tw";
			if ($token->language == "zh-CN") $lang = "cn";
			if ($token->language == "ko-KR") $lang = "ko";
		}

		$coupons = $this->CouponModel->getTicketsByBest($lang, $param);

		$result["success"] = TRUE;
		$result["data"] = $coupons;
	
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function business()
	{
		$this->load->model('User');
		$this->load->model('CouponModel');
		
		$param = $this->input->get_post(NULL, TRUE);
		if (isset($param["access_token"]))
			$token = $this->User->get_token($param);
		else
			$token = NULL;
		
		if (!$token && !isset($param["lang"])) {
			$result["success"] = FALSE;
			$result["message"] = "access_denined : token validate failure";
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		else {
			if (isset($param["lang"])) {
				$lang = $param["lang"];
			}
			else {
				$lang = "ko";
				if ($token->language == "zh-TW") $lang = "tw";
				if ($token->language == "zh-CN") $lang = "cn";
				if ($token->language == "ko-KR") $lang = "ko";
			}

			$business = $this->CouponModel->getBusiness($lang, $param);

			$result["success"]	= TRUE;
			$result["data"]		= $business;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
	}
	
	public function position()
	{
		$this->load->model('User');
		$this->load->model('StoreModel');
		
		$param = $this->input->get_post(NULL, TRUE);
		if (isset($param["access_token"]))
			$token = $this->User->get_token($param);
		else
			$token = NULL;
		
		if (!$token && !isset($param["lang"])) {
			$result["success"] = FALSE;
			$result["message"] = "access_denined : token validate failure";
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		else {
			if (isset($param["lang"])) {
				$lang = $param["lang"];
			}
			else {
				$lang = "ko";
				if ($token->language == "zh-TW") $lang = "tw";
				if ($token->language == "zh-CN") $lang = "cn";
				if ($token->language == "ko-KR") $lang = "ko";
			}

			$business = $this->StoreModel->getPosition($lang, $param);

			$result["success"]	= TRUE;
			$result["data"]		= $business;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
	}
	
	public function best()
	{

	}
	
	public function mdchoice()
	{
		$this->load->view('welcome_message');
	}
	
	public function search()
	{
		$this->load->view('welcome_message');
	}

	public function detail($id)
	{
		$this->load->model('User');
		$this->load->model('StoreModel');
		$this->load->model('CouponModel');
		
		$param = $this->input->get(NULL, TRUE);
		if (isset($param["access_token"]))
			$token = $this->User->get_token($param);
		else
			$token = NULL;
		
		if (!$token && !isset($param["lang"])) {
			$result["success"] = FALSE;
			$result["message"] = "access_denined : token validate failure";
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		else {
			
			if (isset($param["lang"])) {
				$lang = $param["lang"];
			}
			else {
				$lang = "ko";
				if ($token->language == "zh-TW") $lang = "tw";
				if ($token->language == "zh-CN") $lang = "cn";
				if ($token->language == "ko-KR") $lang = "ko";
			}

			$coupons = $this->CouponModel->get($id, $lang);
			$coupons->store = $this->StoreModel->get_by_id($coupons->store, $lang);
			unset($coupons->store->password);

			$coupons->reviews = array();
			$coupons->photos = array();

			$result["success"] = TRUE;
			$result["data"] = $coupons;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
	}
	
	public function ticket_data($id)
	{
		$this->load->model('CouponModel');
		
		$param = $this->input->post(NULL, TRUE);
		$coupon = $this->CouponModel->get($id, $param["region"]);

		$result["success"] = TRUE;
		$result["data"] = $coupon;
	
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function mytickets()
	{
		$this->load->model('Store');
		$this->load->model('CouponModel');
		
		$param = $this->input->post(NULL, TRUE);
		$coupons = $this->CouponModel->list_by_mime($param["uid"], $param["region"], $param["type"]);

		$result["success"] = TRUE;
		$result["data"] = $coupons;
	
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function create()
	{
		$this->load->model('StoreModel');
		$this->load->model('CouponModel');
		
		$param = $this->input->post(NULL, TRUE);
		$sdata["username"] = $param["u_username"];
		$sdata["password"] = $param["u_password"];
		
		$token = $this->StoreModel->signin($sdata);
		if ($token == NULL) {
			$result["success"] = FALSE;
			$result["message"] = "access_denined : token validate failure";
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		else {
			$param["store"] = $token->id;
			$coupon = $this->CouponModel->create($param);

			$result["success"] = TRUE;
			$result["data"] = $coupon;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		
	}
	
	public function modify($id)
	{
		$this->load->model('Store');
		$this->load->model('CouponModel');
		
		$param = $this->input->post(NULL, TRUE);
		$sdata["username"] = $param["u_username"];
		$sdata["password"] = $param["u_password"];
		
		$token = $this->Store->signin($sdata);
		if ($token == NULL) {
			$result["success"] = FALSE;
			$result["message"] = "access_denined : token validate failure";
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		else {
			$param["store"] = $token->id;
			$coupon = $this->CouponModel->modify($param, $id);

			$result["success"] = TRUE;
			$result["data"] = $coupon;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */