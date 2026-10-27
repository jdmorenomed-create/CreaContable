<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - CreaContable</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #0b0c10;
            color: #f3f4f6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* BARRA SUPERIOR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
            background-color: #121318;
            border-bottom: 1px solid #1f222e;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        /* BRANDING DEL LOGO CON LÍNEAS/FLECHAS */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-box {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25);
        }

        .logo-text-wrapper {
            display: flex;
            flex-direction: column;
        }

        .logo-brand-text {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
        }

        .logo-crea {
            color: #ffffff;
            font-style: italic;
            font-family: 'Georgia', 'Times New Roman', serif;
        }

        .logo-contable {
            color: #f59e0b;
        }

        .logo-arrows {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 3px;
        }

        .logo-arrow-line {
            height: 2px;
            background-color: #f59e0b;
            position: relative;
            border-radius: 2px;
        }

        .logo-arrow-line::after {
            content: '';
            position: absolute;
            right: -2px;
            top: -2px;
            width: 0;
            height: 0;
            border-top: 3px solid transparent;
            border-bottom: 3px solid transparent;
            border-left: 5px solid #f59e0b;
        }

        .logo-arrow-line.top {
            width: 100%;
        }

        .logo-arrow-line.bottom {
            width: 80%;
        }

        .btn-volver {
            text-decoration: none;
            color: #9ca3af;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .btn-volver:hover {
            color: #f59e0b;
        }

        /* FORMULARIO */
        .register-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .register-card {
            background-color: #161820;
            border: 1px solid #252836;
            border-radius: 16px;
            padding: 40px 35px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            text-align: center;
        }

        .register-card h2 {
            color: #f59e0b;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .register-subtitle {
            color: #8b92a5;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: #9ca3af;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            background-color: #0f1015;
            border: 1px solid #232635;
            border-radius: 8px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #f59e0b;
        }

        .form-group select option {
            background-color: #161820;
            color: #ffffff;
        }

        .password-help {
            display: block;
            margin-top: 8px;
            color: #8b92a5;
            font-size: 0.78rem;
            line-height: 1.45;
        }

        .password-rules {
            list-style: none;
            margin: 10px 0 0;
            padding: 10px 12px;
            border: 1px solid #34394b;
            border-radius: 8px;
            background: #111722;
            text-align: left;
            display: grid;
            gap: 6px;
        }
        .password-rules li { color: #ff5a70; font-size: .78rem; line-height: 1.35; display: flex; gap: 7px; align-items: flex-start; }
        .password-rules li .rule-icon { font-weight: 800; min-width: 13px; }
        .password-rules li.valid { color: #34d399; }
        .password-rules li.valid .rule-icon { color: #34d399; }
        .password-rules li.valid .rule-icon::before { content: '✓'; }
        .password-rules li.valid .rule-icon { font-size: 0; }
        .password-rules li.valid .rule-icon::before { font-size: .9rem; }

        .btn-registrar {
            width: 100%;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0d0e12;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            transition: opacity 0.2s;
        }

        .btn-registrar:hover {
            opacity: 0.9;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #8b92a5;
        }

        .login-link a {
            color: #f59e0b;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
    </style>

    <style id="tratamiento-datos-styles">
      .password-field{position:relative;width:100%;}
      .password-field input{width:100%;box-sizing:border-box;padding-right:48px!important;}
      .password-toggle{position:absolute;right:8px;top:50%;transform:translateY(-50%);width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:7px;background:transparent;color:#94a3b8;cursor:pointer;padding:6px;z-index:2;}
      .password-toggle:hover{background:rgba(148,163,184,.14);color:#f59e0b;}
      .password-toggle:focus-visible{outline:2px solid #f59e0b;outline-offset:1px;}
      .password-toggle svg{width:20px;height:20px;display:block;pointer-events:none;}

      .privacy-menu-link{appearance:none;background:transparent;border:1px solid #f59e0b;color:#f59e0b;padding:9px 13px;border-radius:8px;font:500 .9rem 'Segoe UI',Arial,sans-serif;text-decoration:none;cursor:pointer;white-space:nowrap;display:inline-flex;align-items:center;justify-content:center;gap:6px}
      .privacy-menu-link:hover{background:rgba(245,158,11,.12);color:#ffc107}
      .privacy-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.72);display:none;align-items:center;justify-content:center;padding:20px;z-index:10000}
      .privacy-modal-overlay.is-open{display:flex}
      .privacy-modal{width:min(560px,100%);background:#161820;color:#f3f4f6;border:1px solid #34394b;border-radius:16px;box-shadow:0 24px 80px rgba(0,0,0,.55);padding:26px;position:relative}
      .privacy-modal h2{color:#f59e0b;font-size:1.35rem;margin:0 40px 16px 0}
      .privacy-modal p{font-size:.98rem;line-height:1.7;color:#e5e7eb;margin-bottom:22px}
      .privacy-modal-close{position:absolute;right:16px;top:14px;border:0;background:transparent;color:#d1d5db;font-size:1.8rem;line-height:1;cursor:pointer;padding:4px 8px}
      .privacy-modal-actions{display:flex;justify-content:flex-end}
      .privacy-modal-ok{border:0;border-radius:8px;background:#f59e0b;color:#111827;font-weight:700;padding:10px 20px;cursor:pointer}
    </style>


    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/theme.css">
    <script src="<?php echo URLROOT; ?>/public/js/theme.js" defer></script>
</head>
<body>

    <nav class="navbar">
        <a href="<?php echo URLROOT; ?>/home" class="logo-container">
            <div class="logo-box">🧮</div>
            <div class="logo-text-wrapper">
                <div class="logo-brand-text">
                    <span class="logo-crea">Crea</span><span class="logo-contable">Contable</span>
                </div>
                <div class="logo-arrows">
                    <div class="logo-arrow-line top"></div>
                    <div class="logo-arrow-line bottom"></div>
                </div>
            </div>
        </a>
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <button type="button" class="privacy-menu-link" onclick="abrirModalTratamiento(event)">Tratamiento de datos</button>
            <a href="<?php echo URLROOT; ?>/home" class="btn-volver">← Volver al Inicio</a>
        </div>
    </nav>

    <div class="register-container">
        <div class="register-card">
            <h2>Crear una Cuenta</h2>
            <p class="register-subtitle">Únete a CreaContable hoy mismo</p>

            <?php if (!empty($data['error'])): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo URLROOT; ?>/auth/register" method="POST">
                <div class="form-group">
                    <label for="rol">Tipo de Usuario:</label>
                    <select id="rol" name="rol_id" required>
                        <option value="2">Cliente (Persona / Empresa)</option>
                        <option value="1">Contador</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ingresa tu nombre" value="<?php echo isset($data['nombre']) ? $data['nombre'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required placeholder="Ingresa tu apellido" value="<?php echo isset($data['apellido']) ? $data['apellido'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com" value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" inputmode="numeric" autocomplete="tel-national" pattern="[0-9]*" maxlength="15" placeholder="Ej. 3001234567" value="<?php echo htmlspecialchars((string)($data['telefono'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" aria-describedby="telefono-ayuda">
                    <small class="password-help" id="telefono-ayuda">Solo números, sin espacios ni guiones.</small>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                           placeholder="Crea una contraseña segura" minlength="8"
                           pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9\s]).{8,}"
                           title="Usa mínimo 8 caracteres, una mayúscula, un número y un carácter especial."
                           aria-describedby="password-help password-rules">
                        <button type="button" class="password-toggle" data-toggle-password="password" aria-label="Mostrar contraseña" aria-pressed="false" title="Mostrar contraseña">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <small class="password-help" id="password-help">La contraseña debe cumplir estos cuatro requisitos:</small>
                    <ul class="password-rules" id="password-rules" aria-live="polite">
                        <li data-rule="length"><span class="rule-icon">✕</span> Mínimo 8 caracteres</li>
                        <li data-rule="uppercase"><span class="rule-icon">✕</span> Una letra mayúscula</li>
                        <li data-rule="number"><span class="rule-icon">✕</span> Un número</li>
                        <li data-rule="special"><span class="rule-icon">✕</span> Un carácter especial (@, $, !, etc.)</li>
                    </ul>
                </div>

                <button type="submit" class="btn-registrar">Registrarse</button>
            </form>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="<?php echo URLROOT; ?>/auth/login">Inicia sesión aquí</a>
            </div>
        </div>
    </div>


<div class="privacy-modal-overlay" id="modalTratamientoDatos" role="dialog" aria-modal="true" aria-labelledby="tituloTratamientoDatos" onclick="cerrarModalTratamientoFuera(event)">
  <div class="privacy-modal" role="document">
    <button type="button" class="privacy-modal-close" aria-label="Cerrar ventana" onclick="cerrarModalTratamiento()">&times;</button>
    <h2 id="tituloTratamientoDatos">Tratamiento de datos</h2>
    <p>El presente proyecto tiene fines exclusivamente académicos. Los datos utilizados durante el desarrollo, las pruebas y la demostración del sistema son ficticios y fueron creados únicamente con fines de simulación. El sistema no recolecta, almacena ni procesa datos personales reales de estudiantes, docentes o terceros.</p>
    <div class="privacy-modal-actions"><button type="button" class="privacy-modal-ok" onclick="cerrarModalTratamiento()">Entendido</button></div>
  </div>
</div>
<script id="tratamiento-datos-script">
function abrirModalTratamiento(event){if(event)event.preventDefault();var modal=document.getElementById('modalTratamientoDatos');if(modal){modal.classList.add('is-open');document.body.style.overflow='hidden';var close=modal.querySelector('.privacy-modal-close');if(close)close.focus();}}
function cerrarModalTratamiento(){var modal=document.getElementById('modalTratamientoDatos');if(modal){modal.classList.remove('is-open');document.body.style.overflow='';}}
function cerrarModalTratamientoFuera(event){if(event.target===event.currentTarget)cerrarModalTratamiento();}
document.addEventListener('keydown',function(event){if(event.key==='Escape')cerrarModalTratamiento();});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action$="/auth/register"]');
    const password = document.getElementById('password');
    const phone = document.getElementById('telefono');
    const rules = {
        length: value => value.length >= 8,
        uppercase: value => /[A-Z]/.test(value),
        number: value => /[0-9]/.test(value),
        special: value => /[^A-Za-z0-9\s]/.test(value)
    };

    function validatePassword() {
        if (!password) return false;
        const value = password.value;
        let valid = true;
        Object.keys(rules).forEach(function (name) {
            const item = document.querySelector('[data-rule="' + name + '"]');
            const passed = rules[name](value);
            if (item) {
                item.classList.toggle('valid', passed);
                const icon = item.querySelector('.rule-icon');
                if (icon) icon.textContent = passed ? '✓' : '✕';
            }
            if (!passed) valid = false;
        });
        password.setCustomValidity(valid ? '' : 'La contraseña debe cumplir los cuatro requisitos indicados.');
        return valid;
    }

    if (password) {
        password.addEventListener('input', validatePassword);
        password.addEventListener('blur', validatePassword);
    }
    if (phone) {
        phone.addEventListener('input', function () {
            const cleaned = phone.value.replace(/[^0-9]/g, '');
            if (phone.value !== cleaned) phone.value = cleaned;
            phone.setCustomValidity('');
        });
        phone.addEventListener('paste', function () {
            setTimeout(function () { phone.value = phone.value.replace(/[^0-9]/g, ''); }, 0);
        });
    }
    if (form) {
        form.addEventListener('submit', function (event) {
            const passwordOk = validatePassword();
            const phoneOk = !phone || phone.value === '' || /^[0-9]+$/.test(phone.value);
            if (!passwordOk || !phoneOk) {
                event.preventDefault();
                if (!passwordOk && password) password.reportValidity();
                else if (!phoneOk && phone) phone.reportValidity();
            }
        });
    }
});
</script>


<script id="password-visibility-script">
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.getAttribute('data-toggle-password'));
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.setAttribute('aria-pressed', show ? 'true' : 'false');
            button.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
            button.setAttribute('title', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
            button.innerHTML = show
                ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a16.7 16.7 0 0 1-3.1 3.9"/><path d="M6.6 6.6C3.7 8.3 2 12 2 12s3.6 7 10 7a10.8 10.8 0 0 0 4-.8"/></svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
        });
    });
});
</script>

</body>
</html>