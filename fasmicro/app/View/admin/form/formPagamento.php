<?= exibeAlerta() ?>

<?php

$disabled = $action === 'view' ? 'disabled' : '';
// dd($d);
if ($action === 'insert') {
    $actionForm = baseUrl() . "{$controller}/{$action}";
} else {
    $actionForm = baseUrl() . "{$controller}/{$action}/{$d['PAGI_ID']}";
}

?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/despesa/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Novo Pagamento</h4>
            <p class="text-muted small m-0">Lançamento de contas a pagar e despesas.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <form class="row g-4" method="POST"
            action="<?= $actionForm ?>"
        >
            <input type="hidden" name="PAGI_ID" value="<?= $d['PAGI_ID'] ?? '' ?>">
            <div class="col-12">
                <label class="form-label" for="PAGI_PAG_ID">Despesa</label>
                <select class="form-select" id="PAGI_PAG_ID" name="PAGI_PAG_ID" <?= $disabled ?> required>
                    <option value="" selected disabled hidden
                    >
                        Selecione uma despesa...
                    </option>
                    <?php foreach ($despesas as $desp): ?>
                        <option
                            value="<?= $desp['PAG_ID'] ?>"
                            <?= $desp['PAG_ID'] == ($despesaSelecionada) ? 'selected' : '' ?>
                            data-saldo_aberto="<?= $desp['PAG_VALOR_ABERTO'] ?>"
                        >
                            <?= "Despesa: {$desp['PAG_ID']} - {$desp['CPF_CNPJ']} - {$desp['PES_NOME']}" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="PAGI_VALOR" id="labelPAGI_VALOR">Valor</label>
                <input 
                    type="number" class="form-control" id="PAGI_VALOR" name="PAGI_VALOR"
                    value="<?= $d['PAGI_VALOR'] ?? '' ?>" min="0.01" step="0.01"
                    <?= $disabled ?> max required
                >
            </div>
            <div class="col-md-6">
                <label class="form-label" for="PAGI_TIPO_DOCUMENTO">Forma de pagamento</label>
                <select class="form-select" id="PAGI_TIPO_DOCUMENTO" name="PAGI_TIPO_DOCUMENTO" <?= $disabled ?> required>
                    <option value="" selected disabled hidden
                    >
                        Selecione uma forma de pagamento...
                    </option>
                    <?php foreach ($tiposDocumentos as $tipoDoc): ?>
                        <option
                            value="<?= $tipoDoc['TDC_ID'] ?>"
                            <?= $tipoDoc['TDC_ID'] == ($despesaSelecionada) ? 'selected' : '' ?>
                        >
                            <?= "{$tipoDoc['TDC_ID']} - {$tipoDoc['TDC_DESCRICAO']}" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label" for="PAGI_OBSERVACAO">Observação</label>
                <textarea
                    class="form-control" id="PAGI_OBSERVACAO" name="PAGI_OBSERVACAO" <?= $disabled ?>
                ><?= $d['PAGI_OBSERVACAO'] ?? '' ?></textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a type="button" class="btn btn-light border px-4" href="#">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary-custom px-5">
                    Salvar Pagamento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const PAGI_PAG_ID = document.querySelector('#PAGI_PAG_ID')

    PAGI_PAG_ID.addEventListener('change', carregaValorMaximoPermitido)

    function carregaValorMaximoPermitido() {
        const optionSelecionado = PAGI_PAG_ID.options[PAGI_PAG_ID.selectedIndex]

        const saldoAberto = optionSelecionado.dataset.saldo_aberto

        if (saldoAberto == undefined) {
            return;
        }

        const labelPAGI_VALOR = document.querySelector('#labelPAGI_VALOR')
        const PAGI_VALOR = document.querySelector('#PAGI_VALOR')

        labelPAGI_VALOR.innerHTML = `Valor (max ${saldoAberto})`;

        PAGI_VALOR.max = saldoAberto
    }

    document.addEventListener('DOMContentLoaded', carregaValorMaximoPermitido)
</script>