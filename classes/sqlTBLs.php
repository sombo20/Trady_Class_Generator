<?php
class sqlTBLs {

	private $resp;
	
	public function getResp() {
		return $this->resp;
	}

	public function __construct() {}
	
	public function retornaTabelas() {
		$db = new DB();
		$db->conexao();
		$sql = "SHOW TABLES FROM ".$_POST['banco'];
		$query = mysql_query($sql);
		while($obj = mysql_fetch_row($query)) {
			$arr[] = new basicasTBLs($obj[0]);
		}
		$this->resp = $arr;
		$db->exitConexao();
		return true;
	}

}
?>