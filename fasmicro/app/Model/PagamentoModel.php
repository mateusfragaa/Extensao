<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\Validator;

class PagamentoModel extends ModelMain
{
    protected $table = "tb_pagamento_item";

    public $primaryKey = 'PAGI_ID';

    public $listaStatus = [
        'A' => 'ABERTO',
        'P' => 'PAGO',
        'C' => 'CANCELADO'
    ];

    public $validationRules = [
        'PAGI_PAG_ID' => [
            'label' => 'código da despesa',
            'rules' => 'required|int'
        ],
        'PAGI_VALOR' => [
            'label' => 'valor do pagamento',
            'rules' => 'required|float'
        ],
        'PAGI_TIPO_DOCUMENTO' => [
            'label' => 'forma de pagamento',
            'rules' => 'required|int'
        ],
        'PAG_OBSERVACAO' => [
            'label' => 'observação',
            'rules' => ''
        ],
    ];

    public function list($params)
    {
        if (trim($params['despesa'] ?? '') != '') {
            $this->db->where('PAGI_PAG_ID', $params['despesa']);
        }

        return $this->db
            ->orderBy('PAGI_CREATED_AT')
            ->join('tb_pagamento', 'PAGI_PAG_ID = PAG_ID')
            ->join('tb_pessoa', 'PES_ID = PAG_FAVORECIDO_ID')
            ->join('tb_tipo_documento', 'TDC_ID = PAGI_TIPO_DOCUMENTO')
            ->findAll();
    }

    public function insert($dados)
    {
        $isValido = $this->validateInsert($dados, $this->validationRules);

        if (!$isValido['status']) {
            return $isValido;
        } else {
            unset($dados[$this->primaryKey]);

            if ($this->db->insert($dados) > 0) {
                return [
                    'status' => true
                ];
            }

            return [
                'status' => false
            ];
        }
    }

    private function validateInsert($dados, $validationRules)
    {
        if (Validator::make($dados, $validationRules)) {
            return [
                'status' => false
            ];
        }

        $despesaModel = new DespesaModel;

        $despesa = $despesaModel->getById($dados['PAGI_PAG_ID']);

        if ($despesa['PAG_STATUS'] !== 'A') {
            return [
                'status'  => false,
                'msgErro' => 'Esta despesa não está em aberto'
            ];
        }

        if ($dados['PAGI_VALOR'] > $despesa['PAG_VALOR_ABERTO']) {
            return [
                'status'  => false,
                'msgErro' => 
                    "Não é possivel fazer um pagamento com valor acima do saldo atual da despesa (R$ " .
                    formatNumber($despesa['PAG_VALOR_ABERTO']) .
                    ")"
            ];
        }

        return [
            'status' => true
        ];
    }
}