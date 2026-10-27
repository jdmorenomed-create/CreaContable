<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$nombreUsuario = $_SESSION['user_nombre'] ?? 'Contador';
$publicaciones = $data['publicaciones'] ?? [];
$busqueda = (string)($data['busqueda'] ?? '');
$categoriaSeleccionada = (string)($data['categoriaSeleccionada'] ?? '');
$comentariosPorTema = $data['comentarios'] ?? [];
$csrf = $data['csrf'] ?? '';
$mensaje = $data['mensaje'] ?? '';
$error = $data['error'] ?? '';
function foro_h($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function foro_fecha($fecha) {
    $timestamp = strtotime((string)$fecha);
    return $timestamp ? date('d/m/Y · H:i', $timestamp) : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foro profesional | CreaContable</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
    <style>
        :root{--foro-bg:#10131a;--foro-card:#191e29;--foro-border:#2b3241;--foro-text:#f3f4f6;--foro-muted:#a6afc0;--foro-gold:#f59e0b;--foro-blue:#2563eb}
        *{box-sizing:border-box}body{margin:0;background:var(--foro-bg);color:var(--foro-text);font-family:Arial,Helvetica,sans-serif}
        .foro-nav{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:14px clamp(16px,4vw,48px);background:#12151c;border-bottom:1px solid var(--foro-border)}
        .foro-brand{display:flex;align-items:center;gap:10px;text-decoration:none;color:#fff;font-weight:700;font-size:1.2rem}
        .foro-brand-mark{display:grid;place-items:center;width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#111827}
        .foro-brand span span{color:var(--foro-gold)}
        .foro-nav-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.foro-account{display:flex;align-items:center;gap:9px;padding:5px 11px 5px 5px;border:1px solid var(--foro-border);border-radius:999px;background:#191e29}.foro-avatar{width:34px;height:34px;display:grid;place-items:center;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);color:#171717;font-weight:800;flex-shrink:0}.foro-account-name{font-size:.9rem;font-weight:650;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .foro-link,.foro-button{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 14px;border:1px solid var(--foro-border);border-radius:8px;text-decoration:none;color:var(--foro-text);background:#202635;font-weight:600;cursor:pointer;font-size:.92rem}
        .foro-button.primary{background:var(--foro-gold);color:#171717;border-color:var(--foro-gold)}
        .foro-main{max-width:1120px;width:calc(100% - 32px);margin:32px auto 60px}
        .foro-hero{padding:30px;border-radius:16px;background:linear-gradient(120deg,#1d3154,#182235);border:1px solid #304465;margin-bottom:22px}
        .foro-eyebrow{color:#fbbf24;font-size:.78rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
        .foro-hero h1{margin:9px 0;font-size:clamp(1.7rem,4vw,2.4rem)}.foro-hero p{margin:0;color:#d3dced;line-height:1.6;max-width:760px}
        .foro-alert{padding:13px 16px;border-radius:9px;margin:0 0 16px}.foro-alert.ok{background:#123a2a;border:1px solid #226747;color:#b7f7d2}.foro-alert.err{background:#492326;border:1px solid #813b40;color:#ffd2d2}
        .foro-grid{display:grid;grid-template-columns:minmax(270px,.75fr) minmax(0,1.5fr);gap:22px;align-items:start}
        .foro-card{background:var(--foro-card);border:1px solid var(--foro-border);border-radius:14px;padding:22px;min-width:0}
        .foro-card h2{font-size:1.18rem;margin:0 0 8px}.foro-muted{color:var(--foro-muted);font-size:.9rem;line-height:1.5}
        .foro-field{margin-top:17px}.foro-field label{display:block;font-weight:600;font-size:.9rem;margin-bottom:7px}
        .foro-field input,.foro-field select,.foro-field textarea,.foro-reply textarea{width:100%;padding:12px 13px;border-radius:8px;border:1px solid #3a4355;background:#111620;color:var(--foro-text);font:inherit}
        .foro-field textarea{min-height:150px;resize:vertical;line-height:1.5}
        .foro-field input:focus,.foro-field select:focus,.foro-field textarea:focus,.foro-reply textarea:focus{outline:2px solid #4f83ff;outline-offset:1px}
        .foro-submit{width:100%;margin-top:17px}
        .foro-search{background:var(--foro-card);border:1px solid var(--foro-border);border-radius:12px;padding:15px;margin-bottom:16px}.foro-search label{display:block;font-weight:600;font-size:.9rem;margin-bottom:9px}.foro-search-row{display:flex;gap:8px;flex-wrap:wrap}.foro-search input,.foro-search select{flex:1;min-width:170px;padding:11px 12px;border-radius:8px;border:1px solid #3a4355;background:#111620;color:var(--foro-text);font:inherit}.foro-search input:focus,.foro-search select:focus{outline:2px solid #4f83ff;outline-offset:1px}.foro-search select{padding:11px 12px;border-radius:8px;border:1px solid #3a4355;background:#111620;color:var(--foro-text);font:inherit}.foro-topics{display:flex;flex-direction:column;gap:16px}.foro-topic{background:var(--foro-card);border:1px solid var(--foro-border);border-radius:14px;padding:20px;min-width:0;scroll-margin-top:20px}
        .foro-topic-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px}.foro-topic h3{font-size:1.12rem;margin:0 0 8px;overflow-wrap:anywhere}
        .foro-tag{display:inline-flex;padding:5px 9px;border-radius:20px;background:#2b3445;color:#dbeafe;font-size:.76rem;font-weight:700}
        .foro-meta{font-size:.82rem;color:var(--foro-muted);margin-top:8px}.foro-topic-content{white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.65;margin:17px 0}
        .foro-comments{border-top:1px solid var(--foro-border);padding-top:15px;margin-top:12px}.foro-comments h4{font-size:.95rem;margin:0 0 12px}
        .foro-comment{padding:12px 13px;background:#131822;border:1px solid #252c39;border-radius:9px;margin:9px 0}
        .foro-comment p{white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.55;margin:8px 0 0}
        .foro-reply{margin-top:14px}.foro-reply textarea{min-height:80px;resize:vertical}.foro-reply .foro-button{margin-top:9px}
        .foro-empty{padding:35px 18px;text-align:center;border:1px dashed #414b5d;border-radius:12px;color:var(--foro-muted)}
        @media(max-width:520px){.foro-search-row{flex-direction:column}.foro-search-row .foro-button{width:100%}}
        @media(max-width:800px){.foro-grid{grid-template-columns:1fr}.foro-hero{padding:23px}.foro-card{padding:18px}}
        @media(max-width:520px){.foro-nav{align-items:flex-start;flex-direction:column}.foro-nav-actions{width:100%}.foro-link{flex:1}.foro-topic-head{flex-direction:column}.foro-main{margin-top:20px}}
    </style>

    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/theme.css">
    <script src="<?php echo URLROOT; ?>/public/js/theme.js" defer></script>
</head>
<body>
<header class="foro-nav">
    <a class="foro-brand" href="<?php echo URLROOT; ?>/">
        <span class="foro-brand-mark">🧮</span>
        <span>Crea<span>Contable</span></span>
    </a>
    <div class="foro-nav-actions">
        <div class="foro-account"><span class="foro-avatar"><?php $partesNombre=preg_split('/\s+/',trim($nombreUsuario)); echo foro_h(strtoupper(substr($partesNombre[0]??'U',0,1).(isset($partesNombre[1])?substr($partesNombre[1],0,1):''))); ?></span><span class="foro-account-name">Hola, <?php echo foro_h($nombreUsuario); ?></span></div>
        <a class="foro-link" href="<?php echo URLROOT; ?>/dashboard">▦ Panel de control</a>
        <a class="foro-link" href="<?php echo URLROOT; ?>/auth/logout">Cerrar sesión</a>
    </div>
</header>

<main class="foro-main">
    <section class="foro-hero">
        <div class="foro-eyebrow">Comunidad profesional</div>
        <h1>Foro de contadores</h1>
        <p>Comparte novedades normativas, conversa sobre cambios de la DIAN, facturación, impuestos y buenas prácticas contables. Publica un tema y construye la conversación con otros profesionales.</p>
    </section>

    <?php if ($mensaje !== ''): ?><div class="foro-alert ok" role="status"><?php echo foro_h($mensaje); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="foro-alert err" role="alert"><?php echo foro_h($error); ?></div><?php endif; ?>

    <div class="foro-grid">
        <section class="foro-card">
            <h2>Iniciar una conversación</h2>
            <p class="foro-muted">Plantea una pregunta o comparte información útil. Evita publicar datos personales de clientes.</p>
            <form action="<?php echo URLROOT; ?>/foro/publicar" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo foro_h($csrf); ?>">
                <div class="foro-field">
                    <label for="titulo">Título del tema</label>
                    <input id="titulo" name="titulo" maxlength="160" required placeholder="Ej. Cambios recientes en facturación electrónica">
                </div>
                <div class="foro-field">
                    <label for="categoria">Categoría</label>
                    <select id="categoria" name="categoria" required>
                        <option value="Cambios normativos">Cambios normativos</option>
                        <option value="Impuestos y DIAN">Impuestos y DIAN</option>
                        <option value="Facturación">Facturación</option>
                        <option value="Contabilidad">Contabilidad</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div class="foro-field">
                    <label for="contenido">Descripción</label>
                    <textarea id="contenido" name="contenido" maxlength="10000" required placeholder="Cuéntales a los demás contadores qué cambió, qué dudas tienes o qué experiencia quieres compartir."></textarea>
                </div>
                <button class="foro-button primary foro-submit" type="submit">Publicar tema</button>
            </form>
        </section>

        <section>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin:0 0 14px">
                <h2 style="margin:0;font-size:1.2rem">Conversaciones recientes</h2>
                <span class="foro-muted"><?php echo count($publicaciones); ?> tema(s)</span>
            </div>
            <form class="foro-search" action="<?php echo URLROOT; ?>/foro" method="get" role="search" aria-label="Buscar temas del foro">
                <label for="buscar-temas">Buscar temas y filtrar por categoría</label>
                <div class="foro-search-row">
                    <input id="buscar-temas" type="search" name="q" value="<?php echo foro_h($busqueda); ?>" maxlength="120" placeholder="Buscar por título, contenido o autor...">
                    <select id="filtro-categoria" name="categoria" aria-label="Seleccionar categoría">
                        <option value="">Todos los temas</option>
                        <?php foreach (['Cambios normativos', 'Impuestos y DIAN', 'Facturación', 'Contabilidad', 'General'] as $categoriaOpcion): ?>
                            <option value="<?php echo foro_h($categoriaOpcion); ?>" <?php echo $categoriaSeleccionada === $categoriaOpcion ? 'selected' : ''; ?>><?php echo foro_h($categoriaOpcion); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="foro-button primary" type="submit">Buscar</button>
                    <?php if ($busqueda !== '' || $categoriaSeleccionada !== ''): ?><a class="foro-button" href="<?php echo URLROOT; ?>/foro">Limpiar</a><?php endif; ?>
                </div>
                <?php if ($busqueda !== '' || $categoriaSeleccionada !== ''): ?><p class="foro-muted" style="margin-top:8px">Filtros aplicados: <strong><?php echo foro_h($busqueda !== '' ? $busqueda : 'sin texto'); ?></strong><?php echo $categoriaSeleccionada !== '' ? ' · ' . foro_h($categoriaSeleccionada) : ''; ?> · <?php echo count($publicaciones); ?> tema(s)</p><?php endif; ?>
            </form>
            <?php if (!$publicaciones): ?>
                <div class="foro-empty"><strong><?php echo ($busqueda !== '' || $categoriaSeleccionada !== '') ? 'No encontramos temas con esos filtros.' : 'Todavía no hay temas publicados.'; ?></strong><br><?php echo ($busqueda !== '' || $categoriaSeleccionada !== '') ? 'Prueba con otras palabras, cambia la categoría o limpia los filtros.' : 'Inicia la conversación con una novedad normativa o una pregunta contable.'; ?></div>
            <?php else: ?>
                <div class="foro-topics">
                    <?php foreach ($publicaciones as $tema): ?>
                        <?php $temaId = (int)$tema['id']; $comentarios = $comentariosPorTema[$temaId] ?? []; ?>
                        <article class="foro-topic" id="tema-<?php echo $temaId; ?>">
                            <div class="foro-topic-head">
                                <div>
                                    <span class="foro-tag"><?php echo foro_h($tema['categoria']); ?></span>
                                    <h3 style="margin-top:10px"><?php echo foro_h($tema['titulo']); ?></h3>
                                    <div class="foro-meta">Por <?php echo foro_h(trim(($tema['autor'] ?? '') . ' ' . ($tema['apellido'] ?? ''))); ?> · <?php echo foro_h(foro_fecha($tema['fecha_creacion'])); ?></div>
                                </div>
                                <span class="foro-tag"><?php echo count($comentarios); ?> respuesta(s)</span>
                            </div>
                            <div class="foro-topic-content"><?php echo foro_h($tema['contenido']); ?></div>
                            <div class="foro-comments">
                                <p class="foro-muted" style="margin:0 0 12px">Lee las respuestas de los contadores y participa en la conversación.</p>
                                <a class="foro-button primary" href="<?php echo URLROOT; ?>/foro/detalle/<?php echo $temaId; ?>">Abrir chat · Ver respuestas (<?php echo count($comentarios); ?>)</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>
</body>
</html>
