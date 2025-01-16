<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2015. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldDcourses extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Dcourses';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{	

            if(is_array( json_decode(self::getItems('s7dpayments','items',$_GET['id']),true) )):
               $priceTotal = 0;
               $produtos   = [];
               $criancas = [];
               foreach(json_decode(self::getItems('s7dpayments','items',$_GET['id'])) as $k => $items):
                  $valor = 0; 
                  $cartItemTitle = self::getCat(self::getCat($items->catid,'parent_id'),'title');
            
                  if($items->discount){
                     //Preço com desconto
                     $originalPrice    = $items-> price;
                     $finalPrice    = $items->price - ($items->price * ($items-> discount/100));
            
                  }else{
                     $finalPrice = $items-> price;
                  }


                  foreach($items->criancas as $cri)
                  {
                  	$valor = $valor + count($cri);
                  	$criOut[] = '<td>'.$cri->nome.'</td>';

                  	array_push($criancas,implode('',$criOut));
                  }

                  $itemPrice = $finalPrice * $valor;

                  $priceTotal = $priceTotal + $itemPrice;
                  $res = '<tr class="criRow">';
            	  $res .= '<td class="tresph">Ingresso</td><td><span class="criTi"><strong>'.$cartItemTitle.'</strong> - '.$items->course.'</span>';
            	  $res .= '<table class="table table-cri">';
            	  
            	  foreach($items->criancas as $cri)
                  {
                  	$res .= '<tr>';
                  	$res .= '<td>'.$cri->nome.'</td>';
                  	$res .= '</tr>';
                  }
                  
                  $res .= '</table>';
            	  $res .= '</td>';
            	  $res .= '<td class="tresph">Categoria</td><td>'.$items->cattitle.'</td>';
            	  $res .= '<td class="tresph">Quant.</td><td>'.$valor.'</td>';
            	  $res .= '<td class="tresph">Preço</td><td> R$ '.number_format( $itemPrice , 2, ',', '.').'</td>';
            	  $res .= '</tr>';

            	  array_push($produtos,$res);
            	 
                endforeach;
            endif;

            
                  
             if(is_array($produtos)){
            /*Produtos*/
            $pOut[] = '<div class="table-pine">';
            $pOut[] = '<table class="table table-action">';
            $pOut[] = '<thead><tr><th>Ingresso</th><th>Categoria</th><th>Quant.</th><th>Preço</th></tr></thead>';
            $pOut[] = implode('',$produtos);
            $pOut[] = '<tr><td colspan="4" class="pPrice">Total <strong>R$ '.number_format( $priceTotal , 2, ',', '.').'</strong></td></tr>';
            $pOut[] = '</table>';
            $pOut[] = '</div>';

            $prodF = implode('',$pOut);

            echo $prodF;
         }
	}

	protected function getItems($table,$name,$id)
	{
		$cdb = JFactory::getDbo();

        $cdb->setQuery("SELECT #__$table.$name FROM #__$table where id = ".$id);

        return $cdb->loadResult();
	}

	protected function getCat($id,$data)
	{
		$db = JFactory::getDbo();
		$id = $db->quote($id);

        $db->setQuery("SELECT #__categories.$data FROM #__categories where id = $id");

        return $db->loadResult();
	}

	public static function getAbstract()
	{

	}
}