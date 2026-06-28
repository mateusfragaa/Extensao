<?php

namespace App\Model;

use Core\Library\ModelMain;
use DateTime;

class DespesaModel extends ModelMain
{
    protected $table = "tb_pagamento";

    public $primaryKey = 'PAG_ID';

    public $listaStatus = [
        'A' => 'ABERTO',
        'P' => 'PAGO',
        'C' => 'CANCELADO'
    ];

    public $validationRules = [
        'PAG_FAVORECIDO_ID' => [
            'label' => 'fornecedor / Favorecido',
            'rules' => 'required|int'
        ],
        'PAG_DESCRICAO' => [
            'label' => 'descrição da Despesa',
            'rules' => 'required|max:30'
        ],
        'PAG_VALOR' => [
            'label' => 'valor',
            'rules' => 'required|float'
        ],
        'PAG_DATA_VENCIMENTO' => [
            'label' => 'data de vencimento',
            'rules' => 'required|date'
        ],
        'PAG_STATUS' => [
            'label' => 'status',
            'rules' => 'required|max:1'
        ],
        'PAG_OBSERVACAO' => [
            'label' => 'observação',
            'rules' => ''
        ],
    ];

    public function list($params)
    {
        if (($params['idFornecedor'] ?? '' != '') && filter_var($params['idFornecedor'], FILTER_VALIDATE_INT)) {
            $this->db->where('PAG_FAVORECIDO_ID', (int) $params['idFornecedor']);
        }

        if (($params['nomeFornecedor'] ?? '' != '')) {
            $this->db->whereLike('UPPER(PES_NOME)', mb_strtoupper($params['nomeFornecedor']));
        }

        if (($params['vencimento'] ?? '' != '') && $params['vencimento'] != 'Todos') {
            $hoje = (new DateTime())->format('Y-m-d');

            if ($params['vencimento'] == 'vencidos') {
                $this->db->where('PAG_DATA_VENCIMENTO <', $hoje)
                    ->where('PAG_STATUS', 'A');
            } elseif ($params['vencimento'] == 'aVencer') {
                $this->db->where('PAG_DATA_VENCIMENTO >=', $hoje)
                    ->where('PAG_STATUS', 'A');
            }
        } elseif (($params['status'] ?? '' != '') && $params['status'] != 'Todos') {
            $this->db->where('PAG_STATUS', $params['status']);
        }

        return $this->db
            ->orderBy('PAG_DATA_VENCIMENTO')
            ->join('tb_pessoa', 'PES_ID = PAG_FAVORECIDO_ID')
            ->findAll();
    }

    public function getTotalPagarMes()
    {
        $hoje = new DateTime();
        $mes = $hoje->format('m');
        $ano = $hoje->format('Y');

        return $this->db->select('SUM(PAG_VALOR_ABERTO) AS TOTAL_MES')
            ->where('MONTH(PAG_DATA_VENCIMENTO)', $mes)
            ->where('YEAR(PAG_DATA_VENCIMENTO)', $ano)
            ->where('PAG_STATUS', 'A')
            ->first()['TOTAL_MES'] ?? '0';
    }

    public function getVenceHoje()
    {
        $hoje = (new DateTime())->format('Y-m-d');

        return $this->db->select('SUM(PAG_VALOR_ABERTO) AS TOTAL_HOJE')
            ->where('PAG_DATA_VENCIMENTO', $hoje)
            ->where('PAG_STATUS', 'A')
            ->first()['TOTAL_HOJE'] ?? '0';
    }

    public function getDebitosAtraso()
    {
        $hoje = (new DateTime())->format('Y-m-d');

        return $this->db->select('SUM(PAG_VALOR_ABERTO) AS TOTAL_ATRASADO')
            ->where('PAG_DATA_VENCIMENTO <', $hoje)
            ->where('PAG_STATUS', 'A')
            ->first()['TOTAL_ATRASADO'] ?? '0';
    }

    public function getDespesasSelect()
    {
        return $this->db
            ->select('PAG_ID, CPF_CNPJ, PES_NOME, PAG_VALOR_ABERTO')
            ->orderBy('PAG_ID')
            ->join('tb_pessoa', 'PES_ID = PAG_FAVORECIDO_ID')
            ->where('PAG_STATUS', 'A')
            ->findAll();
    }

    public function cancelarRegistro($params)
    {
        return $this->db
            ->where($this->primaryKey, $params[$this->primaryKey])
            ->update([
                'PAG_STATUS' => 'C'
            ]);
    }
}