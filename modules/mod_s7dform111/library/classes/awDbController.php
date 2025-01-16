<?php

/**
 * @package     
 * @subpackage  mod AwForm
 **/

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseFactory;


/********
 Classe Aw DbController.
 Desenvolvido por Carlos (IBS WEB)
********/

class awDbController {


    public static function setDb($iPosts,&$params,$iFiles,$moduleId)
    {

        $dbData = explode(',',$params->get('dataDb'));
        $user = Factory::getUser();

        $dbColumn = array();
        $dbValues = array();

        //Vars Inputs
        parse_str(http_build_query($iPosts));

        if(!$params->get('activDb'))
        {
            return false;
            exit();
        }

        //Vars globais.
        $awToken = $_SESSION['awToken'];
        $date = date('Y-m-d H:m:s');
        $formId = $moduleId;

        // Initialiase variables.
        $db = Factory::getDbo();

        foreach($dbData as $d)
        {
            $dN = $d;
            $dV = $$dN;

            //$dV = is_array($dV) ? implode(", ",$dV) : $dV;
            if(is_array($dV)) {

                //Verificar se é um array multidimicional
                if(awUtilitario::is_multi_array($dV)){
                    $dV = json_encode(array_values($dV));
                }else{
                    $dV = implode(", ",$dV);
                }
            }

            //getVars
            $gV = explode(':',$d);
            if(count($gV) > 1)
            {
                list($nN,$nV) = $gV;
                $dN = $nN;
                $dV = isset($$nV) ? $$nV : $nV;
            }

            //Joomla
            if($params->get('awJoomla'))
            {
                $awCampo = $params->get('awJCampo');
                list($awCampoName,$awCampoText) = explode(':',$awCampo);

                $dV = $d == $awCampoName ? awValid::validJoomla($params->get('awJoomla'),$dV) : $dV;
            }

            array_push($dbColumn,$db->quoteName($dN));
            array_push($dbValues,$db->quote($dV));
        }
        

        if($params->get('dataMedia'))
        {
            array_push($dbColumn,$db->quoteName($params->get('dataMedia')));


            $dadosUser = isset($_POST['aw_anexos']) ? call_user_func_array('array_merge_recursive', $_POST['aw_anexos']) : [];




            /*print_r(awFileUploader::upload($iFiles,$moduleId,$dadosUser));
            return false;

            $dadosUser = [];

            //$filesDados = self::getDados($params->get('db'),$user->id)[0]->anexos;
            //$dadosUser = json_decode($filesDados) ? json_decode($filesDados,true) : [];*/

            array_push($dbValues,$db->quote(awFileUploader::upload($iFiles,$moduleId,$dadosUser)));
        }
        

        $query = $db->getQuery(true);
        

        //Adicionar tabela e campos caso não existam.
        self::updateTable($params->get('db'), $dbColumn);


        if(self::salvarDados($params->get('db'), $user->id, $dbColumn, $dbValues,$formId,$params)){
            return true;
        }else{
            echo 'oieee';
        return;
            return false;
        }

    
        
        // Create the base insert statement.
        /*$query->insert($db->quoteName($params->get('db')))
            ->columns($dbColumn)
            ->values(implode(",",$dbValues));
        
        // Set the query and execute the insert.
        $db->setQuery($query);
        
        try
        {
            $db->execute();
            
        }
        catch (RuntimeException $e)
        {
            echo awUtilitario::awMessages(JError::raiseWarning(500, $e->getMessage()),'danger');
            return false;
        }*/

        return true;
    }

/**********
 *Tabela relacionada
**********/
public static function setTableRelated(&$params,$iPosts,$insertId)
{
    // Initialiase variables.
    $db    = Factory::getDbo();

    //Vars Inputs
    parse_str(http_build_query($iPosts));

    //Vars globais.
    $awToken = $_SESSION['awToken'];
    $date = date('Y-m-d H:m:s');

    $tableRelated = $params->get('tableRelated');
    $campoRelated = $params->get('campoRelated');
    $camposRelated = $params->get('camposRelated');

    $camposRelated = explode(',',$camposRelated);
    //Transformar campos em var relacionados.

    //Dados de inserção
    $cols = array();
    $vls = array();

    foreach($camposRelated as $item)
    {
        $cmps = explode(':',$item);
        list($cData,$cVal) = $cmps;

        $cVal = isset($$cVal) ? $$cVal : $cVal;

        //is Array
        $cVal = is_array($cVal) ? implode(", ",$cVal) : $cVal;

        array_push($cols,$db->quoteName($cData));
        array_push($vls,$db->quote($cVal));
    }

    array_push($cols,$campoRelated);
    array_push($vls, $insertId);

    $query = $db->getQuery(true);
    
    // Create the base insert statement.
    $query->insert($db->quoteName($tableRelated))
        ->columns(array(implode(',',$cols)))
        ->values(implode(',',$vls));
    
    // Set the query and execute the insert.
    $db->setQuery($query);
    
    try
    {
        $db->execute();
    }
    catch (RuntimeException $e)
    {
        echo awUtilitario::awMessages(JError::raiseWarning(500, $e->getMessage()),'danger');
        return false;
    }

    return true;
}

    /*
    * Trazer os dados da tabela com o id do usuário
    */
    public function getDados($tabela,$id, $params){
        // Criar uma instância da classe de banco de dados
        $db = Factory::getDBO();

        if(empty($id) || $params->get('editUser') != 1){

            return ['user' => false];
        }

        // Criar uma consulta
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName(trim($tabela)));
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($id));

        // Executar a consulta
        $db->setQuery($query);

        // Obter os resultados
        $results = $db->loadObjectList();


        return $results;
    }


    /*
    * Salavar os dados desse usuário
    */
    public static function salvarDados($tabela, $id, $dbColumn, $dbValues,$formId,&$params,$isertId = false) {

        // Criar uma instância da classe de banco de dados
        $db = Factory::getDBO();



        if($params->get('editUser')){
            // Verificar se já existe um registro com o user_id especificado
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from($db->quoteName(trim($tabela)));
            $query->where($db->quoteName('user_id') . ' = ' . $db->quote($id));
            $db->setQuery($query);


            try {

                $registroExistente = $db->loadAssoc();
            } catch (Exception $e) {
                Factory::getApplication()->enqueueMessage('Erro ao atualizar registro: ' . $e->getMessage(), 'error');
            }
        }


        if ($registroExistente && $id !== 0 && $params->get('editUser')) {
            // Se o registro existir, atualize-o
            $query = $db->getQuery(true);

            // Construa a parte SET da consulta UPDATE
            $set = array();
            for ($i = 0; $i < count($dbColumn); $i++) {
                $set[] = $dbColumn[$i] . ' = ' . $dbValues[$i];
            }

            // Condição para identificar o registro a ser atualizado
            $condicao = $db->quoteName('user_id') . ' = ' . $db->quote($id);

            $query->clear()
                  ->update($db->quoteName($tabela))
                  ->set($set)
                  ->where($condicao);

            try {
                $db->setQuery($query);
                $db->execute();

                Factory::getApplication()->enqueueMessage('Registro atualizado com sucesso.', 'message');
                return true; // Sucesso
            } catch (Exception $e) {

                echo Factory::getApplication()->enqueueMessage('Erro ao atualizar registro: ' . $e->getMessage(), 'error');
                
                return false; // Falha
                exit();
            }
        } else {
            // Se o registro não existir, insira um novo
            $query = $db->getQuery(true);

            array_push($dbColumn, 
                $db->quoteName('user_id'),
                $db->quoteName('form_id'),
                $db->quoteName('data')
            );
            array_push($dbValues, 
                $id,
                $db->quote($formId),
                $db->quote(date('Y-m-d H:i:s'))
            );

            $query->insert($db->quoteName($tabela))
                ->columns($dbColumn)
                ->values(implode(',', $dbValues));

            try {
                $db->setQuery($query);
                $db->execute();

                if($params->get('dbRelated'))
                {   
                    if(!self::setTableRelated($params,$iPosts,$db->insertid()))
                    {
                        return false;
                    }
                }
                
                Factory::getApplication()->enqueueMessage('Registro inserido com sucesso.', 'message');
                if($inserId){
                    return $db->insertId();
                }
                return true; // Sucesso
            } catch (Exception $e) {
                echo awUtilitario::awMessages('Erro ao inserir registro: ' . $e->getMessage(),'danger');
                return false; 
                exit();
            }
        }
    }

    // Responsável por adicionar novos campos na tabela
    public function updateTable($tabela, $novosCampos)
    {

       $db = Factory::getDbo();
       $tableName = $tabela;

       // Tente obter informações da tabela
       try {
           $query = $db->getQuery(true)
               ->select('1')
               ->from($db->quoteName($tableName))
               ->setLimit(1);

           $db->setQuery($query);
           $db->execute();

           // Se a execução ocorrer sem erro, a tabela existe
           $tableExists = true;
       } catch (\Exception $e) {
           // Se ocorrer um erro, a tabela não existe
           $tableExists = false;
       }

        // Verificar se a tabela existe
        if (!$tableExists) {
            // Se a tabela não existir, crie-a
            try {
                $query = "CREATE TABLE " . $db->quoteName($tabela) . " (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) NOT NULL,
                    form_id VARCHAR(255),
                    awToken VARCHAR(255),
                    data DateTime
                )";

                $db->setQuery($query);
                $db->execute();
            } catch (Exception $e) {
                Factory::getApplication()->enqueueMessage('Erro ao criar a tabela ' . $tabela . ': ' . $e->getMessage(), 'error');
                return;
            }
        }

        //Inserir campos globais que ainda não existem para o funcionamento perfeito do s7dform.
        array_push($novosCampos, 
            $db->quoteName('user_id'),
            $db->quoteName('form_id'),
            $db->quoteName('data')
        );

        $camposDaTabela = $db->getTableColumns($tabela);


        // Agora, adicione os novos campos
        foreach ($novosCampos as $novoCampo) {
            if (!array_key_exists($novoCampo, $camposDaTabela)) {

                // O campo não existe, então adicione-o
                try {
                    $query = "ALTER TABLE " . $db->quoteName($tabela) . "
                        ADD COLUMN " . $novoCampo . " TEXT";
                    
                    $db->setQuery($query);
                    $db->execute();

                    Factory::getApplication()->enqueueMessage('Campo ' . $novoCampo . ' adicionado com sucesso.', 'message');
                } catch (Exception $e) {
                    Factory::getApplication()->enqueueMessage('Erro ao adicionar campo ' . $novoCampo . ': ' . $e->getMessage(), 'error');
                }
            }
        }
    }

    //Atualizar dados por token
    public static function atualizarDados($sDb,$token,$params){


        if(!self::getConfirm($sDb,'awToken',$token)){
            return false;
        }

        // Initialiase variables.
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        $dadosAtualizar = explode(',', $params->get('confirmDados'));

        // Create the base update statement.
        $query->update($db->quoteName($sDb));



        foreach ($dadosAtualizar as $dados) {
            $dado = explode(':', $dados);
            
            // Verificar se houve erro na divisão e definir valores padrão
            if (count($dado) == 2) {
                list($name, $value) = $dado;
                $query->set($db->quoteName($name) . ' = ' . $db->quote($value));
            } 
        }
        
        $query->where($db->quoteName('awToken') . ' = ' . $db->quote($token));
        
        // Set the query and execute the update.
        $db->setQuery($query);
        
        try
        {
            $db->execute();
            return true;
        }
        catch (RuntimeException $e)
        {
            echo JError::raiseWarning(500, $e->getMessage());
            return false;
        }

        return true;
    }

    public static function removeToken($sDb,$token,$params){


        if(!self::getConfirm($sDb,'awToken',$token)){
            return false;
        }

        // Initialiase variables.
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        // Create the base update statement.
        $query->update($db->quoteName($sDb));



        //Limpando token
        $query->set($db->quoteName('awToken') . ' = ' . $db->quote(''));
        
        $query->where($db->quoteName('awToken') . ' = ' . $db->quote($token));
        
        // Set the query and execute the update.
        $db->setQuery($query);
        
        try
        {
            $db->execute();
            return true;
        }
        catch (RuntimeException $e)
        {
            echo JError::raiseWarning(500, $e->getMessage());
            return false;
        }

        return true;
    }


    public static function getConfirm($sDb,$dado,$token)
    {

        if(empty($token))
        {
            return false;
        }

        // Get a db connection.
        $db = Factory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select($db->quoteName(array($dado)));
        $query->from($db->quoteName($sDb));
        $query->where($db->quoteName($dado) . ' = ' . $db->quote($token));

        // Reset the query using our newly populated query object.
        $db->setQuery($query);

        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectList();

        return count($results);
    }

    public static function getConfirmDados($sDb,$token)
    {

        if(empty($token))
        {
            return false;
        }

        // Get a db connection.
        $db = Factory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select(array('*'));
        $query->from($db->quoteName($sDb));
        $query->where($db->quoteName('awToken') . ' = ' . $db->quote($token));

        // Reset the query using our newly populated query object.
        $db->setQuery($query);

        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectList();

        return $results;
    }

}