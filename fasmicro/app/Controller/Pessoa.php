<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Csrf;
use Core\Library\Redirect;
use Core\Library\Session;

class Pessoa extends ControllerMain
{
    public function __construct()
    {
        $this->loadHelper('formHelper');
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
            ['pessoas' => $this->model->filtroListagem($_POST)],
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
        unset($_POST['PES_ID']);
        $_POST = $this->limparMascaras($_POST);

        $idGerado = $this->model->insert($_POST);

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
        $_POST = $this->limparMascaras($_POST);

        if ($this->model->update($_POST)) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Registro atualizado com sucesso.']);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao atualizar. Verifique os dados informados.';
            Redirect::page('pessoa/formPessoa/update/' . $_POST['PES_ID'], ['msgError' => $msgError]);
        }
    }

    /** Exclui pessoa. */
    public function delete($action = null, $id = null)
    {
        if ($this->model->delete($_POST)) {
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

        $cnpj    = preg_replace('/\D/', '', $_POST['cnpj']    ?? '');
        $dataset = preg_replace('/[^a-z]/', '', strtolower($_POST['dataset'] ?? 'receita'));

        $datasetsPermitidos = ['receita', 'cno', 'rntrc'];
        if (!in_array($dataset, $datasetsPermitidos)) {
            $dataset = 'receita';
        }

        if (strlen($cnpj) !== 14) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'CNPJ inválido: deve conter 14 dígitos.']);
            exit;
        }

        $resultado = $this->_consultarOpenCNPJ($cnpj, $dataset);

        if ($resultado === null) {
            // Fallback automático: tenta datasets alternativos
            foreach ($datasetsPermitidos as $ds) {
                if ($ds === $dataset) continue;
                $resultado = $this->_consultarOpenCNPJ($cnpj, $ds);
                if ($resultado !== null) {
                    $resultado['dataset_usado'] = $ds;
                    break;
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
    private function _consultarOpenCNPJ(string $cnpj, string $dataset): ?array
    {
        $url      = "https://api.opencnpj.org/{$cnpj}?dataset={$dataset}";
        $resposta = $this->_httpGet($url);

        if ($resposta === null) {
            return null;
        }

        $dados = json_decode($resposta, true);

        // Erro explícito da API (CNPJ inválido, não encontrado, etc.)
        if (!$dados || isset($dados['error']) || isset($dados['message'])) {
            $msg = $dados['message'] ?? ($dados['error'] ?? 'CNPJ não localizado neste dataset.');
            return ['sucesso' => false, 'mensagem' => $msg, 'dataset' => $dataset];
        }

        // ── Lê os campos com os nomes corretos da API opencnpj.org ──
        $nome      = $dados['razao_social']      ?? 'Não informado';
        $fantasia  = $dados['nome_fantasia']     ?? '';
        $situacao  = $dados['situacao_cadastral'] ?? 'Desconhecida';
        $municipio = $dados['municipio']         ?? '';
        $uf        = $dados['uf']                ?? '';
        $cep       = preg_replace('/\D/', '', $dados['cep']       ?? '');
        $logradouro= $dados['logradouro']        ?? '';
        $numero    = $dados['numero']            ?? '';
        $bairro    = $dados['bairro']            ?? '';

        // A API retorna "Ativa", "Inapta", "Baixada", "Suspensa", "Nula"
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

    /**
     * Helper HTTP GET com timeout, User-Agent e verificação de status.
     * Retorna null em qualquer falha para que o chamador faça fallback.
     */
    private function _httpGet(string $url): ?string
    {
        $ctx = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'timeout'       => 8,
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
    // AJAX — Validação CPF via HTML capturado do popup da Receita Federal
    // ══════════════════════════════════════════════════════════════════

    public function validarReceitaAjax()
    {
        header('Content-Type: application/json');

        $htmlRaw = $_POST['html_receita'] ?? null;

        if (!$htmlRaw) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum HTML recebido.']);
            exit;
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($htmlRaw, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        libxml_clear_errors();

        $qNome = $xpath->query("//span[@class='clConteudoDados'][contains(text(), 'Nome:')]/b");
        $qSit  = $xpath->query("//span[@class='clConteudoDados'][contains(text(), 'Situação Cadastral:')]/b");

        $nome     = $qNome->length > 0 ? trim($qNome->item(0)->nodeValue) : null;
        $situacao = $qSit->length  > 0 ? trim($qSit->item(0)->nodeValue)  : 'REGULAR';

        if (!$nome) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível extrair o nome. Conclua a consulta no site da Receita antes de fechar.']);
            exit;
        }

        echo json_encode(['sucesso' => true, 'nome' => $nome, 'situacao' => $situacao]);
        exit;
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
        unset($dados['csrf_token'], $dados['sem_numero']);

        // CPF: 000.000.000-00 → 00000000000 (11 dígitos)
        // CNPJ: 00.000.000/0000-00 → 00000000000000 (14 dígitos)
        if (!empty($dados['CPF_CNPJ'])) {
            $dados['CPF_CNPJ'] = preg_replace('/\D/', '', $dados['CPF_CNPJ']);
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
