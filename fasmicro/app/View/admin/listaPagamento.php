<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Contas a Pagar</h4>
        <a class="btn btn-primary-custom shadow-sm" href="/pagamento/formPagamento">
            <i class="bi bi-plus-lg me-2"></i> Novo Pagamento
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom stat-card p-3">
                <small class="text-muted fw-bold">TOTAL A PAGAR (MÊS)</small>
                <h3 class="fw-bold m-0">R$ 15.200,00</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card vence-hoje p-3">
                <small class="text-muted fw-bold">VENCE HOJE</small>
                <h3 class="fw-bold m-0 text-warning">R$ 2.100,00</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card atrasado p-3">
                <small class="text-muted fw-bold">DÉBITOS EM ATRASO</small>
                <h3 class="fw-bold m-0 text-danger">R$ 450,00</h3>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <form class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Fornecedor ou Categoria</label>
                    <input type="text" class="form-control" placeholder="Ex: Aluguel, Fornecedor X...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Tipo de Gasto</label>
                    <select class="form-select">
                        <option selected>Todos</option>
                        <option>Fixos</option>
                        <option>Variáveis</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select class="form-select">
                        <option selected>Todos os Status</option>
                        <option>Pendente</option>
                        <option>Vencido</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom overflow-hidden card-ajuste-footer">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th>Vencimento</th>
                        <th>Fornecedor / Descrição</th>
                        <th>Categoria</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>28/03/2026</td>
                        <td class="fw-bold">Imobiliária Central<br><small class="text-muted fw-normal">Aluguel Sede
                                - Março</small></td>
                        <td>Fixo</td>
                        <td class="fw-bold text-primary">R$ 3.500,00</td>
                        <td><span class="badge-pgto status-urgente">Vence Hoje</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border" title="Editar"><i class="bi bi-eye text-primary"></i></button>
                            <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top ">
            <small class="text-muted">Total de 3 registros encontrados</small>
        </div>
    </div>
</div>