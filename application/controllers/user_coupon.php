<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Coupon extends CI_Controller {

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
	public function download($id)
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
			
			$lang = "ko";
			if ($token->language == "zh-TW") $lang = "tw";
			if ($token->language == "zh-CN") $lang = "cn";
			if ($token->language == "ko-KR") $lang = "ko";

			$coupons = $this->CouponModel->download($token, $id);

			$result["success"] = TRUE;
			$result["data"] = $coupons;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
	}
	
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
			
			$lang = "ko";
			if ($token->language == "zh-TW") $lang = "tw";
			if ($token->language == "zh-CN") $lang = "cn";
			if ($token->language == "ko-KR") $lang = "ko";

			$coupons = $this->CouponModel->hastickets($token, $lang);

			$result["success"] = TRUE;
			$result["data"] = $coupons;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */