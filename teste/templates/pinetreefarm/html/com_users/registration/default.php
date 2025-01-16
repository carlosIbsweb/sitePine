<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$_SESSION['derror'] = true;
header("Location:".$_SESSION['serrorlogin'] );
?>
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<div class="registration<?php echo $this->pageclass_sfx?>">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
			<?php endif; ?>

			<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate" enctype="multipart/form-data">

				<div class="form-group">
					<button type="submit" class="btn btn-primary validate"><?php echo JText::_('JREGISTER');?></button>
					<a class="btn btn-danger" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="registration.register" />
				</div>
				<?php echo JHtml::_('form.token');?>
			</form>
		</div>
	</div>
</div>
