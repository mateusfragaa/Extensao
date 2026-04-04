<div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">Catálogo de Produtos</h4>
            <div class="d-flex gap-2">
                <a class="btn btn-primary-custom shadow-sm" href="/produto/formProduto">
                    <i class="bi bi-plus-lg me-2"></i> Novo Produto
                </a>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted">Pesquisar Produto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Nome, código ou SKU...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Categoria</label>
                        <select class="form-select">
                            <option selected>Todas as Categorias</option>
                            <option>Eletrônicos</option>
                            <option>Acessórios</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Estoque</label>
                        <select class="form-select">
                            <option selected>Todos</option>
                            <option>Abaixo do Mínimo</option>
                            <option>Em estoque</option>
                        </select>
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
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Preço Venda</th>
                            <th>Estoque</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="img-produto d-flex align-items-center justify-content-center me-3 shadow-sm">
                                        <i class="bi bi-laptop"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Notebook Pro 15"</div>
                                        <small class="text-muted">SKU: NB-PRO-001</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-secondary">Eletrônicos</span></td>
                            <td class="fw-bold">R$ 5.490,00</td>
                            <td>12 unidades</td>
                            <td><span class="badge-estoque estoque-ok">Disponível</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light border" title="Visualizar"><i class="bi bi-eye text-primary"></i></button>
                                <button class="btn btn-sm btn-light border me-1"><i class="bi bi-pencil"></i></button>
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