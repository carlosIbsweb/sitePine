<?php
$a = file_get_contents('https://pinetreefarm.com.br/temporada-2022/20-a-24-de-junho');

$doc = new DOMDocument();
$doc->loadHTMLFile("https://pinetreefarm.com.br/temporada-2022/20-a-24-de-junho");
$v = $doc->getElementById('tabs');



$j = $v->getElementsByTagName('li');

$saida = [];
$meses = [];

$section = array();
$periodo = array();
$turnos = array();

foreach($j as $k=> $m){

	$el = $doc->getElementById('section-'.$k);
	$hum = $el->getElementsByTagName('div');

	foreach($hum as $k=> $hu)
	{
		$class = $hu->attributes->getNamedItem('class')->nodeValue;
		
		if($class == 'dStoreItems')
		{
			$title = $hu->getElementsByTagName('h4')[0]->nodeValue;
			$title = end(explode('de',$title));
			$periodo[] = $title; 
		}
	}
}

$perid = array_unique(array_values($periodo));

foreach($j as $k=> $m){

	$el = $doc->getElementById('section-'.$k);
	$elid = $el->attributes->getNamedItem('id')->nodeValue;
	$hum = $el->getElementsByTagName('div');
	$ids = array();

	array_push($turnos, $m->nodeValue);
}


foreach($j as $k=> $m){

	$el = $doc->getElementById('section-'.$k);
	$elid = $el->attributes->getNamedItem('id')->nodeValue;
	$hum = $el->getElementsByTagName('div');
	$ids = array();

	
	foreach($hum as $k=> $hu)
	{
		$class = $hu->attributes->getNamedItem('class')->nodeValue;
		$modos = array();
		
		if($class == 'dStoreItems')
		{
			$hums = $hu->getElementsByTagName('div');
			$title = $hu->getElementsByTagName('h4')[0]->nodeValue;
			$title = end(explode('de',$title));
			$periodo[] = $title; 
			
			foreach($hums as $k=> $hm)
			{
				$class = $hm->attributes->getNamedItem('class')->nodeValue;
				$p = array();
				if($class == 'PriceDe')
				{
					array_push($p,trim($hm->nodeValue));
				}

				if($class == 'dSprice')
				{
					array_push($p,trim($hm->nodeValue));

				}
			}
		}	
	}
	$section[] = $p;
}

echo json_encode($section);


//print_r(array_combine($perid,$ids));

?>


<script>	


</script>