<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

paymentsForum::executTopic();

/*if(!empty($coursesBlock)):
	echo 'Permitido';
else:
	echo 'Bloqueado';
endif;
*/

?>

<div class="dPagTopic">
<?php foreach (paymentsForum::getTopic() as $k => $items): ?>
	<div class="dPagTopicHeader">
	<span class="dImgUser">

	</span>
	<h3><?php echo $items->title; ?></h3>
	<p>
	<?= $items->description;?>
	</p>
	<p class="dDadosTopic">
		<span class="tname"><?= s7dPayments::getUserName('name',$items->userId);?></span> - 
		<span class="tdate"><?= s7dPayments::diffDates($items->date); ?></span>
	</p>
	</div>
	
	<?php
	//$count = json_encode(array_slice(json_decode($items->discussions, true),4,4));

	foreach (json_decode($items->discussions) as $kd => $ditens):?>

		<div class="dPagTopicItems">
		<span class="dImgUser">
			
		</span>
		<p class="dMessage"><?= str_replace(array("[1][","][2]","[l]","\r\n"),array('<a href="','" target="_blank" >',"</a>","<br />"),$ditens-> message);?></p>
		<p class="dDadosTopic">
			<span class="tname"><?= s7dPayments::getUserName('name',$ditens->userId);?></span> - 
			<span class="tdate"><?= s7dPayments::diffDates($ditens->datePost); ?></span>
		</p>
		</div>
	<?php endforeach ?>

<?php endforeach ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#pega").click(function(){
    	var ip = $(".ip").val();
    	var ipa = $(".ipa").val();

    	var junt = '[1]['+ip+'][2]'+ipa+'[l]';

    	var env = insertAtCaret('textareaid',junt);

    	return env;

    });

})

function fm(){
	document.getElementById("dPagLink").style.display="none";
}
function fa(){
	document.getElementById("dPagLink").style.display="block";
}
</script>





<div class="dPagTopicMessage">
<div id="dPagLink" style="display:none">
<span class="fm" onclick="fm()">X</span>
<input type="text" class="ip" value="http://">
<input type="text" class="ipa" value="" placeholder="Texto do link">
<span id="pega" onclick="fm()">Inserir</span>
</div>
<span onclick="fa()" class="dPagLink"></span>
<h5>Sua resposta</h5>
<form action="" method="post">
	<textarea name="topic[discussions][message]" id="textareaid"></textarea>
	<input type="submit" value="enviar" name="tenviar">
</form>
</div>