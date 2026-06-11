<?php
$usuario = $data['usuario'] ?? [];
$isEdit = isset($usuario['USU_ID']) && $usuario['USU_ID'] > 0;
?>

<div class="container py-5">

    <?php echo exibeAlerta(); ?>

    <div class="d-flex align-items-center mb-4">
        <a href="/usuario/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0"><?= $isEdit ? 'Editar Usuário' : 'Novo Usuário' ?></h4>
            <p class="text-muted small m-0">Configure as credenciais de acesso ao sistema.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <form action="/usuario/salvar" method="POST" class="row g-4">
            <?= csrfField() ?>
            <input type="hidden" name="USU_ID" value="<?= $data['usuario']['USU_ID'] ?? '0' ?>">
            <div class="col-md-6">
                <label class="form-label">Nome Completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="USU_NOME" class="form-control" placeholder="Ex: João Silva" value="<?= $data['usuario']['USU_NOME'] ?? '' ?>">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">E-mail Corporativo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="USU_EMAIL" class="form-control" placeholder="joao@empresa.com" value="<?= $data['usuario']['USU_EMAIL'] ?? '' ?>" >
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Nome de Usuário (Login)</label>
                <input type="text" name="USU_LOGIN" class="form-control" placeholder="joao.silva" value="<?= $data['usuario']['USU_LOGIN'] ?? '' ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nível de Acesso</label>
                <select class="form-select" name="USU_NIVEL">
                    <option selected disabled>Selecione uma permissão...</option>
                    <option value="admin" <?= ($data['usuario']['USU_NIVEL'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="vendedor" <?= ($data['usuario']['USU_NIVEL'] ?? '') == 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                    <!-- <option value="financeiro" <?=($data['usuario']['USU_NIVEL'] ?? '') == 'financeiro' ? 'selected' : ''?>>Financeiro</option> -->
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status da Conta</label>
                <select name="USU_STATUS" class="form-select">
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Senha de Acesso</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="USU_SENHA" class="form-control" placeholder="••••••••">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirmar Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                    <input type="password" name="CONFIRMAR_SENHA" class="form-control" placeholder="••••••••">
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
                <a type="button" class="btn btn-light border px-4" href="\Usuario\">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary-custom px-5">
                    <?= $isEdit ? 'Salvar Alterações' : 'Criar Conta' ?>
                </button>
            </div>
        </form>
    </div>
</div>