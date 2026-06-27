<?php

use Core\Library\Csrf;

$pessoas = $data['data']['pessoas'] ?? [];
$documentos = $data['data']['documentos'] ?? [];
$recebimento = $data['data']['recebimento'] ?? [];
$id = $data['data']['recebimento']['REC_ID'] ?? [];
$status = $data['data']['status'] ?? [];
$action = $data['data']['action'] ?? 'insert';
var_dump("/recebimento/formRecebimento/<?= $action ?>/<?= $id ?>");
?>
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/recebimento/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Novo Recebimento</h4>
            <p class="text-muted small m-0">Lançamento de títulos de contas a receber.</p>
        </div>
    </div>
    <?= exibeAlerta() ?>
    <div class="card card-custom p-4">
        <form class="row g-4"
            action="/recebimento/formRecebimento/<?= $action ?>/<?= $id ?>"
            method="post">
            <?= Csrf::getHiddenField() ?>
            <input type="hidden" name="REC_ID" value="<?= $recebimento['REC_ID'] ?? 0 ?>">
            <div class="col-md-6">
                <label class="form-label">Cliente / Origem</label>
                <select class="form-select" name="rec_devedor_id">
                    <?php foreach ($pessoas as $pes): ?>
                        <option
                            value="<?= $pes['PES_ID'] ?>"
                            <?= $pes['PES_ID'] == $recebimento['REC_DEVEDOR_ID'] ? 'selected' : '' ?>>
                            <?= $pes['PES_NOME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Data criação</label>
                <input type="text" class="form-control" value="<?= $recebimento['REC_CREATED_AT'] ?>" disabled>
            </div>

            <div class="col-md-3">
                <label class="form-label">Valor (R$)</label>
                <input type="number" step="0.01" class="form-control" name="rec_valor" value="<?= $recebimento['REC_VALOR'] ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Vencimento</label>
                <input type="date" class="form-control" name="rec_vencimento" value="<?= $recebimento['REC_VENCIMENTO'] ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Recebimento</label>
                <input type="date" class="form-control" name="rec_data_baixa" value="<?= $recebimento['REC_DATA_BAIXA'] ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="rec_status">
                    <?php foreach ($status as $key => $descricao): ?>
                        <option
                            value="<?= $key ?>"
                            <?= $key == $recebimento['REC_STATUS'] ? 'selected' : '' ?>>
                            <?= $descricao ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Forma de Recebimento</label>
                <select class="form-select" name="rec_tipo_documento_id">
                    <?php foreach ($documentos as $key => $rec): ?>
                        <option
                            value="<?= $rec['TDC_ID'] ?>"
                            <?= $rec['TDC_ID'] == $recebimento['REC_TIPO_DOCUMENTO_ID'] ? 'selected' : '' ?>>
                            <?= $rec['TDC_DESCRICAO'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Observações</label>
                <textarea
                    class="form-control"
                    rows="1"
                    name="rec_observacao"><?= $recebimento['REC_OBSERVACAO'] ?? '' ?></textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light border px-4" href="#">Cancelar</a>
                <button type="submit" class="btn btn-primary-custom px-5">Salvar Recebimento</button>
            </div>
        </form>
    </div>
</div>