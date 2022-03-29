<?php

class MSGs {

	private $classe;
	private $estado;

	// inicializa as variaveis para a conexão com o banco
	public function __construct($classe="", $estado="") {
		$this->classe = $classe;
		$this->estado = $estado;
	}
	
	public function getClasse() {
		return $this->classe;
	}
	
	public function getEstado() {
		return $this->estado;
	}

}
?>