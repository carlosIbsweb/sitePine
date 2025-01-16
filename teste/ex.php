<?php
$j = '[{"title":"1","data":"23/05/2018 23:45","price":600,"course":"Tarde - Crianças de 4 a 11 anos","img":"images/img_produto3-2.jpg","id":"40","catid":"33","cattitle":"Tarde 14h - 18h","courseCode":"tarde-14h-18h","criancas":{"_eo89o9vuj":{"nome-label":"Nome da Criança","nome":"RAFAEL SEVERUS MARQUES PESSOA","nascimento-label":"Data de Nascimento","nascimento":"19/04/2013","escola-label":"Nome da Escola","escola":"MARISTINHA PIO XII","medicamento-label":"A criança toma algum medicamento ou apresenta alguma condição de saúde que a impeça de realizar atividades físicas apropriadas para sua faixa etária?","medicamento":"NÃO","alergia-label":"A criança apresenta alguma alergia ou intolerância a algum medicamento ou alimento?","alergia":"NÃO","autorizada-label":"Na eventualidade de quedas, arranhões ou mal estar, a enfermeira está autorizada a administrar os primeiros socorros e administrar os medicamentos básicos?","autorizada":"Sim"}}}]';




$arr = array();
foreach(json_decode($j) as $ak=> $item)
{
  array_push($arr,$item->catid);
}


print_r($arr);