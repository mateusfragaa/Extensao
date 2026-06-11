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
     * Prepara o formulário para as ações e faz o require do mesmo
     */
    public function formPessoa($acao = 'view', $id = null)
    {
        $data = [];

        // Se houver erros de validação na sessão, recupera os dados digitados
        $formInputs = Session::getDestroy('formInputs');

        if ($acao == 'insert') {
            $data["action_form"] = "insert";
            $data["pessoa"] = $formInputs ?: [];
        } else {
            $data["action_form"] = $acao;
            // Se falhou a validação no update, usa o que o usuário digitou, senão busca do banco
            $data["pessoa"] = $formInputs ?: $this->model->getById($id);
        }

        $this->view(
            'admin/form/formPessoa',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    public function update($action = null, $id = null)
    {
        if ($this->model->update($_POST)) {
            Redirect::page("pessoa/", ['msgSucesso' => 'Sucesso ao atualizar o registro']);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao atualizar registro, verifique os dados!';
            Redirect::page("pessoa/formPessoa/update/" . $_POST['PES_ID'], ['msgError' => $msgError]);
        }
    }

    public function delete($action = null, $id = null)
    {
        if ($this->model->delete($_POST)) {
            Redirect::page("pessoa/", ['msgSucesso' => 'Sucesso ao apagar o registro']);
        } else {
            Redirect::page("pessoa/", ['msgError' => 'Erro ao apagar registro!']);
        }
    }
    public function insert($action = null, $id = null)
    {
        // Remove o PES_ID para que o banco de dados gere o ID automaticamente (AUTO_INCREMENT)
        if (isset($_POST['PES_ID'])) {
            unset($_POST['PES_ID']);
        }

        $idGerado = $this->model->insert($_POST);

        if ($idGerado) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Sucesso ao inserir registro, nova pessoa: ' . $idGerado]);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao inserir registro, verifique os campos obrigatórios!';
            Redirect::page('pessoa/formPessoa/insert', ['msgError' => $msgError]);
        }
    }

    public function validarReceitaAjax()
    {
        // Garante que a resposta devolvida seja estritamente um JSON
        header('Content-Type: application/json');

        // Recupera o HTML que o JavaScript vai enviar via POST
        $htmlRaw = $_POST['html_receita'] ?? null;

        if (!$htmlRaw) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum código HTML foi recebido pelo servidor.']);
            exit;
        }

        // Processamento do HTML usando o DOMDocument e XPath com base na sua imagem real
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();

        // Tratamento de encoding para não estragar acentos ou cedilhas no nome
        $dom->loadHTML(mb_convert_encoding($htmlRaw, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        libxml_clear_errors();

        // Seletores exatos baseados na árvore HTML do site da Receita Federal que você me mandou:
        // Pega o <b> dentro do span com classe clConteudoDados que contém o texto "Nome:"
        $queryNome = $xpath->query("//span[@class='clConteudoDados'][contains(text(), 'Nome:')]/b");

        // Tentativa automática de pegar a situação cadastral (geralmente segue o mesmo padrão de classe)
        $querySituacao = $xpath->query("//span[@class='clConteudoDados'][contains(text(), 'Situação Cadastral:')]/b");

        $nomeCompleto = $queryNome->length > 0 ? trim($queryNome->item(0)->nodeValue) : null;
        $situacao = $querySituacao->length > 0 ? trim($querySituacao->item(0)->nodeValue) : 'REGULAR';

        if (!$nomeCompleto) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível ler o nome no HTML. Certifique-se de que a consulta foi concluída.'
            ]);
            exit;
        }

        // Se achou o nome com sucesso, devolve o JSON limpo para o front-end
        echo json_encode([
            'sucesso' => true,
            'nome' => $nomeCompleto,
            'situacao' => $situacao
        ]);
        exit;
    }

    /**
     * Consulta CNPJ na API publica receitaws.com.br e retorna JSON
     * Rota: /pessoa/consultarCNPJAjax  (POST, AJAX)
     */
    public function consultarCNPJAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');

        if (strlen($cnpj) !== 14) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'CNPJ invalido: deve ter 14 digitos.']);
            exit;
        }

        $url = "    {$cnpj}";

        $ctx = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'timeout'       => 8,
                'ignore_errors' => true,
                'header'        => "User-Agent: PHP-MVC-Validator\r\n",
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $resposta = @file_get_contents($url, false, $ctx);

        if ($resposta === false) {
            echo json_encode([
                'sucesso'  => false,
                'mensagem' => 'Nao foi possivel conectar a Receita Federal. Verifique sua conexao.'
            ]);
            exit;
        }

        $dados = json_decode($resposta, true);

        if (!$dados || !isset($dados['status'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Resposta inesperada da Receita Federal.']);
            exit;
        }

        if (strtoupper($dados['status']) === 'ERROR') {
            $msg = $dados['message'] ?? 'CNPJ nao localizado na Receita Federal.';
            echo json_encode(['sucesso' => false, 'mensagem' => $msg]);
            exit;
        }

        $situacao    = $dados['situacao']    ?? 'Desconhecida';
        $nome        = $dados['nome']        ?? 'Nao informado';
        $fantasia    = $dados['fantasia']    ?? '';
        $municipio   = $dados['municipio']   ?? '';
        $uf          = $dados['uf']          ?? '';

        if (strtoupper($situacao) !== 'ATIVA') {
            echo json_encode([
                'sucesso'  => false,
                'mensagem' => "O CNPJ pertence a \"$nome\" mas a situacao cadastral e \"$situacao\" (nao ATIVA)."
            ]);
            exit;
        }

        echo json_encode([
            'sucesso'   => true,
            'nome'      => $nome,
            'fantasia'  => $fantasia,
            'situacao'  => ucfirst(strtolower($situacao)),
            'municipio' => $municipio,
            'uf'        => $uf,
        ]);
        exit;
    }
}