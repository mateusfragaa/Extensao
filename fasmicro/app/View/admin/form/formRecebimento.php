<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/recebimento/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Novo Recebimento</h4>
            <p class="text-muted small m-0">Lançamento de títulos de contas a receber.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <form class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Cliente / Origem</label>
                <select class="form-select">
                    <option selected>Selecione o cliente...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descrição do Título</label>
                <input type="text" class="form-control" placeholder="Ex: Parcela 01/03 - Venda #102">
            </div>

            <div class="col-md-3">
                <label class="form-label">Valor (R$)</label>
                <input type="number" step="0.01" class="form-control" placeholder="0,00">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Vencimento</label>
                <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Recebimento</label>
                <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option value="pendente">Pendente</option>
                    <option value="concluido">Recebido</option>
                    <option value="atrasado">Atrasado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Forma de Recebimento</label>
                <select class="form-select">
                    <option>Dinheiro</option>
                    <option>Pix</option>
                    <option>Cartão de Crédito</option>
                    <option>Boleto</option>
                    <option>Transferência</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="1" placeholder="Informações adicionais..."></textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light border px-4" href="#">Cancelar</a>
                <button type="submit" class="btn btn-primary-custom px-5">Salvar Recebimento</button>
            </div>
        </form>
    </div>
</div>