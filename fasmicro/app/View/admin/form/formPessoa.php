<?php
$action_form = formDadosInput($data, 'pessoa');
$errors      = \Core\Library\Session::get('formErrors');
?>
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/pessoa/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Cadastro de Pessoa <?= formSubTitulo($action_form) ?></h4>
            <p class="text-muted small m-0">Pessoa Física (CPF) ou Pessoa Jurídica (CNPJ).</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <?php echo exibeAlerta(); ?>

        <form class="row g-4" action="/pessoa/<?= $action_form ?>" method="POST">

            <?php if ($action_form !== 'insert'): ?>
                <input type="hidden" name="PES_ID" value="<?= setValue('PES_ID') ?>">
            <?php endif; ?>

            <!-- ── Tipo de Pessoa ─────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">Tipo de Pessoa <span class="text-danger">*</span></label>
                <select name="TIPO_PESSOA" id="tipo_pessoa"
                        class="form-select <?= isset($errors['TIPO_PESSOA']) ? 'is-invalid' : '' ?>"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>
                    <option value="F" <?= (setValue('TIPO_PESSOA', 'F') === 'F') ? 'selected' : '' ?>>Pessoa Física</option>
                    <option value="J" <?= (setValue('TIPO_PESSOA') === 'J') ? 'selected' : '' ?>>Pessoa Jurídica</option>
                </select>
                <?php if (isset($errors['TIPO_PESSOA'])): ?>
                    <div class="invalid-feedback"><?= $errors['TIPO_PESSOA'] ?></div>
                <?php endif; ?>
            </div>

            <!-- ── Nome / Razão Social ───────────────────────────── -->
            <div class="col-md-9">
                <label class="form-label" id="label_nome_pfpj">
                    Nome Completo <span class="text-danger">*</span>
                </label>
                <input type="text" name="PES_NOME" id="nome_pfpj"
                    class="form-control <?= isset($errors['PES_NOME']) ? 'is-invalid' : '' ?>"
                    placeholder="Digite o nome completo"
                    value="<?= setValue('PES_NOME') ?>"
                    maxlength="45"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <?php if (isset($errors['PES_NOME'])): ?>
                    <div class="invalid-feedback"><?= $errors['PES_NOME'] ?></div>
                <?php endif; ?>
            </div>

            <!-- ── CPF / CNPJ ────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label" id="label_cpf_cnpj">
                    CPF <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="text" name="CPF_CNPJ" id="cpf_cnpj"
                        class="form-control <?= isset($errors['CPF_CNPJ']) ? 'is-invalid' : '' ?>"
                        placeholder="000.000.000-00"
                        value="<?= setValue('CPF_CNPJ') ?>"
                        maxlength="18"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>

                    <?php if ($action_form === 'insert' || $action_form === 'update'): ?>
                        <button class="btn btn-outline-secondary" type="button"
                                id="btn_validar_receita"
                                title="Verificar na Receita Federal">
                            <i class="bi bi-shield-check text-success"></i>
                            <span id="btn_verificar_label">Verificar</span>
                        </button>
                    <?php endif; ?>

                    <?php if (isset($errors['CPF_CNPJ'])): ?>
                        <div class="invalid-feedback"><?= $errors['CPF_CNPJ'] ?></div>
                    <?php endif; ?>
                </div>
                <small id="cpf_cnpj_fonte" class="text-muted"></small>
            </div>

            <!-- ── E-mail ─────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">
                    E-mail <span class="text-danger">*</span>
                </label>
                <input type="email" name="EMAIL"
                    class="form-control <?= isset($errors['EMAIL']) ? 'is-invalid' : '' ?>"
                    placeholder="exemplo@email.com"
                    value="<?= setValue('EMAIL') ?>"
                    maxlength="50"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <?php if (isset($errors['EMAIL'])): ?>
                    <div class="invalid-feedback"><?= $errors['EMAIL'] ?></div>
                <?php endif; ?>
            </div>

            <!-- ── Telefone ───────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">Telefone / WhatsApp</label>
                <input type="text" name="TELEFONE" id="input_telefone"
                    class="form-control"
                    placeholder="(00) 00000-0000"
                    value="<?= setValue('TELEFONE') ?>"
                    maxlength="15"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── CEP ───────────────────────────────────────────── -->
            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" name="CEP" id="cep"
                    class="form-control" placeholder="00000-000"
                    value="<?= setValue('CEP') ?>"
                    maxlength="9"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <small id="cep_status" class="text-muted small"></small>
            </div>

            <!-- ── Endereço ───────────────────────────────────────── -->
            <div class="col-md-7">
                <label class="form-label">Endereço</label>
                <input type="text" name="ENDERECO" id="endereco"
                    class="form-control" placeholder="Rua, Av., Logradouro..."
                    value="<?= setValue('ENDERECO') ?>"
                    maxlength="50"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── Número ─────────────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">Número</label>
                <div class="input-group">
                    <input type="text" name="NUMERO" id="numero_casa"
                        class="form-control" placeholder="123"
                        value="<?= setValue('NUMERO') ?>"
                        maxlength="6"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>
                </div>
                <?php if ($action_form !== 'view'): ?>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" id="sem_numero"
                            <?= (setValue('NUMERO') === 'S/N') ? 'checked' : '' ?>>
                        <label class="form-check-label small" for="sem_numero">Sem número</label>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ── Bairro ─────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">Bairro</label>
                <input type="text" name="BAIRRO" id="bairro"
                    class="form-control"
                    value="<?= setValue('BAIRRO') ?>"
                    maxlength="40"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── Cidade ─────────────────────────────────────────── -->
            <div class="col-md-5">
                <label class="form-label">Cidade</label>
                <input type="text" name="CIDADE" id="cidade"
                    class="form-control"
                    value="<?= setValue('CIDADE') ?>"
                    maxlength="40"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── UF ─────────────────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">UF</label>
                <select name="UF" id="uf" class="form-select"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>
                    <option value="">Selecione...</option>
                    <?php
                    $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS',
                            'MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC',
                            'SP','SE','TO'];
                    foreach ($ufs as $sigla): ?>
                        <option value="<?= $sigla ?>"
                            <?= (setValue('UF') === $sigla) ? 'selected' : '' ?>>
                            <?= $sigla ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ── Botões ─────────────────────────────────────────── -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light border px-4" href="/pessoa/">Cancelar</a>
                <?php if ($action_form !== 'view'): ?>
                    <button type="submit" class="btn btn-primary px-5">
                        <?= $action_form === 'delete' ? 'Confirmar Exclusão' : 'Salvar' ?>
                    </button>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>

<script>
(function () {
    'use strict';

    // ── Elementos ──────────────────────────────────────────────────
    const selectTipo      = document.getElementById('tipo_pessoa');
    const inputCPFCNPJ    = document.getElementById('cpf_cnpj');
    const labelCPFCNPJ    = document.getElementById('label_cpf_cnpj');
    const labelNome       = document.getElementById('label_nome_pfpj');
    const inputNome       = document.getElementById('nome_pfpj');
    const inputTelefone   = document.getElementById('input_telefone');
    const inputCEP        = document.getElementById('cep');
    const cepStatus       = document.getElementById('cep_status');
    const inputNumero     = document.getElementById('numero_casa');
    const checkSemNumero  = document.getElementById('sem_numero');
    const btnVerificar    = document.getElementById('btn_validar_receita');
    const fonteSpan       = document.getElementById('cpf_cnpj_fonte');

    // ── Toast ──────────────────────────────────────────────────────
    function mostrarToast(mensagem, tipo) {
        tipo = tipo || 'success';
        const cores = {
            success: { bg: '#198754', ico: 'bi-check-circle-fill' },
            danger:  { bg: '#dc3545', ico: 'bi-exclamation-triangle-fill' },
            warning: { bg: '#fd7e14', ico: 'bi-info-circle-fill' },
            info:    { bg: '#0dcaf0', ico: 'bi-info-circle' }
        };
        const c = cores[tipo] || cores.info;
        let container = document.getElementById('_toast_box');
        if (!container) {
            container = document.createElement('div');
            container.id = '_toast_box';
            container.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;';
            document.body.appendChild(container);
        }
        const t = document.createElement('div');
        t.style.cssText = `background:${c.bg};color:#fff;padding:.85rem 1.2rem;border-radius:.5rem;`
            + 'box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;gap:.65rem;'
            + 'min-width:280px;max-width:440px;font-size:.92rem;opacity:0;transition:opacity .3s;';
        t.innerHTML = `<i class="bi ${c.ico}" style="font-size:1.2rem;flex-shrink:0"></i><span>${mensagem}</span>`;
        container.appendChild(t);
        requestAnimationFrame(() => { t.style.opacity = '1'; });
        setTimeout(() => {
            t.style.opacity = '0';
            setTimeout(() => t.remove(), 350);
        }, 6000);
    }

    // ── Tipo de pessoa → atualiza labels e máscaras ────────────────
    function atualizarTipo(tipo) {
        if (tipo === 'J') {
            if (labelNome)    labelNome.innerHTML    = 'Razão Social <span class="text-danger">*</span>';
            if (inputNome)    inputNome.placeholder  = 'Digite a razão social';
            if (labelCPFCNPJ) labelCPFCNPJ.innerHTML = 'CNPJ <span class="text-danger">*</span>';
            if (inputCPFCNPJ) { inputCPFCNPJ.placeholder = '00.000.000/0000-00'; inputCPFCNPJ.maxLength = 18; }
        } else {
            if (labelNome)    labelNome.innerHTML    = 'Nome Completo <span class="text-danger">*</span>';
            if (inputNome)    inputNome.placeholder  = 'Digite o nome completo';
            if (labelCPFCNPJ) labelCPFCNPJ.innerHTML = 'CPF <span class="text-danger">*</span>';
            if (inputCPFCNPJ) { inputCPFCNPJ.placeholder = '000.000.000-00'; inputCPFCNPJ.maxLength = 14; }
        }
    }

    if (selectTipo) {
        atualizarTipo(selectTipo.value);
        selectTipo.addEventListener('change', function () {
            atualizarTipo(this.value);
            if (inputCPFCNPJ) { inputCPFCNPJ.value = ''; inputCPFCNPJ.className = 'form-control'; }
        });
    }

    // ── Máscara CPF / CNPJ ─────────────────────────────────────────
    function aplicarMascara(v, tipo) {
        v = v.replace(/\D/g, '');
        if (tipo === 'F') {
            v = v.substring(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            v = v.substring(0, 14);
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
        }
        return v;
    }

    if (inputCPFCNPJ && selectTipo) {
        inputCPFCNPJ.addEventListener('input', function (e) {
            e.target.value = aplicarMascara(e.target.value, selectTipo.value);
        });
    }

    // ── Máscara Telefone ───────────────────────────────────────────
    if (inputTelefone) {
        inputTelefone.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 11);
            v = v.replace(/^(\d{2})(\d)/, '($1) $2');
            v = v.replace(/(\d)(\ d{4})$/, '$1-$2');
            e.target.value = v;
        });
    }

    // ── Sem número ────────────────────────────────────────────────
    if (inputNumero && checkSemNumero) {
        if (checkSemNumero.checked) inputNumero.readOnly = true;
        checkSemNumero.addEventListener('change', function () {
            if (this.checked) { inputNumero.value = 'S/N'; inputNumero.readOnly = true; }
            else              { if (inputNumero.value === 'S/N') inputNumero.value = ''; inputNumero.readOnly = false; }
        });
    }

    // ── CEP → ViaCEP ──────────────────────────────────────────────
    if (inputCEP) {
        inputCEP.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 8);
            e.target.value = v.length > 5 ? v.substring(0, 5) + '-' + v.substring(5) : v;
            if (v.length === 8) buscarCEP(v);
        });
    }

    function buscarCEP(cep) {
        if (cepStatus) { cepStatus.textContent = 'Buscando...'; cepStatus.className = 'text-primary small'; }
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(r => r.json())
            .then(d => {
                if (d.erro) {
                    if (cepStatus) { cepStatus.textContent = 'CEP não encontrado.'; cepStatus.className = 'text-danger small'; }
                    return;
                }
                preencherEndereco(d.logradouro, d.bairro, d.localidade, d.uf);
                if (cepStatus) cepStatus.textContent = '';
            })
            .catch(() => { if (cepStatus) { cepStatus.textContent = 'Erro ao buscar CEP.'; cepStatus.className = 'text-danger small'; } });
    }

    function preencherEndereco(logradouro, bairro, cidade, uf) {
        const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
        setEl('endereco', logradouro);
        setEl('bairro', bairro);
        setEl('cidade', cidade);
        // UF — seleciona no <select>
        const selUF = document.getElementById('uf');
        if (selUF) selUF.value = uf || '';
    }

    // ── Botão Verificar (CNPJ → API | CPF → popup Receita) ────────
    if (btnVerificar) {
        btnVerificar.addEventListener('click', function () {
            const tipo = selectTipo ? selectTipo.value : 'F';

            if (tipo === 'J') {
                // ── CNPJ ──────────────────────────────────────────
                const cnpjRaw = inputCPFCNPJ ? inputCPFCNPJ.value.replace(/\D/g, '') : '';
                if (cnpjRaw.length !== 14) {
                    mostrarToast('Preencha o CNPJ completo (14 dígitos) antes de verificar.', 'warning');
                    if (inputCPFCNPJ) inputCPFCNPJ.focus();
                    return;
                }
                setBtnLoading(true);
                fetch('/pessoa/consultarCNPJAjax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({ cnpj: cnpjRaw })
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.sucesso) {
                        inputCPFCNPJ.classList.remove('is-invalid');
                        inputCPFCNPJ.classList.add('is-valid');
                        if (inputNome && resp.nome)    inputNome.value = resp.nome;
                        // Preenche endereço se a API retornou
                        if (resp.cep) {
                            const cepEl = document.getElementById('cep');
                            if (cepEl) {
                                let c = resp.cep.replace(/\D/g,'');
                                cepEl.value = c.length > 5 ? c.substring(0,5)+'-'+c.substring(5) : c;
                            }
                            preencherEndereco(resp.logradouro, resp.bairro, resp.municipio, resp.uf);
                        }
                        if (fonteSpan) fonteSpan.textContent = 'Fonte: ' + (resp.fonte || '');
                        mostrarToast('CNPJ válido! Empresa: <b>' + resp.nome + '</b> — ' + resp.situacao, 'success');
                    } else {
                        if (inputCPFCNPJ) { inputCPFCNPJ.classList.remove('is-valid'); inputCPFCNPJ.classList.add('is-invalid'); }
                        mostrarToast(resp.mensagem, 'danger');
                    }
                })
                .catch(() => mostrarToast('Erro ao consultar o CNPJ. Verifique sua conexão.', 'danger'))
                .finally(() => setBtnLoading(false));

            } else {
                // ── CPF — popup Receita Federal ────────────────────
                const urlReceita = 'https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaPublica.asp';
                const popup = window.open(urlReceita, 'ConsultaReceita', 'width=800,height=600,scrollbars=yes');
                if (!popup) { mostrarToast('Bloqueador de pop-ups ativado. Permita para este site.', 'warning'); return; }

                setBtnLoading(true);
                const timer = setInterval(() => {
                    try {
                        if (popup.closed) { clearInterval(timer); setBtnLoading(false); return; }
                        if (popup.document.querySelector('.clConteudoDados')) {
                            clearInterval(timer);
                            const html = popup.document.documentElement.innerHTML;
                            popup.close();
                            fetch('/pessoa/validarReceitaAjax', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                                body: new URLSearchParams({ html_receita: html })
                            })
                            .then(r => r.json())
                            .then(d => {
                                if (d.sucesso) {
                                    if (inputNome) { inputNome.value = d.nome; inputNome.classList.add('is-valid'); }
                                    mostrarToast('CPF validado! Nome: <b>' + d.nome + '</b>', 'success');
                                } else {
                                    mostrarToast('Erro na validação: ' + d.mensagem, 'danger');
                                }
                            })
                            .catch(() => mostrarToast('Erro interno ao processar os dados.', 'danger'))
                            .finally(() => setBtnLoading(false));
                        }
                    } catch (e) { /* Same-Origin durante navegação — ignorar */ }
                }, 1500);
            }
        });
    }

    function setBtnLoading(loading) {
        if (!btnVerificar) return;
        const label = document.getElementById('btn_verificar_label');
        if (loading) {
            btnVerificar.disabled = true;
            if (label) label.textContent = 'Consultando...';
            btnVerificar.querySelector('i')?.classList.replace('bi-shield-check', 'bi-hourglass-split');
        } else {
            btnVerificar.disabled = false;
            if (label) label.textContent = 'Verificar';
            btnVerificar.querySelector('i')?.classList.replace('bi-hourglass-split', 'bi-shield-check');
        }
    }

})();
</script>

<?php \Core\Library\Session::destroy('formErrors'); ?>
