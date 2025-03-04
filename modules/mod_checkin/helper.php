<?php 

defined('_JEXEC') or die;

class ModCheckinHelper
{
    protected static $db;

    public function __construct()
    {
		// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
		date_default_timezone_set('America/Sao_Paulo');
        self::$db = JFactory::getDbo();
    }

    public function itemsAjax(){
        
$code = JFactory::getApplication()->input->getString('code', '');
        // DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
		date_default_timezone_set('America/Sao_Paulo');
        self::$db = JFactory::getDbo();

        return self::processarCheckCrianca($code);
        
        exit();
    }

    /**
     * Método principal para processar check-in/check-out
     */
    public static function processarCheckCrianca($code)
    {
        if (!$code) {
            return ['error' => 'Código da criança não fornecido'];
        }

        // Buscar dados da criança no banco usando o código JSON
        $dadosCrianca = self::buscarDadosCrianca($code);

        if (!$dadosCrianca) {
            return ['error' => 'Criança não encontrada no sistema'];
        }
        

        $dadosCriancaFiltrado = self::filtrarItensPorData($dadosCrianca,$code);
 

		if(!self::filtrarItensPorData($dadosCrianca,$code)){
			return ['error' => 'Criança fora do périodo Isabella de Paula da conceição.'];
		}

        $crianca_id = $dadosCrianca->crianca_id;

        // Se tem check-in ativo, faz check-out. Senão, faz check-in.
        if ($dadosCheckin = self::verificarCheckinAtivo($code,$dadosCriancaFiltrado)) {
            $horaExtra = self::filtrarItensPorData($dadosCrianca, $code, true, $dadosCheckin );
            return self::realizarCheckout($dadosCriancaFiltrado,$code,$horaExtra);
        } else {
            return self::realizarCheckin($dadosCriancaFiltrado,$code);
        }
    }

    /**
     * Busca os dados da criança no banco usando o código JSON
     */
    private function buscarDadosCrianca($code)
    {
        $query = self::$db->getQuery(true)
            ->select([
				'userid',
    'items',
    'ref AS ingresso_ref', 
    "JSON_UNQUOTE(JSON_EXTRACT(items, '$[0].criancas.".$code.".nome')) AS nome",
    "JSON_UNQUOTE(JSON_EXTRACT(items, '$[0].periodo')) AS periodo",
    "JSON_UNQUOTE(JSON_EXTRACT(items, '$[0].diarias')) AS diarias"
            ])
            ->from(self::$db->quoteName('#__s7dpayments'))
            ->where("JSON_CONTAINS_PATH(items, 'one', '$[0].criancas.".$code."')")
            ->where(self::$db->quoteName('status')." IN(3,4) ");

        self::$db->setQuery($query);

        return self::$db->loadObjectList();
    }

    /**
     * Verifica se a criança já tem um check-in ativo (sem data de check-out)
     */
    private function verificarCheckinAtivo($code,$dadosCrianca)
    {
        $dadosCrianca = $dadosCrianca[0];

        $query = self::$db->getQuery(true)
            ->select('*')
            ->from(self::$db->quoteName('#__colonia_check'))
            ->where(self::$db->quoteName('crianca_id') . ' = ' . self::$db->quote($code))
            ->where(self::$db->quoteName('catid') . ' = ' . self::$db->quote($dadosCrianca['catid']))
            ->where(self::$db->quoteName('pedido_ref') . ' = ' . self::$db->quote($dadosCrianca['referencia']))
            ->where(self::$db->quoteName('userid') . ' = ' . self::$db->quote($dadosCrianca['userid']))
            ->where(self::$db->quoteName('data_checkout') . ' IS NULL');

        self::$db->setQuery($query);
        return self::$db->loadObject();
    }

    /**
     * Realiza o check-out da criança
     */
    private function realizarCheckout($dadosCrianca,$code,$horaExtra)
    {

		// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
		date_default_timezone_set('America/Sao_Paulo');

        $dadosCrianca = $dadosCrianca[0];
        $userid = $dadosCrianca['userid'];
        $crianca_id = $code;
        $nome = $dadosCrianca['nome'];
        $ingresso_ref = $dadosCrianca['referencia'];
        $catid = $dadosCrianca['catid'];

        $data_checkout = date('Y-m-d H:i:s');

        $query = self::$db->getQuery(true)
            ->update(self::$db->quoteName('#__colonia_check'))
            ->set(self::$db->quoteName('data_checkout') . ' = ' . self::$db->quote($data_checkout))
            ->set(self::$db->quoteName('status') . ' = ' . self::$db->quote('check-out'))
            ->set(self::$db->quoteName('hora_extra') . ' = ' . self::$db->quote($horaExtra))
            ->where(self::$db->quoteName('crianca_id') . ' = ' . self::$db->quote($code))
            ->where(self::$db->quoteName('userid') . ' = ' . self::$db->quote($userid))
            ->where(self::$db->quoteName('pedido_ref') . ' = ' . self::$db->quote($ingresso_ref))
            ->where(self::$db->quoteName('catid') . ' = ' . self::$db->quote($catid))
            ->where(self::$db->quoteName('data_checkout') . ' IS NULL');

        self::$db->setQuery($query);

        try {
            self::$db->execute();
            return [
                'success' => 'Check-out realizado com sucesso', 
                'crianca_id' => $crianca_id, 
                'data_checkout' => $data_checkout,
                'crianca' => $dadosCrianca, 
            ];
        } catch (Exception $e) {
            return ['error' => 'Erro ao realizar check-out: ' . $e->getMessage()];
        }
    }

    /**
     * Realiza o check-in da criança inserindo um novo registro
     */
    private function realizarCheckin($dadosCrianca,$code)
    {
		// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
		date_default_timezone_set('America/Sao_Paulo');

		$dadosCrianca = $dadosCrianca[0];
        $userid = $dadosCrianca['userid'];
        $crianca_id = $code;
        $nome = $dadosCrianca['nome'];
        $ingresso_ref = $dadosCrianca['referencia'];
        $catid = $dadosCrianca['catid'];
        $data_checkin = date('Y-m-d H:i:s');
        $status = 'check-in';

        $queryInsert = self::$db->getQuery(true)
            ->insert(self::$db->quoteName('#__colonia_check'))
            ->columns(self::$db->quoteName(['userid', 'crianca_id', 'data_checkin', 'data_checkout', 'status', 'pedido_ref','catid']))
            ->values(implode(',', [
                self::$db->quote($userid),
                self::$db->quote($crianca_id),
                self::$db->quote($data_checkin),
                'NULL',
                self::$db->quote($status),
                self::$db->quote($ingresso_ref),
                self::$db->quote($catid)
            ]));

        self::$db->setQuery($queryInsert);

        try {
            self::$db->execute();
            return [
                'success' => 'Check-in de '.$nome.' realizado com sucesso', 
                'crianca' => $dadosCrianca,
                'colonia' => self::getCategoryHierarchy($catid),
                'data_checkin' => $data_checkin
            ];
        } catch (Exception $e) {
            return ['error' => 'Erro ao salvar check-in: ' . $e->getMessage()];
        }
    }

	function filtrarItensPorData($dadosArray, $code, $exibirHoraExtra = false, $dadosCheckin = [])
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

                $dataCheckin = date('Y-m-d',strtotime($dadosCheckin->data_checkin));
                $hora1 = new DateTime($dataCheckin.' '.$horarioFim);
                $hora2 = new DateTime(date('Y-m-d').' '.$horaAtual);

                $horaExtra = '00:00';

                // Verifica se a hora atual é maior que a hora fim
                if ($hora2 > $hora1) {
                    // Calcular a diferença entre os horários
                    $diferenca = $hora1->diff($hora2);
                    $horaExtra = sprintf("%02d:%02d", $diferenca->h, $diferenca->i);
                }

                if($exibirHoraExtra){
                    return $horaExtra;
                }

                if ($horarioInicio && $horarioFim) {

                    if ($horaAtual >= $horarioInicio && $horaAtual < $horarioFim) {
                        // Se a hora atual estiver dentro do horário do curso, adiciona ao resultado
                        $itensFiltrados[] = $item;
                    }
                }
            }
        }
    }

    return $itensFiltrados;
}

public static function getCategoryHierarchy($categoryId) {
    
    $db = self::$db;
    $query = self::$db->getQuery(true);

    // Define a consulta SQL com prefixo dinâmico
    $query = $db->getQuery(true)
        ->select([
            'per.title AS periodo',
            'sem.title AS semana',
            'col.title AS colonia'
        ])
        ->from($db->quoteName("#__categories", 'per'))
        ->leftJoin($db->quoteName("#__categories", 'sem') . ' ON sem.id = per.parent_id')
        ->leftJoin($db->quoteName("#__categories", 'col') . ' ON col.id = sem.parent_id')
        ->where($db->quoteName('per.id') . ' = ' . (int) $categoryId);

    // Prepara e executa a consulta
    $db->setQuery($query);

    // Retorna o resultado como um objeto associativo
    return $db->loadAssoc();
}

}
