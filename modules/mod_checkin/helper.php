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


			print_r(self::filtrarItensPorData($dadosCrianca,$code));
		
		
		exit;

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
    'items',
    'ref AS ingresso_ref', 
    "JSON_UNQUOTE(JSON_EXTRACT(items, '$[0].criancas.".$code.".nome')) AS nome",
    "JSON_UNQUOTE(JSON_EXTRACT(items, '$[0].periodo')) AS periodo",
    "JSON_UNQUOTE(JSON_EXTRACT(items, '$[0].diarias')) AS diarias"
            ])
            ->from($this->db->quoteName('#__s7dpayments'))
            ->where("JSON_CONTAINS_PATH(items, 'one', '$[0].criancas.".$code."')");

        $this->db->setQuery($query);

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

	function filtrarItensPorData($dadosArray, $code)
{
	// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');

    // Obter a data e hora de hoje
    $hoje = date('d/m/Y');
    $horaAtual = date('H:i'); // Exemplo: 14:30

    // Array para armazenar os itens filtrados
    $itensFiltrados = [];

    // Percorrer cada objeto do array principal
    foreach ($dadosArray as $dados) {
        // Verificar se existe a chave "items" e se é um JSON válido
        if (!isset($dados->items)) {
            continue;
        }

        // Decodificar o JSON dentro de "items"
        $itens = json_decode($dados->items, true);
        if (!$itens) {
            continue; // Se não for um JSON válido, pula para o próximo
        }

        foreach ($itens as $item) {
            // Extrair os dados relevantes
            $periodo = isset($item['periodo']) ? $item['periodo'] : '';
            $diarias = isset($item['diarias']) ? $item['diarias'] : '';
            $courseCode = isset($item['courseCode']) ? $item['courseCode'] : '';
            $criancas = isset($item['criancas']) ? $item['criancas'] : [];

            // Se a criança especificada não estiver no grupo, ignora o item
            if (!isset($criancas[$code])) {
                continue;
            }

            // Se houver diárias, verificar se a data de hoje está listada
            if (!empty($diarias)) {
                $diasDiarias = array_map('trim', explode(',', $diarias));
                if (!in_array(date('d'), $diasDiarias)) {
                    continue; // Se hoje não estiver em diarias, ignora este item
                }
            }

            // Se não houver diárias, validar pelo período
            $validarPeriodo = false;
            if (!empty($periodo)) {
                // Extrair a data de início e fim do período
                $periodoParts = explode(' ', $periodo);
                if (count($periodoParts) >= 2) {
                    $dataInicio = DateTime::createFromFormat('d/m/Y', trim($periodoParts[0]));
                    $dataFim = DateTime::createFromFormat('d/m/Y', trim($periodoParts[1]));
                    $dataHoje = DateTime::createFromFormat('d/m/Y', $hoje);

                    // Se a data de hoje estiver no intervalo, permite validar pelo horário
                    if ($dataHoje >= $dataInicio && $dataHoje <= $dataFim) {
                        $validarPeriodo = true;
                    }
                }
            }

            // Agora validar o horário
            if ($validarPeriodo) {
                $horarioInicio = null;
                $horarioFim = null;

                // Extrair horário do campo "courseCode"
                if (preg_match('/(\d{1,2})h-(\d{1,2})h/', $courseCode, $matches)) {
                    $horarioInicio = sprintf('%02d:00', $matches[1]); // Exemplo: "08:00"
                    $horarioFim = sprintf('%02d:00', $matches[2]); // Exemplo: "12:00"
                }

                if ($horarioInicio && $horarioFim) {
                    if ($horaAtual >= $horarioInicio && $horaAtual <= $horarioFim) {
                        // Se a hora atual estiver dentro do horário do curso, adiciona ao resultado
                        $itensFiltrados[] = $item;
                    }
                }
            }
        }
    }

    return $itensFiltrados;
}

}
