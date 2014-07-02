<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Imgs extends CI_Controller {

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
	public function get($id)
	{
		$this->load->helper('file');
		$this->load->model('Attachfile');
		
		$_file = $this->Attachfile->file_by_id($id);
		$image_path = 'upload/'.$_file->serv_path.'/'.$_file->filename;

		$this->output->set_content_type(get_mime_by_extension($image_path));
		$this->output->set_output(file_get_contents($image_path));
	}
	
	public function thumb($id)
	{
		$this->load->helper('file');
		$this->load->model('Attachfile');
		
		$_file = $this->Attachfile->file_by_id($id);

		if($_GET['w'] && $_GET['h'] && $_GET['t']){
			$config['image_library'] = 'gd2';
			$config['source_image']	= 'upload/'.$_file->filename;
			$config['new_image'] = 'upload/';
            $config['thumb_marker'] = '_' . $_GET['w'].'x'.$_GET['h'];
            $config['maintain_ratio'] = FALSE;
			$config['create_thumb'] = TRUE;
			$config['maintain_ratio'] = TRUE;
			$config['width']	 = $_GET['w'];
			$config['height']	= $_GET['h'];

			$this->load->library('image_lib', $config); 

			$this->image_lib->resize();
			$this->image_lib->clear();

			$ext = pathinfo($config['source_image']);

			$this->output->set_content_type(get_mime_by_extension($config['new_image'].$ext['filename'].$config['thumb_marker'].'.'.$ext['extension']));
			$this->output->set_output(file_get_contents($config['new_image'].$ext['filename'].$config['thumb_marker'].'.'.$ext['extension']));
		}else{
			$image_path = 'upload/'.$_file->serv_path.'/'.$_file->filename;

			$this->output->set_content_type(get_mime_by_extension($image_path));
			$this->output->set_output(file_get_contents($image_path));
		}
	}
	
	public function create(){
		$this->load->model(array('attachfile'));
		$foldername = './upload/';

		$config['upload_path']   = $foldername;
		$config['file_name']     = md5(rand().microtime());
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	     = '10240';

		$this->load->library('upload', $config);

		if ($this->upload->do_upload())
		{
			$upfile = $this->upload->data();
			
			$data = array(
					'serv_path'		=> date("Ym"),
					'filename'		=> $upfile['file_name'],
					'originfile'	=> $_FILES['userfile']['name'],
					'extension'	=> $upfile['file_ext'],
					'content_type'	=> $upfile['image_type'],
					'byte_length'	=> $upfile['file_size'],
					'use'			=> 0
				);

			if($result = $this->attachfile->create_file($data)){
				$this->output->set_content_type('application/json')->set_output(json_encode($this->attachfile->file_by_id($result)));
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */