<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Contador - CreaContable</title>
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
            font-family: Arial, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        :root {
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #333333;
            --subtext-color: #666666;
            --navbar-bg: #181818;
            --border-color: #e0e0e0;
            --dropdown-bg: #ffffff;
            --profile-box-bg: #f0f4f9;
            --table-header-bg: #f8f9fa;
            --hero-bg: #0d6efd;
            --input-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --subtext-color: #aaa;
            --navbar-bg: #181818;
            --border-color: #333333;
            --dropdown-bg: #242424;
            --profile-box-bg: #2d2d2d;
            --table-header-bg: #282828;
            --hero-bg: #0d6efd;
            --input-bg: #2a2a2a;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        /* NAVBAR SUPERIOR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 30px;
            background-color: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
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

        .tab-btn {
            padding: 8px 16px;
            background-color: #2b2b2b;
            color: var(--text-color);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn.active {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        /* BANNER HERO AZUL INSTITUCIONAL */
        .hero-banner {
            background-color: var(--hero-bg);
            color: white;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .hero-banner h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .hero-banner p {
            font-size: 1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }

        .seccion-contenido {
            display: none;
        }

        .seccion-contenido.active {
            display: block;
        }

        .container {
            max-width: 1300px;
            width: 95%;
            margin: 30px auto;
            padding: 0 10px;
        }

        .card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
            width: 100%;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

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
            vertical-align: middle;
            word-break: break-word;
        }

        table th {
            background-color: var(--table-header-bg);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--subtext-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cliente-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-avatar {
            width: 38px;
            height: 38px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .contacto-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .contacto-email {
            font-size: 0.88rem;
            color: var(--text-color);
        }

        .contacto-tel {
            font-size: 0.8rem;
            color: var(--subtext-color);
        }

        .badge-estado {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            white-space: nowrap;
        }

        .badge-respondido { background: #dcfce7; color: #166534; }
        .badge-pendiente {
            background-color: rgba(255, 193, 7, 0.15);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .btn-responder {
            background-color: #0d6efd;
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);
        }

        .btn-responder:hover {
            background-color: #0b5ed7;
        }

        /* DISEÑO DEL CHAT CON INTERFAZ AZUL */
        .chat-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            height: min(72vh, 760px);
            min-height: 520px;
            overflow: hidden;
        }

        .chat-sidebar {
            min-height: 0;
            overflow: hidden;
            background-color: var(--table-header-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            font-weight: bold;
            color: #0d6efd;
        }

        .chat-contacts-list { overflow-y: auto; overflow-x: hidden; flex: 1; min-height: 0; scrollbar-width: thin; scrollbar-color: #64748b #202124; }
        .chat-contacts-list::-webkit-scrollbar { width: 10px; }
        .chat-contacts-list::-webkit-scrollbar-track { background: #202124; }
        .chat-contacts-list::-webkit-scrollbar-thumb { background: #64748b; border-radius: 8px; border: 2px solid #202124; }



        .chat-status-toggle { width:100%; border:0; text-align:left; cursor:pointer; font:inherit; }
        .chat-status-toggle:hover { filter:brightness(1.08); }
        .chat-status-list { display:block; min-width:0; }
        #chatMessages .message, #chatMessages .message-bubble {
            max-width:min(78%, 720px);
            min-width:0;
            white-space:pre-wrap;
            overflow-wrap:anywhere;
            word-break:normal;
            box-sizing:border-box;
        }

        .chat-status-heading{display:flex;align-items:center;justify-content:space-between;padding:11px 13px;font-size:.82rem;font-weight:700;position:sticky;top:0;z-index:1;background:var(--card-bg);border-bottom:1px solid var(--border-color)}
        .pending-heading{color:#b77900}
        .answered-heading{color:#15803d;border-top:1px solid var(--border-color)}
        .chat-status-heading span{border-radius:999px;padding:2px 8px;background:var(--profile-box-bg);font-size:.75rem}
        .chat-contact-button{width:100%;text-align:left;color:var(--text-color);background:transparent;border-left:0;border-right:0;border-top:0;font:inherit}
        .chat-contact-copy{display:flex;flex-direction:column;gap:4px;min-width:0}
        .chat-contact-copy strong,.chat-contact-copy small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .chat-contact-copy strong{font-size:.85rem}
        .chat-contact-copy small{font-size:.75rem;color:var(--subtext-color)}
        .chat-empty-list{padding:10px 14px;font-size:.78rem;color:var(--subtext-color)}


        .contact-item {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            border-bottom: 1px solid var(--border-color);
        }

        .contact-item:hover, .contact-item.active {
            background-color: var(--profile-box-bg);
        }

        .chat-main-area {
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-color);
        }

        .chat-header {
            padding: 15px;
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Barra de desplazamiento visible y usable en el chat del contador, igual que en el del cliente. */
        .chat-messages { overflow-y: scroll; min-height: 0; height: 0; scrollbar-width: thin; scrollbar-color: #64748b #202124; }
        .chat-messages::-webkit-scrollbar { width: 10px; }
        .chat-messages::-webkit-scrollbar-track { background: #202124; border-radius: 8px; }
        .chat-messages::-webkit-scrollbar-thumb { background: #64748b; border-radius: 8px; border: 2px solid #202124; }
        .chat-messages::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .message {
            max-width: 65%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.9rem;
            line-height: 1.4;
            word-break: break-word;
        }

        .message.incoming {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            align-self: flex-start;
            color: var(--text-color);
        }

        .message.outgoing {
            background-color: #0d6efd;
            color: white;
            align-self: flex-end;
        }

        .chat-input-area {
            padding: 15px;
            background-color: var(--card-bg);
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 10px 18px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 25px;
            color: var(--text-color);
            outline: none;
        }

        .btn-send {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            border: none;
            cursor: pointer;
        }

        /* AVATAR Y DROPDOWN */
        .user-avatar-container { position: relative; }

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
            transition: transform 0.2s ease;
        }

        .avatar-btn:hover {
            transform: scale(1.05);
        }

        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 55px;
            background-color: var(--dropdown-bg);
            width: 280px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            border-radius: 14px;
            padding: 20px;
            z-index: 1000;
            border: 1px solid var(--border-color);
        }

        .user-dropdown.show { display: block; }

        .dropdown-profile-box {
            background-color: var(--profile-box-bg);
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

        .dropdown-name {
            font-size: 1.05rem;
            font-weight: bold;
            color: var(--text-color);
        }

        .dropdown-email {
            font-size: 0.82rem;
            color: var(--subtext-color);
            margin-top: 2px;
            word-break: break-all;
        }

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
            transition: background-color 0.2s;
        }

        .dropdown-item-link.logout:hover {
            background-color: rgba(239, 68, 68, 0.2);
        }
    
        .consulta-filtros { display:flex; flex-wrap:wrap; gap:8px; margin:0 0 14px; }
        .consulta-filtro-contador { border:1px solid var(--border-color); background:var(--card-bg); color:var(--text-color); border-radius:8px; padding:9px 14px; cursor:pointer; font-weight:600; }
        .consulta-filtro-contador:hover, .consulta-filtro-contador.active { background:#0d6efd; border-color:#0d6efd; color:#fff; }
        @media (max-width: 640px) { .consulta-filtros { gap:6px; } .consulta-filtro-contador { flex:1 1 auto; } }
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

    <?php 
        $userNombre = $_SESSION['user_nombre'] ?? 'Felipe';
        $userEmail  = $_SESSION['user_email'] ?? 'felipe.contador@creacontable.com';
        $inicial    = strtoupper(substr($userNombre, 0, 1));
    ?>

    <!-- NAVBAR SUPERIOR -->
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
            
            <div style="display: flex; gap: 10px;">
                <button type="button" id="btn-tab-consultas" class="tab-btn active" onclick="mostrarSeccion('consultas', this)">
                    💬 Consultas Agendadas
                </button>
                <button type="button" id="btn-tab-citas" class="tab-btn" onclick="mostrarSeccion('citas', this)">
                    📅 Citas Agendadas
                </button>
                <button type="button" id="btn-tab-chat" class="tab-btn" onclick="mostrarSeccion('chat', this)">
                    💬 Chat de clientes
                </button>
                <?php if ((int)($_SESSION['user_rol'] ?? 2) === 3): ?><a class="tab-btn cc-top-services-btn" href="<?php echo URLROOT; ?>/marketplace/admin" style="text-decoration:none;display:inline-flex;align-items:center;background:#f59e0b;color:#171717;border-color:#f59e0b;font-weight:800">🛡️ Administración</a><?php else: ?><a class="tab-btn cc-top-services-btn" href="<?php echo URLROOT; ?>/marketplace/servicios" style="text-decoration:none;display:inline-flex;align-items:center;background:#f59e0b;color:#171717;border-color:#f59e0b;font-weight:800">🧾 Centro de servicios</a><?php endif; ?>
                <a href="<?php echo URLROOT; ?>/foro" class="tab-btn" style="background:#f59e0b;color:#171717;border-color:#f59e0b;">
                    🗣️ Foro profesional
                </a>
            </div>
        </div>

        <div class="user-avatar-container">
            <button type="button" class="avatar-btn" id="avatarBtn" onclick="toggleDropdown()" title="Perfil del contador">
                <?php echo $inicial; ?>
            </button>

            <div class="user-dropdown" id="userDropdown">
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
    </nav>

    <!-- BANNER HERO AZUL INSTITUCIONAL -->
    <div class="hero-banner">
        <h1>Gestión de Consultas Contables en Línea</h1>
        <p>Resuelve las dudas tributarias de los clientes y gestiona sus citas de forma ágil y eficiente.</p>
    </div>

    <div class="container">
        <!-- 1. SECCIÓN CONSULTAS -->
        <div id="seccion-consultas" class="seccion-contenido active">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="color: #0d6efd;">💬 Gestor de Consultas de Clientes</h3>
                    <span style="font-size: 0.9rem; color: var(--subtext-color);">Contador: <strong><?php echo htmlspecialchars($userNombre); ?></strong></span>
                </div>

                <label for="buscarConsultasContador" style="display:block;margin:10px 0 6px;font-weight:600">Buscar consultas</label><input id="buscarConsultasContador" type="search" placeholder="Buscar por cliente, título, correo o detalle..." oninput="buscarConsultasContador()" style="width:100%;max-width:460px;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;margin-bottom:12px">
                <div class="consulta-filtros" role="group" aria-label="Filtrar consultas agendadas">
                    <button type="button" class="consulta-filtro-contador active" data-filtro="todas" onclick="filtrarConsultasContador('todas', this)">Todas</button>
                    <button type="button" class="consulta-filtro-contador" data-filtro="pendiente" onclick="filtrarConsultasContador('pendiente', this)">Pendientes</button>
                    <button type="button" class="consulta-filtro-contador" data-filtro="respondido" onclick="filtrarConsultasContador('respondido', this)">Respondidas</button>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 22%;">Contacto</th>
                                <th style="width: 20%;">Asunto / Título</th>
                                <th style="width: 23%;">Detalle / Mensaje</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 5%; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['consultas'])): ?>
                                <?php foreach ($data['consultas'] as $consulta): ?>
                                    <?php 
                                        $idUsuario     = $consulta['usuario_id'] ?? $consulta['id_usuario'] ?? 0;
                                        $nombreCliente = $consulta['usuario_nombre'] ?? $consulta['nombre_usuario'] ?? $consulta['nombre'] ?? $consulta['cliente'] ?? 'Cliente';
                                        $emailCliente  = $consulta['usuario_email'] ?? $consulta['email'] ?? $consulta['correo'] ?? 'Sin correo';
                                        $telCliente    = $consulta['telefono'] ?? $consulta['celular'] ?? 'Sin teléfono';
                                        $tituloCons    = $consulta['titulo'] ?? 'Consulta';
                                        $detalleCons   = $consulta['detalle'] ?? '';
                                        $estadoCons    = $consulta['estado'] ?? 'Pendiente';
                                        $estadoFiltro  = in_array(strtolower(trim((string)$estadoCons)), ['respondido', 'respondida'], true) ? 'respondido' : 'pendiente';
                                        $inicialesCli  = strtoupper(substr(trim($nombreCliente), 0, 2));
                                    ?>
                                    <tr data-consulta-cliente="<?php echo (int)$idUsuario; ?>" data-estado-consulta-filtro="<?php echo $estadoFiltro; ?>">
                                        <td>
                                            <div class="cliente-info-cell">
                                                <div class="table-avatar"><?php echo $inicialesCli; ?></div>
                                                <strong><?php echo htmlspecialchars($nombreCliente); ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contacto-info">
                                                <span class="contacto-email"><?php echo htmlspecialchars($emailCliente); ?></span>
                                                <span class="contacto-tel"><?php echo htmlspecialchars($telCliente); ?></span>
                                            </div>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($tituloCons); ?></strong></td>
                                        <td><?php echo htmlspecialchars($detalleCons); ?></td>
                                        <td><?php $respondida = strtolower($estadoCons) === 'respondido'; ?><span class="badge-estado estado-consulta <?php echo $respondida ? 'badge-respondido' : 'badge-pendiente'; ?>" data-estado-consulta="<?php echo (int)($consulta['id'] ?? 0); ?>"><?php echo $respondida ? '✓ Respondido' : '◷ Pendiente'; ?></span></td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn-responder" onclick='window.abrirChatDirecto(<?php echo json_encode((string)$idUsuario); ?>, <?php echo json_encode($nombreCliente, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo json_encode($inicialesCli); ?>, <?php echo json_encode($tituloCons, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo json_encode($detalleCons, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo (int)($consulta['id'] ?? 0); ?>)'>
                                                💬 Responder
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--subtext-color);">
                                        No hay consultas pendientes registradas.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. SECCIÓN CITAS -->
        <div id="seccion-citas" class="seccion-contenido">
            <div class="card">
                <h3 style="color: #0d6efd; margin-bottom: 20px;">📅 Citas Solicitadas por Clientes</h3>
                <?php if (!empty($data['citas'])): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['citas'] as $cita): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($cita['usuario_nombre'] ?? 'Cliente'); ?></strong></td>
                                        <td><?php echo htmlspecialchars($cita['fecha'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($cita['hora'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($cita['motivo'] ?? ''); ?></td>
                                        <td><span class="badge-estado badge-pendiente"><?php echo htmlspecialchars($cita['estado'] ?? 'Pendiente'); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: var(--subtext-color);">No hay citas agendadas.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3. SECCIÓN CHAT INTERACTIVO -->
        <div id="seccion-chat" class="seccion-contenido">
            <div class="chat-layout">
                <div class="chat-sidebar">
                    <div class="chat-sidebar-header">💬 Mensajes de Clientes</div>
                    <div class="chat-contacts-list">
                        <?php
                            $consultasChat = $data['consultas'] ?? [];
                            $consultasPendientesChat = [];
                            $consultasRespondidasChat = [];
                            foreach ($consultasChat as $consultaChat) {
                                $estadoChat = strtolower(trim($consultaChat['estado'] ?? 'pendiente'));
                                if ($estadoChat === 'respondido' || $estadoChat === 'respondida') $consultasRespondidasChat[] = $consultaChat;
                                else $consultasPendientesChat[] = $consultaChat;
                            }
                        ?>
                        <button type="button" class="chat-status-heading pending-heading chat-status-toggle" aria-expanded="true" aria-controls="chatPendientesList" onclick="toggleListaConsultas('chatPendientesList', this)">◷ Pendientes <span><?php echo count($consultasPendientesChat); ?></span></button>
                        <div id="chatPendientesList" class="chat-status-list">
                        <?php if ($consultasPendientesChat): foreach ($consultasPendientesChat as $consultaChat):
                            $idChat = (int)($consultaChat['usuario_id'] ?? $consultaChat['id_usuario'] ?? 0);
                            $nombreChat = $consultaChat['usuario_nombre'] ?? $consultaChat['nombre_usuario'] ?? $consultaChat['nombre'] ?? $consultaChat['cliente'] ?? 'Cliente';
                            $tituloChat = $consultaChat['titulo'] ?? 'Consulta';
                            $inicialesChat = strtoupper(substr(trim($nombreChat), 0, 2));
                            $detalleChat = $consultaChat['detalle'] ?? '';
                        ?>
                            <button type="button" class="contact-item chat-contact-button" data-consulta-id="<?php echo (int)($consultaChat['id'] ?? 0); ?>" onclick='window.abrirChatDirecto(<?php echo json_encode((string)$idChat); ?>, <?php echo json_encode($nombreChat, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo json_encode($inicialesChat); ?>, <?php echo json_encode($tituloChat, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo json_encode($detalleChat, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo (int)($consultaChat['id'] ?? 0); ?>)'>
                                <span class="table-avatar"><?php echo htmlspecialchars($inicialesChat); ?></span>
                                <span class="chat-contact-copy"><strong><?php echo htmlspecialchars($nombreChat); ?></strong><small><?php echo htmlspecialchars($tituloChat); ?></small></span>
                            </button>
                        <?php endforeach; else: ?><p class="chat-empty-list">No hay consultas pendientes.</p><?php endif; ?>
                        </div>

                        <button type="button" class="chat-status-heading answered-heading chat-status-toggle" aria-expanded="true" aria-controls="chatRespondidosList" onclick="toggleListaConsultas('chatRespondidosList', this)">✓ Respondidos <span><?php echo count($consultasRespondidasChat); ?></span></button>
                        <div id="chatRespondidosList" class="chat-status-list">
                        <?php if ($consultasRespondidasChat): foreach ($consultasRespondidasChat as $consultaChat):
                            $idChat = (int)($consultaChat['usuario_id'] ?? $consultaChat['id_usuario'] ?? 0);
                            $nombreChat = $consultaChat['usuario_nombre'] ?? $consultaChat['nombre_usuario'] ?? $consultaChat['nombre'] ?? $consultaChat['cliente'] ?? 'Cliente';
                            $tituloChat = $consultaChat['titulo'] ?? 'Consulta';
                            $inicialesChat = strtoupper(substr(trim($nombreChat), 0, 2));
                            $detalleChat = $consultaChat['detalle'] ?? '';
                        ?>
                            <button type="button" class="contact-item chat-contact-button" data-consulta-id="<?php echo (int)($consultaChat['id'] ?? 0); ?>" onclick='window.abrirChatDirecto(<?php echo json_encode((string)$idChat); ?>, <?php echo json_encode($nombreChat, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo json_encode($inicialesChat); ?>, <?php echo json_encode($tituloChat, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo json_encode($detalleChat, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP); ?>, <?php echo (int)($consultaChat['id'] ?? 0); ?>)'>
                                <span class="table-avatar"><?php echo htmlspecialchars($inicialesChat); ?></span>
                                <span class="chat-contact-copy"><strong><?php echo htmlspecialchars($nombreChat); ?></strong><small><?php echo htmlspecialchars($tituloChat); ?></small></span>
                            </button>
                        <?php endforeach; else: ?><p class="chat-empty-list">No hay consultas respondidas.</p><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="chat-main-area">
                    <div class="chat-header">
                        <div class="table-avatar" id="chatHeaderAvatar">CL</div>
                        <div>
                            <h4 id="chatHeaderName">Cliente</h4>
                            <span style="font-size:0.78rem; color:#198754;">● En línea • Responde en minutos</span>
                        </div>
                        <button type="button" onclick="mostrarSeccion('consultas', document.getElementById('btn-tab-consultas'))" style="margin-left: auto; background: none; border: 1px solid var(--border-color); color: var(--text-color); padding: 5px 10px; border-radius: 5px; cursor: pointer;">Volver al Gestor</button>
                    </div>

                    <div class="chat-messages" id="chatMessages">
                        <div class="message incoming" id="initialMessage">
                            <strong>Consulta:</strong><br>
                            Selecciona una consulta en el gestor para iniciar el chat.
                        </div>
                    </div>

                    <form class="chat-input-area" id="chatSendForm" onsubmit="event.preventDefault(); enviarMensajeChat();">
                        <input type="text" id="chatInput" class="chat-input" placeholder="Escribe tu respuesta contable aquí..." autocomplete="off" required>
                        <button type="submit" class="btn-send" aria-label="Enviar mensaje">➔</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let clienteActualId = null;
        let consultaActualId = null;
        const miUsuarioId = <?php echo json_encode($_SESSION['user_id'] ?? 1); ?>;
        // El formulario ya llama enviarMensajeChat() desde onsubmit; no registrar un segundo envío.
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('chatInput');
            if (input) input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    document.getElementById('chatSendForm').requestSubmit();
                }
            });
        });

        function toggleListaConsultas(id, boton) {
            const lista = document.getElementById(id);
            if (!lista) return;
            const ocultar = lista.style.display !== 'none';
            lista.style.display = ocultar ? 'none' : '';
            boton.setAttribute('aria-expanded', ocultar ? 'false' : 'true');
        }

        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('show');
        }

        window.addEventListener('click', function(e) {
            const avatarBtn = document.getElementById('avatarBtn');
            const dropdown  = document.getElementById('userDropdown');
            if (avatarBtn && dropdown && !avatarBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        function mostrarSeccion(seccion, elemento) {
            document.querySelectorAll('.seccion-contenido').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

            const target = document.getElementById('seccion-' + seccion);
            if (target) target.classList.add('active');
            const tab = elemento || document.getElementById('btn-tab-' + seccion);
            if (tab) tab.classList.add('active');
        }

        // Se expone en window para que el botón Responder siempre pueda invocarla.
        window.abrirChatDirecto = function(clienteId, nombre, iniciales, titulo, detalle, consultaId) {
            consultaActualId = Number(consultaId || 0);
            clienteActualId = String(clienteId || '').trim();
            if (!clienteActualId || !/^\d+$/.test(clienteActualId)) {
                alert('No se pudo identificar al cliente de esta consulta.');
                return;
            }

            const nombreSeguro = String(nombre || 'Cliente');
            const inicialesSeguras = String(iniciales || nombreSeguro.substring(0, 2).toUpperCase());
            const tituloSeguro = String(titulo || 'Consulta');

            const headerName = document.getElementById('chatHeaderName');
            const headerAvatar = document.getElementById('chatHeaderAvatar');
            const sidebarAvatar = document.getElementById('chatSidebarAvatar');
            const sidebarName = document.getElementById('chatSidebarName');
            const sidebarTitle = document.getElementById('chatSidebarTitle');
            const chatSection = document.getElementById('seccion-chat');
            const chatTab = document.getElementById('btn-tab-chat');

            if (!chatSection || !chatTab) {
                alert('No se encontró la sección de chat. Recarga la página e inténtalo de nuevo.');
                return;
            }

            if (headerName) headerName.textContent = nombreSeguro;
            if (headerAvatar) headerAvatar.textContent = inicialesSeguras;
            if (sidebarAvatar) sidebarAvatar.textContent = inicialesSeguras;
            if (sidebarName) sidebarName.textContent = nombreSeguro;
            if (sidebarTitle) sidebarTitle.textContent = tituloSeguro;

            // Cambiar explícitamente a la pestaña Chat de clientes.
            mostrarSeccion('chat', chatTab);
            chatSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

            const contenedorChat = document.getElementById('chatMessages');
            if (contenedorChat) {
                contenedorChat.innerHTML = '<div class="message incoming">Cargando conversación…</div>';
            }
            cargarMensajesContador();
            const inputChat = document.getElementById('chatInput');
            if (inputChat) inputChat.focus({ preventScroll: true });
        };

        async function cargarMensajesContador() {
            if (!clienteActualId || !consultaActualId) return;

            try {
                const res = await fetch('<?php echo URLROOT; ?>/consultas/obtenerMensajes?consulta_id=' + encodeURIComponent(consultaActualId), {
                    credentials: 'same-origin', cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const contentType = res.headers.get("content-type");
                if (!contentType || contentType.indexOf("application/json") === -1) return;

                const mensajes = await res.json();
                const container = document.getElementById('chatMessages');
                container.innerHTML = '';

                if (!Array.isArray(mensajes) || mensajes.length === 0) {
                    container.innerHTML = '<div class="message incoming"><strong>Sin mensajes aún:</strong><br>Escribe la primera respuesta para este cliente.</div>';
                    return;
                }

                mensajes.forEach(msg => {
                    const div = document.createElement('div');
                    const esMio = Number(msg.remitente_id) === Number(miUsuarioId);
                    div.className = esMio ? 'message outgoing' : 'message incoming';
                    div.textContent = msg.mensaje;
                    container.appendChild(div);
                });

                container.scrollTop = container.scrollHeight;
            } catch (e) {
                console.error("Error al cargar mensajes del contador:", e);
            }
        }

        async function enviarMensajeChat() {
            const input = document.getElementById('chatInput');
            const mensaje = input.value.trim();
            if (!mensaje || !clienteActualId || !consultaActualId || input.dataset.enviando === '1') return;
            input.dataset.enviando = '1';
            // Vaciar inmediatamente para que Enter y el botón tengan la misma respuesta visual.
            input.value = '';

            const formData = new FormData();
            formData.append('destinatario_id', clienteActualId);
            formData.append('mensaje', mensaje);
            if (consultaActualId > 0) formData.append('consulta_id', String(consultaActualId));

            try {
                const res = await fetch('<?php echo URLROOT; ?>/consultas/guardarMensaje', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const contentType = res.headers.get("content-type");
                if (res.ok && contentType && contentType.indexOf("application/json") !== -1) {
                    const data = await res.json();
                    if (data.status === 'success') {
                        // Vaciar el campo en cuanto el servidor confirme que guardó el mensaje.
                        input.value = '';
                        input.focus();
                        await cargarMensajesContador();
                        await actualizarEstadosConsultas();
                    } else {
                        console.error("Error al guardar mensaje:", data.message);
                        input.value = mensaje;
                        alert(data.message || 'No se pudo confirmar el envío. Revisa la conexión e inténtalo de nuevo.');
                    }
                } else {
                    input.value = mensaje;
                    alert('El servidor no devolvió una respuesta válida.');
                }
            } catch (e) {
                console.error("Error al enviar mensaje:", e);
                input.value = mensaje;
                alert('No se pudo enviar el mensaje. Comprueba la conexión.');
            } finally {
                input.dataset.enviando = '0';
            }
        }

        async function actualizarEstadosConsultas() {
            try {
                const res = await fetch('<?php echo URLROOT; ?>/consultas/obtenerEstadosConsultas', { credentials: 'same-origin', cache: 'no-store' });
                if (!res.ok) return;
                const estados = await res.json();
                document.querySelectorAll('.estado-consulta[data-estado-consulta]').forEach(badge => {
                    const estado = String(estados[badge.dataset.estadoConsulta] || 'Pendiente').toLowerCase();
                    const respondida = estado === 'respondido' || estado === 'respondida';
                    badge.classList.toggle('badge-respondido', respondida);
                    badge.classList.toggle('badge-pendiente', !respondida);
                    badge.textContent = respondida ? '✓ Respondido' : '◷ Pendiente';
                    const filaConsulta = badge.closest('tr[data-estado-consulta-filtro]');
                    if (filaConsulta) filaConsulta.dataset.estadoConsultaFiltro = respondida ? 'respondido' : 'pendiente';
                });
                // Mover cada duda individual entre Pendientes y Respondidos sin agrupar por cliente.
                const lista = document.querySelector('.chat-contacts-list');
                if (lista) {
                    const encabezados = Array.from(lista.querySelectorAll('.chat-status-heading'));
                    const pendientesHead = encabezados.find(e => e.classList.contains('pending-heading'));
                    const respondidosHead = encabezados.find(e => e.classList.contains('answered-heading'));
                    if (pendientesHead && respondidosHead) {
                        let pendientes = pendientesHead.nextElementSibling;
                        let respondidos = respondidosHead.nextElementSibling;
                        const crearVacio = (texto) => { const p = document.createElement('p'); p.className = 'chat-empty-list'; p.textContent = texto; return p; };
                        const items = Array.from(lista.querySelectorAll('.chat-contact-button[data-consulta-id]'));
                        items.forEach(item => {
                            const estado = String(estados[item.dataset.consultaId] || 'Pendiente').toLowerCase();
                            const destino = (estado === 'respondido' || estado === 'respondida') ? respondidosHead : pendientesHead;
                            destino.parentNode.insertBefore(item, destino.nextElementSibling);
                        });
                        // Rebuild the two lists in the original order, keeping headings and their counts accurate.
                        const all = Array.from(lista.querySelectorAll('.chat-contact-button[data-consulta-id]'));
                        const pendingItems = all.filter(i => !['respondido','respondida'].includes(String(estados[i.dataset.consultaId] || 'pendiente').toLowerCase()));
                        const answeredItems = all.filter(i => ['respondido','respondida'].includes(String(estados[i.dataset.consultaId] || 'pendiente').toLowerCase()));
                        const clearSection = (head, items, emptyText) => {
                            let n = head.nextElementSibling;
                            while (n && !n.classList.contains('chat-status-heading')) { const next = n.nextElementSibling; n.remove(); n = next; }
                            items.forEach(i => head.parentNode.insertBefore(i, head.nextElementSibling));
                            if (!items.length) head.parentNode.insertBefore(crearVacio(emptyText), head.nextElementSibling);
                            head.querySelector('span').textContent = String(items.length);
                        };
                        clearSection(pendientesHead, pendingItems, 'No hay consultas pendientes.');
                        clearSection(respondidosHead, answeredItems, 'No hay consultas respondidas.');
                    }
                }
            } catch (e) {
                console.error('No se pudieron actualizar los estados de consultas:', e);
            }
        }
        actualizarEstadosConsultas();
        setInterval(actualizarEstadosConsultas, 2500);

        setInterval(() => {
            const chatSeccion = document.getElementById('seccion-chat');
            if (chatSeccion && chatSeccion.classList.contains('active') && clienteActualId) {
                cargarMensajesContador();
            }
        }, 2500);
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
function filtrarConsultasContador(filtro, boton) {
    document.querySelectorAll('.consulta-filtro-contador').forEach(function(b) { b.classList.toggle('active', b === boton); });
    document.querySelectorAll('tr[data-estado-consulta-filtro]').forEach(function(fila) {
        fila.hidden = filtro !== 'todas' && fila.dataset.estadoConsultaFiltro !== filtro;
    });
}
</script>

<script>function buscarConsultasContador(){const q=(document.getElementById('buscarConsultasContador')?.value||'').toLocaleLowerCase('es');document.querySelectorAll('tr[data-estado-consulta-filtro]').forEach(r=>{r.style.display=r.innerText.toLocaleLowerCase('es').includes(q)?'':'none';});}</script>

<?php
$ccRol = (int)($_SESSION['user_rol'] ?? 2);
$ccNombre = $_SESSION['user_nombre'] ?? 'Usuario';
?>


</body>
</html>