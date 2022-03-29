<?php

class basicasColunas {

	private $coluna;
	private $chave;
	
	public function __construct($coluna="", $chave="") {
		$this->coluna = $coluna;
		$this->chave = $chave;
	}
	
	public function getColuna() {
		return $this->coluna;
	}
	
	public function getChave() {
		return $this->chave;
	}

}
?>