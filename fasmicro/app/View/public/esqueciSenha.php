<div class="container-login">
    <div class="login-card">
        <div class="brand-logo d-flex align-items-center justify-content-center">
            <i class="bi bi-grid-1x2-fill me-2"></i> ERP System
        </div>

        <div class="text-center mb-4">
            <h5 class="fw-bold">Recuperar senha</h5>
            <p class="text-muted small">Informe seu e-mail cadastrado para receber o link de redefinição</p>
        </div>

        <?php echo exibeAlerta(); ?>
        <form action="/auth/esqueciSenha" method="POST">
            <?= csrfField() ?>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="USU_EMAIL" class="form-control border-start-0" id="email"
                        placeholder="nome@exemplo.com" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login">Enviar link de recuperação</button>
            <a href="/auth/formLogin" class="btn btn-login">Voltar ao login</a>
        </form>

        <div class="login-footer">
            &copy; 2026 ERP System. Todos os direitos reservados.
        </div>
    </div>
</div>