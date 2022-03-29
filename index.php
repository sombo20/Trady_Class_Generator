<?php
include_once("classes/Gerador.php");
$gerador = new Gerador();
if(file_exists("classes/DB.php")) {
	header("Location: index2.php");
} else {
	if(isset($_POST['acao']) and $_POST['acao'] == "instalar") {
		new Instal($_POST['servidor'], $_POST['usuario'], $_POST['senha']);
	}
 }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Instalação - Trady</title>
	<link href="materialize/css/materialize.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
         <div class="container">
         	<form id="formInstal" name="formInstal" method="post" action="#">
              	<p class="center" >Instalação do gerador de classes Trady</p>
     
              <p class="center" >Informe os dados para a conexão com o banco de dados mysql.</p>
            		<div class="row">
            			<label>Servidor</label>
                    	<input name="servidor" type="text" class="formTexto" id="servidor" />
                  	</div>
                  	<div class="row">
                  		<label>Usúario</label>
                    	<input name="usuario" type="text" class="formTexto" id="usuario" />
                  	</div>
               		<div class="row">
                  		<label>Senha</label>
                    	<input name="senha" type="password" class="formTexto" id="senha" />
                   </div>
                	<div class="row">
                		<input name="acao" 
                		<a class="waves-effect waves-light btn-small" type="submit" id="acao" value="instalar">
                	</div>
                </form>
               </div>
            
         
    <?php echo $gerador->getCopyright();?>
    <script src="materialize/js/materialize.min.js"></script>
</body>
</html>