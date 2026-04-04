<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Tipos de Documento</h4>
            <p class="text-muted small m-0">Defina as formas de pagamento e tipos de cobrança.</p>
        </div>
        <a class="btn btn-primary-custom shadow-sm" href="/tipoDocumento/formTipoDocumento">
            <i class="bi bi-plus-lg me-2"></i> Novo Tipo
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Buscar por código ou nome...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">Pesquisar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Descrição</th>
                        <th>Tipo Fluxo</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-muted fw-bold">#01</td>
                        <td class="fw-bold">Dinheiro (Espécie)</td>
                        <td><span class="text-muted">Imediato</span></td>
                        <td><span class="badge bg-success badge-status">Ativo</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border" title="Editar"><i class="bi bi-eye text-primary"></i></button>
                            <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-light border text-danger"><i
                                    class="bi bi-trash"></i></button>
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