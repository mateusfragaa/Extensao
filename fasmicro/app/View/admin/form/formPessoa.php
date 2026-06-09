<?php
$action_form = formDadosInput($data, 'pessoa');
$errors = Core\Library\Session::get('formErrors');
?>
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/pessoa/" class="btn btn-light border me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0">Cadastrar Cliente <?= formSubTitulo($action_form) ?></h4>
            <p class="text-muted small m-0">Adicione um novo cliente ou contato ao sistema.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <?php echo exibeAlerta(); ?>

        <form class="row g-4" action="/pessoa/<?= $action_form ?>" method="POST">

            <?php if ($action_form !== 'insert'): ?>
                <input type="hidden" name="PES_ID" value="<?= setValue('PES_ID') ?>">
            <?php endif; ?>



            <div class="col-md-8">
                <label class="form-label" id="label_nome_pfpj">Nome Completo / Razão Social</label>
                <input type="text"
                    name="PES_NOME"
                    id="nome_pfpj"
                    class="form-control <?= isset($errors['PES_NOME']) ? 'is-invalid' : '' ?>"
                    placeholder="Digite o nome completo"
                    value="<?= setValue('PES_NOME') ?>"
                    <?= $action_form == 'view' ? 'disabled' : '' ?>>
                <?php if (isset($errors['PES_NOME'])): ?>
                    <div class="invalid-feedback"><?= $errors['PES_NOME'] ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <label class="form-label" id="label_cpf_cnpj">CPF / CNPJ</label>
                <div class="input-group">
                    <input type="text" name="CPF_CNPJ" id="cpf_cnpj"
                        class="form-control <?= isset($errors['CPF_CNPJ']) ? 'is-invalid' : '' ?>"
                        placeholder="000.000.000-00"
                        value="<?= setValue('CPF_CNPJ') ?>"
                        <?= $action_form == 'view' ? 'disabled' : '' ?>>

                    <?php if ($action_form == 'insert' || $action_form == 'update'): ?>
                        <button class="btn btn-outline-secondary" type="button" id="btn_validar_receita" title="Validar na Receita Federal">
                            <i class="bi bi-shield-check text-success"></i> Verificar
                        </button>
                    <?php endif; ?>
                </div>
                <?php if (isset($errors['CPF_CNPJ'])): ?>
                    <div class="invalid-feedback"><?= $errors['CPF_CNPJ'] ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <label class="form-label">E-mail</label>
                <input type="email" name="EMAIL" class="form-control <?= isset($errors['EMAIL']) ? 'is-invalid' : '' ?>"
                    placeholder="exemplo@email.com" value="<?= setValue('EMAIL') ?>"
                    <?= $action_form == 'view' ? 'disabled' : '' ?>>
            </div>

            <div class="col-md-4">
                <label class="form-label">Telefone / WhatsApp</label>
                <input type="text" name="TELEFONE" class="form-control"
                    placeholder="(00) 00000-0000" value="<?= setValue('TELEFONE') ?>"
                    <?= $action_form == 'view' ? 'disabled' : '' ?>>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo de Pessoa</label>
                <select name="TIPO_PESSOA" id="tipo_pessoa" class="form-select" <?= $action_form == 'view' ? 'disabled' : '' ?>>
                    <option value="F" <?= (setValue('TIPO_PESSOA') == 'F') ? 'selected' : '' ?>>Pessoa Física</option>
                    <option value="J" <?= (setValue('TIPO_PESSOA') == 'J') ? 'selected' : '' ?>>Pessoa Jurídica</option>
                </select>
            </div>

            <!-- CEP -->
            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" name="CEP" id="cep" class="form-control" placeholder="00000-000"
                    value="<?= setValue('CEP') ?>" <?= $action_form == 'view' ? 'disabled' : '' ?> maxlength="9">
                <small id="cep_status" class="text-muted small"></small>
            </div>

            <div class="col-md-8">
                <label class="form-label">Endereço</label>
                <input type="text" name="ENDERECO" id="endereco" class="form-control" placeholder="Rua, Av, Logradouro..."
                    value="<?= setValue('ENDERECO') ?>" <?= $action_form == 'view' ? 'disabled' : '' ?>>
            </div>
            <div class="col-md-2">
                <label class="form-label">Número</label>
                <div class="input-group">
                    <input type="text" name="NUMERO" id="numero_casa" class="form-control" placeholder="123"
                        value="<?= setValue('NUMERO') ?>" <?= $action_form == 'view' ? 'disabled' : '' ?>>
                </div>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="sem_numero" <?= (setValue('NUMERO') == 'S/N') ? 'checked' : '' ?> <?= $action_form == 'view' ? 'disabled' : '' ?>>
                    <label class="form-check-label small" for="sem_numero">
                        Sem número
                    </label>
                </div>
            </div>

            <div class="col-md-5">
                <label class="form-label">Cidade</label>
                <input type="text" name="CIDADE" id="cidade" class="form-control" value="<?= setValue('CIDADE') ?>"
                    <?= $action_form == 'view' ? 'disabled' : '' ?>>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bairro</label>
                <input type="text" name="BAIRRO" id="bairro" class="form-control" value="<?= setValue('BAIRRO') ?>"
                    <?= $action_form == 'view' ? 'disabled' : '' ?>>
            </div>
            <div class="col-md-3">
                <label class="form-label">UF</label>
                <select name="UF" id="uf" class="form-select" <?= $action_form == 'view' ? 'disabled' : '' ?>>
                    <option value="">Selecione...</option>
                    <?php
                    $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                    foreach ($ufs as $uf): ?>
                        <option value="<?= $uf ?>" <?= (setValue('UF') == $uf) ? 'selected' : '' ?>><?= $uf ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a type="button" class="btn btn-light border px-4" href="/pessoa/">Cancelar</a>
                <?php if ($action_form != 'view'): ?>
                    <button type="submit" class="btn btn-primary-custom px-5">
                        <?= $action_form == 'delete' ? 'Confirmar Exclusão' : 'Salvar Cliente' ?>
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof initFormPessoa !== 'function') {
        window.initFormPessoa = function() {
            console.log('Inicializando comportamentos do formulário de pessoa...');

            // CPF / CNPJ Dinâmico
            const selectTipo = document.getElementById('tipo_pessoa');
            const inputCPFCNPJ = document.getElementById('cpf_cnpj');
            const labelCPFCNPJ = document.getElementById('label_cpf_cnpj');

            function atualizarCPFCNPJ(tipo) {
                if (!labelCPFCNPJ || !inputCPFCNPJ) return;
                if (tipo === 'F') {
                    labelCPFCNPJ.textContent = 'CPF';
                    inputCPFCNPJ.placeholder = '000.000.000-00';
                    inputCPFCNPJ.maxLength = 14;
                } else if (tipo === 'J') {
                    labelCPFCNPJ.textContent = 'CNPJ';
                    inputCPFCNPJ.placeholder = '00.000.000/0000-00';
                    inputCPFCNPJ.maxLength = 18;
                }
            }

            // Nome Completo / Razão Social Dinâmico
            const labelNome = document.getElementById('label_nome_pfpj');
            const inputNome = document.getElementById('nome_pfpj');

            function atualizarNomePFPJ(tipo) {
                if (!labelNome || !inputNome) return;

                if (tipo === 'F') {
                    labelNome.textContent = 'Nome Completo';
                    inputNome.placeholder = 'Digite o nome completo';
                } else if (tipo === 'J') {
                    labelNome.textContent = 'Razão Social';
                    inputNome.placeholder = 'Digite a razão social';
                }
            }

            if (selectTipo && inputNome) {
                atualizarNomePFPJ(selectTipo.value);
                selectTipo.addEventListener('change', function() {
                    atualizarNomePFPJ(this.value);
                });
            }

            if (selectTipo && inputCPFCNPJ) {
                atualizarCPFCNPJ(selectTipo.value);
                selectTipo.removeEventListener('change', handleTipoChange); // Evita duplicados
                selectTipo.addEventListener('change', handleTipoChange);
            }

            function handleTipoChange() {
                atualizarCPFCNPJ(this.value);
                validarDocumento();
            }
            // --- IMPLEMENTAÇÃO DE MÁSCARA AUTOMÁTICA ---
            if (inputCPFCNPJ) {
                inputCPFCNPJ.addEventListener('input', function(e) {
                    let v = e.target.value.replace(/\D/g, ''); // Remove tudo que não é número
                    const tipo = selectTipo.value;

                    if (tipo === 'F') {
                        // Máscara de CPF: 000.000.000-00
                        v = v.substring(0, 11);
                        v = v.replace(/(\d{3})(\d)/, '$1.$2');
                        v = v.replace(/(\d{3})(\d)/, '$1.$2');
                        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    } else {
                        // Máscara de CNPJ: 00.000.000/0000-00
                        v = v.substring(0, 14);
                        v = v.replace(/^(\d{2})(\d)/, '$1.$2');
                        v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                        v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
                        v = v.replace(/(\d{4})(\d)/, '$1-$2');
                    }

                    e.target.value = v;
                });
            }

            // --- MÁSCARA PARA TELEFONE (BÔNUS) ---
            const inputTelefone = document.querySelector('input[name="TELEFONE"]');
            if (inputTelefone) {
                inputTelefone.addEventListener('input', function(e) {
                    let v = e.target.value.replace(/\D/g, '');
                    v = v.substring(0, 11);
                    v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                    v = v.replace(/(\d)(\d{4})$/, '$1-$2');
                    e.target.value = v;
                });
            }


            function validarDocumento() {
                const valor = inputCPFCNPJ.value.replace(/\D/g, '');
                const tipo = selectTipo.value;
                let valido = false;

                if (valor === '') return;

                if (tipo === 'F') {
                    valido = (valor.length === 11) && validarCPF_JS(valor);
                } else {
                    valido = (valor.length === 14) && validarCNPJ_JS(valor);
                }

                if (valido) {
                    inputCPFCNPJ.classList.remove('is-invalid');
                    inputCPFCNPJ.classList.add('is-valid');
                } else {
                    inputCPFCNPJ.classList.remove('is-valid');
                    inputCPFCNPJ.classList.add('is-invalid');
                }
            }

            function validarCPF_JS(cpf) {
                if (/^(\d)\1+$/.test(cpf)) return false;
                let soma = 0,
                    resto;
                for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
                resto = (soma * 10) % 11;
                if ((resto === 10) || (resto === 11)) resto = 0;
                if (resto !== parseInt(cpf.substring(9, 10))) return false;
                soma = 0;
                for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
                resto = (soma * 10) % 11;
                if ((resto === 10) || (resto === 11)) resto = 0;
                if (resto !== parseInt(cpf.substring(10, 11))) return false;
                return true;
            }

            function validarCNPJ_JS(cnpj) {
                if (/^(\d)\1+$/.test(cnpj)) return false;
                let tamanho = cnpj.length - 2;
                let numeros = cnpj.substring(0, tamanho);
                let digitos = cnpj.substring(tamanho);
                let soma = 0,
                    pos = tamanho - 7;
                for (let i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) pos = 9;
                }
                let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(0)) return false;
                tamanho = tamanho + 1;
                numeros = cnpj.substring(0, tamanho);
                soma = 0;
                pos = tamanho - 7;
                for (let i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) pos = 9;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(1)) return false;
                return true;
            }

            if (inputCPFCNPJ) {
                inputCPFCNPJ.addEventListener('blur', validarDocumento);
                inputCPFCNPJ.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) validarDocumento();
                });
            }

            const inputNumero = document.getElementById('numero_casa');
            const checkSemNumero = document.getElementById('sem_numero');

            if (inputNumero && checkSemNumero) {
                checkSemNumero.removeEventListener('change', handleSemNumeroChange);
                checkSemNumero.addEventListener('change', handleSemNumeroChange);

                if (checkSemNumero.checked) {
                    inputNumero.readOnly = true;
                }
            }

            function handleSemNumeroChange() {
                if (this.checked) {
                    inputNumero.value = 'S/N';
                    inputNumero.readOnly = true;
                } else {
                    if (inputNumero.value === 'S/N') inputNumero.value = '';
                    inputNumero.readOnly = false;
                }
            }

            // CEP
            const inputCEP = document.getElementById('cep');
            const cepStatus = document.getElementById('cep_status');

            if (inputCEP) {
                inputCEP.removeEventListener('input', handleCEPInput);
                inputCEP.addEventListener('input', handleCEPInput);
            }

            function handleCEPInput() {
                let v = this.value.replace(/\D/g, '').substring(0, 8);
                this.value = v.length > 5 ? v.substring(0, 5) + '-' + v.substring(5) : v;
                if (v.length === 8) buscarCEP(v);
            }

            function buscarCEP(cep) {
                if (!cepStatus) return;
                cepStatus.textContent = 'Buscando...';
                cepStatus.className = 'text-primary small';

                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.erro) {
                            cepStatus.textContent = 'CEP não encontrado.';
                            cepStatus.className = 'text-danger small';
                            return;
                        }
                        preencherEndereco(data);
                        cepStatus.textContent = ''; // Limpa o status em caso de sucesso
                    })
                    .catch(() => {
                        if (cepStatus) {
                            cepStatus.textContent = 'Erro ao buscar CEP.';
                            cepStatus.className = 'text-danger small';
                        }
                    });
            }

            function preencherEndereco(data) {
                const fields = {
                    'endereco': data.logradouro,
                    'bairro': data.bairro,
                    'cidade': data.localidade,
                    'uf': data.uf
                };
                for (let id in fields) {
                    const el = document.getElementById(id);
                    if (el) el.value = fields[id] || '';
                }
            }

            // --- INTEGRAÇÃO COM A RECEITA FEDERAL ---

            // -------------------------------------------------------
            // Toast: substitui alert() por notificacoes elegantes
            // -------------------------------------------------------
            function mostrarToast(mensagem, tipo) {
                tipo = tipo || 'success';
                const cores = {
                    success: { bg: '#198754', icone: 'bi-check-circle-fill' },
                    danger:  { bg: '#dc3545', icone: 'bi-exclamation-triangle-fill' },
                    warning: { bg: '#fd7e14', icone: 'bi-info-circle-fill' },
                    info:    { bg: '#0dcaf0', icone: 'bi-info-circle' }
                };
                const c = cores[tipo] || cores.info;
                let container = document.getElementById('toast-container-custom');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toast-container-custom';
                    container.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;'
                        + 'z-index:9999;display:flex;flex-direction:column;gap:.5rem;';
                    document.body.appendChild(container);
                }
                const toast = document.createElement('div');
                toast.style.cssText = 'background:' + c.bg + ';color:#fff;'
                    + 'padding:.85rem 1.2rem;border-radius:.5rem;'
                    + 'box-shadow:0 4px 12px rgba(0,0,0,.2);'
                    + 'display:flex;align-items:center;gap:.65rem;'
                    + 'min-width:280px;max-width:420px;font-size:.92rem;'
                    + 'opacity:0;transition:opacity .3s;';
                toast.innerHTML = '<i class="bi ' + c.icone + '" style="font-size:1.2rem;flex-shrink:0"></i>'
                    + '<span>' + mensagem + '</span>';
                container.appendChild(toast);
                requestAnimationFrame(function() { toast.style.opacity = '1'; });
                setTimeout(function() {
                    toast.style.opacity = '0';
                    setTimeout(function() { toast.remove(); }, 350);
                }, 5000);
            }

            const btnValidarReceita = document.getElementById('btn_validar_receita');

            if (btnValidarReceita) {
                btnValidarReceita.addEventListener('click', function() {
                    const tipoPessoa = selectTipo.value;

                    // == CNPJ: consulta API receitaws.com.br ==
                    if (tipoPessoa === 'J') {
                        const cnpjRaw = inputCPFCNPJ.value.replace(/\D/g, '');
                        if (cnpjRaw.length !== 14) {
                            mostrarToast('Preencha o CNPJ completo (14 digitos) antes de verificar.', 'warning');
                            inputCPFCNPJ.focus();
                            return;
                        }
                        btnValidarReceita.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Consultando...';
                        btnValidarReceita.disabled = true;
                        fetch('/pessoa/consultarCNPJAjax', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new URLSearchParams({ cnpj: cnpjRaw })
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(resp) {
                            if (resp.sucesso) {
                                inputCPFCNPJ.classList.remove('is-invalid');
                                inputCPFCNPJ.classList.add('is-valid');
                                const inputNome = document.getElementById('nome_pfpj');
                                if (inputNome && resp.nome) inputNome.value = resp.nome;
                                mostrarToast('CNPJ valido! Empresa: <b>' + resp.nome + '</b> &mdash; Situacao: <b>' + resp.situacao + '</b>', 'success');
                            } else {
                                inputCPFCNPJ.classList.remove('is-valid');
                                inputCPFCNPJ.classList.add('is-invalid');
                                mostrarToast(resp.mensagem, 'danger');
                            }
                        })
                        .catch(function() {
                            mostrarToast('Erro ao consultar o CNPJ. Tente novamente.', 'danger');
                        })
                        .finally(function() {
                            btnValidarReceita.innerHTML = '<i class="bi bi-shield-check text-success"></i> Verificar';
                            btnValidarReceita.disabled = false;
                        });
                        return;
                    }

                    // == CPF: popup da Receita Federal ==
                    if (tipoPessoa !== 'F') {
                        mostrarToast('Selecione o Tipo de Pessoa antes de verificar.', 'warning');
                        return;
                    }

                    const urlReceita = "https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaPublica.asp";
                    const popup = window.open(urlReceita, "ConsultaReceita", "width=800,height=600,scrollbars=yes");

                    if (!popup) {
                        mostrarToast('Bloqueador de popups ativado. Permita popups para este site.', 'warning');
                        return;
                    }

                    btnValidarReceita.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aguardando...`;
                    btnValidarReceita.disabled = true;

                    const checarPopup = setInterval(function() {
                        try {
                            if (popup.closed) {
                                clearInterval(checarPopup);
                                btnValidarReceita.innerHTML = `<i class="bi bi-shield-check text-success"></i> Verificar`;
                                btnValidarReceita.disabled = false;
                                return;
                            }
                            if (popup.document.querySelector('.clConteudoDados')) {
                                clearInterval(checarPopup);
                                const htmlCapturado = popup.document.documentElement.innerHTML;
                                popup.close();
                                const dadosParaPost = new URLSearchParams();
                                dadosParaPost.append('html_receita', htmlCapturado);
                                fetch('/pessoa/validarReceitaAjax', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: dadosParaPost
                                })
                                .then(function(response) { return response.json(); })
                                .then(function(data) {
                                    if (data.sucesso) {
                                        const inputNome = document.getElementById('nome_pfpj');
                                        if (inputNome) {
                                            inputNome.value = data.nome;
                                            inputNome.classList.add('is-valid');
                                        }
                                        mostrarToast('CPF validado! Nome: <b>' + data.nome + '</b>', 'success');
                                    } else {
                                        mostrarToast('Erro na validacao: ' + data.mensagem, 'danger');
                                    }
                                })
                                .catch(function() {
                                    mostrarToast('Erro interno ao processar os dados.', 'danger');
                                })
                                .finally(function() {
                                    btnValidarReceita.innerHTML = `<i class="bi bi-shield-check text-success"></i> Verificar`;
                                    btnValidarReceita.disabled = false;
                                });
                            }
                        } catch (e) {
                            // Same-Origin Policy durante navegacao - ignorar
                        }
                    }, 1500);
                });
            }

        };
    }

    // inicialização
    initFormPessoa();
</script>

<?php
Core\Library\Session::destroy('formErrors');
?>