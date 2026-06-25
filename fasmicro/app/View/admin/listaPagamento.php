<?= exibeAlerta() ?>

<?php

$despesaEspecifica = !is_null($despesa);

if ($despesaEspecifica) {
    $linkNovoPagamento = baseUrl() . "{$controller}/form/insert?despesa=" . $despesa['PAG_ID'];
} else {
    $linkNovoPagamento = baseUrl() . "{$controller}/form/insert";
}

?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Pagamentos</h4>
            <p class="text-muted small m-0">Mais controle sobre despesas e compromissos financeiros.</p>
        </div>
        <a
            class="btn btn-primary-custom shadow-sm"
            href="<?= $linkNovoPagamento ?>"
        >
            <i class="bi bi-plus-lg me-2"></i> Novo Pagamento
        </a>
    </div>

    <?php if ($despesaEspecifica): ?>

        <div class="card card-custom mb-4">
            <div class="table-responsive">
                <table class="table table-custom m-0">
                    <thead>
                        <tr>
                            <th>Vencimento</th>
                            <th>Fornecedor / Descrição</th>
                            <th>Valor</th>
                            <th>Saldo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= formatDate($despesa['PAG_DATA_VENCIMENTO']) ?></td>
                            <td>
                                <strong>
                                    <?= "{$pessoa['CPF_CNPJ']} - {$pessoa['PES_NOME']}" ?>
                                </strong>
                                <br>
                                <?= $despesa['PAG_DESCRICAO'] ?>
                            </td>
                            <td class="fw-bold text-primary text-end"><?= formatNumber($despesa['PAG_VALOR']) ?></td>
                            <td class="text-end"><?= formatNumber($despesa['PAG_VALOR_ABERTO']) ?></td>
                            <td><?= $aDespesaStatus[$despesa['PAG_STATUS']] ?? '' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif ?>

    <div class="card card-custom card-ajuste-footer">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Data pagamento</th>
                        <th>Fornecedor / Descrição</th>
                        <th>Valor</th>
                        <th>Forma pagamento</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista as $v): ?>
                        <tr>
                            <td>
                                <a href="<?= baseUrl() . "pagamento?despesa={$v['PAGI_ID']}" ?>">
                                    <i class="bi bi-card-list"></i>
                                </a>
                            </td>
                            <td><?= formatDate($v['PAGI_CREATED_AT']) ?></td>
                            <td>
                                <strong>
                                    <?= "{$v['CPF_CNPJ']} - {$v['PES_NOME']}" ?>
                                </strong>
                                <br>
                                <?= $v['PAG_DESCRICAO'] ?>
                            </td>
                            <td class="fw-bold text-primary text-end"><?= formatNumber($v['PAGI_VALOR']) ?></td>
                            <td><?= $v['TDC_DESCRICAO'] ?></td>
                            <td>
                                <a
                                    class="btn btn-sm btn-light border" 
                                    href="<?= baseUrl() . "{$controller}/form/view/" . $v['PAG_ID'] ?>"
                                    title="Visualizar"
                                >
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                                <!-- <a
                                    class="btn btn-sm btn-light border" 
                                    href="<= baseUrl() . "{$controller}/form/update/" . $v['PAG_ID'] ?>"
                                    title="Editar"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a
                                    class="btn btn-sm btn-light border text-danger" 
                                    href="<= baseUrl() . "{$controller}/form/delete/" . $v['PAG_ID'] ?>"
                                    title="Excluir"
                                >
                                    <i class="bi bi-trash"></i>
                                </a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top ">
            <small class="text-muted">Total de 3 registros encontrados</small>
        </div>
    </div>
</div>