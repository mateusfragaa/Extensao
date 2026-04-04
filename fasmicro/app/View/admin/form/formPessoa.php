<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/pessoa/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Cadastrar Cliente</h4>
            <p class="text-muted small m-0">Adicione um novo cliente ou contato ao sistema.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <form class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nome Completo / Razão Social</label>
                <input type="text" class="form-control" placeholder="Digite o nome completo">
            </div>
            <div class="col-md-4">
                <label class="form-label">CPF / CNPJ</label>
                <input type="text" class="form-control" placeholder="000.000.000-00">
            </div>

            <div class="col-md-4">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" placeholder="exemplo@email.com">
            </div>
            <div class="col-md-4">
                <label class="form-label">Telefone / WhatsApp</label>
                <input type="text" class="form-control" placeholder="(00) 00000-0000">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipo de Pessoa</label>
                <select class="form-select">
                    <option value="PF">Pessoa Física</option>
                    <option value="PJ">Pessoa Jurídica</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" class="form-control" placeholder="00000-000">
            </div>
            <div class="col-md-8">
                <label class="form-label">Endereço</label>
                <input type="text" class="form-control" placeholder="Rua, Av, Logradouro...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Número</label>
                <input type="text" class="form-control" placeholder="123">
            </div>

            <div class="col-md-5">
                <label class="form-label">Cidade</label>
                <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bairro</label>
                <input type="text" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">UF</label>
                <select class="form-select">
                    <option>Selecione...</option>
                    <option>SP</option>
                    <option>RJ</option>
                    <option>MG</option>
                </select>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a type="button" class="btn btn-light border px-4" href="#">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary-custom px-5">
                    Salvar Cliente
                </button>
            </div>
        </form>
    </div>
</div>