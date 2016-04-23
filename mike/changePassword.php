<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class changePassword extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('Changepass_model');
		$this->load->library('session');
	}
	public function index(){
		$this->load->view('changePasswordView',$data); 
	}
	function changePassword2(){
		$count = $this->Changepass_model->checkNewFrOld($_GET['txtOldPass']);
		if($count>0){
			if($this->Changepass_model->changePass($_GET['txtNewPass'])){
				echo "dialogAlert('Password succesfully changed!');";
			}else{
				echo "dialogAlert('An Error Occured');";		
			}
		}else{
			echo "dialogAlert('Old Password is Invalid');";	
		}
	}
}
?>