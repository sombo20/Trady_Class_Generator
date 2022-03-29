<?php
/*
 *	Sistema: Gerador de Classes PHP
 *	Autor: Vicente Victor Sombo
 *	Email: vicentevictorsombo
 *	Versão: 1.0
 *	Data da criação: 20/01/2021
 *	Hora da criação: 10:4:05
 */

function __autoload($classe) {
	$dir = str_replace("\\","/",dirname(__FILE__));
	if(file_exists($dir."/".$classe.".php")) {
		include($dir."/".$classe.".php");
	} else {
		if(file_exists($dir."/classes_geradoras/".$classe.".php")) {
			include($dir."/classes_geradoras/".$classe.".php");
		} else {
			return false;
		}
	}
}

class Gerador {
	
	// variaveis para saida de informações
	private $msg;
	private $bgColor;
	private $color;
	private $creditos;
	
	// variaveis para instancia de classes
	private $sqlDBs;
	private $sqlTBLs;
	private $sqlColunas;
	private $msgs;
	
	// variaveis do sistema de geração
	private $diretorio;
	private $status;
		
	// função construct da classe
	public function __construct() {
		$this->sqlDBs = new sqlDBs();
		$this->sqlTBLs = new sqlTBLs();
		$this->sqlColunas = new sqlColunas();
		$this->msgs = new MSGs();
	}
	
	// função que seta as corres da mensagem
	// apos geração das classes
	private function setColors($tipo) {
		if($tipo == 1) {
			$this->color = "#00CC99";
			$this->bgColor = "#C4FBDA";
		} else {
			$this->color = "#FF0000";
			$this->bgColor = "#FFBFC1";
		}
	}
	
	// // função que informa o estado da geração
	public function getStatus() {
		return $this->status;
	}
	
	// função que retorna a mensagem apos
	// geração das classes
	public function getMsg() {
		return $this->msg;
	}
	
	// função que retorna a cor da tabela
	// apos geração das classes
	public function getColor() {
		return $this->color;
	}
	
	// função que retorna a cor de fundo da tabela
	// apos geração das classes
	public function getBgColor() {
		return $this->bgColor;
	}

	// função que imprime no rodape das paginas
	// do gerador
	public function getCopyright() {
		$txt  = "Desenvolvido Vicente Victor Sombo<br>";
		$txt .= "Email: vicentevictorsombo643@gmail.com<br>";
		$txt .= "Versão: 1.0";
		return $txt;
	}
	
	// função que informa os creditos que ira
	// sair em todos os arquivos gerados
	public function getCreditos() {
		$str  = "/*\n";
		$str .= " *\tSistema: Gerador de Classes PHP\n";
		$str .= " *\tAutor: Vicente Victor Sombo\n";
		$str .= " *\tEmail: vicentevictorsombo643@gmail.com\n";
		$str .= "*\tFacebook: Vicente Victor Sombo\n";
		$str .= "*\tGithub: https://github.com/sombo20\n";
		$str .= " *\tVersão: 1.0\n";
		$str .= " *\tData da criação: 20/01/2022\n";
		$str .= " *\tHora da criação: 10:4:05\n";
		$str .= " *\n";
		$str .= " *\tData da geração do arquivo: ".date("d-m-Y \a\s H:i:s")."\n";
		if(isset($_POST['banco'])) {
			$str .= " *\tReferente ao banco de dados: ".$_POST['banco']."\n";
		}
		$str .= " */\n\n";
		
		return $str;
	}
	
	// mostra os bancos de dados
	public function selecionaDBs() {
		if($this->sqlDBs->retornaDBs()) {
			return $this->sqlDBs->getResp();
		} else {
			return false;
		}
	}
	// mostra as tabelas do banco de dados
	public function selecionaTBLs() {
		if($this->sqlTBLs->retornaTabelas()) {
			return $this->sqlTBLs->getResp();
		} else {
			return false;
		}
	}
	
	// função que retoan as colunas da tabela
	public function selecinaColunas($tabela) {
		if($this->sqlColunas->retornaColunas($tabela)) {
			return $this->sqlColunas->getResp();
		} else {
			return false;
		}
	}
	
	// função que transforma o primeiro caractere para maiusculo
	public function transformaCaractere($string) {
		$arr = str_split($string);
		$carc1 = strtoupper($arr[0]);
		foreach($arr as $chave=>$valor) {
			if($chave == 0) {
				$str = $carc1;
			} else {
				$str .= $valor;
			}
		}
		return $str;
	}
	
	// mostra a pasta criada
	public function getPasta() {
		return $this->diretorio;
	}
	
	// função que gera as classes
	public function geraClasses() {
		$diretorio = self::criaPastas();
		$this->diretorio = $diretorio;
		self::geraClassesBasicas($diretorio);
		self::geraClassesSQLs($diretorio);
		self::geraClasseDB($diretorio);
		self::geraClassePrincipal($diretorio);
		$this->status = true;
	}
	
	// função que cria as patas para salvar os arquivos
	private function criaPastas() {
		$data = date("d-m-Y_\a\s_H_i_s");
		$pref = md5(microtime() * 9999);
		$gerada = $_POST['banco'].$data."_".$pref;
		if(!mkdir("classes_geradas/".$gerada)) {
			self::setColors(0);
			$this->msg = "Erro na criação da pasta \"".$gerada."\".";
			return false;
		}
		if(!mkdir("classes_geradas/".$gerada."/classesSQL/")) {
			self::setColors(0);
			$this->msg = "Erro na criação da pasta \"classesSQL\".";
			return false;
		}
		if(!mkdir("classes_geradas/".$gerada."/classesBasicas/")) {
			self::setColors(0);
			$this->msg = "Erro na criação da pastas \"classesBasicas\".";
			return false;
		}
		return "classes_geradas/".$gerada;
	}
	
	// função que chama as funções que geram as classes basicas
	private function geraClassesBasicas($diretorio) {
		$geraBasicas = new GeraBasicas();
		foreach($_POST['tabelas'] as $tabela) {
			$geraBasicas->geraClassesBasicas($tabela, $diretorio);
		}
	}
	
	// função que chama as funções que geram as classes de comandos sql
	private function geraClassesSQLs($diretorio) {
		$geraSQLs = new GeraSQLs();
		foreach($_POST['tabelas'] as $tabela) {
			$geraSQLs->geraClassesSQLs($tabela, $diretorio);
		}
	}
	
	// função que chama a classe gera a classe DB
	private function geraClasseDB($diretorio) {
		$geraDB = new GeraDB();
		$geraDB->geraClasseDB($diretorio);
	}
	
	// função que chama a classe gera a classe Principal
	private function geraClassePrincipal($diretorio) {
		$geraPrincipal = new GeraPrincipal();
		$geraPrincipal->geraClassePrincipal($diretorio);
	}
	
	// função que retorna as mensagens
	public function retornaMsgs() {
		return $this->msgs;
	}

}
?>