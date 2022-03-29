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
	<link href="materialize/css/tabela.css" rel="stylesheet">
</head>
<script language="javascript">
// função para selecionar todas as tabelas
function check(count) {
	var elem = document.formTabelas.elements;
	if(count == 0) {
		for(i=0;i<elem.length;i++) {
			var x = elem[i];
			if(x.name == "tabelas[]") {
				x.checked = true;
			}
		}
	} else {
		for(i=0;i<elem.length;i++) {
			var x = elem[i];
			if(x.name == "tabelas[]") {
				x.checked = false;
			}
		}
	}
}
</script>
<body>
	<form id="formTabelas" name="formTabelas" method="post" action="gerarClasses.php">
            <p class="center">Seleccione as tabela a serem mapeadas</p>
            <div class="up">
            	<a href="#" onclick="check(0)">Marcar Tudo</a>
            	<a href="#" onclick="check(1)">Desmarcar</a>
            </div>
			<?php foreach($gerador->selecionaTBLs() as $tabela) {?>
               <div class="row">
                <input type="checkbox" value="<?php echo $tabela->getTabela();?>" name="tabelas[]" id="tabelas[]" />
             
                <?php echo $tabela->getTabela();?>
			 </div>
			<?php }?>
			
                <input name="banco" type="hidden" id="banco" value="<?php echo $_POST['banco'];?>" />
                <input name="Submit" type="submit" class="formButton" id="button" value="Gerar Classes" />
       		</form>
    <?php echo $gerador->getCopyright();?></td>
    
</body>
</html>