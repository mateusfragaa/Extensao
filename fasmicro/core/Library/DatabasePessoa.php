<?php

namespace Core\Library;

/**
 * DatabasePessoa
 *
 * Extende Database sem modificá-lo.
 * Adiciona wrappers que relançam exceptions
 * para que o ModelMain::handleDatabaseError
 * exiba mensagens amigáveis.
 *
 * Como $where/$params são private no pai, não os
 * acessamos diretamente — chamamos o método original
 * do pai e relançamos qualquer exception que ele retorne
 * via Session como string bruta.
 */
class DatabasePessoa extends Database
{
    /**
     * insertComTratamento
     * Chama parent::insert e relança exception se houver erro.
     */
    public function insertComTratamento(array $data)
    {
        // Limpa a sessão antes para detectar se o pai gravou erro
        Session::destroy('msgError');

        $id = parent::insert($data);

        // O pai grava msgError na sessão em vez de lançar exception
        // Então verificamos se ele gravou algo e lançamos nós mesmos
        $erro = Session::get('msgError');
        if ($erro !== false && $erro !== '') {
            Session::destroy('msgError');
            throw new \RuntimeException($erro);
        }

        return $id;
    }

    /**
     * updateComTratamento
     * Chama parent::update e relança exception se houver erro.
     */
    public function updateComTratamento(array $data)
    {
        Session::destroy('msgError');

        $rs = parent::update($data);

        $erro = Session::get('msgError');
        if ($erro !== false && $erro !== '') {
            Session::destroy('msgError');
            throw new \RuntimeException($erro);
        }

        return $rs;
    }
}
