
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

class awFileUploader {
		public static function upload($files, $modId, $dadosUser = [])
		{
		    $fJson = array();
		    $user = jFactory::getUser();
		    $folderUser = $user->id ? 'user-'.$user->id : '';

		    foreach ($files as $k => $file) {
		        $fieldData = array();

		        if (is_array($file['name']) && awUtilitario::is_multi_array($file['name'])) {
		            // Se for multidimensional, percorra os campos internos
		            foreach ($file['name'] as $subK => $subFile) {
		                if (is_array($subFile)) {
		                    $subFieldData = array();

		                    foreach ($subFile as $subIndex => $subFileName) {
		                        if (!empty($subFileName)) {
		                            //$fid = uniqid();
		                            $ex = strtolower(pathinfo($subFileName, PATHINFO_EXTENSION));
		                            //$fileName = $fname . '.' . $ex;
		                             $fileName = $fn;

		                            // Realize o upload do arquivo
		                            $path = JPATH_ROOT . '/s7dforms/' . $modId . '/' . $k . '/' .$folderUser;
		                            $folder = $path . '/';

		                            if (!is_dir($path)) {
		                                mkdir($path, 0777, true);
		                                chmod($path, 0777);
		                            }

		                            JFile::upload($file['tmp_name'][$subK][$subIndex], $folder . $fileName);

		                            $subFieldData[] = $fileName;
		                        }
		                    }

		                    $fieldData[$subK] = $subFieldData;
		                }
		            }
		        } else {
		            // Use um loop foreach para percorrer os arquivos
		            foreach ($file['name'] as $if => $fn) {
		                // Verifique se o nome do arquivo está vazio ou não
		                if (!empty($fn)) {
		                    //$fid = uniqid();
		                    $ex = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
		                    //$fileName = $fid . '.' . $ex;
		                     $fileName = $fn;

		                    // Realize o upload do arquivo
		                    $path = JPATH_ROOT . '/s7dforms/' . $modId . '/' . $k .'/' . $folderUser;
		                    $folder = $path . '/';
		                    if (!is_dir($path)) {
		                        mkdir($path, 0777, true);
		                        chmod($path, 0777);
		                    }
		                    JFile::upload($file['tmp_name'][$if], $folder . $fileName);

		                    $fieldData[] = $fileName;
		                }
		            }
		        }

		        $fJson[$k] = $fieldData;
		    }

//garantir que seja sempre array os dados do user
foreach ($dadosUser as $chave => $valor) {
    if (!is_array($valor)) {
        // Se o valor não for um array, transforme-o em um array com um único elemento
        $dadosUser[$chave] = [$valor];
    }
}
           /*$dadosJson = array_map(function($a, $b) {
                return array_diff_key($a, $b);
            }, $dadosUser, $fJson);*/

            //mergin valor pra salvar no banco
            
            //$dados = json_encode(array_filter(array_merge_recursive($dadosUser,$fJson)));

            // Suponha que $dadosUser e $fJson já tenham sido mesclados
$arrayMesclado = array_merge_recursive($dadosUser, $fJson);




/*// Função de filtragem personalizada para remover valores vazios
function removeValoresVazios($value) {
    if (is_array($value)) {
        return array_filter($value, 'removeValoresVazios');
    }
    return $value !== '';
}

// Use a função de filtragem para remover valores vazios
$arrayLimpo = array_filter($arrayMesclado, 'removeValoresVazios');

// Codifique o array limpo em JSON
$resultado = json_encode($arrayLimpo);*/

return json_encode($arrayMesclado);
		}




		public static function uploadFail($files, $exPer)
{

    // Extensions
    $exPer = $exPer != '' ? explode(',', $exPer) : ['jpg', 'png'];

    foreach ($files as $k => $file) {
        $name = $k;

        if (is_array($file['name'])) {
            // Verifique se é um array multidimensional
            if (awUtilitario::is_multi_array($file['name'])) {

                // Se for um array multidimensional, itere por ele
                foreach ($file['name'] as $fk => $fn) {
                    // Verifique se o nome do arquivo é um array
                    if (is_array($fn)) {
                        foreach ($fn as $fileIndex => $fileName) {
                            if(!self::handleFile($fileName, $exPer, $k, $fk, $fileIndex, $name))
                            {
                            	return false;
                            }
                            
                        }
                    } else {
                        if(!self::handleFile($fn, $exPer, $k, $fk, null, $name)){
                        	return false;
                        }
                    }
                }
            } else {

                // Se for um array simples, manipule o único arquivo
                foreach ($file['name'] as $fileIndex => $fn) {
                    if(!self::handleFile($fn, $exPer, $k, $fileIndex, null, $name)){
                    	return false;
                    }
                }
            }
        } else {
            // Se não for um array, estamos lidando com um único arquivo
            if(!self::handleFile($file['name'], $exPer, $k, null, null, $name))
            {
            	return false;
            }
        }
    }

    return true;
}

// Função para lidar com um arquivo individual
function handleFile($fn, $exPer, $k, $fk = null, $fileIndex = null, $name = '') {
    // Verifique se o nome do arquivo é um array
    if (is_array($fn)) {
        foreach ($fn as $fileName) {
            // Verifique se o nome do arquivo está vazio ou não
            if (!empty($fileName)) {
                // Use pathinfo para obter informações sobre o nome do arquivo
                $pathInfo = pathinfo($fileName);
                $ex = strtolower($pathInfo['extension']);

                // Você pode ajustar essa parte para criar nomes de arquivos personalizados
                $fileName = 'arquivo_' . $k . ($fk !== null ? '_' . $fk : '') . ($fileIndex !== null ? '_' . $fileIndex : '') . '.' . $ex;

                if (!in_array($ex, $exPer)) {
                    $dados = [];
                    //$dados[$name] = 'O campo '.$campo.' é Obrigatório';
                    $dados[$name.'[]'] = 'Apenas anexos (' . implode(',', $exPer).')';

                    echo json_encode($dados).',';

                   /* echo awUtilitario::awMessages('Upload de arquivos não permitidos. Só são permitidos arquivos <strong>' . implode(',', $exPer) . '</strong>', 'danger');*/
                    return false;
                }
            }
        }
    } else {
        // Verifique se o nome do arquivo está vazio ou não
        if (!empty($fn)) {
            // Use pathinfo para obter informações sobre o nome do arquivo
            $pathInfo = pathinfo($fn);
            $ex = strtolower($pathInfo['extension']);

            // Você pode ajustar essa parte para criar nomes de arquivos personalizados
            $fileName = 'arquivo_' . $k . ($fk !== null ? '_' . $fk : '') . '.' . $ex;

            if (!in_array($ex, $exPer)) {
                $dados = [];
                //$dados[$name] = 'O campo '.$campo.' é Obrigatório';
                $dados[$name.'[]'] = 'Apenas anexos (' . implode(',', $exPer).')';

                echo json_encode($dados).',';

               /* echo awUtilitario::awMessages('Upload de arquivos não permitidos. Só são permitidos arquivos <strong>' . implode(',', $exPer) . '</strong>', 'danger');*/
                return false;
            }
        }
    }

    return true;
}



}