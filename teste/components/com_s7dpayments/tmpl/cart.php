<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/

// No direct access.
defined('_JEXEC') or die;

$user = JFactory::getUser();
$userid = $user->id;

$count = count(json_decode(paymentsCart::getCart($cartid,'cartid','products'),true));
?>

<div class="dcartitems">
	<?php !empty($userid) ? require_once('default_logout.php') : null; ?>
	<a class="mcart" href="<?= $url;?>?cart"><span class="dcartm">Meu carrinho </span><span class="dmcartc"><?= $count == 0 ? ' Vazio' : ($count >= 2 ? '<span class="scnt">'.$count.'</span> <span class="scni">Items</span>' : '<span class="scnt">'.$count.'</span> <span class="scni">Item</span>'); ?></span></a>
</div>
