<?php

class GeraDB {

	private $gerador;
	private $diretorio;

	// função construct da classe
	public function __construct() {
		$this->gerador = new Gerador();
		$this->db = new DB();
	}
	
	// função responsavel por chamar as funções
	// que geram o conteudo da classe
	public function geraClasseDB($diretorio) {
		$this->diretorio = $diretorio;
		$str  = "<?php\n";
		$str .= $this->gerador->getCreditos();
		$str .= "class DB {\n\n";
		$str .= self::geraVariaveis();
		$str .= self::geraConstruct();
		$str .= self::geraFunctionAcessoAoBanco();
		$str .= self::geraFunctionsAdicionaisDaClasse();
		$str .= "}\n";
		$str .= "?>";
		return self::salvaClasseDB($str);
	}
	
	// função que salva as classes
	private function salvaClasseDB($conteudo) {
		$caminho = $this->diretorio."/DB.php";
		$file = fopen($caminho,"w+");
		if(fwrite($file, $conteudo)) {
			$msgs = new Msgs($caminho,true);
		} else {
			$msgs = new Msgs($caminho,false);
		}
		fclose($file);
		return $msgs;
	}
	
	// função que gera as variaveis
	private function geraVariaveis() {
		$str  = "\tprivate \$server;\n";
		$str .= "\tprivate \$usuario;\n";
		$str .= "\tprivate \$senha;\n";
		$str .= "\tprivate \$banco;\n";
		$str .= "\tprivate \$conn;\n";
		$str .= "\tprivate \$msgErroQuery;\n";
		$str .= "\tprivate \$query;\n\n";
		return $str;
	}
	
	 //função que gera a função construct
	 private function geraConstruct() {
	 	$str  = "\t// inicializa as variaveis para a conexão com o banco\n";
		$str  .= "\tpublic function __construct() {\n";
			$str .= "\t\tself::escreveDados(\$this->server, \"\");\n";
			$str .= "\t\tself::escreveDados(\$this->usuario, \"\");\n";
			$str .= "\t\tself::escreveDados(\$this->senha, \"\");\n";
			$str .= "\t\tself::escreveDados(\$this->banco, \"".$_POST['banco']."\");\n";
			$str .= "\t\tself::conexao();\n";
		$str .= "\t}\n\n";
		return $str;
	 }
	 
	 // função que gera funções de conexão com o banco
	 private function geraFunctionAcessoAoBanco() {
		$str  = "\t// cria uma conexão com o mysql\n";
		$str .= "\tprivate function conexao() {\n";
			$str .= "\t\t\$conect = mysql_connect(\$this->server,\$this->usuario,\$this->senha) or\n";
						$str .= "\t\t\t\tdie(\"Não foi possivel conectar ao servidor mysql.<br>\".mysql_error());\n";
			$str .= "\t\t\$this->conn = \$conect;\n";
			$str .= "\t\tself::selecionaDB();\n";
		$str .= "\t}\n\n";
		
		$str .= "\tpublic function exitConexao() {\n";
			$str .= "\t\treturn mysql_close(\$this->conn);\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// seleciona o banco\n";
		$str .= "\tprivate function selecionaDB() {\n";
			$str .= "\t\tmysql_select_db(\$this->banco,\$this->conn) or\n";
			$str .= "\t\tdie(\"Não foi possivel selecionar a base de dados.<br>\".mysql_error());\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// escreve dados para as variaveis\n";
		$str .= "\tprivate function escreveDados(&\$var, \$param) {\n";
			$str .= "\t\treturn \$var = \$param;\n";
		$str .= "\t}\n\n";
		return $str;
	 }
	 
	// gera funções adicionais para a classe
	private function geraFunctionsAdicionaisDaClasse() {
		$str  = "\t// faz uma query\n";
		$str .= "\tpublic function query(\$sql) {\n";
			$str .= "\t\t\$query = mysql_query(\$sql);\n";
			$str .= "\t\tif(\$query) {\n";
				$str .= "\t\t\t\$this->query = \$query;\n";
				$str .= "\t\t\treturn true;\n";
			$str .= "\t\t} else {\n";
				$str .= "\t\t\t\$this->msgErroQuery = mysql_error();\n";
				$str .= "\t\t\treturn false;\n";
			$str .= "\t\t}\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// retorna o fetchObject da ultima consulta\n";
		$str .= "\tpublic function fetchObj() {\n";
			$str .= "\t\treturn mysql_fetch_object(\$this->query);\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// retorna o id do insert referido\n";
		$str .= "\tpublic function ultimoId() {\n";
			$str .= "\t\treturn mysql_insert_id(\$this->query);\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// retorna a quantidade de registro encontrados\n";
		$str .= "\tpublic function quantidadeRegistros() {\n";
			$str .= "\t\treturn mysql_num_rows(\$this->query);\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// mostra mensagem de erro na query\n";
		$str .= "\tpublic function getErro() {\n";
			$str .= "\t\treturn \$this->msgErroQuery;\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
}