<?php

class GeraPrincipal {

	private $gerador;
	private $diretorio;
	private $geraSqls;

	// função construct da classe
	public function __construct() {
		$this->gerador = new Gerador();
		$this->db = new DB();
		$this->geraSqls = new GeraSQLs();
	}
	
	// função responsavel por chamar as funções
	// que geram o conteudo da classe
	public function geraClassePrincipal($diretorio) {
		$this->diretorio = $diretorio;
		$str  = "<?php\n";
		$str .= $this->gerador->getCreditos();
		$str .= self::geraAutoload();
		$str .= "class Principal {\n\n";
		$str .= self::geraVariaveis();
		$str .= self::geraConstruct();
		$str .= self::geraEspelhos();
		$str .= "}\n";
		$str .= "?>";
		return self::salvaClassePrincipal($str);
	}
	
	// função que salva as classes
	private function salvaClassePrincipal($conteudo) {
		$caminho = $this->diretorio."/Principal.php";
		$file = fopen($caminho,"w+");
		if(fwrite($file, $conteudo)) {
			$msgs = new Msgs($caminho,true);
		} else {
			$msgs = new Msgs($caminho,false);
		}
		fclose($file);
		return $msgs;
	}
	
	// função que gera função autoload
	private function geraAutoload() {
		$str  = "function __autoload(\$classe) {\n";
			$str .= "\t\$dir = str_replace(\"\\\\\",\"/\",dirname(__FILE__));\n";
			$str .= "\tif(file_exists(\$dir.\"/\".\$classe.\".php\")) {\n";
				$str .= "\t\tinclude(\$dir.\"/\".\$classe.\".php\");\n";
			$str .= "\t} else {\n";
				$str .= "\t\tif(file_exists(\$dir.\"/classesBasicas/\".\$classe.\".php\")) {\n";
					$str .= "\t\t\tinclude(\$dir.\"/classesBasicas/\".\$classe.\".php\");\n";
				$str .= "\t\t} else {\n";
					$str .= "\t\t\tif(file_exists(\$dir.\"/classesSQL/\".\$classe.\".php\")) {\n";
						$str .= "\t\t\t\tinclude(\$dir.\"/classesSQL/\".\$classe.\".php\");\n";
					$str .= "\t\t\t} else {\n";
						$str .= "\t\t\t\texit(\"Arquivo não encontrado!\");\n";
					$str .= "\t\t\t}\n";
				$str .= "\t\t}\n";
			$str .= "\t}\n";
		$str .= "}\n\n";
		return $str;
	}
	
	// função que gera as variaveis
	private function geraVariaveis() {
		$str = "";
		$arr = $_POST['tabelas'];
		$num = count($arr);
		for($i=0;$i<$num;$i++) {
			$str .= "\tprivate \$basica".$this->gerador->transformaCaractere($arr[$i]).";\n";
			$str .= "\tprivate \$sql".$this->gerador->transformaCaractere($arr[$i]).";\n";
		}
		$str .= "\n";
		
		return $str;
	}
	
	// função que gera a classe construct
	private function geraConstruct() {
		$str  = "\tpublic function __construct() {\n";
			$arr = $_POST['tabelas'];
			$num = count($arr);
			for($i=0;$i<$num;$i++) {
				$str .= "\t\t\$this->sql".$this->gerador->transformaCaractere($arr[$i])." = new Sql".$this->gerador->transformaCaractere($arr[$i])."();\n";
			}
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função responsavel por chamar as funções
	// que iram gerar as chamadas das outras classes
	// essas serão as funções espelho
	private function geraEspelhos() {
		$str = "";
		$arr = $_POST['tabelas'];
			$num = count($arr);
			for($i=0;$i<$num;$i++) {
			$str .= "\n\n\t// SETOR REFERENTE AS CLASSES DA TABELA ".strtoupper($arr[$i])."\n\n";
			$str .= self::geraFunctionInstanciadora($arr[$i]);
			$str .= self::geraGetFunctionInstanciadora($arr[$i]);
			$str .= self::espelhoRetorno($arr[$i]);
			$str .= self::espelhoInserir($arr[$i]);
			$str .= self::espelhoAtualiza($arr[$i]);
			$str .= self::espelhoExcluir($arr[$i]);
			$str .= self::espelhoQuantidadeRegistros($arr[$i]);
		}
		return $str;
	}
	
	// função que gera o espelho das funções de retorno
	private function espelhoRetorno($tabela) {
		$str  = "\tpublic function retorna".$this->gerador->transformaCaractere($tabela)."(\$extra=\"\") {\n";
			$str .= "\t\tif(\$this->sql".$this->gerador->transformaCaractere($tabela)."->retorna".$this->gerador->transformaCaractere($tabela)."(\$extra)) {\n";
				$str .= "\t\t\treturn \$this->sql".$this->gerador->transformaCaractere($tabela)."->getResp();\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que monta os parametros passados para uma função
	private function montaParametrosParaInsercao($tabela) {
		foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
			$arr[] = $coluna->getColuna();
		}
		$num = count($arr);
		$this->geraSqls->verificaChavePrimaria($tabela);
		$str = "";
		for($i=0;$i<$num;$i++) {
			if($this->geraSqls->getChave($tabela) != $arr[$i]) {
				if(($i + 1) == $num) {
					$str .= "\$".$arr[$i];
				} else {
					$str .= "\$".$arr[$i].", ";
				}
			} else {
				if(($i + 1) == $num) {
					$str .= "\$".$arr[$i];
				} else {
					$str .= "\$".$arr[$i].", ";
				}
			}
		}
		return $str;
	}
	
	// gera função que chama que cria uma instancia
	private function geraFunctionInstanciadora($tabela) {
		$str = "\tpublic function ".$tabela."(".self::montaParametrosParaInsercao($tabela).") {\n";
			$str .= "\t\t\$this->basica".$this->gerador->transformaCaractere($tabela)." = new Basica".$this->gerador->transformaCaractere($tabela)."(".self::montaParametrosParaInsercao($tabela).");\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que cria o get da função instanciadora
	private function geraGetFunctionInstanciadora($tabela) {
		$str = "\tpublic function get".$this->gerador->transformaCaractere($tabela)."() {\n";
			$str .= "\t\treturn \$this->basica".$this->gerador->transformaCaractere($tabela).";\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que gera o espelho das funções de inserção
	private function espelhoInserir($tabela) {
		$str  = "\tpublic function inserir".$this->gerador->transformaCaractere($tabela)."() {\n";
			//$str .= "\t\tnew Basica".$this->gerador->transformaCaractere($tabela)."(\$this->basica".$this->gerador->transformaCaractere($tabela).");\n";
			$str .= "\t\tif(\$this->sql".$this->gerador->transformaCaractere($tabela)."->inserir".$this->gerador->transformaCaractere($tabela)."(self::get".$this->gerador->transformaCaractere($tabela)."())) {\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que monta os parametros passados para uma função
	private function montaParametrosParaAtualizacao($tabela,$param="") {
		foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
			$arr[] = $coluna->getColuna();
		}
		$num = count($arr);
		$str = "";
		$this->geraSqls->verificaChavePrimaria($tabela);
		for($i=0;$i<$num;$i++) {
			if($this->geraSqls->getChave($tabela) == $arr[$i]) {
				if(($i + 1) == $num) {
					$str .= "\$".$arr[$i];
				} else {
					$str .= "\$".$arr[$i].", ";
				}
			} else {
				if($param == "") {
					if(($i + 1) == $num) {
						$str .= "\$".$arr[$i]."";
					} else {
						$str .= "\$".$arr[$i].", ";
					}
				} else {
					if(($i + 1) == $num) {
						$str .= "\$".$arr[$i]."=\"\"";
					} else {
						$str .= "\$".$arr[$i]."=\"\", ";
					}
				}
			}
		}
		return $str;
	}
	
	// função que gera o espelho das funções de inserção
	private function espelhoAtualiza($tabela) {
		$str  = "\tpublic function atualiza".$this->gerador->transformaCaractere($tabela)."() {\n";
			//$str .= "\t\tnew Basica".$this->gerador->transformaCaractere($tabela)."(".self::montaParametrosParaAtualizacao($tabela).");\n";
			$str .= "\t\tif(\$this->sql".$this->gerador->transformaCaractere($tabela)."->atualiza".$this->gerador->transformaCaractere($tabela)."(self::get".$this->gerador->transformaCaractere($tabela)."())) {\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que gera o espelho das funções de inserção
	private function espelhoExcluir($tabela) {
		$this->geraSqls->verificaChavePrimaria($tabela);
		$str  = "\tpublic function deleta".$this->gerador->transformaCaractere($tabela)."(\$".$this->geraSqls->getChave().") {\n";
			$str .= "\t\tif(\$this->sql".$this->gerador->transformaCaractere($tabela)."->deleta".$this->gerador->transformaCaractere($tabela)."(\$".$this->geraSqls->getChave().")) {\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que gera o espelho das funções de inserção
	private function espelhoQuantidadeRegistros($tabela) {
		$this->geraSqls->verificaChavePrimaria($tabela);
		$str  = "\tpublic function retornaQuantidadeRegistros".$this->gerador->transformaCaractere($tabela)."(\$extra=\"\") {\n";
			$str .= "\t\tif(\$this->sql".$this->gerador->transformaCaractere($tabela)."->retornaQuantidadeRegistros".$this->gerador->transformaCaractere($tabela)."(\$extra)) {\n";
				$str .= "\t\t\treturn \$this->sql".$this->gerador->transformaCaractere($tabela)."->getResp();\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
}