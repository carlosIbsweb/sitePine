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


jimport('joomla.application.component.view');

/**
 * View class for a list of S7dpayments.
 *
 * @since  1.6
 */
class S7dpaymentsViewPayments extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
         $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
        }

        S7dpaymentsHelper::addSubmenu('payments');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT . '/helpers/s7dpayments.php';

        $state = $this->get('State');
        $canDo = S7dpaymentsHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_S7DPAYMENTS_TITLE_PAYMENTS'), 'payments.png');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/payment';

        if (file_exists($formPath))
        {
            if ($canDo->get('core.create'))
            {
                JToolBarHelper::addNew('payment.add', 'JTOOLBAR_NEW');
                JToolbarHelper::custom('payments.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
            }

            if ($canDo->get('core.edit') && isset($this->items[0]))
            {
                JToolBarHelper::editList('payment.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state'))
        {
            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::custom('payments.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('payments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            }
            elseif (isset($this->items[0]))
            {
                // If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'payments.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('payments.archive', 'JTOOLBAR_ARCHIVE');
            }

            if (isset($this->items[0]->checked_out))
            {
                JToolBarHelper::custom('payments.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state))
        {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
            {
                JToolBarHelper::deleteList('', 'payments.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            }
            elseif ($canDo->get('core.edit.state'))
            {
                JToolBarHelper::trash('payments.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_s7dpayments');
        }

        // Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_s7dpayments&view=payments');

        $this->extra_sidebar = '';

        JHtmlSidebar::addFilter(

            JText::_('JOPTION_SELECT_PUBLISHED'),

            'filter_published',

            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

        );
        
    }

    /**
     * Method to order fields 
     *
     * @return void 
     */
    protected function getSortFields()
    {
        return array(
            'a.`id`' => JText::_('JGRID_HEADING_ID'),
            'a.`name`' => JText::_('COM_S7DPAYMENTS_PAYMENTS_TITLE'),
            'a.`state`' => JText::_('JSTATUS'),
            'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
            'a.`created_by`' => JText::_('COM_S7DPAYMENTS_PAYMENTS_CREATED_BY'),
            'a.`date`' => JText::_('COM_S7DPAYMENTS_PAYMENTS_DATE'),
        );
    }

    //Pegar Status
    public static function getStatus($statusId)
    {
        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();
        $cleanStatus = $statusId;

        $statusId = $db->quote($statusId);

        $text = '';

            //Buscando Dados existentes
            $db->setQuery('SELECT #__s7dpayments_status.status FROM #__s7dpayments_status WHERE statusId = '.$statusId);
            $result = $db->loadResult();

            switch ($cleanStatus) {
                case '1':
                    $text = '<span class="ap-status ap-aguarde">'.$result.'</span>';
                    break;
                case '3':
                case '4':
                    $text = '<span class="ap-status ap-success">'.$result.'</span>';
                    break;
                case '6':
                    $text = '<span class="ap-status ap-dev">'.$result.'</span>';
                    break;
                case '7':
                     $text = '<span class="ap-status ap-error">'.$result.'</span>';
                    break;
                case '0':
                    $text = '<span class="ap-ini ap-status">'.$result.'</span>';
                    break;
                default:
                    $text = $result;
                    break;
            }

        return $text;
    }

    /********************
     Listando Criancas
    ********************/

    public static function getCriList($exEscola = false)
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__s7dpayments'))
            ->where($db->quoteName('state') . ' IN(1)') //Exibir arquivados 2
            ->where($db->quoteName('status') . ' IN(4,3)')
            ->order($db->quoteName('ordering') . ' ASC');
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $escolas = array();
        $emails = array();
        foreach(self::getCatList($_SESSION['fId']) as $ks=> $cat)
        {
            foreach(self::getCatList($cat->id) as $c)
            {

                array_push($escolas,self::getCriListT($c->id,false,1));
                array_push($emails,self::getCriListT($c->id,false,2));
            }
        }

        if($exEscola === 1){
            $ar = array_unique(array_filter(explode(',',implode('',$escolas))));
            return $ar;
        }

        if($exEscola === 2){
            $ar = array_unique(array_filter(explode(',',implode('',$emails))));
            return implode(',',$ar);
        }

        /*************
         Lista json
        *************/

        echo '<table class="criList">';
        echo  
        '<thead>
            <th width="20%">Nome da Criança</th>
            <th>Nasc.</th>
            <th>Escola</th>
            <th>Perg.1</th>
            <th>Perg.2</th>
            <th>Perg.3</th>
            <th width="15%">Nome do Responsável</th>
            <th width="15%">E-mail</th>
            <th width="10%">Tel.</th>
            <th width="10%">Semana</th>
            <th width="10%">Per.</th>
            
        </thead>';

        $soma = 0;
        $i = 0;
        foreach(self::getCatList($_SESSION['fId']) as $ks=> $cat)
        {
            //echo $cat->title.'<br>';
            echo '<tbody class="sm'.$ks.'">';
            foreach(self::getCatList($cat->id) as $c)
            {
                $soma = $soma + self::getCriListT($c->id,1);
                self::getCriListT($c->id);

            }
            echo '</tbody>';
           
        }

        echo '</table>';

        if(empty($soma)){
            echo '<p class="piDanger">Nenhum dado encontrado</p>';
        }

        if(!empty($soma)){
            echo '<p class="piT"><strong>Total de crianças:</strong> '.$soma.'</p>';
        

         //Condições
        echo '<div class="piCond">';
        echo '<h3>Ficha Médica</h3>';
        echo '<table>';
        echo '<tr><td><strong>Perg.1</strong></td><td>A criança toma algum medicamento ou apresenta alguma condição de saúde que a impeça de realizar atividades físicas apropriadas para sua faixa etária?</td></tr>';
        echo '<tr><td><strong>Perg.2</strong></td><td>A criança apresenta alguma alergia ou intolerância a algum medicamento ou alimento?</td></tr>';
        echo '<tr><td><strong>Perg.3</strong></td><td>Na eventualidade de quedas, arranhões ou mal estar, a enfermeira está autorizada a administrar os primeiros socorros e administrar os medicamentos básicos?</td></tr>';
        echo '</table>';
        echo '</div>';
        }

    }

    public static function pegarCategoria($resid = false,$id = '')
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('published') . ' IN (1)') //Exibir arquivados 2
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_s7dpayments'))
            ->where($db->quoteName('level') . ' = ' . $db->quote(1))
            ->order($db->quoteName('id') . ' ASC');
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $title = '';
        $form = '<form action="" method="post" id="selectCategory" class="mForm">';
        $form .= '<select name="fId" onchange="this.form.submit()">';
        $form .= '<option value="">Selecione a Temporada</option>';
        foreach($result as $res){
            $selected = $_SESSION['fId'] == $res->id ? 'selected' : '';
            $form .= '<option value="'.$res->id.'" '.$selected.'>'.$res->title.'</option>';
            if($id == $res->id)
            {
                $title = $res->title;
            }
        }
        $form .= '</select>'; 
        $form .= '</form>';

        if($resid)
        {
            return $title;
        }else{
            return $form;
        }
        
    }


    /*Filtrar escola*/

     public static function escola()
    {
        $form = '<form action="" method="post" id="selectEscola" class="mForm">';
        $form .= '<select name="escolaN" onchange="this.form.submit()">';
        $form .= '<option value="">Selecione a Escola</option>';
        foreach(self::getCriList(1) as $res){
            $selected = $_POST['escolaN'] == $res ? 'selected' : '';
            if($res != 'Selecione'){
                $form .= '<option value="'.$res.'" '.$selected.'>'.$res.'</option>';
            }
            
        }
        $form .= '</select>'; 
        $form .= '</form>';

        return $form;

    }

    //Get Emails
     public static function emails()
    {
        return self::getCriList(2);
    }


    public static function getCategory($id,$nivel = 0)
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('published') . ' IN (1)') //Exibir arquivados 2
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_s7dpayments'))
            ->where($db->quoteName('parent_id') . ' = ' . $db->quote($id))
            ->order($db->quoteName('id') . ' ASC');
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }


        foreach($result as $cat)
        {
            $sint = str_repeat("–&nbsp;", $nivel);
            $sel = isset($_POST['catId']) && $cat->id == $_POST['catId'] ? 'selected' : null;
            if(empty($nivel))
            {
               echo '<option data-type="cat" class="iStrong" value="'.$cat->id.'" '.$sel.'><strong>'.$sint.$cat->title.'</strong></option>'; 
               
            }else{
                echo '<option data-type="cat" value="'.$cat->id.'" '.$sel.'>'.$sint.$cat->title.'</option>'; 
            }
            
            foreach(self::getItems($cat->id) as $item){
                $sint = str_repeat("&nbsp–&nbsp; ", $nivel);
                $sela = isset($_POST['itemId']) && $item->id == $_POST['itemId'] ? 'selected' : null;
                echo '<option data-type="item" class="iCri" value="'.$item->id.'" '.$sela.'>'.$item->title.'</option>';
            }
            self::getCategory($cat->id,$nivel + 1);
        }

    }

        public static function getItems($catid)
    {

        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        //$cats = implode(",",self::getCats($catid));
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__s7dpayments_courses'))
            ->where($db->quoteName('state') . ' = ' . $db->quote('1'))
            ->where($db->quoteName('catid') . ' = ' .$db->quote($catid))
            ->order($db->quoteName('ordering') . ' ASC');
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        return $result;
    }

     public function getCats($id)
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('id') . ' = ' . $id . '  or '.$db->quoteName('parent_id') . ' = ' . $id);
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $cats = [];
        foreach($result as $item)
        {
            array_push($cats,$item->id);
        }

        return $cats;
    }

     public function getCat($id,$tag = 'span')
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('id') . ' = ' . $id);
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $soma = 0;
        foreach($result as $item)
        {
            self::getCat($item->parent_id);

            if($item->level != 0)
            {
                echo '<'.$tag.'>'.$item->title.'</'.$tag.'>';
            }
            
            
        }

        

    }

    /*******************
     Exibindo os dados conforme a categoria escolhida na busca.
    *******************/
    public static function getCatHeader($id)
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__s7dpayments_courses'))
            ->where($db->quoteName('state') . ' = ' . $db->quote('1'))
            //->where($db->quoteName('extension') . ' = ' . $db->quote('com_s7dpayments'))
            ->where($db->quoteName('id') . ' = ' . $db->quote($id))
            ->order($db->quoteName('id') . ' ASC');
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        if(!empty($_POST['itemId']))
        {
            foreach($result as $cat)
            {
                echo self::getCat($cat->catid);
                echo '<span>'.$cat->title.'</span>';
            }
        }
        
        if(isset($_POST['catId']))
        {
            if(!empty($_POST['catId'])){
               self::getCat($_POST['catId']); 
           }else{
            self::getCat($_SESSION['fId']);
            echo '<span>Todos</span>';
           }
            
        }elseif(!isset($_POST['catId']) and !isset($_POST['itemId'])){
            self::getCat($_SESSION['fId']);
            echo '<span>Todos</span>';
        }
    }

    public function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do","dá","com", "I", "II", "III", "IV", "V", "VI"))
    {
        /*
         * Exceptions in lower case are words you don't want converted
         * Exceptions all in upper case are any words you don't want converted to title case
         *   but should be converted to upper case, e.g.:
         *   king henry viii or king henry Viii should be King Henry VIII
         */
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
       }//foreach
       return $string;
    }

    protected function getT($table,$name,$where,$id)
    {
        $cdb = JFactory::getDbo();

        $cdb->setQuery("SELECT #__$table.$name FROM #__$table where $db->quote($where) = ".$id);

        return $cdb->loadResult();
    }

     public function getCatList($id)
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('parent_id') . ' = ' . $id)
            ->order($db->quoteName('lft') . ' ASC');
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        return $result;

    }









    public static function getCriListT($catid,$count = '',$exEscola = false)
    {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__s7dpayments'))
            ->where($db->quoteName('state') . ' IN(1)') //Exibir arquivados 2
            ->where($db->quoteName('status') . ' IN(4,3)')
            ->order($db->quoteName('ordering') . ' DESC');
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $output = '';
        $soma = '';
        //Lista da galera
        $lPine = '';
        $escolas = '';
        foreach($result as $k=> $item)
        {
            
            foreach(json_decode($item->items) as $ks=> $dados)
            {
                
                $buscGet = isset($_POST['catId']) && $_POST['catId'] != '' ? 'catid' : (isset($_POST['itemId']) ? 'id' : false);
                $buscType = isset($_POST['catId']) && $buscGet ? self::getCats($_POST['catId']) : (isset($_POST['itemId']) ? array($_POST['itemId']) : [] );
                $buscType = !empty($buscType) ? in_array($dados->{$buscGet},$buscType) : true;


                if($dados->catid == $catid && $buscType):

     
                foreach($dados->criancas as $id => $cri)
                {   

                    /*Filtrar por escola */
                $escolas .= ','.$cri->escola;
                
                    
                   

                    if(($cri->escola == $_POST['escolaN']) || !isset($_POST['escolaN']) || empty($_POST['escolaN']) ) {
                        $emails .= ','.JFactory::getUser($item->userid)->email;

                    
                    $resp2 = !empty(JFactory::getUser($item->userid)->name2) ? ' <br>2 - <span class="sm2">'.self::titleCase(JFactory::getUser($item->userid)->name2).'</span>' : null;
                    $tel2 = !empty(JFactory::getUser($item->userid)->telefone2) ? '<br>2 - '.self::titleCase(JFactory::getUser($item->userid)->telefone2) : null;
                    $output .= '<tr>';
                    $output .= '<td class="criN"><div class="discC"><span class="dis"></span><span class="dis"></span><span class="dis"></span><span class="dis"></span><span class="dis"></span></div><div class="dc">'.self::titleCase($cri->nome).'</div></td>';
                    $output .= '<td>'.$cri->nascimento.'</td>';
                    $output .= '<td>'.self::titleCase($cri->escola).'</td>';
                    $output .= '<td>'.ucfirst(mb_strtolower($cri->medicamento)).'</td>';
                    $output .= '<td>'.ucfirst(mb_strtolower($cri->alergia)).'</td>';
                    $output .= '<td>'.ucfirst(mb_strtolower($cri->autorizada)).'</td>';
                    $output .= '<td>1 - '.self::titleCase(JFactory::getUser($item->userid)->name).$resp2.'</td>';
                    $output .= '<td>'.JFactory::getUser($item->userid)->email.'</td>';
                    $output .= '<td> 1 - '.JFactory::getUser($item->userid)->telefone.$tel2.'</td>';
                    $output .= '<td class="sm">'.self::getT('categories','title','id',self::getT('categories','parent_id','id',$dados->catid)).'</td>';
                    $output .= '<td>'.$dados->course.'</td>';
                    $output .= '</tr>';

                    $lPine .= JFactory::getUser($item->userid)->email.';'.self::getT('categories','title','id',self::getT('categories','parent_id','id',$dados->catid)).'</br>';

                  
                    $soma = $soma + 1;

                    }
        
                }
                  
                endif; 

            }
        }


        if($exEscola === 1){
            return $escolas;
        }

        if($exEscola === 2){
            return $emails;
        }


         if($count):
           return $soma;
         else:

            if(isset($_GET['mail'])){
                    echo $lPine;
            }else{
                echo $output;
            }
        
    endif;

}



}