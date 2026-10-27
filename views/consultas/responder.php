<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $esContadorActual ? 'Chat de consulta' : 'Mi consulta'; ?> - CreaContable</title>
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
            --navbar-bg: #ffffff;
            --border-color: #e0e0e0;
            --sidebar-bg: #f0f4f9;
            --msg-client-bg: #e9ecef;
            --msg-contador-bg: #0d6efd;
            --msg-contador-text: #ffffff;
            --input-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --subtext-color: #aaa;
            --navbar-bg: #181818;
            --border-color: #333333;
            --sidebar-bg: #252525;
            --msg-client-bg: #2d2d2d;
            --msg-contador-bg: #0d6efd;
            --msg-contador-text: #ffffff;
            --input-bg: #2a2a2a;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 30px;
            background-color: #121318;
            border-bottom: 1px solid #292d38;
        }

        .logo-container { display:flex; align-items:center; gap:10px; text-decoration:none; flex-shrink:0; }
        .logo-box { background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%); color:#0b0c10; width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; box-shadow:0 4px 10px rgba(245,158,11,.25); }
        .logo-text-wrapper { display:flex; flex-direction:column; }
        .logo-brand-text { font-size:1.6rem; font-weight:700; line-height:1; }
        .logo-crea { color:var(--text-color); font-style:italic; font-family:Georgia,serif; }
        .logo-contable { color:#f59e0b; }
        .logo-arrows { display:flex; flex-direction:column; gap:2px; margin-top:3px; }
        .logo-arrow-line { height:2px; background:#f59e0b; position:relative; border-radius:2px; }
        .logo-arrow-line::after { content:''; position:absolute; right:-2px; top:-2px; width:0; height:0; border-top:3px solid transparent; border-bottom:3px solid transparent; border-left:5px solid #f59e0b; }
        .logo-arrow-line.top { width:100%; }
        .logo-arrow-line.bottom { width:80%; }
        @media(max-width:700px) { .navbar { padding:12px 14px; gap:10px; flex-wrap:wrap; } .logo-brand-text { font-size:1.3rem; } }

        .tab-btn {
            padding: 8px 16px;
            background-color: var(--sidebar-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        /* CONTENEDOR PRINCIPAL TIPO CHAT LAYOUT */
        .chat-container {
            max-width: 1300px;
            width: 95%;
            margin: 25px auto;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
            height: calc(100vh - 140px);
            min-height: 550px;
        }

        /* PANEL IZQUIERDO: LISTA DE CLIENTES */
        .chat-sidebar {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-title {
            font-size: 1rem;
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 15px;
            padding-left: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .item-cliente {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-color);
            margin-bottom: 8px;
            border: 1px solid transparent;
        }

        .item-cliente:hover, .item-cliente.active {
            background-color: var(--sidebar-bg);
            border-color: var(--border-color);
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
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

        .info-cliente-sidebar h4 {
            font-size: 0.9rem;
            font-weight: bold;
        }

        .info-cliente-sidebar p {
            font-size: 0.78rem;
            color: var(--subtext-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 190px;
        }

        /* PANEL DERECHO: ÁREA DE CHAT */
        .chat-main {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: var(--card-bg);
        }

        .header-status {
            font-size: 0.78rem;
            color: #198754;
            font-weight: 500;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        /* GLOBOS DE MENSAJES */
        .message-bubble {
            max-width: min(78%, 720px);
            min-width: 0;
            box-sizing: border-box;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            word-break: normal;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.92rem;
            line-height: 1.4;
        }

        .message-bubble.propio {
            align-self: flex-end; background-color: var(--msg-contador-bg); color: var(--msg-contador-text); border-bottom-right-radius: 2px;
        }
        .message-bubble.cliente {
            align-self: flex-start;
            background-color: var(--msg-client-bg);
            color: var(--text-color);
            border-bottom-left-radius: 2px;
        }

        .message-bubble.contador,
        .message-bubble.recibido {
            align-self: flex-start;
            background-color: var(--msg-client-bg);
            color: var(--text-color);
            border-bottom-left-radius: 2px;
        }

        .consulta-header-box {
            font-weight: bold;
            font-size: 0.85rem;
            margin-bottom: 4px;
            opacity: 0.85;
        }

        .message-time {
            font-size: 0.72rem;
            margin-top: 5px;
            opacity: 0.75;
            text-align: right;
        }

        /* FORMULARIO DE ENVÍO DE MENSAJE */
        .chat-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
            background-color: var(--card-bg);
        }

        .chat-input-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .chat-input {
            flex: 1;
            padding: 12px 18px;
            border-radius: 25px;
            border: 1px solid var(--border-color);
            background-color: var(--input-bg);
            color: var(--text-color);
            font-size: 0.92rem;
            outline: none;
        }

        .chat-input:focus {
            border-color: #0d6efd;
        }

        .btn-send {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: background-color 0.2s;
        }

        .btn-send:hover {
            background-color: #0b5ed7;
        }
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
        $userNombre = $_SESSION['user_nombre'] ?? 'Usuario';
        $rolActual = (int)($_SESSION['user_rol'] ?? 2);
        $esContadorActual = in_array($rolActual, [1,3], true);
        $consulta   = $data['consulta'] ?? null;
        $nombreCli  = $consulta['usuario_nombre'] ?? $consulta['nombre_usuario'] ?? $consulta['nombre'] ?? 'Cliente';
        $inicialCli = strtoupper(substr(trim($nombreCli), 0, 2));
        $consultaId = (int)($data['consulta_id'] ?? $consulta['id'] ?? 0);
        $clienteId = (int)($data['cliente_id'] ?? $consulta['usuario_id'] ?? 0);
        $esContador = in_array((int)($_SESSION['user_rol'] ?? 2), [1,3], true);
        $destinatarioId = $esContador ? $clienteId : (int)($data['contador_id'] ?? 1);
    ?>

    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="<?php echo URLROOT; ?>/home" class="logo-container" aria-label="CreaContable, ir al inicio">
                <div class="logo-box" aria-hidden="true">🧮</div>
                <div class="logo-text-wrapper">
                    <div class="logo-brand-text"><span class="logo-crea">Crea</span><span class="logo-contable">Contable</span></div>
                    <div class="logo-arrows" aria-hidden="true"><div class="logo-arrow-line top"></div><div class="logo-arrow-line bottom"></div></div>
                </div>
            </a>
            <a href="<?php echo URLROOT; ?>/dashboard" class="tab-btn">
                ⬅ <?php echo $esContadorActual ? 'Volver al gestor' : 'Volver a mis consultas'; ?>
            </a>
        </div>
        <div style="font-size: 0.9rem; color: var(--subtext-color);">
            <?php echo $esContador ? 'Contador' : 'Cliente'; ?>: <strong><?php echo htmlspecialchars($userNombre); ?></strong>
        </div>
    </nav>

    <div id="consultaNotification" role="status" aria-live="polite" style="display:none;position:fixed;top:78px;right:24px;z-index:9999;background:#d6a653;color:#151515;padding:14px 20px;border-radius:10px;box-shadow:0 8px 24px #0005;font-weight:700;max-width:360px;">🔔 Nuevo mensaje en esta consulta</div>
    <div class="chat-container">
        <!-- SIDEBAR DE MENSAJES -->
        <div class="chat-sidebar">
            <div class="sidebar-title">
                <?php echo $esContadorActual ? 'Consultas de clientes' : 'Mis consultas'; ?>
            </div>
            
            <?php if (!empty($data['consultas'])): ?>
                <?php foreach ($data['consultas'] as $item): ?>
                    <?php 
                        $nCli = $item['usuario_nombre'] ?? $item['nombre_usuario'] ?? $item['nombre'] ?? 'Cliente';
                        $iniCli = strtoupper(substr(trim($nCli), 0, 2));
                        $isCurrent = ($consulta && $consulta['id'] == $item['id']);
                    ?>
                    <a href="<?php echo URLROOT; ?>/consultas/responder/<?php echo $item['id']; ?>" class="item-cliente <?php echo $isCurrent ? 'active' : ''; ?>">
                        <div class="avatar-circle"><?php echo $iniCli; ?></div>
                        <div class="info-cliente-sidebar">
                            <h4><?php echo htmlspecialchars($nCli); ?></h4>
                            <p><?php echo htmlspecialchars($item['titulo'] ?? $item['detalle'] ?? ''); ?></p>
                            <?php $estadoItem = $item['estado'] ?? 'Pendiente'; $estadoPendiente = strtolower((string)$estadoItem) !== 'respondido'; ?>
                            <small style="display:inline-block;margin-top:4px;font-weight:700;color:<?php echo $estadoPendiente ? '#f59e0b' : '#16a34a'; ?>"><?php echo $estadoPendiente ? '◷ Pendiente' : '✓ Respondido'; ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <a href="#" class="item-cliente active">
                    <div class="avatar-circle"><?php echo $inicialCli; ?></div>
                    <div class="info-cliente-sidebar">
                        <h4><?php echo htmlspecialchars($nombreCli); ?></h4>
                        <p><?php echo htmlspecialchars($consulta['titulo'] ?? ''); ?></p>
                    </div>
                </a>
            <?php endif; ?>
        </div>

        <!-- VENTANA DEL CHAT -->
        <div class="chat-main">
            <!-- HEADER DEL CHAT -->
            <div class="chat-header">
                <div class="avatar-circle"><?php echo $inicialCli; ?></div>
                <div>
                    <h3 style="font-size: 1rem;"><?php echo htmlspecialchars($nombreCli); ?></h3>
                    <span class="header-status"><?php echo $esContadorActual ? 'Chat de esta consulta' : 'Conversación de tu consulta'; ?></span>
                </div>
            </div>

            <!-- Historial exclusivo de esta consulta -->
            <div class="chat-messages" id="chatMessages" data-consulta-id="<?php echo $consultaId; ?>">
                <div class="message-bubble cliente">
                    <div class="consulta-header-box">Consulta #<?php echo $consultaId; ?>: <?php echo htmlspecialchars($consulta['titulo'] ?? ''); ?></div>
                    <?php echo htmlspecialchars($consulta['detalle'] ?? ''); ?>
                    <div class="message-time">Mensaje original del cliente</div>
                </div>
            </div>
            <div class="chat-footer">
                <form id="formConsultaChat" class="chat-input-container">
                    <input type="text" id="mensajeConsulta" class="chat-input" placeholder="Escribe tu mensaje aquí..." required autocomplete="off">
                    <button type="submit" class="btn-send" aria-label="Enviar">➔</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    (function(){
      const consultaId = <?php echo $consultaId; ?>;
      const clienteId = <?php echo $clienteId; ?>;
      const destinatarioId = <?php echo $destinatarioId; ?>;
      const miUsuarioId = <?php echo (int)($_SESSION['user_id'] ?? 0); ?>;
      const urlBase = '<?php echo URLROOT; ?>';
      const box = document.getElementById('chatMessages');
      const form = document.getElementById('formConsultaChat');
      const input = document.getElementById('mensajeConsulta');
      const notification = document.getElementById('consultaNotification');
      let firstLoad = true; let knownIncoming = new Set(); let toastTimer = null;
      function esc(s){return String(s ?? '').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
      async function cargar(){
        const r = await fetch(urlBase+'/consultas/obtenerMensajes?consulta_id='+encodeURIComponent(consultaId),{credentials:'same-origin',cache:'no-store',headers:{'X-Requested-With':'XMLHttpRequest'}});
        if(!r.ok) return;
        const rows = await r.json();
        box.innerHTML = '<div class="message-bubble cliente"><div class="consulta-header-box">Consulta #'+consultaId+': <?php echo htmlspecialchars(addslashes($consulta['titulo'] ?? ''), ENT_QUOTES); ?></div><?php echo htmlspecialchars(addslashes($consulta['detalle'] ?? ''), ENT_QUOTES); ?><div class="message-time">Mensaje original del cliente</div></div>';
        const newIncoming = [];
        rows.forEach(m=>{
          const mid = Number(m.id || 0);
          if (Number(m.remitente_id) !== miUsuarioId && mid && !knownIncoming.has(mid) && !firstLoad) newIncoming.push(m);
          if (mid) knownIncoming.add(mid);
          const el=document.createElement('div');
          // La posición depende de quién está usando el chat: mis mensajes a la derecha,
          // los mensajes de la otra persona a la izquierda, tanto para cliente como contador.
          const esMio = Number(m.remitente_id) === miUsuarioId;
          el.className = 'message-bubble ' + (esMio ? 'propio' : 'recibido');
          const nombre = [m.nombre || '', m.apellido || ''].filter(Boolean).join(' ');
          el.innerHTML=esc(m.mensaje)+'<div class="message-time">'+esc(nombre || 'Usuario')+' · '+esc(m.fecha || '')+'</div>';
          box.appendChild(el);
        });
        box.scrollTop=box.scrollHeight;
        if (newIncoming.length) {
          const last = newIncoming[newIncoming.length - 1];
          notification.textContent = '🔔 Nuevo mensaje de ' + ([last.nombre || '', last.apellido || ''].filter(Boolean).join(' ') || 'tu contacto');
          notification.style.display = 'block';
          if (toastTimer) clearTimeout(toastTimer);
          toastTimer = setTimeout(()=>{ notification.style.display='none'; }, 6500);
        }
        firstLoad = false;
      }
      form.addEventListener('submit',async e=>{
        e.preventDefault(); const mensaje=input.value.trim(); if(!mensaje)return;
        const fd=new FormData(); fd.append('destinatario_id',destinatarioId); fd.append('mensaje',mensaje); fd.append('consulta_id',consultaId);
        let d; try { const r=await fetch(urlBase+'/consultas/guardarMensaje',{method:'POST',credentials:'same-origin',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}}); d=await r.json(); } catch(err) { alert('No se pudo confirmar el envío. Revisa la conexión e inténtalo de nuevo.'); return; } if(d.status==='success'){input.value='';await cargar();} else alert(d.message || 'No se pudo enviar el mensaje.');
      });
      cargar(); setInterval(cargar,4000);
    })();
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

</body>
</html>