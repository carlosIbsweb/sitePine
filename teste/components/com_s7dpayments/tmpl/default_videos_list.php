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

if(count(json_decode(s7dPayments::getItens()[0]->videos),true) >= 1)
{
	foreach(json_decode(s7dPayments::getItens()[0]->videos) as $ivds)
	{
		foreach($ivds as $item)
		{
			if($item->title != '')
			{
				echo '<a href="'.$url.'?cat='.$_GET['cat'].'&courseId='.$_GET['courseId'].'&video='.$item->link.'">'.$item->title.'</a>';
			}
		}
	}
}
else
{
	echo '<p>Não há vídeos</p>';
}
