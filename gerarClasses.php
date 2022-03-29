<?php
set_time_limit(0);
include("classes/Gerador.php");
$gerador = new Gerador();
$gerador->geraClasses();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Instalação - Trady</title>
	<link href="materialize/css/tabela.css" rel="stylesheet">
</head>
<body>

            <td height="25" align="center" valign="middle" bgcolor="<?php echo $gerador->getBgColor();?>" class="pretoNegrito12"><?php echo $gerador->getMsg();?></td>
          
		<?php if($gerador->getStatus()) {?>
        <td height="30" align="center" valign="middle" bgcolor="#FFFFFF" class="vermelhoNegrito14">Classes Geradas Com sucesso.<br />
          Acesse a pasta  &quot;<?php echo $gerador->getPasta();?>&quot; onde estarão sua classes.</td>
        <td align="right" valign="top" background="imagens/layout pg/linha_direita.jpg" bgcolor="#FFFFFF">&nbsp;</td>
      </tr>
		<?php }?>
      
</body>
</html>