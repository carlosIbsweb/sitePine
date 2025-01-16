<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/

// No direct access.
defined('_JEXEC') or die;

$return = base64_encode(JUri::base().$menuLink.'?user=login');

?>

<form action="" method="post" id="login-form" class="form-inline">
	<div class="dloguser">Olá, <?= explode(" ",$user->name)[0]; ?></div>
		<input type="submit" name="Submit" class="btnlog" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</form>