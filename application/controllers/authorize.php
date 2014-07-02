<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authorize extends CI_Controller {

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
	 
	public function stores()
	{
		echo $this->input->post("region", TRUE);
		echo "A";
	
	}
	
	public function signin()
	{
		$param = $this->input->post(NULL, TRUE);
		
		$this->load->model('User');

		if (!isset($param["username"]) || !$param["username"]) {
			$result["success"] = FALSE;
			$result["message"] = "전화번호가 올바르지 않습니다.";
		}
		else {
			$user_id = $this->User->get_or_create($param);
			$token   = $this->User->create_token($user_id, $_SERVER["REMOTE_ADDR"]);
			
			$result["success"]      = TRUE;
			$result["access_token"] = $token;
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function update_token()
	{
		$this->load->model('User');
		
		$param = $this->input->post(NULL, TRUE);
		$token = $this->User->update_token($param);
		
		$result["success"]      = TRUE;
		$result["access_token"] = $token;
		
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
	public function get_token()
	{
		$this->load->model('User');
		
		$param = $this->input->post(NULL, TRUE);
		$token = $this->User->get_token($param);
		
		$result["success"] = TRUE;
		$result["data"]    = $token;
		
		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */