<?php
include("classes/Gerador.php");
$gerador = new Gerador();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Instalação - Trady</title>
	<link href="materialize/css/index2.css" rel="stylesheet">
</head>

<body>
	<div class="container">
		<form id="formBancos" name="formBancos" method="post" action="mostraTabelas.php">
  
      		<p class="center" >Selecione o banco de dados para ser mapeado.</p>
      
        		<select name="banco" class="formTexto" id="banco" style="width:100%" onchange="document.formBancos.submit()">
        			<option selected="selected" disabled="disabled" >-->Selecione o banco <--</option>
            			<?php foreach($gerador->selecionaDBs() as $dbs) {?>
            		<option value="<?php echo $dbs->getDatabase();?>"><?php echo $dbs->getDatabase();?></option>
            		<?php }?>
        		</select>
		</form>
	</div>
	<?php echo $gerador->getCopyright();?></td>
</body>
</html>