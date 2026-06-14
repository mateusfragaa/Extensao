<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;

class Pessoa extends ControllerMain
{
    public function __construct()
    {
        $this->loadHelper('formHelper');
        return parent::__construct();
    }

    /**
     * Lista todas as pessoas cadastradas.
     */
    public function index($action = null, $id = null)
    {
        $this->view(
            'admin/listaPessoa',
            [
                "pessoas" => $this->model->lista('PES_NOME')
            ],
            'sistema'
        );
    }

    /**
     * Filtragem via AJAX ou requisição normal.
     */
    public function filtroListagemPessoa($action = null, $id = null)
    {
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
        $layout = $isAjax ? null : 'sistema';

        $this->view(
            'admin/listaPessoa',
            [
                "pessoas" => $this->model->filtroListagem($_POST)
            ],
            $layout
        );
    }

    /**
     * Prepara o formulário para as ações insert / update / delete / view.
     */
    public function formPessoa($acao = 'view', $id = null)
    {
        $data = [];

        // Se houver falha de validação na sessão, recupera o que o usuário digitou
        $formInputs = Session::getDestroy('formInputs');

        if ($acao === 'insert') {
            $data['action_form'] = 'insert';
            $data['pessoa']      = $formInputs ?: [];
        } else {
            $data['action_form'] = $acao;
            // Em caso de falha de update usa o que estava no POST, senão busca no banco
            $data['pessoa']      = $formInputs ?: $this->model->getById($id);
        }

        $this->view(
            'admin/form/formPessoa',
            ['data' => $data],
            'sistema'
        );
    }

    /**
     * Insere nova pessoa.
     */
    public function insert($action = null, $id = null)
    {
        // Remove PES_ID para que o banco gere via AUTO_INCREMENT
        if (isset($_POST['PES_ID'])) {
            unset($_POST['PES_ID']);
        }

        $idGerado = $this->model->insert($_POST);

        if ($idGerado) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Cadastro realizado com sucesso! (ID: ' . $idGerado . ')']);
        } else {
            $msgError = Session::get('msgError') ?: 'Verifique os campos obrigatórios e tente novamente.';
            Redirect::page('pessoa/formPessoa/insert', ['msgError' => $msgError]);
        }
    }

    /**
     * Atualiza pessoa existente.
     */
    public function update($action = null, $id = null)
    {
        if ($this->model->update($_POST)) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Registro atualizado com sucesso.']);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao atualizar registro. Verifique os dados informados.';
            Redirect::page('pessoa/formPessoa/update/' . $_POST['PES_ID'], ['msgError' => $msgError]);
        }
    }

    /**
     * Exclui pessoa.
     */
    public function delete($action = null, $id = null)
    {
        if ($this->model->delete($_POST)) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Registro excluído com sucesso.']);
        } else {
            Redirect::page('pessoa/', ['msgError' => 'Erro ao excluir registro. Verifique se não há vínculos com outros dados.']);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // AJAX — Consulta CNPJ
    // Rota: POST /pessoa/consultarCNPJAjax
    // ══════════════════════════════════════════════════════════════════

    /**
     * Consulta CNPJ usando múltiplas APIs públicas com fallback automático.
     * Ordem: BrasilAPI → publica.cnpj.ws → receitaws.com.br
     */
    public function consultarCNPJAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');

        if (strlen($cnpj) !== 14) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'CNPJ inválido: deve conter 14 dígitos.']);
            exit;
        }

        // Tenta cada API na ordem; retorna assim que uma funcionar
        $resultado = $this->_consultarBrasilAPI($cnpj)
                  ?? $this->_consultarCnpjWs($cnpj)
                  ?? $this->_consultarReceitaWs($cnpj);

        if ($resultado === null) {
            echo json_encode([
                'sucesso'  => false,
                'mensagem' => 'Não foi possível consultar o CNPJ no momento. Todas as fontes estão indisponíveis. Tente novamente em instantes.'
            ]);
            exit;
        }

        echo json_encode($resultado);
        exit;
    }

    /**
     * BrasilAPI — https://brasilapi.com.br/api/cnpj/v1/{cnpj}
     * Resposta: { cnpj, razao_social, nome_fantasia, situacao_cadastral (string), ... }
     */
    private function _consultarBrasilAPI(string $cnpj): ?array
    {
        $url      = "https://brasilapi.com.br/api/cnpj/v1/{$cnpj}";
        $resposta = $this->_httpGet($url);

        if ($resposta === null) {
            return null; // API indisponível — tentar próxima
        }

        $dados = json_decode($resposta, true);

        if (!$dados || isset($dados['type'])) {
            // BrasilAPI retorna { type, message, ... } em erros
            $msg = $dados['message'] ?? 'CNPJ não localizado.';
            return ['sucesso' => false, 'mensagem' => $msg];
        }

        $situacao = $dados['descricao_situacao_cadastral'] ?? ($dados['situacao_cadastral'] ?? 'Desconhecida');

        if (strtoupper($situacao) !== 'ATIVA') {
            $nome = $dados['razao_social'] ?? 'Não informado';
            return [
                'sucesso'  => false,
                'mensagem' => "CNPJ pertence a \"{$nome}\" mas a situação cadastral é \"{$situacao}\" (não ATIVA)."
            ];
        }

        return [
            'sucesso'   => true,
            'nome'      => $dados['razao_social']   ?? 'Não informado',
            'fantasia'  => $dados['nome_fantasia']  ?? '',
            'situacao'  => ucfirst(strtolower($situacao)),
            'municipio' => $dados['municipio']      ?? '',
            'uf'        => $dados['uf']             ?? '',
            'cep'       => preg_replace('/\D/', '', $dados['cep'] ?? ''),
            'logradouro'=> $dados['logradouro_tipo'] . ' ' . ($dados['logradouro'] ?? ''),
            'numero'    => $dados['numero']         ?? '',
            'bairro'    => $dados['bairro']         ?? '',
            'fonte'     => 'BrasilAPI',
        ];
    }

    /**
     * publica.cnpj.ws — https://publica.cnpj.ws/cnpj/{cnpj}
     * Resposta: { razao_social, estabelecimento: { situacao_cadastral, ... } }
     */
    private function _consultarCnpjWs(string $cnpj): ?array
    {
        $url      = "https://publica.cnpj.ws/cnpj/{$cnpj}";
        $resposta = $this->_httpGet($url);

        if ($resposta === null) {
            return null;
        }

        $dados = json_decode($resposta, true);

        if (!$dados || isset($dados['status']) || empty($dados['razao_social'])) {
            return null; // Formato inesperado — tentar próxima
        }

        $est      = $dados['estabelecimento'] ?? [];
        $situacao = $est['situacao_cadastral'] ?? 'Desconhecida';

        if (strtoupper($situacao) !== 'ATIVA') {
            $nome = $dados['razao_social'] ?? 'Não informado';
            return [
                'sucesso'  => false,
                'mensagem' => "CNPJ pertence a \"{$nome}\" mas a situação cadastral é \"{$situacao}\" (não ATIVA)."
            ];
        }

        $end = $est['endereco'] ?? [];

        return [
            'sucesso'   => true,
            'nome'      => $dados['razao_social']      ?? 'Não informado',
            'fantasia'  => $est['nome_fantasia']       ?? '',
            'situacao'  => ucfirst(strtolower($situacao)),
            'municipio' => $end['municipio']['ibge_nome'] ?? ($end['municipio']['nome'] ?? ''),
            'uf'        => $end['estado']['sigla']     ?? '',
            'cep'       => preg_replace('/\D/', '', $end['cep'] ?? ''),
            'logradouro'=> $end['logradouro']          ?? '',
            'numero'    => $end['numero']              ?? '',
            'bairro'    => $end['bairro']              ?? '',
            'fonte'     => 'cnpj.ws',
        ];
    }

    /**
     * receitaws.com.br — https://receitaws.com.br/v1/cnpj/{cnpj}
     * Resposta: { status, nome, fantasia, situacao, municipio, uf, ... }
     * ATENÇÃO: tem rate limit de 3 req/min no plano gratuito.
     */
    private function _consultarReceitaWs(string $cnpj): ?array
    {
        $url      = "https://receitaws.com.br/v1/cnpj/{$cnpj}";
        $resposta = $this->_httpGet($url);

        if ($resposta === null) {
            return null;
        }

        $dados = json_decode($resposta, true);

        if (!$dados || !isset($dados['status'])) {
            return null;
        }

        if (strtoupper($dados['status']) === 'ERROR') {
            $msg = $dados['message'] ?? 'CNPJ não localizado na Receita Federal.';
            return ['sucesso' => false, 'mensagem' => $msg];
        }

        $situacao = $dados['situacao'] ?? 'Desconhecida';

        if (strtoupper($situacao) !== 'ATIVA') {
            $nome = $dados['nome'] ?? 'Não informado';
            return [
                'sucesso'  => false,
                'mensagem' => "CNPJ pertence a \"{$nome}\" mas a situação cadastral é \"{$situacao}\" (não ATIVA)."
            ];
        }

        return [
            'sucesso'   => true,
            'nome'      => $dados['nome']      ?? 'Não informado',
            'fantasia'  => $dados['fantasia']  ?? '',
            'situacao'  => ucfirst(strtolower($situacao)),
            'municipio' => $dados['municipio'] ?? '',
            'uf'        => $dados['uf']        ?? '',
            'cep'       => preg_replace('/\D/', '', $dados['cep'] ?? ''),
            'logradouro'=> $dados['logradouro'] ?? '',
            'numero'    => $dados['numero']    ?? '',
            'bairro'    => $dados['bairro']    ?? '',
            'fonte'     => 'ReceitaWS',
        ];
    }

    /**
     * Helper HTTP GET com timeout e SSL flexível.
     * Retorna null em caso de falha de conexão (para o fallback agir).
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

        // Verifica se foi HTTP 429 (rate limit) ou outros erros por header
        if ($resposta !== false && isset($http_response_header)) {
            $statusLine = $http_response_header[0] ?? '';
            preg_match('/HTTP\/\d\.\d\s+(\d{3})/', $statusLine, $m);
            $httpCode = (int)($m[1] ?? 200);

            if ($httpCode === 429 || $httpCode >= 500) {
                return null; // Tratar como indisponível
            }
        }

        return $resposta !== false ? $resposta : null;
    }

    // ══════════════════════════════════════════════════════════════════
    // AJAX — Validação CPF via HTML da Receita Federal (popup)
    // Rota: POST /pessoa/validarReceitaAjax
    // ══════════════════════════════════════════════════════════════════

    /**
     * Recebe o HTML capturado do popup da Receita Federal
     * e extrai o nome e situação cadastral do CPF.
     */
    public function validarReceitaAjax()
    {
        header('Content-Type: application/json');

        $htmlRaw = $_POST['html_receita'] ?? null;

        if (!$htmlRaw) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum HTML recebido pelo servidor.']);
            exit;
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($htmlRaw, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        libxml_clear_errors();

        $queryNome     = $xpath->query("//span[@class='clConteudoDados'][contains(text(), 'Nome:')]/b");
        $querySituacao = $xpath->query("//span[@class='clConteudoDados'][contains(text(), 'Situação Cadastral:')]/b");

        $nomeCompleto = $queryNome->length     > 0 ? trim($queryNome->item(0)->nodeValue)     : null;
        $situacao     = $querySituacao->length > 0 ? trim($querySituacao->item(0)->nodeValue) : 'REGULAR';

        if (!$nomeCompleto) {
            echo json_encode([
                'sucesso'  => false,
                'mensagem' => 'Não foi possível extrair o nome do HTML. Certifique-se de que a consulta foi concluída no site da Receita.'
            ]);
            exit;
        }

        echo json_encode([
            'sucesso'  => true,
            'nome'     => $nomeCompleto,
            'situacao' => $situacao
        ]);
        exit;
    }
}
