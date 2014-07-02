<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mgr_Authorize extends CI_Controller {

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
	
	public function signin()
	{

		$this->load->model('StoreModel');
		$param = $this->input->post(NULL, TRUE);
		$token = $this->StoreModel->signin($param);
		
		if ($token == NULL) 
			$result["message"] = "아이디 혹은 비밀번호가 올바르지 않습니다.";
		else 
			unset($token->password);
			
		if ($token != NULL && !$token->activated) {
			$result["success"] = FALSE;
			$result["message"] = "인증된 회원이 아닙니다.";
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
			return;
		}
		
		$result["success"] = ($token != NULL);
		$result["data"]    = $token;
	
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function signup_manager()
	{
		$this->load->model('StoreModel');
		
		$param = $this->input->post(NULL, TRUE);
		if ($this->StoreModel->already_join($param["username"])) {
		
			$result["success"] = FALSE;
			$result["message"] = "이미 사용중인 아이디입니다.";
	
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
			return;
		}
	
		$token = $this->StoreModel->create_manager($param);
	
		$result["success"] = ($token != NULL);
		$result["data"]    = $token;
	
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function signup_d1()
	{
		$this->load->model('StoreModel');
		$id = $this->input->post("id", TRUE);

		$param = $this->input->post(NULL, TRUE);
		if ($this->StoreModel->already_join($param["username"])) {
		
			$result["success"] = FALSE;
			$result["message"] = "이미 사용중인 아이디입니다.";
	
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
			return;
		}
		
		if (!$id) {
			$token = $this->StoreModel->create($param);
		
			$result["success"] = ($token != NULL);
			$result["data"]    = $token;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		else {
			$token = $this->StoreModel->update($id, $param);
		
			$result["success"] = ($token != NULL);
			$result["data"]    = $token;
		
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
		
	}
	
	public function signup_d2()
	{
		$this->load->model('StoreModel');
		
		$param = $this->input->post(NULL, TRUE);
		$token = $this->StoreModel->create_detail($param, "ko");
		
		$result["success"] = ($token != NULL);
		$result["data"]    =  $token;
		
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function signup_d3($id)
	{
		$this->load->model('StoreModel');
		
		$param = $this->input->post(NULL, TRUE);
		$token = $this->StoreModel->create($param);
		
		$result["success"] = ($token != NULL);
		$result["data"]    = $token;
		
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	
	public function get()
	{
		$this->load->model('StoreModel');
		$param = $this->input->post(NULL, TRUE);
		$token = $this->StoreModel->get($param);
		
		if ($token == NULL) 
			$result["message"] = "인증실패.";
		else 
			unset($token->password);
		
		$result["success"] = ($token != NULL);
		$result["data"]    = $token;
	
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */