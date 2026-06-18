<?php
use Core\Library\Csrf;
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Tipos de Documento</h4>
            <p class="text-muted small m-0">Defina as formas de pagamento e tipos de cobrança do sistema.</p>
        </div>
        <a href="/tipoDocumento/formTipoDocumento/insert" class="btn btn-primary-custom shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Novo Tipo
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <?php exibeAlerta(); ?>
            <form id="formFiltroTipo" class="row g-2" action="/tipoDocumento/filtroListagemTipoDocumento" data-filtro="true" data-tabela="tabelaContainer" method="POST">
                <?= Csrf::getHiddenField() ?>
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="filtroDescricao" name="filtroDescricao"
                            class="form-control border-start-0"
                            placeholder="Buscar por descrição..."
                            value="<?= htmlspecialchars($_POST['filtroDescricao'] ?? '') ?>"
                            autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filtroStatus" name="filtroStatus" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="1" <?= (($_POST['filtroStatus'] ?? '') === '1') ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= (($_POST['filtroStatus'] ?? '') === '0') ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom overflow-hidden" id="tabelaContainer">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th style="width:80px;">Cód.</th>
                        <th>Descrição</th>
                        <th>Observação</th>
                        <th style="width:110px;">Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tiposDocumento)): ?>
                        <?php foreach ($tiposDocumento as $t): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= str_pad($t['TDC_ID'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($t['TDC_DESCRICAO']) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($t['TDC_OBSERVACAO'] ?? '—') ?></td>
                                <td>
                                    <span class="badge <?= $t['TDC_STATUS'] ? 'bg-success' : 'bg-secondary' ?> badge-status">
                                        <?= $t['TDC_STATUS'] ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="/tipoDocumento/formTipoDocumento/view/<?= $t['TDC_ID'] ?>"
                                       class="btn btn-sm btn-light border" title="Visualizar">
                                        <i class="bi bi-eye text-primary"></i>
                                    </a>
                                    <a href="/tipoDocumento/formTipoDocumento/update/<?= $t['TDC_ID'] ?>"
                                       class="btn btn-sm btn-light border" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/tipoDocumento/formTipoDocumento/delete/<?= $t['TDC_ID'] ?>"
                                       class="btn btn-sm btn-light border text-danger" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Nenhum tipo de documento encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de <?= count($tiposDocumento ?? []) ?> registros encontrados</small>
        </div>
    </div>
</div>

<script src="/assests/js/filtroLista.js"></script>
