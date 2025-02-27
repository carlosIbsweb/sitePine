<?php 

defined('_JEXEC') or die;

class ModCheckinHelper
{
    protected $db;

    public function __construct()
    {
        $this->db = JFactory::getDbo();
    }

    /**
     * Método principal para processar check-in/check-out
     */
    public function processarCheckCrianca($code)
    {
        if (!$code) {
            return ['error' => 'Código da criança não fornecido'];
        }

        // Buscar dados da criança no banco usando o código JSON
        $dadosCrianca = $this->buscarDadosCrianca($code);

        if (!$dadosCrianca) {
            return ['error' => 'Criança não encontrada no sistema'];
        }

        $crianca_id = $dadosCrianca->crianca_id;

        // Se tem check-in ativo, faz check-out. Senão, faz check-in.
        if ($this->verificarCheckinAtivo($crianca_id)) {
            return $this->realizarCheckout($crianca_id);
        } else {
            return $this->realizarCheckin($dadosCrianca);
        }
    }

    /**
     * Busca os dados da criança no banco usando o código JSON
     */
    private function buscarDadosCrianca($code)
    {
        $query = $this->db->getQuery(true)
            ->select([
                'userid',
                "JSON_UNQUOTE(JSON_EXTRACT(items, '$.criancas.{$code}.nome')) AS nome",
                'ref AS ingresso_ref'
            ])
            ->from($this->db->quoteName('#__s7dpayments'))
            ->where("JSON_UNQUOTE(JSON_EXTRACT(items, '$.criancas.{$code}.nome')) IS NOT NULL");

        $this->db->setQuery($query);
		print_r( $this->db->loadObjectList());
		exit;
        return $this->db->loadObjectList();
    }

    /**
     * Verifica se a criança já tem um check-in ativo (sem data de check-out)
     */
    private function verificarCheckinAtivo($crianca_id)
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__colonia_check'))
            ->where($this->db->quoteName('crianca_id') . ' = ' . $this->db->quote($crianca_id))
            ->where($this->db->quoteName('data_checkout') . ' IS NULL');

        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

    /**
     * Realiza o check-out da criança
     */
    private function realizarCheckout($crianca_id)
    {
        $data_checkout = date('Y-m-d H:i:s');

        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__colonia_check'))
            ->set($this->db->quoteName('data_checkout') . ' = ' . $this->db->quote($data_checkout))
            ->set($this->db->quoteName('status') . ' = ' . $this->db->quote('check-out'))
            ->where($this->db->quoteName('crianca_id') . ' = ' . $this->db->quote($crianca_id))
            ->where($this->db->quoteName('data_checkout') . ' IS NULL');

        $this->db->setQuery($query);

        try {
            $this->db->execute();
            return ['success' => 'Check-out realizado com sucesso', 'crianca_id' => $crianca_id, 'data_checkout' => $data_checkout];
        } catch (Exception $e) {
            return ['error' => 'Erro ao realizar check-out: ' . $e->getMessage()];
        }
    }

    /**
     * Realiza o check-in da criança inserindo um novo registro
     */
    private function realizarCheckin($dadosCrianca)
    {
        $userid = $dadosCrianca->userid;
        $crianca_id = $dadosCrianca->crianca_id;
        $nome = $dadosCrianca->nome;
        $ingresso_ref = $dadosCrianca->ingresso_ref;
        $data_checkin = date('Y-m-d H:i:s');
        $status = 'check-in';

        $queryInsert = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__colonia_check'))
            ->columns($this->db->quoteName(['userid', 'crianca_id', 'data_checkin', 'data_checkout', 'status', 'ingresso_ref']))
            ->values(implode(',', [
                $this->db->quote($userid),
                $this->db->quote($crianca_id),
                $this->db->quote($data_checkin),
                'NULL',
                $this->db->quote($status),
                $this->db->quote($ingresso_ref)
            ]));

        $this->db->setQuery($queryInsert);

        try {
            $this->db->execute();
            return ['success' => 'Check-in realizado com sucesso', 'crianca' => $nome, 'data_checkin' => $data_checkin];
        } catch (Exception $e) {
            return ['error' => 'Erro ao salvar check-in: ' . $e->getMessage()];
        }
    }
}
