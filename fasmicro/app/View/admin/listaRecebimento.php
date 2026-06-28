<?php
$recebimentos = $data['data']['recebimentos'] ?? [];
$rec_status = $data['data']['status_rec'] ?? [];
$metricas = $data['data']['metricas'] ?? [];
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Contas a Receber</h4>
            <p class="text-muted small m-0">Gerencie contas recebidas com facilidade.</p>
        </div>
        <div>
            <a class="btn btn-primary-custom shadow-sm" href="/recebimento/formRecebimento/insert">
                <i class="bi bi-plus-lg me-2"></i> Novo Recebimento
            </a>
            <a class="btn btn-primary-custom shadow-sm" href="/baixarRecebimento/formBaixar">
                <i class="bi bi-plus-lg me-2"></i> Baixar Recebimento
            </a>
        </div>
    </div>
    <?= exibeAlerta() ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom stat-card p-3 shadow-sm">
                <small class="text-muted fw-bold">TOTAL A RECEBER</small>
                <h3 class="fw-bold m-0">R$ <?= number_format($metricas['total_geral'] ?? 0, 2, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card recebido p-3 shadow-sm">
                <small class="text-muted fw-bold">RECEBIDO (MÊS)</small>
                <h3 class="fw-bold m-0 text-success">R$ <?= number_format($metricas['total_recebido_mes'] ?? 0, 2, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card atrasado p-3 shadow-sm">
                <small class="text-muted fw-bold">ATRASADO</small>
                <h3 class="fw-bold m-0 text-danger">R$ <?= number_format($metricas['total_atrasado'] ?? 0, 2, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-2">
            <div class="row g-3">
                <form id="form_filtro_recebimento">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" id="filtro_nome" class="form-control" placeholder="Nome">
                        </div>
                        <div class="col-md-3">
                            <select id="filtro_status" class="form-select">
                                <option value="">Todos os Status</option>
                                <?php foreach ($rec_status as $key => $status): ?>
                                    <option value="<?= $key ?>"><?= $status ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="filtro_data" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom m-0" id="tabela_recebimentos">
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
                        <tr
                            data-nome="<?= strtolower($rec['PES_NOME']) ?>"
                            data-status="<?= $rec['REC_STATUS'] ?>"
                            data-vencimento="<?= $rec['REC_VENCIMENTO'] ?>">


                            <td class="text-muted"><?= date('d/m/Y', strtotime($rec['REC_VENCIMENTO'])) ?></td>
                            <td class="fw-bold text-dark"><?= $rec['PES_NOME'] ?></td>
                            <td><?= $rec['REC_OBSERVACAO'] ?></td>
                            <td class="fw-bold">R$ <?= number_format($rec['REC_VALOR'], 2, ',', '.') ?></td>
                            <td><span class="badge-financeiro status-pago"><?= $rec_status[$rec['REC_STATUS']] ?></span></td>
                            <td class="text-end">
                                <a href="/Recebimento/formRecebimento/view/<?= $rec['REC_ID'] ?>" class="btn btn-sm btn-light border" title="View"><i class="bi bi-eye text-primary"></i></a>
                                <a class="btn btn-sm btn-light border me-1" title="Editar" href="/Recebimento/formRecebimento/update/<?= $rec['REC_ID'] ?>">
                                    <i class="bi bi-pencil "></i>
                                </a>
                                <a class="btn btn-sm btn-light border" title="Excluir" href="/Recebimento/formRecebimento/delete/<?= $rec['REC_ID'] ?>">
                                    <i class=" bi bi-trash text-danger"></i>
                                </a>
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
    <?= filtroRecebimetos() ?>
</div>