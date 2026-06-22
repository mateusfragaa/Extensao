<div class="container-login">
    <div class="login-card">
        <div class="brand-logo d-flex align-items-center justify-content-center">
            <i class="bi bi-grid-1x2-fill me-2"></i> ERP System
        </div>

        <div class="text-center mb-4">
            <h5 class="fw-bold">Definir nova senha</h5>
            <p class="text-muted small">Escolha uma nova senha de acesso</p>
        </div>

        <?php echo exibeAlerta(); ?>
        <form action="/auth/resetSenha" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="token" value="<?= $token ?? '' ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Nova senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="USU_SENHA" class="form-control border-start-0" id="password"
                        placeholder="••••••••" required>
                </div>
                <small class="text-muted">Mínimo de 8 caracteres, com maiúscula, minúscula, número e caractere especial.</small>
            </div>

            <div class="mb-3">
                <label for="confirmar" class="form-label">Confirmar nova senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
                    <input type="password" name="CONFIRMAR_SENHA" class="form-control border-start-0" id="confirmar"
                        placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login">Redefinir senha</button>
        </form>

        <div class="login-footer">
            &copy; 2026 ERP System. Todos os direitos reservados.
        </div>
    </div>
</div>