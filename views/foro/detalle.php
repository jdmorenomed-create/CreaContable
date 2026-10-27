<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$tema = $data['tema'] ?? [];
$comentarios = $data['comentarios'] ?? [];
$csrf = $data['csrf'] ?? '';
$mensaje = $data['mensaje'] ?? '';
$error = $data['error'] ?? '';
function foro_det_h($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function foro_det_fecha($fecha) { $t = strtotime((string)$fecha); return $t ? date('d/m/Y · H:i', $t) : ''; }
$usuarioActual = (int)($_SESSION['user_id'] ?? 0);
$temaIdActual = (int)($tema['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo foro_det_h($tema['titulo'] ?? 'Conversación'); ?> | CreaContable</title>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/theme.css">
<script src="<?php echo URLROOT; ?>/public/js/theme.js" defer></script>
<style>
:root{--chat-bg:#f4f7fb;--chat-card:#fff;--chat-text:#24344d;--chat-muted:#64748b;--chat-border:#dce4ef;--chat-blue:#244d82;--chat-blue-soft:#e2edff;--chat-gold:#f59e0b}
*{box-sizing:border-box}body{margin:0;background:var(--chat-bg);color:var(--chat-text);font-family:Inter,Segoe UI,Arial,sans-serif}
.detalle-nav{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px clamp(16px,4vw,48px);background:var(--chat-card);border-bottom:1px solid var(--chat-border)}
.detalle-brand{font-weight:800;color:var(--chat-text);text-decoration:none;font-size:1.1rem}.detalle-brand span{color:#d97706}.detalle-actions{display:flex;gap:10px;flex-wrap:wrap}.det-link{display:inline-flex;align-items:center;padding:9px 13px;border:1px solid var(--chat-border);border-radius:9px;text-decoration:none;color:var(--chat-text);background:var(--chat-card);font-weight:650}
.detalle-layout{width:min(1000px,calc(100% - 32px));margin:24px auto 70px;display:block}
.chat-main{min-width:0}.chat-banner{background:linear-gradient(125deg,#e7efff 0%,#f8fafc 100%);border:1px solid #cad8f0;border-radius:16px;padding:22px 24px;margin-bottom:16px}.chat-banner h1{font-size:clamp(1.35rem,3vw,1.9rem);margin:12px 0 8px;overflow-wrap:anywhere;color:#24344d}.chat-banner p{color:#52647c;line-height:1.6;margin:0}.chat-category{display:inline-flex;padding:5px 10px;border-radius:999px;background:#dce9fb;color:#244d82;font-weight:750;font-size:.78rem}
.chat-alert{padding:12px 15px;margin-bottom:14px;border-radius:10px}.chat-alert.ok{background:#e7f8ee;color:#17643c;border:1px solid #b4e3c5}.chat-alert.err{background:#fff0f0;color:#982c2c;border:1px solid #f1c1c1}.chat-panel{background:var(--chat-card);border:1px solid var(--chat-border);border-radius:16px;overflow:hidden;box-shadow:0 10px 32px rgba(26,47,77,.06)}.chat-head{padding:16px 20px;border-bottom:1px solid var(--chat-border);display:flex;align-items:center;justify-content:space-between;gap:12px}.chat-head h2{font-size:1.05rem;margin:0}.chat-head small{color:var(--chat-muted)}.chat-stream{padding:20px;display:flex;flex-direction:column;gap:15px;background:#f4f7fb;max-height:calc(100vh - 390px);min-height:230px;overflow:auto;scroll-behavior:smooth}.chat-message{max-width:min(85%,760px);padding:13px 15px;border-radius:14px;background:#fff;border:1px solid #dce4ef;align-self:flex-start;box-shadow:0 2px 7px rgba(15,23,42,.03)}.chat-message.mine{align-self:flex-end;background:#e2edff;border-color:#c9dcfb}.chat-message .author{font-size:.85rem;font-weight:800;color:#244d82}.chat-message .time{font-size:.75rem;color:#728096;margin-left:6px}.chat-message p{margin:8px 0 0;white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.6}.chat-compose{padding:17px 20px;border-top:1px solid var(--chat-border);background:var(--chat-card)}.chat-compose label{display:block;font-weight:750;margin-bottom:8px}.chat-compose textarea{width:100%;min-height:95px;resize:vertical;padding:13px;border:1px solid #cbd5e1;border-radius:11px;background:#fff;color:#24344d;font:inherit;line-height:1.5}.chat-compose textarea:focus{outline:3px solid #c8ddff;border-color:#6c9de4}.chat-compose-foot{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:10px}.chat-submit{border:0;border-radius:10px;padding:11px 17px;background:#f59e0b;color:#211606;font-weight:800;cursor:pointer}.chat-submit:hover{filter:brightness(.96)}.chat-empty{padding:28px;text-align:center;color:var(--chat-muted)}
/* Modo oscuro: conserva la paleta azul marino y ámbar de la referencia enviada. */
body.theme-dark{--chat-bg:#10141c;--chat-card:#1a2230;--chat-text:#e8edf5;--chat-muted:#a8b5c8;--chat-border:#344054;background:var(--chat-bg)!important;color:var(--chat-text)!important}
body.theme-dark .detalle-nav{background:#151b26!important;border-color:#30394a!important}body.theme-dark .detalle-brand,body.theme-dark .det-link{color:#edf2fa!important}body.theme-dark .det-link{background:#202838!important;border-color:#344054!important}
body.theme-dark .chat-banner{background:linear-gradient(125deg,#1c3454,#192332)!important;border-color:#344d70!important;color:#edf3fb!important}body.theme-dark .chat-banner h1{color:#f0f5ff!important}body.theme-dark .chat-banner p{color:#d0dced!important}body.theme-dark .chat-category{background:#2a4263!important;color:#d8e8ff!important}body.theme-dark .chat-stream{background:#131c29!important}body.theme-dark .chat-message{background:#202d40!important;border-color:#38475d!important;color:#edf3fb!important}body.theme-dark .chat-message.mine{background:#1e3b60!important;border-color:#315b8b!important}body.theme-dark .chat-message .author{color:#b9d6ff!important}body.theme-dark .chat-compose textarea{background:#111a27!important;color:#edf3fb!important;border-color:#46566e!important}body.theme-dark .chat-submit{background:#f59e0b!important;color:#171717!important}body.theme-dark .chat-alert.ok{background:#173b2d!important;color:#c4f5dc!important;border-color:#28664b!important}body.theme-dark .chat-alert.err{background:#48272c!important;color:#ffd9dd!important;border-color:#78414a!important}
</style>
</head>
<body>
<header class="detalle-nav"><a class="detalle-brand" href="<?php echo URLROOT; ?>/foro">Crea<span>Contable</span> · Foro profesional</a><div class="detalle-actions"><a class="det-link" href="<?php echo URLROOT; ?>/foro">← Volver al foro</a><a class="det-link" href="<?php echo URLROOT; ?>/dashboard">Panel de control</a></div></header>
<main class="detalle-layout">

<section class="chat-main">
<?php if ($mensaje !== ''): ?><div class="chat-alert ok" role="status"><?php echo foro_det_h($mensaje); ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="chat-alert err" role="alert"><?php echo foro_det_h($error); ?></div><?php endif; ?>
<section class="chat-banner"><span class="chat-category"><?php echo foro_det_h($tema['categoria'] ?? 'General'); ?></span><h1><?php echo foro_det_h($tema['titulo'] ?? 'Tema del foro'); ?></h1><p>Publicado por <strong><?php echo foro_det_h(trim(($tema['autor'] ?? '').' '.($tema['apellido'] ?? ''))); ?></strong> · <?php echo foro_det_h(foro_det_fecha($tema['fecha_creacion'] ?? '')); ?></p><p style="margin-top:12px;white-space:pre-wrap;overflow-wrap:anywhere"><?php echo foro_det_h($tema['contenido'] ?? ''); ?></p></section>
<section class="chat-panel" aria-label="Conversación del tema"><div class="chat-head"><h2>Chat de respuestas</h2><small><?php echo count($comentarios); ?> respuesta(s)</small></div>
<div class="chat-stream" id="chat-stream">
<div class="chat-message"><div><span class="author"><?php echo foro_det_h(trim(($tema['autor'] ?? '').' '.($tema['apellido'] ?? ''))); ?></span><span class="time"><?php echo foro_det_h(foro_det_fecha($tema['fecha_creacion'] ?? '')); ?></span></div><p><?php echo foro_det_h($tema['contenido'] ?? ''); ?></p></div>
<?php foreach ($comentarios as $comentario): $esMio = (int)($comentario['usuario_id'] ?? 0) === $usuarioActual; ?>
<div class="chat-message<?php echo $esMio ? ' mine' : ''; ?>"><div><span class="author"><?php echo foro_det_h(trim(($comentario['autor'] ?? '').' '.($comentario['apellido'] ?? ''))); ?></span><span class="time"><?php echo foro_det_h(foro_det_fecha($comentario['fecha_creacion'] ?? '')); ?></span></div><p><?php echo foro_det_h($comentario['contenido'] ?? ''); ?></p></div>
<?php endforeach; ?>
<?php if (!$comentarios): ?><div class="chat-empty">Todavía no hay respuestas. Inicia la conversación con una respuesta útil y respetuosa.</div><?php endif; ?>
</div>
<form class="chat-compose" action="<?php echo URLROOT; ?>/foro/comentar/<?php echo $temaIdActual; ?>" method="post"><input type="hidden" name="csrf_token" value="<?php echo foro_det_h($csrf); ?>"><label for="respuesta-chat">Escribe un mensaje</label><textarea id="respuesta-chat" name="contenido" maxlength="5000" required placeholder="Escribe tu respuesta para la comunidad contable..."></textarea><div class="chat-compose-foot"><small style="color:var(--chat-muted)">Máximo 5.000 caracteres</small><button class="chat-submit" type="submit">Enviar mensaje ➤</button></div></form>
</section></section></main>
<script>document.addEventListener('DOMContentLoaded',function(){var stream=document.getElementById('chat-stream');if(stream)stream.scrollTop=stream.scrollHeight;});</script>
</body></html>
