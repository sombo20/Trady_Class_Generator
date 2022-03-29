<?php
class sqlDBs {

	private $resp;
	
	public function getResp() {
		return $this->resp;
	}

	public function __construct() {}
	
	public function retornaDBs() {
		$db = new DB();
		$db->conexao();
		$db->query("SHOW DATABASES");
		while($obj = $db->fetchObj()) {
			$arr[] = new basicasDBs($obj->Database);
		}
		$this->resp = $arr;
		$db->exitConexao();
		return true;
	}
	

}
?>