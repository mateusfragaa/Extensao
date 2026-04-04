<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/usuario/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Novo Usuário</h4>
            <p class="text-muted small m-0">Configure as credenciais de acesso ao sistema.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <form class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Nome Completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" placeholder="Ex: João Silva">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">E-mail Corporativo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" placeholder="joao@empresa.com">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Nome de Usuário (Login)</label>
                <input type="text" class="form-control" placeholder="joao.silva">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nível de Acesso</label>
                <select class="form-select">
                    <option selected disabled>Selecione uma permissão...</option>
                    <option value="admin">Administrador (Acesso Total)</option>
                    <option value="vendedor">Vendedor (Acesso Restrito)</option>
                    <option value="financeiro">Financeiro</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status da Conta</label>
                <select class="form-select">
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Senha de Acesso</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" placeholder="••••••••">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirmar Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                    <input type="password" class="form-control" placeholder="••••••••">
                </div>
            </div>

            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="resetPass">
                    <label class="form-check-label text-muted small" for="resetPass">
                        Exigir alteração de senha no primeiro login
                    </label>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a type="button" class="btn btn-light border px-4" href="#">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary-custom px-5">
                    Criar Conta
                </button>
            </div>
        </form>
    </div>
</div>