<?php
$action_form = formDadosInput($data, 'produto');
?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/produto/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold m-0">Produto<?= formSubTitulo($action_form) ?></h4>
    </div>
    <div class="card card-custom p-4">
        <form class="row g-4" action="/produto/<?= $action_form ?>" method="post">
            <input type="hidden" name="PRD_ID" id="id" value="<?= setValue('id', setValue('PRD_ID')) ?>">
            <div class="col-md-6">
                <label class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" placeholder="Ex: Notebook Gamer" name="prd_descricao"
                    value="<?= setValue('PRD_DESCRICAO') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">SKU / Código</label>
                <input type="text" class="form-control" placeholder="PROD-001" disabled
                    value="<?= setValue('PRD_ID') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Categoria</label>
                <input type="text" class="form-control" placeholder="Ex: Bebida" name="prd_categoria"
                    value="<?= setValue('PRD_CATEGORIA') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Preço de Custo (R$)</label>
                <input type="number" step="0.01" class="form-control" placeholder="0,00" name="prd_preco_custo"
                    value="<?= setValue('PRD_PRECO_CUSTO') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Preço de Venda (R$)</label>
                <input type="number" step="0.01" class="form-control" placeholder="0,00" name="prd_preco_venda"
                    value="<?= setValue('PRD_PRECO_VENDA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estoque Atual</label>
                <input type="number" class="form-control" placeholder="0" name="prd_estoque"
                    value="<?= setValue('PRD_ESTOQUE') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estoque Mínimo</label>
                <input type="number" class="form-control" placeholder="0" name="prd_estoque_min"
                    value="<?= setValue('PRD_ESTOQUE_MIN') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Data Criação</label>
                <input type="text" class="form-control" disabled
                    value="<?= setValue('PRD_CREATED_AT') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" name="prd_status">
                    <option <?= ((setValue('PRD_STATUS') ?? '') == '') ? 'selected' : '' ?>>Selecione...</option>
                    <option value="1" <?= ((setValue('PRD_STATUS') ?? '') == "1") ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ((setValue('PRD_STATUS') ?? '') == "0") ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unidade</label>
                <select class="form-select" name="prd_unidade">
                    <option <?= ((setValue('PRD_UNIDADE') ?? '') == '') ? 'selected' : '' ?>>Selecione...</option>
                    <option value="UN" <?= ((setValue('PRD_UNIDADE') ?? '') == "UN") ? 'selected' : '' ?>>UN</option>
                    <option value="KG" <?= ((setValue('PRD_UNIDADE') ?? '') == "KG") ? 'selected' : '' ?>>KG</option>
                    <option value="CX" <?= ((setValue('PRD_UNIDADE') ?? '') == "CX") ? 'selected' : '' ?>>CX</option>
                    <option value="M2" <?= ((setValue('PRD_UNIDADE') ?? '') == "M2") ? 'selected' : '' ?>>M2</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Descrição Curta</label>
                <textarea class="form-control" rows="3" placeholder="Detalhes técnicos ou observações..." name="prd_observacao"><?= setValue('PRD_OBSERVACAO') ?></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <button type="reset" class="btn btn-light border px-4">Cancelar</button>
                <?php if ($action_form !== "view") : ?>
                    <button type="submit" class="btn btn-primary-custom px-5">Salvar Produto</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>