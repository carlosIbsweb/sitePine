<?php
/**
* @version   $Id$
* @package   GTranslate
* @copyright Copyright (C) 2008-2017 Edvard Ananyan. All rights reserved.
* @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldLiveChat extends JFormField {

    protected $type = 'live_chat';

    public function getLabel() {
        return '';
    }

    public function getValue() {
        return '';
    }

    public function getInput() {

        $user = JFactory::getUser();
        $name = addslashes($user->name);
        $website = addslashes(JURI::root());

        $live_chat = <<<EOM
        <script>window.intercomSettings = {app_id: "r70azrgx", hide_default_launcher: false, 'name': '{$name}', 'website': '{$website}', 'platform': 'joomla'};(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/r70azrgx';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
EOM;
        return $live_chat;
    }
}