<?php

class GeraBasicas {

	private $gerador;
	private $diretorio;

	// função construct da classe
	public function __construct() {
		$this->gerador = new Gerador();
	}
	
	// função responsavel por chamar as funções
	// que geram o conteudo da classe
	public function geraClassesBasicas($tabela, $diretorio) {
		$this->diretorio = $diretorio;
		$str  = "<?php\n";
		$str .= $this->gerador->getCreditos();
		$str .= "class Basica".$this->gerador->transformaCaractere($tabela)." {\n\n";
		$str .= self::geraVariaveis($tabela);
		$str .= self::geraConstruct($tabela);
		$str .= self::geraFunctionsGets($tabela);
		$str .= "}\n";
		$str .= "?>";
		return self::salvaClassesBasicas($tabela, $str);
	}
	
	// função que gera as variaveis da classe basica
	private function geraVariaveis($tabela) {
		$str = "";
		foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
			$str .= "\tprivate \$".$coluna->getColuna().";\n";
		}
		$str .= "\n";
		return $str;
	}
	
	// função que gera a função construct da classe basica
	private function geraConstruct($tabela) {
		$str = "\tpublic function __construct(";
		foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
			$arr[] = $coluna->getColuna();
		}
		$num = count($arr);
		for($i=0;$i<$num;$i++) {
			if(($i + 1) == $num) {
				$str .= "\$".$arr[$i]."=\"\"";
			} else {
				$str .= "\$".$arr[$i]."=\"\",";
			}
		}
		$str .= ") {\n";
		for($i=0;$i<$num;$i++) {
			$str .= "\t\t\$this->".$arr[$i]." = \$".$arr[$i].";\n";
		}
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que gera as funções get's
	private function geraFunctionsGets($tabela) {
		$str = "";
		foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
			$str .= "\tpublic function get".$this->gerador->transformaCaractere($coluna->getColuna())."() {\n";
				$str .= "\t\treturn \$this->".$coluna->getColuna().";\n";;
			$str .= "\t}\n\n";
		}
		return $str;
	}
	
	// função que salva as classes
	private function salvaClassesBasicas($nomeClasse, $conteudo) {
		$caminho = $this->diretorio."/classesBasicas/Basica".$this->gerador->transformaCaractere($nomeClasse).".php";
		$file = fopen($caminho,"w+");
		if(fwrite($file, $conteudo)) {
			$msgs = new Msgs($caminho,true);
		} else {
			$msgs = new Msgs($caminho,false);
		}
		fclose($file);
		return $msgs;
	}

}