<?php
use Core\Library\Csrf;

$action_form    = formDadosInput($data, 'tipoDocumento');
$errors         = \Core\Library\Session::get('formErrors');
$isReadOnly     = $action_form === 'view' ? 'disabled' : '';
?>
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/tipoDocumento/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Tipo de Documento <?= formSubTitulo($action_form) ?></h4>
            <p class="text-muted small m-0">Configure formas de pagamento e tipos de cobrança.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <?php echo exibeAlerta(); ?>

        <form class="row g-4" action="/tipoDocumento/<?= $action_form ?>" method="POST">

            <?= Csrf::getHiddenField() ?>

            <?php if ($action_form !== 'insert'): ?>
                <input type="hidden" name="TDC_ID" value="<?= setValue('TDC_ID') ?>">
            <?php endif; ?>

            <!-- ── Descrição ───────────────────────────────────────── -->
            <div class="col-md-8">
                <label class="form-label">
                    Descrição <span class="text-danger">*</span>
                </label>
                <input type="text" name="TDC_DESCRICAO"
                    class="form-control <?= isset($errors['TDC_DESCRICAO']) ? 'is-invalid' : '' ?>"
                    placeholder="Ex: Cartão de Crédito, Boleto, PIX..."
                    value="<?= htmlspecialchars(setValue('TDC_DESCRICAO')) ?>"
                    maxlength="45"
                    <?= $isReadOnly ?>>
                <?php if (isset($errors['TDC_DESCRICAO'])): ?>
                    <div class="invalid-feedback"><?= $errors['TDC_DESCRICAO'] ?></div>
                <?php endif; ?>
            </div>

            <!-- ── Status ─────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">
                    Status <span class="text-danger">*</span>
                </label>
                <select name="TDC_STATUS"
                        class="form-select <?= isset($errors['TDC_STATUS']) ? 'is-invalid' : '' ?>"
                        <?= $isReadOnly ?>>
                    <option value="1" <?= (setValue('TDC_STATUS', '1') === '1') ? 'selected' : '' ?>>
                        Ativo
                    </option>
                    <option value="0" <?= (setValue('TDC_STATUS') === '0') ? 'selected' : '' ?>>
                        Inativo
                    </option>
                </select>
                <?php if (isset($errors['TDC_STATUS'])): ?>
                    <div class="invalid-feedback"><?= $errors['TDC_STATUS'] ?></div>
                <?php endif; ?>
            </div>

            <!-- ── Observação ─────────────────────────────────────── -->
            <div class="col-12">
                <label class="form-label">Observação</label>
                <textarea name="TDC_OBSERVACAO"
                    class="form-control"
                    placeholder="Informações adicionais sobre este tipo de documento (opcional)..."
                    rows="3"
                    maxlength="255"
                    <?= $isReadOnly ?>><?= htmlspecialchars(setValue('TDC_OBSERVACAO')) ?></textarea>
                <div class="form-text">Máximo 255 caracteres.</div>
            </div>

            <!-- ── Botões ─────────────────────────────────────────── -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light border px-4" href="/tipoDocumento/">Cancelar</a>
                <?php if ($action_form !== 'view'): ?>
                    <button type="submit" class="btn btn-primary px-5">
                        <?= $action_form === 'delete' ? 'Confirmar Exclusão' : 'Salvar' ?>
                    </button>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>

<?php \Core\Library\Session::destroy('formErrors'); ?>
