<?php
use Core\Library\Csrf;

$action_form = formDadosInput($data, 'pessoa');
$errors      = \Core\Library\Session::get('formErrors');
?>
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/pessoa/" class="btn btn-light border me-3"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold m-0">Cadastro de Pessoa <?= formSubTitulo($action_form) ?></h4>
            <p class="text-muted small m-0">Pessoa Física (CPF) ou Pessoa Jurídica (CNPJ).</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <?php echo exibeAlerta(); ?>

        <form class="row g-4" action="/pessoa/<?= $action_form ?>" method="POST">

            <?= Csrf::getHiddenField() ?>

            <?php if ($action_form !== 'insert'): ?>
                <input type="hidden" name="PES_ID" value="<?= setValue('PES_ID') ?>">
            <?php endif; ?>

            <!-- ── Tipo de Pessoa ──────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">Tipo de Pessoa <span class="text-danger">*</span></label>
                <select name="TIPO_PESSOA" id="tipo_pessoa"
                        class="form-select <?= isset($errors['TIPO_PESSOA']) ? 'is-invalid' : '' ?>"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>
                    <option value="F" <?= (setValue('TIPO_PESSOA', 'F') === 'F') ? 'selected' : '' ?>>Pessoa Física</option>
                    <option value="J" <?= (setValue('TIPO_PESSOA') === 'J')       ? 'selected' : '' ?>>Pessoa Jurídica</option>
                </select>
                <?php if (isset($errors['TIPO_PESSOA'])): ?><div class="invalid-feedback"><?= $errors['TIPO_PESSOA'] ?></div><?php endif; ?>
            </div>

            <!-- ── Nome / Razão Social ─────────────────────────────── -->
            <div class="col-md-9">
                <label class="form-label" id="label_nome_pfpj">Nome Completo <span class="text-danger">*</span></label>
                <input type="text" name="PES_NOME" id="nome_pfpj"
                    class="form-control <?= isset($errors['PES_NOME']) ? 'is-invalid' : '' ?>"
                    placeholder="Digite o nome completo"
                    value="<?= htmlspecialchars(setValue('PES_NOME')) ?>"
                    maxlength="100"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <?php if (isset($errors['PES_NOME'])): ?><div class="invalid-feedback"><?= $errors['PES_NOME'] ?></div><?php endif; ?>
            </div> 

            <!-- ── CPF / CNPJ ─────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label" id="label_cpf_cnpj">CPF <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="CPF_CNPJ" id="cpf_cnpj"
                        class="form-control <?= isset($errors['CPF_CNPJ']) ? 'is-invalid' : '' ?>"
                        placeholder="000.000.000-00"
                        value="<?php
                            // Ao exibir, reaplicar máscara nos dados vindos do banco (só dígitos)
                            $rawDoc = preg_replace('/\D/', '', setValue('CPF_CNPJ'));
                            if (strlen($rawDoc) === 11) {
                                echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $rawDoc);
                            } elseif (strlen($rawDoc) === 14) {
                                echo preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $rawDoc);
                            } else {
                                echo htmlspecialchars(setValue('CPF_CNPJ'));
                            }
                        ?>"
                        maxlength="18"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>

                    <?php if ($action_form === 'insert' || $action_form === 'update'): ?>
                        <button class="btn btn-outline-secondary" type="button"
                                id="btn_validar_receita" title="Verificar na Receita Federal">
                            <i class="bi bi-shield-check text-success"></i>
                            <span id="btn_verificar_label">Verificar</span>
                        </button>
                    <?php endif; ?>

                    <?php if (isset($errors['CPF_CNPJ'])): ?>
                        <div class="invalid-feedback"><?= $errors['CPF_CNPJ'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Seletor de dataset (apenas para CNPJ) -->
                <div id="bloco_dataset" class="mt-1" style="display:none;">
                    <select id="sel_dataset" class="form-select form-select-sm">
                        <option value="receita">Receita Federal</option>
                        <option value="cno">CNO — Cadastro Nacional de Obras</option>
                        <option value="rntrc">RNTRC — Transportadores</option>
                    </select>
                    <small class="text-muted">Selecione a fonte de consulta. Se não encontrar, tente outra.</small>
                </div>
                <small id="cpf_cnpj_fonte" class="text-muted"></small>
            </div>

            <!-- ── E-mail ──────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">E-mail <span class="text-danger">*</span></label>
                <input type="email" name="EMAIL"
                    class="form-control <?= isset($errors['EMAIL']) ? 'is-invalid' : '' ?>"
                    placeholder="exemplo@email.com"
                    value="<?= htmlspecialchars(setValue('EMAIL')) ?>"
                    maxlength="50"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <?php if (isset($errors['EMAIL'])): ?><div class="invalid-feedback"><?= $errors['EMAIL'] ?></div><?php endif; ?>
            </div>

            <!-- ── Telefone ────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">Telefone / WhatsApp</label>
                <input type="text" name="TELEFONE" id="input_telefone"
                    class="form-control"
                    placeholder="(00) 00000-0000"
                    value="<?php
                        // Reaplicar máscara no telefone vindo do banco
                        $tel = preg_replace('/\D/', '', setValue('TELEFONE'));
                        if (strlen($tel) === 11) {
                            echo '(' . substr($tel,0,2) . ') ' . substr($tel,2,5) . '-' . substr($tel,7,4);
                        } elseif (strlen($tel) === 10) {
                            echo '(' . substr($tel,0,2) . ') ' . substr($tel,2,4) . '-' . substr($tel,6,4);
                        } else {
                            echo htmlspecialchars(setValue('TELEFONE'));
                        }
                    ?>"
                    maxlength="15"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── CEP ────────────────────────────────────────────── -->
            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" name="CEP" id="cep"
                    class="form-control" placeholder="00000-000"
                    value="<?php
                        $cep = preg_replace('/\D/', '', setValue('CEP'));
                        echo strlen($cep) === 8
                            ? substr($cep,0,5) . '-' . substr($cep,5,3)
                            : htmlspecialchars(setValue('CEP'));
                    ?>"
                    maxlength="9"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <small id="cep_status" class="text-muted small"></small>
            </div>

            <!-- ── Endereço ───────────────────────────────────────── -->
            <div class="col-md-7">
                <label class="form-label">Endereço</label>
                <input type="text" name="ENDERECO" id="endereco"
                    class="form-control" placeholder="Rua, Av., Logradouro..."
                    value="<?= htmlspecialchars(setValue('ENDERECO')) ?>"
                    maxlength="50"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── Número ─────────────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">Número</label>
                <input type="text" name="NUMERO" id="numero_casa"
                    class="form-control" placeholder="123"
                    value="<?= htmlspecialchars(setValue('NUMERO')) ?>"
                    maxlength="6"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
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
                    value="<?= htmlspecialchars(setValue('BAIRRO')) ?>"
                    maxlength="40"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── Cidade ─────────────────────────────────────────── -->
            <div class="col-md-5">
                <label class="form-label">Cidade</label>
                <input type="text" name="CIDADE" id="cidade"
                    class="form-control"
                    value="<?= htmlspecialchars(setValue('CIDADE')) ?>"
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
                        <option value="<?= $sigla ?>" <?= (setValue('UF') === $sigla) ? 'selected' : '' ?>>
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

    const selectTipo    = document.getElementById('tipo_pessoa');
    const inputDoc      = document.getElementById('cpf_cnpj');
    const labelDoc      = document.getElementById('label_cpf_cnpj');
    const labelNome     = document.getElementById('label_nome_pfpj');
    const inputNome     = document.getElementById('nome_pfpj');
    const inputTel      = document.getElementById('input_telefone');
    const inputCEP      = document.getElementById('cep');
    const cepStatus     = document.getElementById('cep_status');
    const inputNumero   = document.getElementById('numero_casa');
    const checkSemNum   = document.getElementById('sem_numero');
    const btnVerificar  = document.getElementById('btn_validar_receita');
    const fonteSpan     = document.getElementById('cpf_cnpj_fonte');
    const blocoDataset  = document.getElementById('bloco_dataset');
    const selDataset    = document.getElementById('sel_dataset');

    // ── Toast ──────────────────────────────────────────────────────
    function toast(msg, tipo) {
        tipo = tipo || 'success';
        const c = { success:'#198754', danger:'#dc3545', warning:'#fd7e14', info:'#0dcaf0' };
        const i = { success:'bi-check-circle-fill', danger:'bi-exclamation-triangle-fill', warning:'bi-info-circle-fill', info:'bi-info-circle' };
        let box = document.getElementById('_toast_box');
        if (!box) {
            box = document.createElement('div');
            box.id = '_toast_box';
            box.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;max-width:440px;';
            document.body.appendChild(box);
        }
        const t = document.createElement('div');
        t.style.cssText = `background:${c[tipo]||c.info};color:#fff;padding:.85rem 1.2rem;border-radius:.5rem;`
            + 'box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;gap:.65rem;font-size:.92rem;opacity:0;transition:opacity .3s;';
        t.innerHTML = `<i class="bi ${i[tipo]||i.info}" style="font-size:1.2rem;flex-shrink:0"></i><span>${msg}</span>`;
        box.appendChild(t);
        requestAnimationFrame(() => t.style.opacity = '1');
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 350); }, 6000);
    }

    // ── Tipo de pessoa → atualiza labels ──────────────────────────
    function atualizarTipo(tipo) {
        if (tipo === 'J') {
            if (labelNome) labelNome.innerHTML = 'Razão Social <span class="text-danger">*</span>';
            if (inputNome) inputNome.placeholder = 'Digite a razão social';
            if (labelDoc)  labelDoc.innerHTML  = 'CNPJ <span class="text-danger">*</span>';
            if (inputDoc)  { inputDoc.placeholder = '00.000.000/0000-00'; inputDoc.maxLength = 18; }
            if (blocoDataset) blocoDataset.style.display = 'block';
        } else {
            if (labelNome) labelNome.innerHTML = 'Nome Completo <span class="text-danger">*</span>';
            if (inputNome) inputNome.placeholder = 'Digite o nome completo';
            if (labelDoc)  labelDoc.innerHTML  = 'CPF <span class="text-danger">*</span>';
            if (inputDoc)  { inputDoc.placeholder = '000.000.000-00'; inputDoc.maxLength = 14; }
            if (blocoDataset) blocoDataset.style.display = 'none';
        }
    }

    if (selectTipo) {
        atualizarTipo(selectTipo.value);
        selectTipo.addEventListener('change', function () {
            atualizarTipo(this.value);
            if (inputDoc) {
                inputDoc.value = '';
                inputDoc.className = 'form-control';
                // Remove feedback de validação (borda + texto) ao trocar tipo
                const inputGroup = inputDoc.closest('.input-group');
                const colPai     = inputGroup ? inputGroup.parentNode : inputDoc.parentNode;
                const fb = colPai.querySelector('.doc-feedback');
                if (fb) fb.remove();
            }
        });
    }

    // ── Máscara CPF / CNPJ ────────────────────────────────────────
    function mascararDoc(v, tipo) {
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

    if (inputDoc && selectTipo) {
        inputDoc.addEventListener('input', function (e) {
            e.target.value = mascararDoc(e.target.value, selectTipo.value);
            // Valida em tempo real assim que o tamanho estiver completo
            const digits = e.target.value.replace(/\D/g, '');
            const tipo   = selectTipo.value;
            const completo = (tipo === 'F' && digits.length === 11)
                          || (tipo === 'J' && digits.length === 14);
            if (completo) validarDocumento(digits, tipo);
            // Se apagou dígitos, limpa feedback
            if (!completo) {
                inputDoc.classList.remove('is-valid', 'is-invalid');
                const fb = inputDoc.parentNode.querySelector('.doc-feedback');
                if (fb) fb.remove();
            }
        });

        // Valida ao sair do campo (blur)
        inputDoc.addEventListener('blur', function () {
            const digits = this.value.replace(/\D/g, '');
            const tipo   = selectTipo.value;
            if (digits.length > 0) validarDocumento(digits, tipo);
        });
    }

    // ── Validação dos dígitos verificadores (CPF e CNPJ) ──────────
    function validarDocumento(digits, tipo) {
        const inputEl = inputDoc;
        let valido = false;
        let msg    = '';

        if (tipo === 'F') {
            valido = _validarCPF(digits);
            msg    = valido ? 'CPF válido ✓' : 'CPF inválido — verifique os dígitos';
        } else {
            valido = _validarCNPJ(digits);
            msg    = valido ? 'CNPJ válido ✓' : 'CNPJ inválido — verifique os dígitos';
        }

        // Remove feedback anterior — busca no col-md-4 pai do input-group
        const inputGroup = inputEl.closest('.input-group');
        const colPai     = inputGroup ? inputGroup.parentNode : inputEl.parentNode;
        let fb = colPai.querySelector('.doc-feedback');
        if (!fb) {
            fb = document.createElement('div');
            fb.className = 'doc-feedback small mt-1';
            // Inserir após o input-group dentro do col
            if (inputGroup) inputGroup.insertAdjacentElement('afterend', fb);
            else colPai.appendChild(fb);
        }

        if (valido) {
            inputEl.classList.remove('is-invalid');
            inputEl.classList.add('is-valid');
            fb.style.color  = '#198754';
            fb.textContent  = msg;
        } else {
            inputEl.classList.remove('is-valid');
            inputEl.classList.add('is-invalid');
            fb.style.color  = '#dc3545';
            fb.textContent  = msg;
        }

        return valido;
    }

    function _validarCPF(cpf) {
        // Rejeita sequências repetidas (000.000.000-00, 111... etc.)
        if (/^(\d)\1+$/.test(cpf) || cpf.length !== 11) return false;
        let soma = 0, resto;
        for (let i = 1; i <= 9; i++) soma += parseInt(cpf[i - 1]) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf[9])) return false;
        soma = 0;
        for (let i = 1; i <= 10; i++) soma += parseInt(cpf[i - 1]) * (12 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        return resto === parseInt(cpf[10]);
    }

    function _validarCNPJ(cnpj) {
        if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;
        const calc = (n, tam) => {
            let soma = 0, pos = tam - 7;
            for (let i = tam; i >= 1; i--) {
                soma += parseInt(cnpj[tam - i]) * pos--;
                if (pos < 2) pos = 9;
            }
            const r = soma % 11;
            return r < 2 ? 0 : 11 - r;
        };
        return calc(cnpj, 12) === parseInt(cnpj[12])
            && calc(cnpj, 13) === parseInt(cnpj[13]);
    }

    // ── Máscara Telefone ──────────────────────────────────────────
    if (inputTel) {
        inputTel.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 11);
            v = v.replace(/^(\d{2})(\d)/, '($1) $2');
            v = v.replace(/(\d{5})(\d{4})$/, '$1-$2');
            e.target.value = v;
        });
    }

    // ── Sem número ────────────────────────────────────────────────
    if (inputNumero && checkSemNum) {
        if (checkSemNum.checked) inputNumero.readOnly = true;
        checkSemNum.addEventListener('change', function () {
            if (this.checked) { inputNumero.value = 'S/N'; inputNumero.readOnly = true; }
            else { if (inputNumero.value === 'S/N') inputNumero.value = ''; inputNumero.readOnly = false; }
        });
    }

    // ── CEP → ViaCEP ─────────────────────────────────────────────
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
                if (d.erro) { if (cepStatus) { cepStatus.textContent = 'CEP não encontrado.'; cepStatus.className = 'text-danger small'; } return; }
                preencherEndereco(d.logradouro, d.bairro, d.localidade, d.uf);
                if (cepStatus) cepStatus.textContent = '';
            })
            .catch(() => { if (cepStatus) { cepStatus.textContent = 'Erro ao buscar CEP.'; cepStatus.className = 'text-danger small'; } });
    }

    function preencherEndereco(logradouro, bairro, cidade, uf) {
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
        set('endereco', logradouro);
        set('bairro', bairro);
        set('cidade', cidade);
        const selUF = document.getElementById('uf');
        if (selUF) selUF.value = uf || '';
    }

    // ── Botão Verificar ───────────────────────────────────────────
    function setBtnLoading(ativo) {
        if (!btnVerificar) return;
        const lbl = document.getElementById('btn_verificar_label');
        const ico = btnVerificar.querySelector('i');
        btnVerificar.disabled = ativo;
        if (ativo) {
            if (lbl) lbl.textContent = 'Consultando...';
            if (ico) { ico.classList.remove('bi-shield-check'); ico.classList.add('bi-hourglass-split'); }
        } else {
            if (lbl) lbl.textContent = 'Verificar';
            if (ico) { ico.classList.remove('bi-hourglass-split'); ico.classList.add('bi-shield-check'); }
        }
    }

    if (btnVerificar) {
        btnVerificar.addEventListener('click', function () {
            const tipo = selectTipo ? selectTipo.value : 'F';

            if (tipo === 'J') {
                // ── CNPJ → opencnpj.org ───────────────────────────
                const cnpjRaw = inputDoc ? inputDoc.value.replace(/\D/g, '') : '';
                if (cnpjRaw.length !== 14) {
                    toast('Preencha o CNPJ completo (14 dígitos) antes de verificar.', 'warning');
                    if (inputDoc) inputDoc.focus();
                    return;
                }
                const dataset = selDataset ? selDataset.value : 'receita';
                setBtnLoading(true);

                // Lê o token CSRF do campo hidden do form
                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';

                fetch('/pessoa/consultarCNPJAjax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': csrfToken
                    },
                    body: new URLSearchParams({ cnpj: cnpjRaw, dataset: dataset, csrf_token: csrfToken })
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.sucesso) {
                        if (inputDoc) { inputDoc.classList.remove('is-invalid'); inputDoc.classList.add('is-valid'); }
                        if (inputNome && resp.nome)       inputNome.value = resp.nome;
                        if (resp.cep) {
                            const cepFmt = resp.cep.length === 8 ? resp.cep.substring(0,5) + '-' + resp.cep.substring(5) : resp.cep;
                            const cepEl  = document.getElementById('cep');
                            if (cepEl) cepEl.value = cepFmt;
                            preencherEndereco(resp.logradouro, resp.bairro, resp.municipio, resp.uf);
                        }
                        if (fonteSpan) fonteSpan.textContent = 'Fonte: ' + (resp.dataset_usado || resp.dataset || '');
                        toast('CNPJ válido! Empresa: <b>' + resp.nome + '</b> — ' + resp.situacao + ' (via ' + (resp.dataset_usado || dataset) + ')', 'success');
                    } else {
                        if (inputDoc) { inputDoc.classList.remove('is-valid'); inputDoc.classList.add('is-invalid'); }
                        const outraFonte = resp.dataset === dataset ? null : resp.dataset;
                        let msgExtra = outraFonte ? ' Tente mudar a fonte de consulta.' : '';
                        toast(resp.mensagem + msgExtra, 'danger');
                    }
                })
                .catch(() => toast('Erro ao consultar o CNPJ. Verifique sua conexão.', 'danger'))
                .finally(() => setBtnLoading(false));

            } else {
                // ── CPF → modal embutido com iframe da Receita ────
                const cpfRaw = inputDoc ? inputDoc.value.replace(/\D/g, '') : '';
                if (cpfRaw.length !== 11) {
                    toast('Preencha o CPF completo (11 dígitos) antes de verificar.', 'warning');
                    if (inputDoc) inputDoc.focus();
                    return;
                }
                abrirModalCPF();
            }
        });
    }

    // ── Modal CPF ─────────────────────────────────────────────────
    // O site da Receita Federal bloqueia interação dentro de iframe (hCaptcha
    // não consegue ser resolvido em contexto cross-origin).
    // Solução: abrir popup nativo do browser (sem as restrições de iframe)
    // e mostrar um painel lateral elegante no formulário enquanto o usuário
    // preenche o captcha na janela separada.
    function abrirModalCPF() {
        const cpfFormatado = inputDoc ? inputDoc.value : '';

        // Abre popup nativo — hCaptcha funciona normalmente aqui
        const popup = window.open(
            'https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaPublica.asp',
            'ReceitaCPF',
            'width=860,height=620,top=80,left=200,scrollbars=yes,resizable=yes'
        );

        if (!popup) {
            toast('Bloqueador de pop-ups ativo. Permita pop-ups para este site nas configurações do navegador.', 'warning');
            return;
        }

        // Remove painel anterior se existir
        const anterior = document.getElementById('_painel_cpf');
        if (anterior) anterior.remove();

        // Exibe painel flutuante no formulário com instruções e CPF copiável
        const painel = document.createElement('div');
        painel.id = '_painel_cpf';
        painel.style.cssText = [
            'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999',
            'background:#fff;border-radius:12px;width:300px',
            'box-shadow:0 8px 32px rgba(0,0,0,.2);border:1px solid #dee2e6',
            'overflow:hidden;animation:_slideUp .25s ease'
        ].join(';');

        painel.innerHTML = `
            <style>
                @keyframes _slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
            </style>
            <div style="background:#1351b4;color:#fff;padding:.7rem 1rem;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-weight:600;font-size:.9rem;">🔒 Consulta na Receita Federal</span>
                <button id="_painel_fechar" style="background:rgba(255,255,255,.2);border:none;color:#fff;border-radius:6px;padding:.25rem .6rem;cursor:pointer;font-size:.9rem;">✕</button>
            </div>
            <div style="padding:1rem;">
                <p style="font-size:.85rem;color:#495057;margin:0 0 .75rem;">
                    Uma janela da Receita Federal foi aberta. Siga os passos:
                </p>
                <ol style="font-size:.82rem;color:#495057;padding-left:1.1rem;margin:0 0 .85rem;line-height:1.7;">
                    <li>Digite o CPF abaixo no campo da Receita</li>
                    <li>Informe sua <b>data de nascimento</b></li>
                    <li>Resolva o <b>captcha</b> ("Sou humano")</li>
                    <li>Verifique a situação cadastral</li>
                </ol>
                <div style="background:#f8f9fa;border:1px solid #dee2e6;border-radius:6px;padding:.5rem .75rem;display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                    <span style="font-weight:700;letter-spacing:.05em;font-size:.95rem;" id="_cpf_display">${cpfFormatado}</span>
                    <button id="_btn_copiar_cpf" style="background:none;border:none;cursor:pointer;color:#1351b4;font-size:.8rem;padding:0;">
                        <i class="bi bi-clipboard"></i> Copiar
                    </button>
                </div>
                <button id="_btn_reabrir_popup" style="width:100%;background:#1351b4;color:#fff;border:none;border-radius:6px;padding:.5rem;font-size:.85rem;cursor:pointer;">
                    <i class="bi bi-box-arrow-up-right"></i> Reabrir janela da Receita
                </button>
            </div>`;

        document.body.appendChild(painel);

        // Botão fechar painel
        document.getElementById('_painel_fechar').addEventListener('click', () => {
            painel.remove();
            if (popup && !popup.closed) popup.close();
        });

        // Copiar CPF
        document.getElementById('_btn_copiar_cpf').addEventListener('click', function () {
            const cpfLimpo = cpfFormatado.replace(/\D/g, '');
            navigator.clipboard.writeText(cpfLimpo).then(() => {
                this.innerHTML = '<i class="bi bi-check2"></i> Copiado!';
                setTimeout(() => { this.innerHTML = '<i class="bi bi-clipboard"></i> Copiar'; }, 2000);
            }).catch(() => {
                // Fallback para browsers sem clipboard API
                const tmp = document.createElement('input');
                tmp.value = cpfLimpo;
                document.body.appendChild(tmp);
                tmp.select();
                document.execCommand('copy');
                tmp.remove();
                this.innerHTML = '<i class="bi bi-check2"></i> Copiado!';
                setTimeout(() => { this.innerHTML = '<i class="bi bi-clipboard"></i> Copiar'; }, 2000);
            });
        });

        // Reabrir popup se o usuário fechou
        document.getElementById('_btn_reabrir_popup').addEventListener('click', function () {
            if (!popup || popup.closed) {
                window.open(
                    'https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaPublica.asp',
                    'ReceitaCPF',
                    'width=860,height=620,top=80,left=200,scrollbars=yes,resizable=yes'
                );
            } else {
                popup.focus();
            }
        });

        // Remove painel automaticamente quando popup for fechado
        const monitorPopup = setInterval(() => {
            if (!popup || popup.closed) {
                clearInterval(monitorPopup);
                // Aguarda um momento antes de remover para não sumir abruptamente
                setTimeout(() => { if (document.getElementById('_painel_cpf')) painel.remove(); }, 1500);
            }
        }, 800);
    }

})();
</script>

<?php \Core\Library\Session::destroy('formErrors'); ?>
