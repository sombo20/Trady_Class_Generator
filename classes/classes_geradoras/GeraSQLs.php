<?php

class GeraSQLs {

	private $gerador;
	private $diretorio;
	private $db;
	private $chave;

	// função construct da classe
	public function __construct() {
		$this->gerador = new Gerador();
		$this->db = new DB();
	}
	
	// função responsavel por chamar as funções
	// que geram o conteudo da classe
	public function geraClassesSQLs($tabela, $diretorio) {
		$this->diretorio = $diretorio;
		$str  = "<?php\n";
		$str .= $this->gerador->getCreditos();
		$str .= "class Sql".$this->gerador->transformaCaractere($tabela)." {\n\n";
		self::verificaChavePrimaria($tabela);
		$str .= self::geraElementosBases($tabela);
		$str .= self::geraFunctionRetorna($tabela);
		$str .= self::geraFunctionInsercao($tabela);
		$str .= self::geraFunctionAtualiza($tabela);
		$str .= self::geraFunctionsDelete($tabela);
		$str .= self::geraFunctionTotalRegitros($tabela);
		$str .= "}\n";
		$str .= "?>";
		return self::salvaClassesSQLs($tabela, $str);
	}
	
	// função que salva as classes
	private function salvaClassesSQLs($nomeClasse, $conteudo) {
		$caminho = $this->diretorio."/classesSQL/Sql".$this->gerador->transformaCaractere($nomeClasse).".php";
		$file = fopen($caminho,"w+");
		if(fwrite($file, $conteudo)) {
			$msgs = new Msgs($caminho,true);
		} else {
			$msgs = new Msgs($caminho,false);
		}
		fclose($file);
		return $msgs;
	}
	
	// função que retorna a coluna primaria
	public function getChave() {
		return $this->chave;
	}

	// verifica se a coluna e a chave primaria
	public function verificaChavePrimaria($tabela) {
		foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
			if($coluna->getChave() == "PRI") {
				$this->chave = $coluna->getColuna();
				return true;
			} else {
				return false;
			}
		}
	}

	// função que gera a a variavel, a função __construct e a função getResp
	private function geraElementosBases($tabela) {
	 	$str   = "\tprivate \$resp;\n";
		$str  .= "\tprivate \$db;\n\n";
		$str .= "\tpublic function __construct() {\n";
			$str .= "\t\t\$this->db = new DB();\n";
		$str .= "\t}\n\n";
		$str .= "\tpublic function getResp() {\n";
			$str .= "\t\treturn \$this->resp;\n";
		$str .= "\t}\n\n";
		return $str;
	}
	 
	// função que gera a função que retorna os usuario
	private function geraFunctionRetorna($tabela) {
		$str  = "\tpublic function retorna".$this->gerador->transformaCaractere($tabela)."(\$extra=\"\") {\n";
			$str .= "\t\t\$sql = \"SELECT * FROM ".$tabela." \".\$extra;\n";
			$str .= "\t\t\$this->db->query(\$sql);\n";
			$str .= "\t\tif(\$this->db->quantidadeRegistros() > 0) {\n";
				$str .= "\t\t\twhile(\$obj = \$this->db->fetchObj()) {\n";
					$str .= "\t\t\t\t\$arr[] = new Basica".$this->gerador->transformaCaractere($tabela)."(".self::retornaCamposParaFetchObj($tabela).");\n";
				$str .= "\t\t\t}\n";
				$str .= "\t\t\t\$this->resp = \$arr;\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		
		return $str;
	}
	
	// função que cria a alocação para o retorno dos metodos
	// do fetchObj
	private function retornaCamposParaFetchObj($tabela) {
		foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
			$arr[] = $coluna->getColuna();
		}
		$num = count($arr);
		$str = "";
		for($i=0;$i<$num;$i++) {
			if(($i + 1) == $num) {
				$str .= "\$obj->".$arr[$i];
			} else {
				$str .= "\$obj->".$arr[$i].", ";
			}
		}
		return $str;
	}
 
	// função que gera a classe de inserção de dados
	private function geraFunctionInsercao($tabela) {
		$str  = "\tpublic function inserir".$this->gerador->transformacaractere($tabela)."(\$".$tabela.") {\n";
			foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
				$arr[] = $coluna->getColuna();
			}
			$num = count($arr);
			for($i=0;$i<$num;$i++) {
				if($this->chave == $arr[$i]) {
					if(($i + 1) == $num) {
						$str .= "\t\t\$dados .= \"''\";\n";
					} else if($i == 0) {
						$str .= "\t\t\$dados  = \"'',\";\n";
					} else {
						$str .= "\t\t\$dados .= \"'',\";\n";
					}
				} else {
					if(($i + 1) == $num) {
						$str .= "\t\t\$dados .= \"'\".\$".$tabela."->get".$this->gerador->transformacaractere($arr[$i])."().\"'\";\n";
					} else if($i == 0) {
						$str .= "\t\t\$dados  = \"'\".\$".$tabela."->get".$this->gerador->transformacaractere($arr[$i])."().\"',\";\n";
					} else {
						$str .= "\t\t\$dados .= \"'\".\$".$tabela."->get".$this->gerador->transformacaractere($arr[$i])."().\"',\";\n";
					}
				}
			}
			$str .= "\t\t\$sql = \"INSERT INTO ".$tabela." VALUES (\".\$dados.\")\";\n";
			$str .= "\t\tif(\$this->db->query(\$sql)) {\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que gera as funções de update
	private function geraFunctionAtualiza($tabela) {
		$str  = "\tpublic function atualiza".$this->gerador->transformacaractere($tabela)."(\$".$tabela.") {\n";
			foreach($this->gerador->selecinaColunas($tabela) as $coluna) {
				$arr[] = $coluna->getColuna();
			}
			$num = count($arr);
			$str .= "\t\t\$sql = \"UPDATE ".$tabela." SET\n";
			for($i=0;$i<$num;$i++) {
				if($this->chave != $arr[$i]) {
					if(($i + 1) == $num) {
						$str .= "\t\t\t\t".$arr[$i]."='\".\$".$tabela."->get".$this->gerador->transformacaractere($arr[$i])."().\"'\n";
					} else if($i == 0) {
						$str .= "\t\t\t\t".$arr[$i]."='\".\$".$tabela."->get".$this->gerador->transformacaractere($arr[$i])."().\"',\n";
					} else {
						$str .= "\t\t\t\t".$arr[$i]."='\".\$".$tabela."->get".$this->gerador->transformacaractere($arr[$i])."().\"',\n";
					}
				}
			}
			$str .= "\t\tWHERE ".$this->chave."='\".\$".$tabela."->get".$this->chave."().\"'\";\n";
			$str .= "\t\tif(\$this->db->query(\$sql)) {\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que cria as funções de deltar
	private function geraFunctionsDelete($tabela) {
		$str  = "\tpublic function deleta".$this->gerador->transformacaractere($tabela)."(\$".$this->chave.") {\n";
			$str .= "\t\t\$sql = \"DELETE FROM ".$tabela." WHERE ".$this->chave."='\".\$".$this->chave.".\"'\";\n";
			$str .= "\t\tif(\$this->db->query(\$sql)) {\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que gera função que retorna o
	// numero de registros encontrados
	private function geraFunctionTotalRegitros($tabela) {
		$str  = "\tpublic function retornaQuantidadeRegistros".$this->gerador->transformaCaractere($tabela)."(\$extra=\"\") {\n";
			$str .= "\t\t\$sql = \"SELECT * FROM ".$tabela." \".\$extra;\n";
			$str .= "\t\t\$this->db->query(\$sql);\n";
			$str .= "\t\tif(\$this->db->quantidadeRegistros() > 0) {\n";
				$str .= "\t\t\t\$this->resp = \$this->db->quantidadeRegistros();\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\t\$this->resp = \"0\";\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		
		return $str;
	}
	
}