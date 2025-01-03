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


<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Meu carrinho</a>
<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
  <p class="dropdown-item-text"><?= $count == 0 ? ' Vazio' : ($count >= 2 ? $count.' Items' : $count.' Item'); ?></p>
  	<div class="dropdown-divider"></div>
  	<a class="dropdown-item" href="<?= $url;?>?cart">Ver carrinho</a>
  	<?php !empty($userid) ? require_once('default_logout.php') : null; ?>
</div>