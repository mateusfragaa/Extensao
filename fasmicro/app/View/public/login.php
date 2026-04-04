<div class="login-card">
    <div class="brand-logo d-flex align-items-center justify-content-center">
        <i class="bi bi-grid-1x2-fill me-2"></i> ERP System
    </div>

    <div class="text-center mb-4">
        <h5 class="fw-bold">Bem-vindo de volta</h5>
        <p class="text-muted small">Insira suas credenciais para acessar</p>
    </div>

    <form>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail ou Usuário</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                <input type="email" class="form-control border-start-0" id="email" placeholder="nome@exemplo.com"
                    required>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label m-0">Senha</label>
                <a href="#" class="forgot-password">Esqueceu a senha?</a>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control border-start-0" id="password" placeholder="••••••••"
                    required>
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember">
            <label class="form-check-label small text-muted" for="remember">Lembrar de mim</label>
        </div>

        <!-- <button type="submit" class="btn btn-login">Entrar no Sistema</button> -->
            <a href="/homeSistema/" class="btn btn-login">Entrar no Sistema</a>
            <a href="/" class="btn btn-login">Voltar à página inicial</a>
    </form>

    <div class="login-footer">
        &copy; 2026 ERP System. Todos os direitos reservados.
    </div>
</div>
