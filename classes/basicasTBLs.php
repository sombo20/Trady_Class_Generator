<?php

class basicasTBLs {

	private $tabela;
	
	public function __construct($tabela="") {
		$this->tabela = $tabela;
	}
	
	public function getTabela() {
		return $this->tabela;
	}

}
?>