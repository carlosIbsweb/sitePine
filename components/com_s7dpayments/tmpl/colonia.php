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

//get the active menu item id
$app = JFactory::getApplication();
$menu   = $app->getMenu();
$active   = $menu->getActive();
$level = $active->level;
$itemAlias = $active->alias;

//helpes
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300&family=Fruktur&family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="<?= $base?>/components/com_s7dpayments/assets/css/component.css?1" />
<?php if (paymentsCart::getCat()): ?>
<?php 
	$cats = paymentsCart::getCat();
	$catsa = paymentsCart::getCat();

	/*$catsa = array_filter($cats,function($it){
		$j = json_decode($it->params)->tabela;
		return $j;
	});*/

	$cats = array_filter($cats,function($it){
		$j = json_decode($it->params)->tabela;
		return $j;
	});

	if(isset($_GET['p'])){
		print_r($cats);
	}
	
	foreach($cats as $k=> $tabs)
	{
		$tab = json_decode($tabs->params)->tabela;
		$list = paymentsCart::getTable(paymentsCart::getCategoryC('parent_id',$tabs->id,'id'));
		$preco = $list->price;

		//print_r(paymentsCart::getCategoryC('parent_id',$tabs->id,'title'));

	}

	if(isset($_GET['ff'])){
	print_r($catsa);

	foreach($catsa as $b)
	{
		echo json_decode($b->params)->diarias;
	}

	echo $preco;
}
?>

<div class="coloniaPine" style="display: none">
	<div class="dist">
		<?= JHtml::_('content.prepare', '{loadposition colonia-top}');?>
	</div>
	<div class="coloniaPage">
		<h1></h1>
		<?= JHtml::_('content.prepare', '{loadposition colonia-title}');?>
		
		<div class="coloniaPageItems container">
			<?php foreach(paymentsCart::getCat() as $k=> $item): ?>
				<div class="coloniaPageItemcContent" data-back="<?= json_decode($item->params)->image;?>" data-color="<?= json_decode($item->params)->image_alt;?>">
					<a href="<?= $itemAlias.'/'.$item->alias;?>" class="<?= $item-> alias; ?>"  title="<?= $items->title;?>">
						<span class="cpitop"></span>
						<span class="cpibottom"></span>

						<span class="cpiData">
							<?= trim($item->title);?>
						</span>
			
					</a>
				</div>
			<?php endforeach ?>
		</div>
	</div>
	<div class="dist">
		<?= JHtml::_('content.prepare', '{loadposition colonia-bottom}');?>
	</div>
</div>
<?php else: ?>
	Temporada inexistente
<?php endif;?>

<script>

	//Titulo da categoria
	let titleCat = document.querySelector('.dmmais').querySelector('a').innerText
		document.querySelector('.coloniaPage').querySelector('h1').innerText = sepMes(titleCat,false)

	console.log(titleCat)
	let mbody = document.querySelector('#sp-main-body')
	mbody.querySelector('.container').classList.remove('container')

	document.querySelector('.dmenu').classList.add('container')
	let colItems = document.querySelectorAll('.coloniaPageItemcContent');
	let colorsItems = [
		'#01a859',
		'#0099de',
		'#fd1f0d',
		'#640058',
		'#ffa000',
		'#ff98cf',
		'#6765ff',
		'#ff7900',
		'#f12b62',
	];

//Adicionar container na div especifica

let coloniaC = document.createElement('div')
coloniaC.setAttribute('class','container')
let ant = document.querySelector('.colonia-container')
let antiga = ant.querySelector('.sppb-container-inner');
antiga.cloneNode(true)
let s = antiga.parentNode

s.replaceChild(coloniaC,antiga)
coloniaC.appendChild(antiga)
//----------------------------------------------------------------

colItems.forEach(function(item,k){

	let cpiData = item.querySelector('.cpiData')
		
		item.querySelector('.cpibottom').style.backgroundImage = 'url(data:image/svg+xml;base64,'+setflip(colorsItems[k])+')'
		//item.querySelector('.cpibottom').style.backgroundColor = colorsItems[k]

		cpiData.style.background = colorsItems[k]
		let ipText = cpiData.innerText
		ipText = sepMes(ipText)
		cpiData.innerText = ipText

		//caso não tenha uma imagem na categoria, ele ira substituir pela biblioteca de imagens e cores.
		let imgCategoria = item.getAttribute('data-back')
		let imgCategoriaColor = item.getAttribute('data-color')

		if(imgCategoria){
			item.querySelector('a').style.background = 'url('+imgCategoria+')'
			if(imgCategoriaColor)
				item.querySelector('.cpibottom').style.backgroundImage = 'url(data:image/svg+xml;base64,'+setflip(imgCategoriaColor)+')'
				cpiData.style.background = imgCategoriaColor
		}else{
			item.querySelector('a').style.background = colorsItems[k]+' url(/images/imagesColonia/colonia'+(k+1)+'.jpg)'
		}
		

})

//Moldura das imagens com cor
function setflip(color){
	var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1366 263"><defs><style>.cls-1{fill:'+color+';}</style></defs><title>Asset 2</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M1366,138.38c-25.34-23.69-65.39-24.81-70.5-24.88-75.07-1.07-106.12,70.08-166,107-127.24,78.45-242.51-85.32-500-85-173,.21-200,74.25-398,65C136.57,196.07,57.35,175.06,0,155V263H1366Z"/><circle class="cls-1" cx="1062.5" cy="36.5" r="36.5"/><circle class="cls-1" cx="1121.5" cy="134.5" r="30.5"/><circle class="cls-1" cx="1008" cy="143" r="69"/></g></g></svg>';		
	var encoded = window.btoa(svg);

	return encoded
}

//Separando o mês e dando um quebra de linha
function sepMes(mes,del = true){
	var p = mes
	p = p.replace("\n",'')
	p = p.split(' ')

	n = p.length-1
	if(del)
	delete p[n-1]
	p.splice(n,-n,"\n")
	var a = document.querySelector('body')
	return p.join(' ')
}

document.addEventListener('DOMContentLoaded',function(){
	document.querySelector('.coloniaPine').removeAttribute('style')
})
</script>

 