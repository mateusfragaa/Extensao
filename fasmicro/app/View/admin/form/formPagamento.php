<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/pagamento/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Novo Pagamento</h4>
            <p class="text-muted small m-0">Lançamento de contas a pagar e despesas.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <form class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Fornecedor / Favorecido</label>
                <select class="form-select">
                    <option selected>Selecione o fornecedor...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descrição da Despesa</label>
                <input type="text" class="form-control" placeholder="Ex: Aluguel mensal, Compra de Insumos...">
            </div>

            <div class="col-md-3">
                <label class="form-label">Valor (R$)</label>
                <input type="number" step="0.01" class="form-control" placeholder="0,00">
            </div>
            <div class="col-md-3">
                <label class="form-label">Vencimento</label>
                <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Pagamento</label>
                <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option value="pendente">Pendente</option>
                    <option value="pago">Pago</option>
                    <option value="agendado">Agendado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Categoria de Custo</label>
                <select class="form-select">
                    <option>Operacional</option>
                    <option>Impostos</option>
                    <option>Pessoal (Folha)</option>
                    <option>Marketing</option>
                    <option>Infraestrutura</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Meio de Pagamento</label>
                <select class="form-select">
                    <option>Boleto</option>
                    <option>Pix</option>
                    <option>Cartão Corporativo</option>
                    <option>Transferência (TED/DOC)</option>
                    <option>Dinheiro</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Código de Barras / Linha Digitável</label>
                <input type="text" class="form-control" placeholder="Opcional">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a type="button" class="btn btn-light border px-4" href="#">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary-custom px-5">
                    Salvar Pagamento
                </button>
            </div>
        </form>
    </div>
</div>