<?
class Changepass_model extends CI_Model {
	
	function __construct(){
        parent::__construct();
		$this->load->library('session');
		
    }
	function checkNewFrOld($oldPass){
		$enOldPass = base64_encode($oldPass);
		echo $sql = "SELECT userName FROM tblusers WHERE userPass = '$enOldPass' AND userId = '".$this->session->userdata('ite-userId')."'";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	function changePass($newPass){
		$enNewPass = base64_encode($newPass);
		$this->db->trans_begin();
		$sqlAdd = "UPDATE tblusers SET userPass = '$enNewPass' WHERE userId = '".$this->session->userdata('ite-userId')."'";	
		$this->db->query($sqlAdd);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return false;
		}
		else{
			$this->db->trans_commit();
			return true;
		}
	}
}
?>