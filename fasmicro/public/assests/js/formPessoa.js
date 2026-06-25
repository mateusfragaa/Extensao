/**
 * formPessoa.js
 * Lógica do formulário de cadastro de Pessoa (Física e Jurídica).
 * - Máscara CPF / CNPJ / Telefone / CEP
 * - Validação de dígitos verificadores em tempo real
 * - Busca de endereço via ViaCEP
 * - Consulta CNPJ via opencnpj.org (com seletor de dataset)
 * - Painel de verificação CPF via Receita Federal
 * - Copiar texto compatível com HTTP e HTTPS
 */
(function () {
    'use strict';

    const selectTipo   = document.getElementById('tipo_pessoa');
    const inputDoc     = document.getElementById('cpf_cnpj');
    const labelDoc     = document.getElementById('label_cpf_cnpj');
    const labelNome    = document.getElementById('label_nome_pfpj');
    const inputNome    = document.getElementById('nome_pfpj');
    const inputTel     = document.getElementById('input_telefone');
    const inputCEP     = document.getElementById('cep');
    const cepStatus    = document.getElementById('cep_status');
    const inputNumero  = document.getElementById('numero_casa');
    const checkSemNum  = document.getElementById('sem_numero');
    const btnVerificar = document.getElementById('btn_validar_receita');
    const fonteSpan    = document.getElementById('cpf_cnpj_fonte');
    const blocoDataset = document.getElementById('bloco_dataset');
    const selDataset   = document.getElementById('sel_dataset');

    // ── Toast ─────────────────────────────────────────────────────
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
        t.style.cssText = 'background:' + (c[tipo]||c.info) + ';color:#fff;padding:.85rem 1.2rem;border-radius:.5rem;'
            + 'box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;gap:.65rem;font-size:.92rem;opacity:0;transition:opacity .3s;';
        t.innerHTML = '<i class="bi ' + (i[tipo]||i.info) + '" style="font-size:1.2rem;flex-shrink:0"></i><span>' + msg + '</span>';
        box.appendChild(t);
        requestAnimationFrame(function () { t.style.opacity = '1'; });
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 350); }, 6000);
    }

    // ── Copiar texto (HTTP + HTTPS) ───────────────────────────────
    function copiarTexto(texto, btnEl) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(texto).then(function () { _feedbackCopiar(btnEl); }).catch(function () { _copiarFallback(texto, btnEl); });
        } else {
            _copiarFallback(texto, btnEl);
        }
    }

    function _copiarFallback(texto, btnEl) {
        var tmp = document.createElement('input');
        tmp.value = texto;
        tmp.style.cssText = 'position:fixed;top:-999px;left:-999px;opacity:0;';
        document.body.appendChild(tmp);
        tmp.focus(); tmp.select(); tmp.setSelectionRange(0, 99999);
        var ok = document.execCommand('copy');
        document.body.removeChild(tmp);
        if (ok) { _feedbackCopiar(btnEl); }
        else if (btnEl) { btnEl.innerHTML = '<i class="bi bi-exclamation-circle"></i> Copie: ' + texto; }
    }

    function _feedbackCopiar(btnEl) {
        if (!btnEl) return;
        var orig = btnEl.innerHTML;
        btnEl.innerHTML = '<i class="bi bi-check2"></i> Copiado!';
        setTimeout(function () { btnEl.innerHTML = orig; }, 2000);
    }

    // ── Tipo de pessoa ────────────────────────────────────────────
    function atualizarTipo(tipo) {
        if (tipo === 'J') {
            if (labelNome) labelNome.innerHTML = 'Razão Social <span class="text-danger">*</span>';
            if (inputNome) inputNome.placeholder = 'Digite a razão social';
            if (labelDoc)  labelDoc.innerHTML  = 'CNPJ <span class="text-danger">*</span>';
            if (inputDoc) {
                inputDoc.placeholder = '00.000.000/0000-00';
                inputDoc.maxLength   = 18;
                // CNPJ 2.0 aceita letras — libera teclado alfanumérico
                inputDoc.setAttribute('inputmode', 'text');
            }
            if (blocoDataset) blocoDataset.style.display = 'block';
        } else {
            if (labelNome) labelNome.innerHTML = 'Nome Completo <span class="text-danger">*</span>';
            if (inputNome) inputNome.placeholder = 'Digite o nome completo';
            if (labelDoc)  labelDoc.innerHTML  = 'CPF <span class="text-danger">*</span>';
            if (inputDoc) {
                inputDoc.placeholder = '000.000.000-00';
                inputDoc.maxLength   = 14;
                // CPF é só numérico
                inputDoc.setAttribute('inputmode', 'numeric');
            }
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
                var inputGroup = inputDoc.closest('.input-group');
                var colPai = inputGroup ? inputGroup.parentNode : inputDoc.parentNode;
                var fb = colPai.querySelector('.doc-feedback');
                if (fb) fb.remove();
            }
        });
    }

    // ── Máscara CPF / CNPJ ────────────────────────────────────────
    function mascararDoc(v, tipo) {
        if (tipo === 'F') {
            // CPF: apenas dígitos — 000.000.000-00
            v = v.replace(/\D/g, '').substring(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            // CNPJ 2.0: aceita A-Z e 0-9 — formato XX.XXX.XXX/XXXX-XX
            // Limpa tudo que não é alfanumérico e deixa só 14 chars
            v = v.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 14);
            // Aplica separadores progressivamente da esquerda para direita
            var r = '';
            for (var k = 0; k < v.length; k++) {
                if (k === 2 || k === 5) r += '.';
                else if (k === 8)       r += '/';
                else if (k === 12)      r += '-';
                r += v[k];
            }
            v = r;
        }
        return v;
    }

    if (inputDoc && selectTipo) {
        inputDoc.addEventListener('input', function (e) {
            e.target.value = mascararDoc(e.target.value, selectTipo.value);
            var tipo    = selectTipo.value;
            // Para CNPJ: mantém letras+dígitos; para CPF: só dígitos
            var digits  = tipo === 'J'
                ? e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')
                : e.target.value.replace(/\D/g, '');
            var completo = (tipo === 'F' && digits.length === 11) || (tipo === 'J' && digits.length === 14);
            if (completo) validarDocumento(digits, tipo);
            if (!completo) {
                inputDoc.classList.remove('is-valid', 'is-invalid');
                var ig = inputDoc.closest('.input-group');
                var cp = ig ? ig.parentNode : inputDoc.parentNode;
                var fb = cp.querySelector('.doc-feedback');
                if (fb) fb.remove();
            }
        });

        inputDoc.addEventListener('blur', function () {
            var tipo   = selectTipo.value;
            var digits = tipo === 'J'
                ? this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')
                : this.value.replace(/\D/g, '');
            if (digits.length > 0) validarDocumento(digits, tipo);
        });
    }

    // ── Validação dígitos verificadores ──────────────────────────
    function validarDocumento(digits, tipo) {
        var valido = tipo === 'F' ? _validarCPF(digits) : _validarCNPJ(digits);
        var msg    = valido
            ? (tipo === 'F' ? 'CPF válido ✓' : 'CNPJ válido ✓')
            : (tipo === 'F' ? 'CPF inválido — verifique os dígitos' : 'CNPJ inválido — verifique os dígitos');

        var inputGroup = inputDoc.closest('.input-group');
        var colPai     = inputGroup ? inputGroup.parentNode : inputDoc.parentNode;
        var fb = colPai.querySelector('.doc-feedback');
        if (!fb) {
            fb = document.createElement('div');
            fb.className = 'doc-feedback small mt-1';
            if (inputGroup) inputGroup.insertAdjacentElement('afterend', fb);
            else colPai.appendChild(fb);
        }

        if (valido) { inputDoc.classList.remove('is-invalid'); inputDoc.classList.add('is-valid'); fb.style.color = '#198754'; }
        else        { inputDoc.classList.remove('is-valid'); inputDoc.classList.add('is-invalid'); fb.style.color = '#dc3545'; }
        fb.textContent = msg;
        return valido;
    }

    function _validarCPF(cpf) {
        if (/^(\d)\1+$/.test(cpf) || cpf.length !== 11) return false;
        var soma = 0, resto;
        for (var i = 1; i <= 9; i++) soma += parseInt(cpf[i-1]) * (11-i);
        resto = (soma * 10) % 11; if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf[9])) return false;
        soma = 0;
        for (var j = 1; j <= 10; j++) soma += parseInt(cpf[j-1]) * (12-j);
        resto = (soma * 10) % 11; if (resto === 10 || resto === 11) resto = 0;
        return resto === parseInt(cpf[10]);
    }

    function _validarCNPJ(cnpj) {
        // CNPJ 2.0 (IN RFB 2.229/2024) — retrocompatível com formato numérico legado
        cnpj = cnpj.toUpperCase();
        if (cnpj.length !== 14) return false;
        if (!/^[A-Z0-9]{12}[0-9]{2}$/.test(cnpj)) return false;
        if (/^(.)\1{13}$/.test(cnpj)) return false; // rejeita repetição única

        // Valor de cada char: código ASCII - 48 (A=17, B=18... Z=42 | 0=0... 9=9)
        var vals = cnpj.split('').map(function(c) { return c.charCodeAt(0) - 48; });

        function calcDV(tam) {
            var soma = 0, pos = tam - 7;
            for (var i = tam; i >= 1; i--) {
                soma += vals[tam - i] * pos--;
                if (pos < 2) pos = 9;
            }
            var r = soma % 11;
            return r < 2 ? 0 : 11 - r;
        }
        return calcDV(12) === vals[12] && calcDV(13) === vals[13];
    }

    // ── Máscara Telefone ──────────────────────────────────────────
    if (inputTel) {
        inputTel.addEventListener('input', function (e) {
            var v = e.target.value.replace(/\D/g, '').substring(0, 11);
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
            var v = e.target.value.replace(/\D/g, '').substring(0, 8);
            e.target.value = v.length > 5 ? v.substring(0, 5) + '-' + v.substring(5) : v;
            if (v.length === 8) buscarCEP(v);
        });
    }

    function buscarCEP(cep) {
        if (cepStatus) { cepStatus.textContent = 'Buscando...'; cepStatus.className = 'text-primary small'; }
        fetch('https://viacep.com.br/ws/' + cep + '/json/')
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.erro) { if (cepStatus) { cepStatus.textContent = 'CEP não encontrado.'; cepStatus.className = 'text-danger small'; } return; }
                preencherEndereco(d.logradouro, d.bairro, d.localidade, d.uf);
                if (cepStatus) cepStatus.textContent = '';
            })
            .catch(function () { if (cepStatus) { cepStatus.textContent = 'Erro ao buscar CEP.'; cepStatus.className = 'text-danger small'; } });
    }

    function preencherEndereco(logradouro, bairro, cidade, uf) {
        function set(id, val) { var el = document.getElementById(id); if (el) el.value = val || ''; }
        set('endereco', logradouro); set('bairro', bairro); set('cidade', cidade);
        var selUF = document.getElementById('uf'); if (selUF) selUF.value = uf || '';
    }

    // ── Botão Verificar ───────────────────────────────────────────
    function setBtnLoading(ativo) {
        if (!btnVerificar) return;
        var lbl = document.getElementById('btn_verificar_label');
        var ico = btnVerificar.querySelector('i');
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
            var tipo = selectTipo ? selectTipo.value : 'F';

            if (tipo === 'J') {
                var cnpjRaw = inputDoc ? inputDoc.value.toUpperCase().replace(/[^A-Z0-9]/g, '') : '';
                if (cnpjRaw.length !== 14) { toast('Preencha o CNPJ completo (14 dígitos) antes de verificar.', 'warning'); if (inputDoc) inputDoc.focus(); return; }
                var dataset    = selDataset ? selDataset.value : 'receita';
                var csrfToken  = (document.querySelector('input[name="csrf_token"]') || {}).value || '';
                setBtnLoading(true);

                fetch('/pessoa/consultarCNPJAjax', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': csrfToken },
                    body: new URLSearchParams({ cnpj: cnpjRaw, dataset: dataset, csrf_token: csrfToken })
                })
                .then(function (r) { return r.json(); })
                .then(function (resp) {
                    if (resp.sucesso) {
                        if (inputDoc) { inputDoc.classList.remove('is-invalid'); inputDoc.classList.add('is-valid'); }
                        if (inputNome && resp.nome) inputNome.value = resp.nome;
                        if (resp.cep) {
                            var cepFmt = resp.cep.length === 8 ? resp.cep.substring(0,5) + '-' + resp.cep.substring(5) : resp.cep;
                            var cepEl = document.getElementById('cep'); if (cepEl) cepEl.value = cepFmt;
                            preencherEndereco(resp.logradouro, resp.bairro, resp.municipio, resp.uf);
                        }
                        if (fonteSpan) fonteSpan.textContent = 'Fonte: ' + (resp.dataset_usado || resp.dataset || '');
                        toast('CNPJ válido! Empresa: <b>' + resp.nome + '</b> — ' + resp.situacao + ' (via ' + (resp.dataset_usado || dataset) + ')', 'success');
                    } else {
                        if (inputDoc) { inputDoc.classList.remove('is-valid'); inputDoc.classList.add('is-invalid'); }
                        toast(resp.mensagem + (resp.dataset !== dataset ? ' Tente mudar a fonte de consulta.' : ''), 'danger');
                    }
                })
                .catch(function () { toast('Erro ao consultar o CNPJ. Verifique sua conexão.', 'danger'); })
                .finally(function () { setBtnLoading(false); });

            } else {
                var cpfRaw = inputDoc ? inputDoc.value.replace(/\D/g, '') : '';
                if (cpfRaw.length !== 11) { toast('Preencha o CPF completo (11 dígitos) antes de verificar.', 'warning'); if (inputDoc) inputDoc.focus(); return; }
                abrirPainelCPF();
            }
        });
    }

    // ── Painel CPF + Popup Receita Federal ────────────────────────
    function abrirPainelCPF() {
        var cpfFormatado = inputDoc ? inputDoc.value : '';
        var anterior = document.getElementById('_painel_cpf');
        if (anterior) anterior.remove();

        var popupRef = null;
        try {
            popupRef = window.open('https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaPublica.asp', 'ReceitaCPF', 'width=860,height=620,top=80,left=200,scrollbars=yes,resizable=yes');
        } catch(e) {}

        var painel = document.createElement('div');
        painel.id = '_painel_cpf';
        painel.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#fff;border-radius:12px;width:310px;box-shadow:0 8px 32px rgba(0,0,0,.22);border:1px solid #dee2e6;overflow:hidden;animation:_slideUp .25s ease';

        var bloqueado = !popupRef || popupRef.closed;
        painel.innerHTML = '<style>@keyframes _slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}</style>'
            + '<div style="background:#1351b4;color:#fff;padding:.75rem 1rem;display:flex;align-items:center;justify-content:space-between;">'
            + '<span style="font-weight:600;font-size:.9rem;">🔒 Consulta na Receita Federal</span>'
            + '<button id="_painel_fechar" style="background:rgba(255,255,255,.2);border:none;color:#fff;border-radius:6px;padding:.25rem .65rem;cursor:pointer;font-size:.95rem;line-height:1;">✕</button></div>'
            + '<div style="padding:1rem;">'
            + (bloqueado
                ? '<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:.6rem .8rem;margin-bottom:.85rem;font-size:.82rem;color:#856404;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Pop-up bloqueado. Clique em <b>"Abrir janela"</b> abaixo ou permita pop-ups nas configurações do browser.</div>'
                : '<p style="font-size:.85rem;color:#495057;margin:0 0 .75rem;">Uma janela da Receita Federal foi aberta. Siga os passos:</p>'
                  + '<ol style="font-size:.82rem;color:#495057;padding-left:1.1rem;margin:0 0 .85rem;line-height:1.8;"><li>Digite o CPF no campo da Receita</li><li>Informe sua <b>data de nascimento</b></li><li>Resolva o <b>captcha</b> ("Sou humano")</li><li>Verifique a situação cadastral</li></ol>')
            + '<div style="background:#f8f9fa;border:1px solid #dee2e6;border-radius:6px;padding:.5rem .85rem;display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">'
            + '<span style="font-weight:700;letter-spacing:.04em;font-size:.95rem;">' + cpfFormatado + '</span>'
            + '<button id="_btn_copiar_cpf" style="background:none;border:none;cursor:pointer;color:#1351b4;font-size:.8rem;padding:0;white-space:nowrap;"><i class="bi bi-clipboard"></i> Copiar</button></div>'
            + '<button id="_btn_abrir_receita" style="width:100%;background:#1351b4;color:#fff;border:none;border-radius:6px;padding:.55rem;font-size:.85rem;cursor:pointer;font-weight:500;">'
            + '<i class="bi bi-box-arrow-up-right me-1"></i>' + (bloqueado ? 'Abrir janela da Receita' : 'Reabrir janela da Receita') + '</button></div>';

        document.body.appendChild(painel);

        document.getElementById('_painel_fechar').addEventListener('click', function () {
            painel.remove(); if (popupRef && !popupRef.closed) popupRef.close();
        });

        document.getElementById('_btn_copiar_cpf').addEventListener('click', function () {
            copiarTexto(cpfFormatado.replace(/\D/g, ''), this);
        });

        document.getElementById('_btn_abrir_receita').addEventListener('click', function () {
            if (popupRef && !popupRef.closed) { popupRef.focus(); return; }
            var novoPop = window.open('https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaPublica.asp', 'ReceitaCPF', 'width=860,height=620,top=80,left=200,scrollbars=yes,resizable=yes');
            if (!novoPop) { toast('Ainda bloqueado. Clique no ícone 🚫 na barra do navegador e permita pop-ups.', 'warning'); }
            else {
                popupRef = novoPop;
                this.innerHTML = '<i class="bi bi-box-arrow-up-right me-1"></i>Reabrir janela da Receita';
                var aviso = painel.querySelector('[style*="fff3cd"]'); if (aviso) aviso.remove();
            }
        });

        if (popupRef && !popupRef.closed) {
            var monitor = setInterval(function () {
                if (!popupRef || popupRef.closed) {
                    clearInterval(monitor);
                    setTimeout(function () { if (document.getElementById('_painel_cpf')) painel.remove(); }, 1500);
                }
            }, 800);
        }
    }

})();

