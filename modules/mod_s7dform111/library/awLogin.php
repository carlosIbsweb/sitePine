
<?php

/**
 * @package     
 * @subpackage  mod AwForm
 **/

// No direct access.
defined('_JEXEC') or die;

/********
 Classe Aw Valid.
 Desenvolvido por Carlos (IBS WEB)
********/

class awLogin {

	public static function awL(&$params,$inputs)
	{
        $d = 'id,nome';
		//campos para login
        $datas = explode(',',$d);


        /********************
         *SET Vars Data
        ********************/
        parse_str(http_build_query($inputs));

        foreach($datas as $data)
        {
            $sData = $data;

            if(!self::getD($data,$params->get('db'),$data,$$sData))
            {
                return false;
                exit();
            }

            echo 'certo';


        }

	}

    public static function getD($name,$sDb,$campo,$val,$token)
    {
        // Build the query for the table list.
      /*  $db = JFactory::getDbo();
        $db->setQuery(
            'SELECT '.$db->quoteName($name)
            . ' FROM '.$sDb
            . ' WHERE '.$db->quoteName($campo).' = ' . $db->quote($val) . ' AND '.$db->quoteName('awToken') ' = '.$token
        );
        
        try {
                $result = $db->loadResult();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        
        return  $result;*/
    }

    public static function setUp($sDb,$inputs,$token,&$params)
    {
        $awToken = $token;

        if(empty($awToken))
        {
            echo '<div class="alert alert-danger"><strong>SUA SESSÃO EXPIROU!</strong> Atualize sua página e tente novamente.</div>';
            return false;
            exit();
        }
        if(($params->get('awUpDex') && !$params->get('awUpDblock')))
        {
            if(self::getDado($params->get('awUpDName'),$sDb,$token) === $params->get('awUpDValue'))
            {
                echo '<div class="alert alert-danger">Acesso bloqueado!</div>';
                return false;
                exit();
            }
        }
        

        $fields = array();

        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        foreach($inputs as $names=> $values)
        {
            if(in_array($names,self::getDDb($sDb,$token)))
            {
                $value = is_array($values) ? implode(', ',$values) : $values;
                $upd = $db->quoteName($names) . ' = ' . $db->quote($value);
                array_push($fields, $upd);
            }
        }

        // Create the base update statement.
        $query->update($db->quoteName($sDb))
            ->set($fields)
            ->where($db->quoteName('awToken') . ' = ' . $db->quote($awToken));
        
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


    public static function awUpDado($sDb,$dado,$value,$token,$block,&$params = null)
    {
        $awToken = $token;

        if(!$block)
        {
            echo '<div class="alert alert-danger">Permissão negada!</div>';
            return false;
            exit();
        }

        if(self::getDado($params->get('awUpDName'),$sDb,$token) === $params->get('awUpDValue') && $params->get('awUpDblock'))
        {
            echo '<div class="alert alert-danger">Acesso bloqueado!</div>';
            return false;
            exit();
        }

        if(empty($awToken))
        {
            echo '<div class="alert alert-danger"><strong>SUA SESSÃO EXPIROU!</strong> Atualize sua página.</div>';
            return false;
            exit();
        }

        if(empty($dado) and empty($value))
        {
            echo '<div class="alert alert-danger"><strong>Campo de Dados está vazio.</div>';
            return false;
            exit();
        }

        $fields = array();

        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Create the base update statement.
        $query->update($db->quoteName($sDb))
            ->set($db->quoteName($dado) . ' = ' . $db->quote($value))
            ->where($db->quoteName('awToken') . ' = ' . $db->quote($awToken));
        
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

    //Buscando names do banco para comparar.
    public static function getDDb($sDb,$token)
    {
       
        $dataDb = array();

        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName($sDb))
            ->where($db->quoteName('awToken') . ' = ' . $db->quote($token));
        
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

        foreach($result[0] as $dat => $item)
        {
            array_push($dataDb,$dat);
        }

        return $dataDb;
    }

    //Pedar dado do banco
     public static function getDado($name,$sDb,$token)
    {
        // Build the query for the table list.
        $db = JFactory::getDbo();
        $db->setQuery(
            'SELECT '.$db->quoteName($name)
            . ' FROM '.$sDb
            . ' WHERE '.$db->quoteName('awToken') . ' = ' .$db->quote($token)
        );
        
        try {
                $result = $db->loadResult();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        
        return  $result;
    }

    public static function validFields(&$params,$inputs,$token = null)
    {
       parse_str(http_build_query($inputs));

       $validFields = explode(",",$params->get('validFields'));
       preg_match_all("/(.+)?{(.+)}/U", $params->get('validFields'), $vFields);

       $vFieldsNames = array();
       //Validando 
       foreach($vFields[2] as $vField)
        {
            array_push($vFieldsNames,$vField);
        }


       // Initialiase variables.
       $db    = JFactory::getDbo();
       $query = $db->getQuery(true);
       
       // Create the base select statement.
       $query->select($db->quoteName($vFieldsNames))
           ->from($db->quoteName($params->get('db')))
           ->where($db->quoteName('awToken') .' != ' .$db->quote($token));
       
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
        
        $status = true;
        
        foreach($result as $k=> $item)
        {
            $i = 0;
            foreach($item as $iN => $iV)
            {
                if($iV === $$iN)
                {
                    $vA .= str_replace(",","",$vFields[1][$i]). ' <strong>' .$iV.'</strong> já está em uso<br>'; 
                    $status = false;
                }
                $i++;
            }
        }

        if($status == false)
        {
            echo '<div class="alert alert-danger">'.$vA.'</div>';
            return false;
        } 

        return true;      
    }

    //Limite de items

    public static function setLimit(&$params) {
        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName($params->get('db')));
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();

            echo count($result);

            if(count($result) >= $params->get('setLimit')){
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
            return false;
        }

        echo 'limite';
        return true;
    }

    public static function oi(){
        echo 'nada';
    }


}