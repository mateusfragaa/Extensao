<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Novo Pedido de Venda</h4>
        <a class="btn btn-outline-secondary btn-sm" href="/venda/">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-custom p-4">
                <form class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">Cliente</label>
                        <select class="form-select bg-light border-0">
                            <option>Selecione o Cliente</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Data Venda</label>
                        <input type="date" class="form-control bg-light border-0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Status</label>
                        <select class="form-select bg-light border-0">
                            <option>Aberto</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Acréscimo</label>
                        <input type="number" class="form-control bg-light border-0" placeholder="0,00">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Desconto</label>
                        <input type="number" class="form-control bg-light border-0" placeholder="0,00">
                    </div>
                    <div class="col-12 mt-4 p-3 bg-light rounded text-end">
                        <div class="small text-muted">Subtotal: R$ 0,00</div>
                        <div class="fw-bold fs-4 text-primary">Total: R$ 0,00</div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-2 mt-3">Salvar Pedido</button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-custom p-4">
                <h6 class="fw-bold mb-3">Produtos do Pedido</h6>
                <div class="input-group mb-3">
                    <input type="text" class="form-control border-light" placeholder="Buscar Produto...">
                    <input type="number" class="form-control border-light" style="max-width: 80px;" value="1">
                    <button class="btn btn-dark">Incluir</button>
                </div>

                <div class="scroll-div">
                    <table class="table align-middle m-0">
                        <thead class="sticky-top bg-white border-bottom">
                            <tr class="small text-muted">
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>