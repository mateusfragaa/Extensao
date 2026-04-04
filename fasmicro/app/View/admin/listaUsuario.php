<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Usuários do Sistema</h4>
            <p class="text-muted small m-0">Gerencie quem tem acesso e quais as permissões de cada um.</p>
        </div>
        <a class="btn btn-primary-custom shadow-sm" href="/usuario/formUsuario">
            <i class="bi bi-person-plus-fill me-2"></i> Novo Usuário
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Buscar por nome ou login...">
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
                        <th style="width: 80px;">Cód.</th>
                        <th>Nome Completo</th>
                        <th>Login / Usuário</th>
                        <th>Nível de Acesso</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-muted fw-bold">#001</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-placeholder me-3">AD</div>
                                <span class="fw-bold">Administrador Geral</span>
                            </div>
                        </td>
                        <td>admin</td>
                        <td><span class="badge-access bg-admin">Administrador</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border" title="Editar"><i
                                    class="bi bi-eye text-primary"></i></button>
                            <button class="btn btn-sm btn-light border" title="Editar"><i
                                    class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-light border text-danger" title="Excluir"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de 3 usuários encontrados</small>
        </div>
    </div>
</div>