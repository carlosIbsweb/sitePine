<style>	
header, #page-footer, .guestsub, .forgetpass {display: none !important}
</style>

<?php
$ent = file_get_contents('http://depaulacertificacoes.ibsweb.virtuaserver.com.br/login/index.php');
echo ' <form action="http://depaulacertificacoes.ibsweb.virtuaserver.com.br/login/index.php" target="_parent" method="post" id="login">';
echo $ent;


?>
