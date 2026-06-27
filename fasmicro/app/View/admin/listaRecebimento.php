<?php
$recebimentos = $data['data']['recebimentos'] ?? [];
$rec_status = $data['data']['status_rec'] ?? [];
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Contas a Receber</h4>
            <p class="text-muted small m-0">Gerencie contas recebidas com facilidade.</p>
        </div>
        <div>
            <a class="btn btn-primary-custom shadow-sm" href="/recebimento/formRecebimento">
                <i class="bi bi-plus-lg me-2"></i> Novo Recebimento
            </a>
            <a class="btn btn-primary-custom shadow-sm" href="/recebimento/bordero">
                <i class="bi bi-plus-lg me-2"></i> Baixar Recebimento
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom stat-card p-3 shadow-sm">
                <small class="text-muted fw-bold">TOTAL A RECEBER</small>
                <h3 class="fw-bold m-0">R$ 12.450,00</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card recebido p-3 shadow-sm">
                <small class="text-muted fw-bold">RECEBIDO (MÊS)</small>
                <h3 class="fw-bold m-0 text-success">R$ 8.200,00</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card atrasado p-3 shadow-sm">
                <small class="text-muted fw-bold">ATRASADO</small>
                <h3 class="fw-bold m-0 text-danger">R$ 1.150,00</h3>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Cliente / Documento</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Nome ou Nº da fatura...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select class="form-select">
                        <option selected>Todos os Status</option>
                        <?php foreach ($rec_status as $key => $status): ?>
                            <option value="<?= $key ?>"><?= $status ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Vencimento</label>
                    <input type="date" class="form-control" value="2026-03">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-primary w-100 fw-bold">Filtrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th>Vencimento</th>
                        <th>Cliente</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recebimentos as $rec) : ?>
                        <tr>
                            <td class="text-muted"><?= $rec['REC_VENCIMENTO'] ?></td>
                            <td class="fw-bold text-dark"><?= $rec['PES_NOME'] ?></td>
                            <td><?= $rec['REC_OBSERVACAO'] ?></td>
                            <td class="fw-bold">R$ <?= number_format($rec['REC_VALOR'], 2, ',', '.') ?></td>
                            <td><span class="badge-financeiro status-pago"><?= $rec_status[$rec['REC_STATUS']] ?></span></td>
                            <td class="text-end">
                                <a href="/venda/formVenda/cancelar/" class="btn btn-sm btn-light border" title="Cencelar"><i class="bi bi-x text-warning"></i></a>
                                <button class="btn btn-sm btn-light border" title="Editar"><i class="bi bi-eye text-primary"></i></button>
                                <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de <?= count($recebimentos) ?> registros encontrados</small>
        </div>
    </div>
</div>