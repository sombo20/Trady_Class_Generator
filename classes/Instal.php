<?php

// classe que instala o sistema 
class Instal {

	private $gerador;
	private $server;
	private $usuario;
	private $senha;

	// função construct da classe instal
	// responsavel pela chama das função que criam o arquivo "DB.php"
	public function __construct($server, $usuario, $senha) {
		$this->gerador	= new Gerador();
		$this->server	= $server;
		$this->usuario	= $usuario;
		$this->senha	= $senha;
		self::validaDados();
		self::createArqDB_php();
		self::verificaInstalacao();
	}

	// função que cria o arquivo
	private function createArqDB_php() {
		$file = fopen("classes/DB.php","w+");
		fwrite($file,self::geraDB_php());
		fclose($file);
	}
	
	// verifica se a instalação ocorreu com sucesso
	private function verificaInstalacao() {
		if(file_exists("classes/DB.php")) {
			header("Location: index2.php");
		} else {
			$msg = "Não foi possivel instalar o sistema.";
			$erro = urlencode(htmlentities($msg, ENT_NOQUOTES, "UTF-8"));
			header("Location: index.php?msg=".$erro);
		}
	}
	
	// função que verifica se os dados são validos
	private function validaDados() {
		if(!mysql_connect($this->server, $this->usuario, $this->senha)) {
			$msg = "Informe os dados para a conexao com o servidor corretamente.";
			$erro = urlencode(htmlentities($msg,ENT_NOQUOTES,"UTF-8"));
			header("Location: index.php?msg=".$erro);
			exit();
		}
	}
	
	// função que gera o arquivo DB.php do GetClass
	private function geraDB_php() {
		$str  = "<?php\n";
		$str .= $this->gerador->getCreditos();
		$str .= "class DB {\n\n";
		$str .= self::getVariaveis();
		$str .= self::get__Construct_DB();
		$str .= self::getFunctionsConexao();
		$str .= self::getFunctionsExtras();
		$str .= "}\n";
		$str .= "?>";
		
		return $str;
	}
	
	// função que gera as variaveis da classe
	private function getVariaveis() {
		$str  = "\tprivate \$server;\n";
		$str .= "\tprivate \$usuario;\n";
		$str .= "\tprivate \$senha;\n";
		$str .= "\tprivate \$conn;\n";
		$str .= "\tprivate \$msgErroQuery;\n";
		$str .= "\tprivate \$query;\n\n";
		return $str;
	}
	
	// função para gerar a função construct da classe db
	private function get__Construct_DB() {
		$str  = "\t// inicializa as variaveis para a conexão com o banco\n";
		$str .= "\tpublic function __construct() {\n";
			$str .= "\t\tself::escreveDados(\$this->server, '".$this->server."');\n";
			$str .= "\t\tself::escreveDados(\$this->usuario, '".$this->usuario."');\n";
			$str .= "\t\tself::escreveDados(\$this->senha, '".$this->senha."');\n";
		$str .= "\t}\n\n";
		return $str;
	}
	
	// função que gera as funções de conexão com o banco
	private function getFunctionsConexao() {
		$str  = "\t// cria uma conexão com o mysql\n";
		$str .= "\tpublic function conexao() {\n";
			$str .= "\t\t\$conect = mysql_connect(\$this->server,\$this->usuario,\$this->senha) or\n";
						$str .= "\t\t\tdie('Não foi possivel conectar ao servidor mysql.<br>'.mysql_error());\n";
			$str .= "\t\t\$this->conn = \$conect;\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// fecha a conexao com o banco de dados\n";
		$str .= "\tpublic function exitConexao() {\n";
			$str .= "\t\treturn mysql_close(\$this->conn);\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// seleciona o banco\n";
		$str .= "\tpublic function selecionaDB() {\n";
			$str .= "\t\tmysql_select_db(\$_POST['banco'],\$this->conn) or\n";
			$str .= "\t\tdie('Não foi possivel selecionar a base de dados.<br>'.mysql_error());\n";
		$str .= "\t}\n\n";
		
		$str .= "\t// escreve dados para as variaveis\n";
		$str .= "\tprivate function escreveDados(&\$var, \$param) {\n";
			$str .= "\t\treturn \$var = \$param;\n";
		$str .= "\t}\n\n";

		return $str;
	}
	
	// função que gera as funções extras da classe
	private function getFunctionsExtras() {

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
?>