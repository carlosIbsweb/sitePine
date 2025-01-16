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

class paymentsUser extends s7dPayments
{

	/*********
		Regsiter Payments
	**********/
	 public static function register($dados = array())
     {  

        //Inserindo os dados no grupo de usuários;
        $db =& JFactory::getDBO();

        $names 	= [];
        $values = [];

        /******* DADOS ********/
        foreach($_POST as $kNames => $iValues)
        {
        	if(in_array($kNames,$dados))
        	{
        		array_push($names,$db->quoteName($kNames));
        		array_push($values,$db->quote($iValues));

        		//Gerando Vars.
        		$varNames 	= $kNames;
        		$$varNames  = $iValues;
        	}
        } 

        /*****************************************
			Dados fixos
		******************************************/

		//Password.
		$salt = JUserHelper::genRandomPassword(32);
        $crypt = JUserHelper::getCryptedPassword($username, $salt);
        $password = $crypt . ':' . $salt;

		//Names
		array_push($names,
			/*1*/$db->quoteName('block'),
			/*2*/$db->quoteName('registerDate'),
			/*3*/$db->quoteName('activation'),
			/*4*/$db->quoteName('params'),
            /*5*/$db->quoteName('password'),
			/*6*/$db->quoteName('email')


		);

		//Values
		array_push($values,
			/*1*/$db->quote(0),
			/*2*/$db->quote(date("Y-m-d H:m:s")),
			/*3*/$db->quote(0),
			/*4*/$db->quote('{"admin_style":"","admin_language":"","language":"","editor":"","helpsite":"","timezone":""}'),
            /*5*/$db->quote($password),
			/*6*/$db->quote(strtolower($username))
		);

        $names 	= implode(",",$names);
        $values = implode(",",$values);

        /*********
        Validar email.
        **********/
        if(!self::validEmail($username)){
            echo '<div class="alert alert-danger" role="alert">
            <strong>E-mail inválido!</strong> Digite um e-mail válido.
            </div>';
            return false;
        }

        /*********
        Vefiricar se e-mail já existe.
        **********/
        if(!empty(self::getLogin($username,'username')))
        {
            echo '<div class="alert alert-danger" role="alert">
            E-mail já está em uso.
            </div>';
            return false;
        }

        /********
        Validar Cpf
        *********/
        if(!self::validaCPF($cpf)) {
            echo '<div class="alert alert-danger" role="alert">
            <strong>CPF inválido!</strong> Digite um CPF válido.
            </div>';
            return false;
        }

        $query = "INSERT INTO `#__users` ($names)
        VALUES ($values);";
        $db->setQuery( $query );
        $execut = $db->query();

        if($execut)
        {
        	self::setRegisterGroup(self::getLogin($username,'id'),'2');

            //Notificação por email Administrador.
            self::sendEmail(JPATH_SITE.'/components/com_s7dpayments/tmpl/default_email_admin.php','NOVO REGISTRO - Colônia de Férias','contato@pinetreefarm.com.br','Pine Tree Farm','coloniapinetreefarm@gmail.com,carlos@ibsweb.com.br',$_POST);
            
            $_SESSION['registerOk'] = 
            '<div class="alert alert-success" role="alert">
                <strong>Cadastro realizado com sucesso!</strong><p>Favor efetue seu login abaixo.</p>
            </div>';

            header('Location:'.$menuLink.'?user=login');
        }
     }

    /*********
	Get Login
	*********/
	public static function getLogin($username,$search)
	{
        $db = JFactory::getDbo();
		$db->setQuery('SELECT #__users.'.$db->quoteName($search).' FROM #__users WHERE  username = '.$db->quote($username));

        return $db->loadResult();     
	}

     /*********
		Regsiter Payments uer map
	**********/
	 protected function setRegisterGroup($userid,$groupid)
     {  

        //Inserindo os dados no grupo de usuários;
        $db =& JFactory::getDBO();

        $query = "INSERT INTO `#__user_usergroup_map` (`user_id`,`group_id`)
        VALUES ($userid,$groupid);";
        $db->setQuery( $query );
        return $db->query();
     }

    protected function validEmail($email){
        $valid = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/";
        if(!preg_match($valid,$email))
        {
            return false;
        }else{
            return true;
        }
    }

    protected function validaCPF($cpf) {
 
    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf{$c} * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf{$c} != $d) {
            return false;
        }
    }
    return true;
    }

    public function login(){
        require_once JPATH_SITE . '/components/com_users/helpers/route.php';
    }
}