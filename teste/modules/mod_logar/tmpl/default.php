<?php
/**
 * @version		$Id: default.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_syndicate
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
echo $_POST['password'];
?>

<script type="text/javascript">
 function resizeIframe(obj){
    {obj.style.height = 0;};
    {obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';}
 }
</script>

<iframe target="_blank" src="/modules/mod_logar/tmpl/logar.php"  width="100%" rameBorder="0" scrolling="no" onload='javascript:resizeIframe(this);' style="border:none"></iframe>
