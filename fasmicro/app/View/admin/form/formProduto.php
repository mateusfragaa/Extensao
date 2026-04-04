<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/produto/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold m-0">Cadastrar Novo Produto</h4>
    </div>

    <div class="card card-custom p-4">
        <form class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" placeholder="Ex: Notebook Gamer">
            </div>
            <div class="col-md-3">
                <label class="form-label">SKU / Código</label>
                <input type="text" class="form-control" placeholder="PROD-001">
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoria</label>
                <select class="form-select">
                    <option selected>Selecione...</option>
                    <option>Eletrônicos</option>
                    <option>Acessórios</option>
                    <option>Serviços</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Preço de Custo (R$)</label>
                <input type="number" step="0.01" class="form-control" placeholder="0,00">
            </div>
            <div class="col-md-3">
                <label class="form-label">Preço de Venda (R$)</label>
                <input type="number" step="0.01" class="form-control" placeholder="0,00">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estoque Atual</label>
                <input type="number" class="form-control" placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estoque Mínimo</label>
                <input type="number" class="form-control" placeholder="0">
            </div>

            <div class="col-12">
                <label class="form-label">Descrição Curta</label>
                <textarea class="form-control" rows="3"
                    placeholder="Detalhes técnicos ou observações..."></textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light border px-4" href="#">Cancelar</a>
                <button type="submit" class="btn btn-primary-custom px-5">Salvar Produto</button>
            </div>
        </form>
    </div>
</div>