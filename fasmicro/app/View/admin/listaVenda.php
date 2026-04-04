<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Gestão de Vendas</h4>
        <a class="btn btn-primary-custom text-white shadow-sm" href="/Venda/formVenda">
            <i class="bi bi-plus-lg me-2"></i> Nova Venda
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Buscar Venda</label>
                    <div class="input-group search-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cliente ou Nº do pedido...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select class="form-select">
                        <option selected>Todos os Status</option>
                        <option>Concluída</option>
                        <option>Pendente</option>
                        <option>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Período</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-light border w-100 fw-medium">
                        <i class="bi bi-filter me-1"></i> Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
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
                    <tr>
                        <td class="fw-bold text-primary">#1025</td>
                        <td class="fw-medium">Ana Beatriz Rocha</td>
                        <td class="text-muted">28/03/2026</td>
                        <td class="fw-bold">R$ 1.540,00</td>
                        <td><span class="badge-status status-concluida">Concluída</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border" title="Editar"><i class="bi bi-eye text-primary"></i></button>
                            <button class="btn btn-sm btn-light border me-1" title="Visualizar"><i
                                    class="bi bi-pencil "></i></button>
                            <button class="btn btn-sm btn-light border" title="Imprimir"><i
                                    class="bi bi-trash text-danger"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de 3 registros encontrados</small>
        </div>
    </div>
</div>