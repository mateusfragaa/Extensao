<?php

if (!function_exists('jsQuillEditor')) {
    function jsQuillEditor(string $editorId, string $fieldId, $initValue = null): string
    {
        static $cssIncluded = false;
        static $scriptIncluded = false;

        $html = '';

        if (!$cssIncluded) {
            $html .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">' . "\n";
            $cssIncluded = true;
        }

        if (!$scriptIncluded) {
            $html .= '<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>' . "\n";
            $scriptIncluded = true;
        }

        $initHtml = html_entity_decode((string)($initValue ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $json = json_encode($initHtml);
        $var  = '_quill_' . preg_replace('/\W/', '_', $fieldId);

        $html .= <<<JS
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window._fasm = window._fasm || { quill: {}, cleave: {} };
        const {$var} = new Quill('#{$editorId}', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ script: 'super' }, { script: 'sub' }],
                    [{ color: [] }, { background: [] }],
                    [{ font: [] }],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ indent: '-1' }, { indent: '+1' }],
                    [{ align: [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });
        const _init_{$var} = {$json};
        if (_init_{$var}) {
            {$var}.root.innerHTML = _init_{$var};
        }
        window._fasm.quill['{$fieldId}'] = {$var};
    });
</script>

JS;

        return $html;
    }
}

if (!function_exists('jsCleaveNumeral')) {
    /**
     * Máscara de entrada numérica com separador decimal vírgula.
     *
     * @param string $displayId  ID do input visível ao usuário
     * @param string $fieldId    ID do input hidden que recebe o valor para o POST
     * @param int    $decimals   Número de casas decimais (padrão: 2)
     * @param string $prefix     Prefixo opcional, ex: 'R$ '
     */
    function jsCleaveNumeral(string $displayId, string $fieldId, int $decimals = 2, string $prefix = ''): string
    {
        static $scriptIncluded = false;

        $html = '';

        if (!$scriptIncluded) {
            $html .= '<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>' . "\n";
            $scriptIncluded = true;
        }

        $options = [
            'numeral'             => true,
            'numeralDecimalMark'  => ',',
            'delimiter'           => '.',
            'numeralDecimalScale' => $decimals,
            'numeralPositiveOnly' => true,
        ];

        if ($prefix !== '') {
            $options['prefix']            = $prefix;
            $options['noImmediatePrefix'] = true;
        }

        $jsonOptions = json_encode($options);
        $var         = '_cleave_' . preg_replace('/\W/', '_', $fieldId);

        $html .= <<<JS
<script>
    window._fasm = window._fasm || { quill: {}, cleave: {} };
    const {$var} = new Cleave('#{$displayId}', {$jsonOptions});
    const _initVal_{$var} = document.getElementById('{$fieldId}').value;
    if (_initVal_{$var} !== '') {
        {$var}.setRawValue(_initVal_{$var}.replace('.', ','));
    }
    window._fasm.cleave['{$fieldId}'] = {$var};
</script>

JS;

        return $html;
    }
}

if (!function_exists('jsPasswordStrength')) {
    /**
     * Renderiza o indicador visual de força de senha, verificação de confirmação
     * e controle do botão Enviar.
     * Os critérios são lidos de App\Config\PasswordConfig — front e back usam as
     * mesmas regras sem nenhuma duplicação.
     *
     * @param string $senhaId       ID do input de senha
     * @param string $confirmId     ID do input de confirmação
     * @param bool   $senhaRequired true quando senha é obrigatória (insert); false no update
     */
    function jsPasswordStrength(string $senhaId, string $confirmId, bool $senhaRequired = false): string
    {
        $config        = json_encode(\App\Config\PasswordConfig::getConfig());
        $jsRequired    = $senhaRequired ? 'true' : 'false';

        ob_start(); ?>

<div class="row mt-1" id="pwd-ui-block" style="display:none;">
    <div class="col-5">
        <div class="progress mb-1" style="height:6px;">
            <div id="pwd-bar" class="progress-bar" role="progressbar"
                 style="width:0%;transition:width .35s ease;"></div>
        </div>
        <span id="pwd-bar-label" class="fw-semibold small"></span>
        <ul id="pwd-checklist" class="list-unstyled mb-0 mt-1 small"></ul>
    </div>
    <div class="col-5 d-flex align-items-center">
        <span id="pwd-match-label" class="fw-semibold small"></span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cfg           = <?= $config ?>;
    const senhaRequired = <?= $jsRequired ?>;
    const senhaEl       = document.getElementById('<?= $senhaId ?>');
    const confirmEl     = document.getElementById('<?= $confirmId ?>');
    const uiBlock       = document.getElementById('pwd-ui-block');
    const bar           = document.getElementById('pwd-bar');
    const barLabel      = document.getElementById('pwd-bar-label');
    const checklist     = document.getElementById('pwd-checklist');
    const matchLabel    = document.getElementById('pwd-match-label');
    const submitBtn     = document.querySelector('button[type="submit"]');

    const levels = [
        { text: 'Muito fraca', barCls: 'bg-danger',   txtCls: 'text-danger',   pct: 20  },
        { text: 'Fraca',       barCls: 'bg-warning',  txtCls: 'text-warning',  pct: 40  },
        { text: 'Razoável',    barCls: 'bg-info',     txtCls: 'text-info',     pct: 60  },
        { text: 'Forte',       barCls: 'bg-primary',  txtCls: 'text-primary',  pct: 80  },
        { text: 'Muito forte', barCls: 'bg-success',  txtCls: 'text-success',  pct: 100 },
    ];

    // Monta apenas os critérios habilitados em PasswordConfig
    const checks = [];
    if (cfg.minLength > 0)    checks.push({ key: 'length',  label: 'Mínimo ' + cfg.minLength + ' caracteres', test: s => s.length >= cfg.minLength });
    if (cfg.requireUppercase) checks.push({ key: 'upper',   label: 'Letra maiúscula (A–Z)',                   test: s => /[A-Z]/.test(s) });
    if (cfg.requireLowercase) checks.push({ key: 'lower',   label: 'Letra minúscula (a–z)',                   test: s => /[a-z]/.test(s) });
    if (cfg.requireNumber)    checks.push({ key: 'number',  label: 'Número (0–9)',                            test: s => /[0-9]/.test(s) });
    if (cfg.requireSpecial)   checks.push({ key: 'special', label: 'Caractere especial (!@#$…)',              test: s => /[^A-Za-z0-9]/.test(s) });

    checks.forEach(function (c) {
        const li = document.createElement('li');
        li.id = 'pwd-chk-' + c.key;
        checklist.appendChild(li);
    });

    function renderCheck(c, ok) {
        const li = document.getElementById('pwd-chk-' + c.key);
        li.innerHTML = (ok
            ? '<span class="text-success">✓</span>'
            : '<span class="text-danger">✗</span>') + ' ' + c.label;
    }

    // Bloqueia ou libera o botão Enviar conforme o estado atual dos campos
    function updateSubmitState(allChecksPassed, passwordsMatch) {
        if (!submitBtn) return;
        const val = senhaEl ? senhaEl.value : '';

        let block = false;
        if (senhaRequired && !val) {
            block = true;                            // senha obrigatória e vazia
        } else if (val) {
            block = !allChecksPassed || !passwordsMatch; // senha preenchida mas inválida
        }
        // senha vazia em update (opcional) → não bloqueia

        submitBtn.disabled = block;
        submitBtn.style.opacity = block ? '0.5' : '';
        submitBtn.style.cursor  = block ? 'not-allowed' : '';
    }

    function updateStrength() {
        const val = senhaEl.value;
        if (!val) {
            uiBlock.style.display = 'none';
            updateSubmitState(false, true);
            return;
        }
        uiBlock.style.display = '';

        let passed = 0;
        checks.forEach(function (c) {
            const ok = c.test(val);
            if (ok) passed++;
            renderCheck(c, ok);
        });

        const total      = checks.length;
        const allPassed  = passed === total;

        // "Muito forte" só quando TODOS os critérios são atendidos
        const maxIdx = allPassed ? levels.length - 1 : levels.length - 2;
        const idx    = total > 0
            ? Math.min(Math.floor((passed / total) * levels.length), maxIdx)
            : 0;
        const lvl = levels[idx];

        bar.className        = 'progress-bar ' + lvl.barCls;
        bar.style.width      = lvl.pct + '%';
        barLabel.className   = 'fw-semibold small ' + lvl.txtCls;
        barLabel.textContent = lvl.text;

        const confirmVal = confirmEl ? confirmEl.value : '';
        updateMatch(allPassed);
        // se senha preenchida, a confirmação deve ser igual (even quando vazia)
        updateSubmitState(allPassed, val === confirmVal);
    }

    function updateMatch(allChecksPassed) {
        const senhaVal   = senhaEl ? senhaEl.value : '';
        const confirmVal = confirmEl ? confirmEl.value : '';

        if (!confirmVal) {
            matchLabel.textContent = '';
        } else {
            const ok = senhaVal === confirmVal;
            matchLabel.className   = 'fw-semibold small ' + (ok ? 'text-success' : 'text-danger');
            matchLabel.textContent = ok ? '✓ Senhas conferem' : '✗ Senhas não conferem';
        }

        // Recalcula allChecksPassed quando chamado pelo evento do confirmEl
        if (allChecksPassed === undefined) {
            const passed = checks.filter(c => c.test(senhaVal)).length;
            allChecksPassed = passed === checks.length;
        }
        updateSubmitState(allChecksPassed, senhaVal === confirmVal);
    }

    // Estado inicial do botão
    updateSubmitState(false, true);

    if (senhaEl)   senhaEl.addEventListener('input', updateStrength);
    if (confirmEl) confirmEl.addEventListener('input', function () { updateMatch(); });
});
</script>

        <?php
        return ob_get_clean();
    }
}

if (!function_exists('jsFormHandler')) {
    /**
     * Handler genérico de submit: sincroniza os valores de editores Quill
     * e máscaras Cleave registrados em window._fasm para seus campos hidden.
     * Deve ser chamado ao final do formulário.
     */
    function jsFormHandler(): string
    {
        return <<<'JS'
<script>
    document.querySelector('form').addEventListener('submit', function (e) {
        window._fasm = window._fasm || { quill: {}, cleave: {} };

        for (const [fieldId, editor] of Object.entries(window._fasm.quill)) {
            const html = editor.root.innerHTML;
            if (html === '<p><br></p>' || html.trim() === '') {
                e.preventDefault();
                editor.root.focus();
                return;
            }
            document.getElementById(fieldId).value = html;
        }

        for (const [fieldId, cleave] of Object.entries(window._fasm.cleave)) {
            document.getElementById(fieldId).value = cleave.getRawValue().replace(/^[^\d]*/, '');
        }
    });
</script>
JS;
    }
}
