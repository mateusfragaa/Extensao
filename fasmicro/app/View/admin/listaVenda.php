<?php
// var_dump($vendas);
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Gestão de Vendas</h4>
            <p class="text-muted small m-0">Mais agilidade para o seu processo de vendas.</p>
        </div>
        <a class="btn btn-primary-custom text-white shadow-sm" href="/Venda/formVenda/">
            <i class="bi bi-plus-lg me-2"></i> Nova Venda
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form action="/venda/filtroListagemVenda" method="post">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Buscar Venda</label>
                        <div class="input-group search-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="id_nome_cliente" placeholder="Cliente ou Nº do pedido...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Status</label>
                        <select class="form-select" name="status_venda">
                            <option value="">Todos os Status</option>
                            <option value="A">Aberta</option>
                            <option value="F">Faturada</option>
                            <option value="C">Cancelada</option>
                            <option value="O">Orçamento</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex">
                        <div class="me-3">
                            <label class="form-label small fw-bold text-muted">Período inicial</label>
                            <input type="date" class="form-control" name="data_inicio">
                        </div>
                        <div>
                            <label class="form-label small fw-bold text-muted">Período final</label>
                            <input type="date" class="form-control" name="data_fim">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-outline-primary w-100 fw-bold" type="submit">Filtrar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
    </script>
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive tabela-scroll">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th>Nº Pedido</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendas as $key => $venda) : ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= $venda['PEV_ID'] ?></td>
                            <td class="fw-medium"><?= $venda['PEV_CLIENTE_ID'] ?></td>
                            <td class="text-muted"><?= $venda['PEV_DATA_VENDA'] ?></td>
                            <td class="fw-bold">R$ <?= number_format($venda['PEV_TOTAL'], 2, ',', '.') ?></td>
                            <td><span class="badge-status status-concluida"><?= $venda['PEV_STATUS'] ?></span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light border" title="Editar"><i class="bi bi-eye text-primary"></i></button>
                                <button class="btn btn-sm btn-light border me-1" title="Visualizar"><i
                                        class="bi bi-pencil "></i></button>
                                <button class="btn btn-sm btn-light border" title="Imprimir"><i
                                        class="bi bi-trash text-danger"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de <?= count($vendas) ?> vendas encontradas</small>
        </div>
    </div>
</div>