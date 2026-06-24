<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;


class Pessoa extends ControllerMain
{
    public function __construct()
    {
        // formHelper já carregado globalmente pelo ControllerMain
        return parent::__construct();
    }

    /** Lista todas as pessoas. */
    public function index($action = null, $id = null)
    {
        $this->view(
            'admin/listaPessoa',
            ['pessoas' => $this->model->lista('PES_NOME')],
            'sistema'
        );
    }

    /** Filtragem via AJAX ou requisição normal. */
    public function filtroListagemPessoa($action = null, $id = null)
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
               && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        $this->view(
            'admin/listaPessoa',
            ['pessoas' => $this->model->filtroListagem($this->request->getPost())],
            $isAjax ? null : 'sistema'
        );
    }

    /** Prepara formulário para insert / update / delete / view. */
    public function formPessoa($acao = 'view', $id = null)
    {
        $formInputs = Session::getDestroy('formInputs');

        if ($acao === 'insert') {
            $data = ['action_form' => 'insert', 'pessoa' => $formInputs ?: []];
        } else {
            $data = [
                'action_form' => $acao,
                'pessoa'      => $formInputs ?: $this->model->getById($id),
            ];
        }

        $this->view('admin/form/formPessoa', ['data' => $data], 'sistema');
    }

    /** Insere nova pessoa — limpa máscaras antes de salvar. */
    public function insert($action = null, $id = null)
    {
        $post = $this->limparMascaras($this->request->getPost());
        unset($post['PES_ID']);

        $idGerado = $this->model->insert($post);

        if ($idGerado) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Cadastro realizado com sucesso! (ID: ' . $idGerado . ')']);
        } else {
            $msgError = Session::get('msgError') ?: 'Verifique os campos obrigatórios e tente novamente.';
            Redirect::page('pessoa/formPessoa/insert', ['msgError' => $msgError]);
        }
    }

    /** Atualiza pessoa existente — limpa máscaras antes de salvar. */
    public function update($action = null, $id = null)
    {
        $post = $this->limparMascaras($this->request->getPost());

        if ($this->model->update($post)) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Registro atualizado com sucesso.']);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao atualizar. Verifique os dados informados.';
            Redirect::page('pessoa/formPessoa/update/' . $post['PES_ID'], ['msgError' => $msgError]);
        }
    }

    /** Exclui pessoa. */
    public function delete($action = null, $id = null)
    {
        if ($this->model->delete($this->request->getPost())) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Registro excluído com sucesso.']);
        } else {
            Redirect::page('pessoa/', ['msgError' => 'Erro ao excluir. Verifique se há vínculos com outros dados.']);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // AJAX — Consulta CNPJ via opencnpj.org com fallback entre datasets
    // Rota: POST /pessoa/consultarCNPJAjax
    // Datasets disponíveis: receita | cno | rntrc
    // ══════════════════════════════════════════════════════════════════

    public function consultarCNPJAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Mantém letras (A-Z) para suportar CNPJ 2.0 alfanumérico — remove só máscara
        $cnpj    = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $_POST['cnpj'] ?? ''));
        $dataset = preg_replace('/[^a-z]/', '', strtolower($_POST['dataset'] ?? 'receita'));

        $datasetsPermitidos = ['receita', 'cno', 'rntrc'];
        if (!in_array($dataset, $datasetsPermitidos)) {
            $dataset = 'receita';
        }

        if (strlen($cnpj) !== 14) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'CNPJ inválido: deve conter 14 dígitos.']);
            exit;
        }

        // Tenta o dataset escolhido pelo usuário primeiro (rápido, caso comum)
        $resultado = $this->_consultarOpenCNPJ($cnpj, $dataset);

        if ($resultado === null) {
            // Falhou (timeout/erro de conexão) → tenta os outros 2 datasets.
            $outros = array_values(array_diff($datasetsPermitidos, [$dataset]));

            if (function_exists('curl_multi_init')) {
                // Caminho rápido: consulta os datasets restantes EM PARALELO,
                // reduzindo o pior caso de "3 × timeout" para "1 × timeout".
                $resultados = $this->_consultarVariosParalelo($cnpj, $outros);
                foreach ($outros as $ds) {
                    if (isset($resultados[$ds]) && $resultados[$ds] !== null) {
                        $resultado = $resultados[$ds];
                        $resultado['dataset_usado'] = $ds;
                        break;
                    }
                }
            } else {
                // Fallback se a extensão curl não estiver habilitada no servidor
                foreach ($outros as $ds) {
                    $resultado = $this->_consultarOpenCNPJ($cnpj, $ds);
                    if ($resultado !== null) {
                        $resultado['dataset_usado'] = $ds;
                        break;
                    }
                }
            }
        }

        if ($resultado === null) {
            echo json_encode([
                'sucesso'  => false,
                'mensagem' => 'Não foi possível consultar o CNPJ em nenhuma das fontes disponíveis. Tente novamente em instantes.'
            ]);
            exit;
        }

        echo json_encode($resultado);
        exit;
    }

    /**
     * opencnpj.org — GET https://api.opencnpj.org/{cnpj}?dataset={dataset}
     *
     * JSON real retornado pela API (campos confirmados na documentação):
     * {
     *   "cnpj": "00000000000000",
     *   "razao_social": "EMPRESA EXEMPLO LTDA",   ← NÃO é "nome"
     *   "nome_fantasia": "EXEMPLO",
     *   "situacao_cadastral": "Ativa",             ← NÃO é "situacao"
     *   "logradouro": "RUA EXEMPLO",
     *   "numero": "123",
     *   "bairro": "BAIRRO",
     *   "cep": "00000000",
     *   "uf": "SP",
     *   "municipio": "SAO PAULO",
     *   ...
     * }
     */
    /**
     * Consulta múltiplos datasets EM PARALELO usando curl_multi.
     * Usado como fallback quando o dataset escolhido pelo usuário falha —
     * evita esperar timeout × quantidade de datasets sequencialmente.
     *
     * @return array<string, array|null> indexado por dataset
     */
    private function _consultarVariosParalelo(string $cnpj, array $datasets): array
    {
        $multiHandle = curl_multi_init();
        $handles     = [];

        foreach ($datasets as $ds) {
            $ch = curl_init("https://api.opencnpj.org/{$cnpj}?dataset={$ds}");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 4,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => [
                    'Accept: application/json',
                    'User-Agent: PHP-ExtensaoMVC/1.0',
                ],
            ]);
            curl_multi_add_handle($multiHandle, $ch);
            $handles[$ds] = $ch;
        }

        // Executa todas as requisições simultaneamente
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);

        $resultados = [];
        foreach ($handles as $ds => $ch) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $resposta = curl_multi_getcontent($ch);

            if ($resposta === false || $resposta === '' || $httpCode === 429 || $httpCode >= 500) {
                $resultados[$ds] = null;
            } else {
                $resultados[$ds] = $this->_parseRespostaOpenCNPJ($resposta, $ds);
            }

            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        return $resultados;
    }

    /**
     * Interpreta o JSON de resposta da opencnpj.org — usado tanto pela
     * consulta única (_consultarOpenCNPJ) quanto pela paralela.
     */
    private function _parseRespostaOpenCNPJ(string $resposta, string $dataset): ?array
    {
        $dados = json_decode($resposta, true);

        if (!$dados || isset($dados['error']) || isset($dados['message'])) {
            $msg = $dados['message'] ?? ($dados['error'] ?? 'CNPJ não localizado neste dataset.');
            return ['sucesso' => false, 'mensagem' => $msg, 'dataset' => $dataset];
        }

        $nome      = $dados['razao_social']      ?? 'Não informado';
        $fantasia  = $dados['nome_fantasia']     ?? '';
        $situacao  = $dados['situacao_cadastral'] ?? 'Desconhecida';
        $municipio = $dados['municipio']         ?? '';
        $uf        = $dados['uf']                ?? '';
        $cep       = preg_replace('/\D/', '', $dados['cep']       ?? '');
        $logradouro= $dados['logradouro']        ?? '';
        $numero    = $dados['numero']            ?? '';
        $bairro    = $dados['bairro']            ?? '';

        if (strtolower($situacao) !== 'ativa') {
            return [
                'sucesso'  => false,
                'mensagem' => "CNPJ pertence a \"{$nome}\" mas a situação cadastral é \"{$situacao}\" (não Ativa).",
                'dataset'  => $dataset,
            ];
        }

        return [
            'sucesso'       => true,
            'nome'          => $nome,
            'fantasia'      => $fantasia,
            'situacao'      => $situacao,
            'municipio'     => $municipio,
            'uf'            => $uf,
            'cep'           => $cep,
            'logradouro'    => $logradouro,
            'numero'        => $numero,
            'bairro'        => $bairro,
            'dataset'       => $dataset,
            'dataset_usado' => $dataset,
        ];
    }

    private function _consultarOpenCNPJ(string $cnpj, string $dataset): ?array
    {
        $url      = "https://api.opencnpj.org/{$cnpj}?dataset={$dataset}";
        $resposta = $this->_httpGet($url);

        if ($resposta === null) {
            return null;
        }

        return $this->_parseRespostaOpenCNPJ($resposta, $dataset);
    }

    /**
     * Helper HTTP GET com timeout, User-Agent e verificação de status.
     * Retorna null em qualquer falha para que o chamador faça fallback.
     */
    private function _httpGet(string $url): ?string
    {
        $ctx = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'timeout'       => 4,
                'ignore_errors' => true,
                'header'        => implode("\r\n", [
                    'Accept: application/json',
                    'User-Agent: PHP-ExtensaoMVC/1.0',
                    'Accept-Charset: UTF-8',
                ]),
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $resposta = @file_get_contents($url, false, $ctx);

        if ($resposta !== false && isset($http_response_header)) {
            preg_match('/HTTP\/\S+\s+(\d{3})/', $http_response_header[0] ?? '', $m);
            $code = (int)($m[1] ?? 200);
            if ($code === 429 || $code >= 500) {
                return null;
            }
        }

        return $resposta !== false ? $resposta : null;
    }

    // ══════════════════════════════════════════════════════════════════
    // Helpers internos
    // ══════════════════════════════════════════════════════════════════

    /**
     * Remove máscaras de formatação dos campos antes de enviar ao banco.
     * CPF/CNPJ, Telefone e CEP ficam apenas com dígitos.
     * TELEFONE é limitado a 15 chars para caber no varchar(15) do banco.
     */
    private function limparMascaras(array $dados): array
    {
        // Remove campos que não existem na tabela
        // Nota: csrf_token já é removido automaticamente por $this->request->getPost()
        unset($dados['sem_numero']);

        // CPF: 000.000.000-00 → 11 dígitos
        // CNPJ 2.0: 00.ABC.345/0001-09 → 14 chars alfanuméricos (A-Z + 0-9)
        if (!empty($dados['CPF_CNPJ'])) {
            $tipoPessoa = $dados['TIPO_PESSOA'] ?? 'F';
            if ($tipoPessoa === 'F') {
                // CPF: remove tudo que não é dígito
                $dados['CPF_CNPJ'] = preg_replace('/\D/', '', $dados['CPF_CNPJ']);
            } else {
                // CNPJ: remove máscara mas preserva letras maiúsculas
                $dados['CPF_CNPJ'] = strtoupper(preg_replace('/[^A-Z0-9]/', '', $dados['CPF_CNPJ']));
            }
        }

        // Telefone: (00) 00000-0000 → somente dígitos, max 15 chars
        if (!empty($dados['TELEFONE'])) {
            $dados['TELEFONE'] = substr(preg_replace('/\D/', '', $dados['TELEFONE']), 0, 15);
        }

        // CEP: 00000-000 → 00000000 (8 dígitos)
        if (!empty($dados['CEP'])) {
            $dados['CEP'] = preg_replace('/\D/', '', $dados['CEP']);
        }

        return $dados;
    }
}