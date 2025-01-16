<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Cronologia
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2018 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access
defined('_JEXEC') or die;



JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


if(isset($_GET['executarCronologia'])){
    
    
    echo '<form enctype="multipart/form-data" action="" method="POST">
   
    <input type="hidden" name="" value="" />
    Enviar esse arquivo: <input name="cronofile" type="file" />
    <input type="submit" value="Enviar arquivo" />
</form>';

$type = $_FILES['cronofile']['type'];

if($type === 'text/csv'){
    



	$json = file_get_contents(__DIR__.'/dadosmm.csv');

	// Exemplo de scrip para exibir os nomes obtidos no arquivo CSV de exemplo

$delimitador = isset($_GET['delimitador'] ? $_GET['delimitador'] : ',';

$cerca = "'";

$arquivo = $_FILES['cronofile']['name'];
           $arquivoTemp = $_FILES['cronofile']['tmp_name'];
           $diretorio = JPATH_ROOT.'/'.$arquivo;
           
           move_uploaded_file($arquivoTemp, $diretorio);
           

// Abrir arquivo para leitura
$f = fopen($diretorio, 'r');

if ($f) {
	JFactory::getDbo()->truncateTable('#__cronologia_');

	$arr = array();
    // Ler cabecalho do arquivo
    $cabecalho = fgetcsv($f, 0, $delimitador, $cerca);
    $indD = array_search('data',$cabecalho);

    // Enquanto nao terminar o arquivo
    while (!feof($f)) { 

    	$arr1 = array();
        // Ler uma linha do arquivo
        $linha = fgetcsv($f, 0, $delimitador, $cerca);

        
        $linha = array_map(function($v) use($indD){

        	static $in = 0;
        	//Converter para utf8
        	$v = utf8_encode($v);

        	if($in == $indD){
        		$res = date('Y-m-d',strtotime(str_replace('/','-',$v)));
        	}else{
        		$res = $v;
        	}
        	$in++;
        	return "'".trim(addslashes($res))."'"; 
        }, $linha);

        if (!$linha) {
            continue;
        }

        array_push($arr1,$cabecalho,$linha);
        array_push($arr, $arr1);
        // Montar registro com valores indexados pelo cabecalho
        $registro = array_combine($cabecalho, $linha);

        // Obtendo o nome
        //echo $registro['tipo_de_instalacao'].PHP_EOL;
    }
    fclose($f);
}

    //inserir dados
	foreach($arr as $k=> $bb)
	{
		//print_r($bb[0]);
		$this->insert($bb[0],$bb[1]);
	}
	
	echo '<p style="background:#239922; padding:5px; color:#fff">Dados inseridos com sucesso. Total <b>'.count($arr).'</b> arquivos inseridos</p>';
	
	//remover arquivo
	unlink($diretorio);
}else{
    
    echo '<p style="background:red; padding:5px; color:#fff">Arquivo inválido. Tem que ser CSV sá.</p>';
}
exit();
}

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_cronologia') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'dadoform.xml');
$canEdit    = $user->authorise('core.edit', 'com_cronologia') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'dadoform.xml');
$canCheckin = $user->authorise('core.manage', 'com_cronologia');
$canChange  = $user->authorise('core.edit.state', 'com_cronologia');
$canDelete  = $user->authorise('core.delete', 'com_cronologia');

// Get the document object.
$doc = JFactory::getDocument();
$doc->addScript(JUri::base(true).'/components/com_cronologia/assets/js/printPreview.js');
$doc->addStyleSheet(JUri::base(true).'/components/com_cronologia/assets/css/style.css');

?>
<?= $this::lmp( 'cronotop', $style = 'xhtml');?>
<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">
	
	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

	
	<div class="table-wrapper-scroll-y table-responsive-portos">
	
			<?php foreach ($this->items as $i => $item) : ?>
				<table class="table table-fixed responsive" cellpadding="3" cellspacing = "0" id="dadoList">
				<tbody>
				<tr>
					<td class="col-xs-2"><b>Diploma</b></td>
					<td><?= $item->diploma;?></td>
				</tr>
				<tr>
					<td class="col-xs-2"><b>Número</b></td>
					<td>
						<?php if (!empty($item->link)): ?>
							 <a target="_blank" href="<?= $item->link;?>"><?= $item->numero;?> <i class="fa fa-external-link-square" aria-hidden="true"></i></a>
						<?php else: ?>
							<?= $item->numero;?>
						<?php endif;?>
						</td>
				</tr>
				<tr>
					<td class="col-xs-2"><b>Data</b></td>
					<td><?= date('d/m/Y',strtotime($item->data));?></td>
				</tr>
				<tr>
					<td class="col-xs-2"><b>Descrição</b></td>
					<td><?= $item->descricao;?></td>
				</tr>
				<tr>
					<td class="col-xs-2"><b>Status</b></td>
					<td><?= $item->status;?></td>
				</tr>
			</tbody>
			</table>
			<?php endforeach; ?>

</div>
			
	<?php /*<div class="table-wrapper-scroll-y">
	<table class="table table-fixed responsive" cellpadding="3" cellspacing = "0" id="dadoList">
		<thead>
		<tr>
				<th class='col-xs-2 atp a'>
				<?php echo JHtml::_('grid.sort',  'COM_CRONOLOGIA_DADOS_DIPLOMA', 'a.diploma', $listDirn, $listOrder); ?>
				</th>
				<th class='col-xs-2 atp b'>
				<?php echo JHtml::_('grid.sort',  'COM_CRONOLOGIA_DADOS_NUMERO', 'a.numero', $listDirn, $listOrder); ?>
				</th>
				<th class='col-xs-2 atp c'>
				<?php echo JHtml::_('grid.sort',  'COM_CRONOLOGIA_DADOS_DATA', 'a.data', $listDirn, $listOrder); ?>
				</th>   
				<th class='col-xs-6 atp d'>
				<?php echo JHtml::_('grid.sort',  '', 'a.descricao', $listDirn, $listOrder); ?>
				</th>


							<?php if ($canEdit || $canDelete): ?>
					<th class="center">
				<?php echo JText::_('COM_CRONOLOGIA_DADOS_ACTIONS'); ?>
				</th>
				<?php endif; ?>

		</tr>
		</thead>
		<tbody>

		<?php foreach ($this->items as $i => $item) : ?>
			
			<tr class="row<?php echo $i % 2; ?>">

				<td class="col-xs-2 a">
					<?php echo $item->diploma; ?>
				</td>
				<td class="col-xs-2 link b">
					<?php if(!empty($item->link)) :?>
						<a href="<?= $item->link; ?>" target="_blank" class="li"><?php echo $item->numero; ?></a>
					<?php else : ?>
						<?php echo $item->numero; ?>
					<?php endif ?>
				</td>
				<td class="col-xs-2 dt c">
					<?php 
						$dateFinal = explode(" ", $item->data);
						$dateFinal = explode("-", $dateFinal[0]);
						$dateFinal = $dateFinal[2].'/'.$dateFinal[1].'/'.$dateFinal[0];	
						echo $dateFinal;
						//echo $item->data; 
					?>
				</td>
				<td class="col-xs-6 desc e">
					<?php echo $item->descricao; ?>
				</td>


								<?php if ($canEdit || $canDelete): ?>
					<td class="center">
					</td>
				<?php endif; ?>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
*/ ?>
	
	<?php if ($canCreate) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_cronologia&task=dadoform.edit&id=0', false, 0); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo JText::_('COM_CRONOLOGIA_ADD_ITEM'); ?></a>
	<?php endif; ?>

<div class="col-md-12 botao">
	<button class="btnPrintB btnPrint">Imprimir</button>
</div>
	<!--<input type='button' id='button' value='Imprimir' onclick='printData();'>-->
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>

	<?php echo JHtml::_('form.token'); ?>

</form>

<div class="col-md-12">
	<?= $this::lmp( 'cronobottom', $style = 'xhtml');?>
</div>

<script type="text/javascript">
 jQuery(function($){
 	function printData()
	{
	   var divToPrint=document.querySelector(".table-responsive-portos");
	   newWin= window.open("");
	   newWin.document.write('<link rel="stylesheet" href="https://portosprivados.com.br/components/com_cronologia/assets/css/style.css" type="text/css" />');
	   newWin.document.write(divToPrint.outerHTML);
	   newWin.print();
	   newWin.close();
	}

	$('button').on('click',function(){
	var vailist = $('#dadoList').find('a');
       vailist.each(function(){
       		$(this).removeAttr('href');
       		$(this).parent('.link').css({"text-decoration":"none"})
       })
	printData();
	})
   })
</script>


<?php if($canDelete) : ?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {

		if (!confirm("<?php echo JText::_('COM_CRONOLOGIA_DELETE_MESSAGE'); ?>")) {
			return false;
		}
	}
</script>

<?php endif; 


