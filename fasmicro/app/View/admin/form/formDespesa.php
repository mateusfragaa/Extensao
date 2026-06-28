<?= exibeAlerta() ?>

<?php

$disabled = $action === 'view' || $action === 'update' ? 'disabled' : '';

if ($action === 'insert') {
    $actionForm = baseUrl() . "{$controller}/{$action}";
} else {
    $actionForm = baseUrl() . "{$controller}/{$action}/{$d['PAG_ID']}";
}

?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= baseUrl() . "{$controller}?status=A" ?>" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Nova Despesa</h4>
            <p class="text-muted small m-0">Lançamento de contas a pagar e despesas.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <form class="row g-4" method="POST"
            action="<?= $actionForm ?>"
        >
            <?= csrfField() ?>
            <input type="hidden" name="PAG_ID" value="<?= $d['PAG_ID'] ?? '' ?>">
            <div class="col-md-6">
                <label class="form-label" for="PAG_FAVORECIDO_ID">Fornecedor / Favorecido</label>
                <select class="form-select" id="PAG_FAVORECIDO_ID" name="PAG_FAVORECIDO_ID" <?= $disabled ?> required>
                    <option value="" selected disabled hidden
                    >
                        Selecione o fornecedor...
                    </option>
                    <?php foreach ($pessoas as $p): ?>
                        <option
                            value="<?= $p['PES_ID'] ?>"
                            <?= $p['PES_ID'] === ($d['PAG_FAVORECIDO_ID'] ?? '') ? 'selected' : '' ?>
                        >
                            <?= "{$p['CPF_CNPJ']} - {$p['PES_NOME']}" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="PAG_DESCRICAO">Descrição da Despesa</label>
                <input 
                    type="text" class="form-control" id="PAG_DESCRICAO" name="PAG_DESCRICAO"
                    placeholder="Ex: Aluguel mensal, Compra de Insumos..."
                    value="<?= $d['PAG_DESCRICAO'] ?? '' ?>"
                    <?= $disabled ?>
                    required
                >
            </div>

            <div class="col-md-4">
                <label class="form-label" for="PAG_VALOR">Valor (R$)</label>
                <input type="number" step="0.01" class="form-control" 
                    id="PAG_VALOR" name="PAG_VALOR" placeholder="0,00"
                    min="0.01" value="<?= $d['PAG_VALOR'] ?? '' ?>"
                    <?= $disabled ?>
                    required
                >
            </div>
            <div class="col-md-4">
                <label class="form-label" for="PAG_DATA_VENCIMENTO">Vencimento</label>
                <input type="date" class="form-control"
                    id="PAG_DATA_VENCIMENTO" name="PAG_DATA_VENCIMENTO"
                    value="<?= $d['PAG_DATA_VENCIMENTO'] ?? '' ?>"
                    <?= $disabled ?>
                    required
                >
            </div>

            <div class="col-md-4">
                <label class="form-label" for="PAG_STATUS">Status</label>
                <select name="PAG_STATUS" id="PAG_STATUS" class="form-control"
                    <?= $action === 'view' ? 'disabled' : '' ?>
                >
                    <?php if ($action === 'insert'): ?>
                        <option value="A">Aberto</option>
                    <?php elseif ($action === 'update'): ?>
                        <option value="C">Cancelado</option>
                    <?php else: ?>
                        <option value="A"
                            <?= ($d['PAG_STATUS'] ?? '') === 'A' ? 'selected' : '' ?>
                        >
                            Aberto
                        </option>
                        <option value="P"
                            <?= ($d['PAG_STATUS'] ?? '') === 'P' ? 'selected' : '' ?>
                        >
                            Pago
                        </option>
                        <option value="C"
                            <?= ($d['PAG_STATUS'] ?? '') === 'C' ? 'selected' : '' ?>
                        >
                            Cancelado
                        </option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label" for="PAG_OBSERVACAO">Observação</label>
                <textarea
                    class="form-control" id="PAG_OBSERVACAO" name="PAG_OBSERVACAO"
                    <?= $disabled ?>
                ><?= $d['PAG_OBSERVACAO'] ?? '' ?></textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light border px-4"
                    href="<?= baseUrl() . "{$controller}?status=A" ?>"
                >
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary-custom px-5">
                    Salvar Pagamento
                </button>
            </div>
        </form>
    </div>
</div>