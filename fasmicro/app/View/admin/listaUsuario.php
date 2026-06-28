<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Usuários do Sistema</h4>
            <p class="text-muted small m-0">Mais segurança e organização para o sistema</p>
        </div>
        <a class="btn btn-primary-custom shadow-sm" href="/usuario/formUsuario">
            <i class="bi bi-person-plus-fill me-2"></i> Novo Usuário
        </a>

    </div>
    <?php echo exibeAlerta(); ?>
    <div class="card card-custom mb-4">

        <div class="card-body p-3">
            <form class="row g-2" id="form_filtro_usuario">
                <div class=" col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Buscar por nome ou login..." id="filtro_nome">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom m-0" id="tabela_usuario">
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
                    <?php if (empty($data['usuarios'])): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Nenhum usuário cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['usuarios'] as $user): ?>
                            <tr
                                data-nome="<?= strtolower($user['USU_NOME']) ?>"
                                data-login="<?= strtolower($user['USU_LOGIN']) ?>">
                                <td class="text-muted fw-bold"><?= $user['USU_ID'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder me-3"><?= strtoupper(substr($user['USU_NOME'], 0, 2)) ?></div>
                                        <span class="fw-bold"><?= $user['USU_NOME'] ?></span>
                                    </div>
                                </td>
                                <td><?= $user['USU_LOGIN'] ?></td>
                                <td><span class="badge-access bg-admin"><?= $user['USU_NIVEL'] ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light border" title="Editar"><i
                                            class="bi bi-eye text-primary"></i></button>
                                    <a href="/usuario/formUsuario/<?= $user['USU_ID'] ?> ?>">
                                        <button class="btn btn-sm btn-light border" title="Editar"><i
                                                class="bi bi-pencil"></i></button>
                                    </a>
                                    <a href="/usuario/excluir/<?= $user['USU_ID'] ?>"
                                        class="btn btn-sm btn-light border text-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                        <i class="bi bi-trash"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de <?= $data['total'] ?> usuários encontrados</small>
        </div>
    </div>
</div>
<?= usuarioFiltro() ?>