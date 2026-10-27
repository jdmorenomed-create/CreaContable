<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userNombre = $_SESSION['user_nombre'] ?? 'Cliente';
$userEmail  = $_SESSION['user_email'] ?? '';
$userId     = $_SESSION['user_id'] ?? 2;
$contadorId = $data['contadorId'] ?? 1;

$inicial = !empty($userNombre) ? strtoupper(substr(trim($userNombre), 0, 1)) : 'C';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Cliente - CreaContable</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        :root {
            --bg-color: #0b0c10;
            --card-bg: #161820;
            --text-color: #f3f4f6;
            --subtext-color: #8b92a5;
            --navbar-bg: #121318;
            --border-color: #252836;
            --input-bg: #0f1015;
            --table-header-bg: #1c1f2b;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        /* BARRA SUPERIOR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
            background-color: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

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

        .logo-crea { color: #ffffff; font-style: italic; font-family: 'Georgia', serif; }
        .logo-contable { color: #f59e0b; }

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

        .logo-arrow-line.top { width: 100%; }
        .logo-arrow-line.bottom { width: 80%; }

        /* TABS DE NAVEGACIÓN */
        .nav-tabs {
            display: flex;
            gap: 10px;
        }

        .tab-btn {
            padding: 9px 18px;
            background-color: #1a1d26;
            color: var(--subtext-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: #ffffff;
            background-color: #242938;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            border-color: #f59e0b;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        /* SECCIÓN DE USUARIO / PERFIL */
        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar-container {
            position: relative;
            display: inline-block;
        }

        .avatar-btn {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            border: 2px solid var(--border-color);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 55px;
            background-color: var(--card-bg);
            width: 280px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            border-radius: 14px;
            padding: 20px;
            z-index: 1000;
            border: 1px solid var(--border-color);
        }

        .user-dropdown.show { display: block; }

        .dropdown-profile-box {
            background-color: var(--input-bg);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
        }

        .dropdown-avatar-big {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.6rem;
            margin: 0 auto 10px auto;
        }

        .dropdown-name { font-size: 1.05rem; font-weight: bold; color: var(--text-color); }
        .dropdown-email { font-size: 0.82rem; color: var(--subtext-color); margin-top: 2px; word-break: break-all; }

        .dropdown-item-link.logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px;
            text-decoration: none;
            color: #ef4444;
            font-size: 0.9rem;
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 8px;
            font-weight: 600;
        }

        .seccion-contenido { display: none; width: 100%; }
        .seccion-contenido.active { display: block; }

        .container {
            max-width: 1250px;
            margin: 30px auto;
            padding: 0 20px;
            width: 100%;
        }

        .card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
            width: 100%;
        }

        /* FORMULARIOS */
        .form-group { margin-bottom: 15px; }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: var(--subtext-color);
            margin-bottom: 6px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 11px 16px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-color);
            font-size: 0.92rem;
            outline: none;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: #f59e0b; }

        .btn-enviar {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            padding: 11px 22px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            transition: opacity 0.2s;
        }

        .btn-enviar:hover { opacity: 0.9; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
        }

        table th {
            background-color: var(--table-header-bg);
            font-size: 0.85rem;
            color: var(--subtext-color);
            text-transform: uppercase;
        }

        /* WIDGET Y CHAT FLOTANTE */
        .widget-chat-flotante {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.7);
            cursor: pointer;
            z-index: 9999;
        }

        .badge-flotante {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 0.75rem;
            display: none;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid #0b0c10;
        }

        .chat-modal-flotante {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 440px;
            height: 560px;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.8);
            z-index: 10000;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-modal-flotante.show { display: flex; }

        .chat-modal-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }

        .chat-modal-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
            background-color: var(--bg-color);
        }

        .chat-modal-footer {
            padding: 12px 15px;
            background-color: var(--card-bg);
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 10px;
        }

        .chat-modal-input {
            flex: 1;
            padding: 10px 16px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 25px;
            color: var(--text-color);
            outline: none;
            font-size: 0.92rem;
        }

        .chat-modal-send {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .msg-bubble {
            max-width: 75%;
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 0.9rem;
            line-height: 1.4;
            word-break: break-word;
        }

        .msg-bubble.incoming {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            align-self: flex-start;
        }

        .msg-bubble.outgoing {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            font-weight: 500;
            align-self: flex-end;
        }
    
        .consulta-filtros { display:flex; flex-wrap:wrap; gap:8px; margin:0 0 14px; }
        .consulta-filtro { border:1px solid var(--border-color); background:var(--card-bg); color:var(--text-color); border-radius:8px; padding:9px 14px; cursor:pointer; font-weight:600; }
        .consulta-filtro:hover, .consulta-filtro.active { background:#0d6efd; border-color:#0d6efd; color:#fff; }
        @media (max-width: 640px) { .consulta-filtros { gap:6px; } .consulta-filtro { flex:1 1 auto; } }
</style>

    <style id="tratamiento-datos-styles">
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
        <div style="display: flex; align-items: center; gap: 30px;">
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
            
            <div class="nav-tabs">
                <button type="button" id="btn-tab-solicitudes" class="tab-btn active" onclick="mostrarSeccion('solicitudes', this)">
                    📋 Mis Solicitudes
                </button>
                <button type="button" id="btn-tab-citas" class="tab-btn" onclick="mostrarSeccion('citas', this)">
                    📅 Mis Citas
                </button>
                <?php if ((int)($_SESSION['user_rol'] ?? 2) === 3): ?><a class="tab-btn cc-top-services-btn" href="<?php echo URLROOT; ?>/marketplace/admin" style="text-decoration:none;display:inline-flex;align-items:center;background:#f59e0b;color:#171717;border-color:#f59e0b;font-weight:800">🛡️ Administración</a><?php else: ?><a class="tab-btn cc-top-services-btn" href="<?php echo URLROOT; ?>/marketplace/servicios" style="text-decoration:none;display:inline-flex;align-items:center;background:#f59e0b;color:#171717;border-color:#f59e0b;font-weight:800">🧾 Centro de servicios</a><?php endif; ?>
            </div>
        </div>

        <div class="user-section">
            <div class="user-avatar-container">
                <button type="button" class="avatar-btn" id="avatarHomeBtn" onclick="toggleHomeDropdown()" title="Perfil de usuario">
                    <?php echo $inicial; ?>
                </button>

                <div class="user-dropdown" id="userHomeDropdown">
                    <div class="dropdown-profile-box">
                        <div class="dropdown-avatar-big">
                            <?php echo $inicial; ?>
                        </div>
                        <div class="dropdown-name"><?php echo htmlspecialchars($userNombre); ?></div>
                        <div class="dropdown-email"><?php echo htmlspecialchars($userEmail); ?></div>
                    </div>

                    <a href="<?php echo URLROOT; ?>/auth/logout" class="dropdown-item-link logout">
                        🚪 Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- SECCIÓN 1: SOLICITUDES (CREAR CONSULTA + HISTORIAL) -->
        <div id="seccion-solicitudes" class="seccion-contenido active">
            
            <!-- CUADRO PARA CREAR NUEVA CONSULTA -->
            <div class="card">
                <h3 style="color: #f59e0b; margin-bottom: 15px; font-size: 1.2rem;">💬 Crear Nueva Consulta o Solicitud</h3>
                <form action="<?php echo URLROOT; ?>/consultas/crear" method="POST">
                    <div class="form-group">
                        <label>Asunto / Título:</label>
                        <input type="text" name="titulo" required placeholder="Ej. Pagos de la DIAN">
                    </div>
                    <div class="form-group">
                        <label>Detalle / Mensaje:</label>
                        <textarea name="detalle" rows="3" required placeholder="Describe tu duda o trámite contable..."></textarea>
                    </div>
                    <button type="submit" class="btn-enviar">Enviar Solicitud</button>
                </form>
            </div>

            <!-- TABLA DE HISTORIAL DE CONSULTAS -->
            <div class="card">
                <h3 style="color: #f59e0b; margin-bottom: 20px; font-size: 1.2rem;">📄 Historial de Solicitudes y Consultas</h3>
                <?php if (!empty($data['consultas'])): ?>
                    <label for="buscarSolicitudesCliente" style="display:block;margin:10px 0 6px;font-weight:600">Buscar consultas</label><input id="buscarSolicitudesCliente" type="search" placeholder="Buscar por título, detalle o estado..." oninput="buscarSolicitudesCliente()" style="width:100%;max-width:460px;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;margin-bottom:12px">
                    <div class="consulta-filtros" role="group" aria-label="Filtrar mis solicitudes">
                        <button type="button" class="consulta-filtro active" data-filtro="todas" onclick="filtrarSolicitudesCliente('todas', this)">Todas</button>
                        <button type="button" class="consulta-filtro" data-filtro="pendiente" onclick="filtrarSolicitudesCliente('pendiente', this)">Pendientes</button>
                        <button type="button" class="consulta-filtro" data-filtro="respondido" onclick="filtrarSolicitudesCliente('respondido', this)">Respondidas</button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Asunto / Título</th>
                                <th>Detalle / Mensaje</th>
                                <th>Estado</th>
                                <th>Conversación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['consultas'] as $con): ?>
                                <?php $estadoConsulta = $con['estado_cliente'] ?? $con['estado'] ?? 'Pendiente'; $respondida = in_array(strtolower(trim((string)$estadoConsulta)), ['respondido', 'respondida'], true); ?>
                                <tr data-estado-solicitud="<?php echo $respondida ? 'respondido' : 'pendiente'; ?>">
                                    <td><strong><?php echo htmlspecialchars($con['titulo'] ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars($con['detalle'] ?? ''); ?></td>
                                    <td><span style="color: <?php echo $respondida ? '#16a34a' : '#f59e0b'; ?>; font-weight: bold;"><?php echo $respondida ? '✓ Respondido' : '◷ Pendiente'; ?></span></td>
                                    <td><a class="btn-enviar" style="display:inline-block;text-decoration:none" href="<?php echo URLROOT; ?>/consultas/responder/<?php echo (int)$con['id']; ?>">Abrir chat</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--subtext-color);">No tienes solicitudes registradas.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- SECCIÓN 2: CITAS (AGENDAR CITA + HISTORIAL) -->
        <div id="seccion-citas" class="seccion-contenido">
            
            <!-- CUADRO PARA AGENDAR NUEVA CITA -->
            <div class="card">
                <h3 style="color: #f59e0b; margin-bottom: 15px; font-size: 1.2rem;">📅 Agendar Nueva Cita Virtual</h3>
                <form action="<?php echo URLROOT; ?>/citas/crear" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fecha de la Cita:</label>
                            <input type="date" name="fecha" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Hora:</label>
                            <input type="time" name="hora" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Motivo / Asunto de la Asesoría:</label>
                        <textarea name="motivo" rows="2" required placeholder="Explica brevemente el tema que deseas tratar en la asesoría..."></textarea>
                    </div>
                    <button type="submit" class="btn-enviar">Agendar Cita</button>
                </form>
            </div>

            <!-- TABLA DE CITAS AGENDADAS -->
            <div class="card">
                <h3 style="color: #f59e0b; margin-bottom: 20px; font-size: 1.2rem;">📅 Mis Citas Agendadas</h3>
                <?php if (!empty($data['citas'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['citas'] as $cita): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cita['fecha'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($cita['hora'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($cita['motivo'] ?? ''); ?></td>
                                    <td><span style="color: #f59e0b; font-weight: bold;"><?php echo htmlspecialchars($cita['estado'] ?? 'Pendiente'); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--subtext-color);">No tienes citas agendadas actualmente.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const miUsuarioId = <?php echo json_encode($userId); ?>;
        const contadorId  = <?php echo json_encode($contadorId); ?>;
        let chatAbierto   = false;

        function toggleHomeDropdown() {
            const dropdown = document.getElementById('userHomeDropdown');
            if (dropdown) dropdown.classList.toggle('show');
        }

        window.addEventListener('click', function(e) {
            const avatarBtn = document.getElementById('avatarHomeBtn');
            const dropdown  = document.getElementById('userHomeDropdown');
            if (avatarBtn && dropdown && !avatarBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        function mostrarSeccion(seccion, elemento) {
            document.querySelectorAll('.seccion-contenido').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

            const target = document.getElementById('seccion-' + seccion);
            if (target) target.classList.add('active');
            if (elemento) elemento.classList.add('active');
        }

        async function comprobarRespuestas() {
            try {
                const res = await fetch('<?php echo URLROOT; ?>/consultas/verificarRespuestas', {
                    credentials: 'same-origin'
                });
                const contentType = res.headers.get("content-type");
                if (!contentType || contentType.indexOf("application/json") === -1) return;

                const data = await res.json();
                const badge = document.getElementById('badgeWidgetCount');
                if (!badge) return;
                
                if (data.mensajes_no_leidos > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = data.mensajes_no_leidos;
                } else {
                    badge.style.display = 'none';
                }

                
            } catch (e) {
                // Silenciar errores menores
            }
        }

        setInterval(comprobarRespuestas, 2500);
        comprobarRespuestas();
    </script>

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
function filtrarSolicitudesCliente(filtro, boton) {
    document.querySelectorAll('.consulta-filtro').forEach(function(b) { b.classList.toggle('active', b === boton); });
    document.querySelectorAll('tr[data-estado-solicitud]').forEach(function(fila) {
        fila.hidden = filtro !== 'todas' && fila.dataset.estadoSolicitud !== filtro;
    });
}
</script>

<script>function buscarSolicitudesCliente(){const q=(document.getElementById('buscarSolicitudesCliente')?.value||'').toLocaleLowerCase('es');document.querySelectorAll('tr[data-estado-solicitud]').forEach(r=>{r.style.display=r.innerText.toLocaleLowerCase('es').includes(q)?'':'none';});}</script>

<?php
$ccRol = (int)($_SESSION['user_rol'] ?? 2);
$ccNombre = $_SESSION['user_nombre'] ?? 'Usuario';
?>


</body>
</html>