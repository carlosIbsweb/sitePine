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

/**
 * Class S7dpaymentsController
 *
 * @since  1.6
 */
class S7dpaymentsController extends JControllerLegacy
{
    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return   JController This object to support chaining.
     *
     * @since    1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        require_once JPATH_COMPONENT . '/helpers/s7dpayments.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'payments');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }

    public function dCourses()
    {   
        if(is_array($_POST['data'])):
        foreach($_POST['data'] as $k=> $items):
            foreach($items as $c=> $me):
                if($c == 'value' and $items['name'] == 'blds'):
                   $id[$me] = $me;
               echo $me;
                endif;
            endforeach;
        endforeach;
        endif;

         if(is_array($_POST['data'])):
        foreach($_POST['data'] as $k=> $items):
            foreach($items as $c=> $me):
                if($c == 'value' and $items['name'] == 'delete'):
                   $iddel[$me] = $me;
                endif;
            endforeach;
        endforeach;
        endif;

        $list = array();
        foreach(json_decode(self::getItems('s7dpayments','coursesid',$_SESSION['itemid'])) as $kk=> $it):
            if($it == $id[$it]):
                $nkeysb = explode(":",$kk)[0].':bl';
                $list[$nkeysb] = $it;
            else:
                $nkeys = explode(":",$kk)[0];
                $list[$nkeys] = $it;
            endif;
        endforeach;
        
        $json = json_encode($list);
        $dellist = array();
        foreach(json_decode($json) as $kk=> $it):
            if($it != $iddel[$it]):
               $dellist[$kk] = $it;
            endif;
        endforeach;

        $codjson = json_encode($dellist);


        $db = JFactory::getDbo();

        $query = "UPDATE #__s7dpayments
        SET coursesid = '".$codjson."'
        WHERE id ='".$_SESSION['itemid']."'
        ";
        $db->setQuery($query);

        if(!empty($json)):
            $db->query();
        endif;

        return true;
    }

    protected function getItems($table,$name,$id)
    {
        $cdb = JFactory::getDbo();

        $cdb->setQuery("SELECT #__$table.$name FROM #__$table where id = ".$id);

        return $cdb->loadResult();
    }

}

