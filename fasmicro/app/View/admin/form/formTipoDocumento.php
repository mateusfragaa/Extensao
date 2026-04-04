<div class="container">
    <div class="d-flex align-items-center mb-4">
        <a href="/tipoDocumento/" class="btn btn-light border me-3"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold m-0">Cadastrar Tipo de Documento</h4>
            <p class="text-muted small m-0">Configure uma nova forma de pagamento ou cobrança.</p>
        </div>
    </div>

    <div class="card card-custom">
        <form class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Nome do Documento / Forma de Pagamento</label>
                <input type="text" class="form-control" placeholder="Ex: Cartão de Crédito Visa">
            </div>

            <div class="col-md-4">
                <label class="form-label">Sigla/Abreviatura</label>
                <input type="text" class="form-control" placeholder="Ex: CC">
            </div>

            <div class="col-md-6">
                <label class="form-label">Tipo de Fluxo</label>
                <select class="form-select">
                    <option selected>Selecione...</option>
                    <option>Dinheiro (Imediato)</option>
                    <option>Bancário (Compensação)</option>
                    <option>Crédito (Prazo)</option>
                    <option>Débito (Imediato)</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option value="ativo">Ativo (Disponível no PDV/Vendas)</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>

            <div class="col-12 mt-4 border-top pt-3">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="permiteParcelas">
                    <label class="form-check-label text-muted" for="permiteParcelas">Permitir parcelamento com este
                        documento</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="geraTaxa">
                    <label class="form-check-label text-muted" for="geraTaxa">Calcular taxa de operadora
                        automaticamente</label>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a type="button" class="btn btn-light border" href="#">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary-custom">
                    Salvar Tipo de Documento
                </button>
            </div>
        </form>
    </div>
</div>
