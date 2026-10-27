<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$userNombre = $_SESSION['user_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiénes somos - CreaContable</title>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
<style>
:root{--page-bg:#0b0c10;--panel:#161820;--text:#f3f4f6;--muted:#aeb4c2;--line:#292d38;--blue:#0d6efd}
*{box-sizing:border-box}body{margin:0;background:var(--page-bg);color:var(--text);font-family:'Segoe UI',Arial,sans-serif}
header{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px clamp(18px,5vw,70px);background:#121318;border-bottom:1px solid var(--line)}
.brand{display:flex;align-items:center;gap:10px;color:white;text-decoration:none;flex-shrink:0}.logo-box{background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);color:#0b0c10;width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;box-shadow:0 4px 10px rgba(245,158,11,.25)}.logo-text-wrapper{display:flex;flex-direction:column}.logo-brand-text{font-size:1.6rem;font-weight:700;line-height:1}.logo-crea{color:#fff;font-style:italic;font-family:Georgia,serif}.logo-contable{color:#f59e0b}.logo-arrows{display:flex;flex-direction:column;gap:2px;margin-top:3px}.logo-arrow-line{height:2px;background:#f59e0b;position:relative;border-radius:2px}.logo-arrow-line::after{content:'';position:absolute;right:-2px;top:-2px;width:0;height:0;border-top:3px solid transparent;border-bottom:3px solid transparent;border-left:5px solid #f59e0b}.logo-arrow-line.top{width:100%}.logo-arrow-line.bottom{width:80%}
nav{display:flex;gap:18px;flex-wrap:wrap}nav a,.back{color:#dbeafe;text-decoration:none;font-weight:600}nav a:hover,.back:hover{color:#60a5fa}
main{width:min(1100px,92%);margin:48px auto 64px}.hero{padding:clamp(28px,5vw,56px);border:1px solid var(--line);border-radius:22px;background:linear-gradient(135deg,#171b27,#101116);box-shadow:0 18px 50px #0004}
.eyebrow{display:inline-block;background:#0d6efd22;color:#8bbaff;border:1px solid #0d6efd66;padding:7px 12px;border-radius:999px;font-size:.85rem;font-weight:700}
h1{font-size:clamp(2rem,5vw,3.2rem);line-height:1.1;margin:20px 0 16px}p{line-height:1.75;color:var(--muted)}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-top:24px}.card{padding:24px;background:var(--panel);border:1px solid var(--line);border-radius:16px}.card h2{font-size:1.15rem;margin-top:0}.team{margin-top:38px}.team-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.person{display:flex;gap:16px;align-items:center}.person img,.initial{width:76px;height:76px;object-fit:cover;border-radius:50%;background:#252b39;display:grid;place-items:center;color:#9cc4ff;font-weight:800;flex-shrink:0}.cta{display:inline-block;margin-top:18px;background:var(--blue);color:white;text-decoration:none;padding:12px 18px;border-radius:10px;font-weight:700}footer{text-align:center;border-top:1px solid var(--line);padding:22px;color:var(--muted)}
@media(max-width:700px){header{align-items:flex-start;flex-direction:column}.logo-brand-text{font-size:1.35rem}.grid,.team-grid{grid-template-columns:1fr}.person{align-items:flex-start}}
</style>

    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/theme.css">
    <script src="<?php echo URLROOT; ?>/public/js/theme.js" defer></script>
</head>
<body>
<header>
<a class="brand logo-container" href="<?php echo URLROOT; ?>/home" aria-label="CreaContable, ir al inicio"><div class="logo-box" aria-hidden="true">🧮</div><div class="logo-text-wrapper"><div class="logo-brand-text"><span class="logo-crea">Crea</span><span class="logo-contable">Contable</span></div><div class="logo-arrows" aria-hidden="true"><div class="logo-arrow-line top"></div><div class="logo-arrow-line bottom"></div></div></div></a>
<nav><a href="<?php echo URLROOT; ?>/home">Inicio</a><a href="<?php echo URLROOT; ?>/dashboard#consultas">Consultas</a><a href="<?php echo URLROOT; ?>/home#nuestros-servicios">Servicios</a></nav>
</header>
<main>
<section class="hero">
<span class="eyebrow">Conoce nuestro proyecto</span>
<h1>Transformamos la gestión contable en Colombia</h1>
<p>CreaContable es una plataforma digital académica diseñada para conectar a emprendedores, pequeñas empresas y profesionales independientes con orientación contable. Busca reunir en un solo lugar las consultas, la solicitud de citas y el seguimiento de cada caso.</p>
<a class="cta" href="<?php echo URLROOT; ?>/home">Volver al inicio</a>
</section>
<section class="grid" aria-label="Nuestros pilares">
<article class="card"><h2>Orientación contable</h2><p>Un espacio para organizar dudas sobre procesos contables, obligaciones tributarias y gestión financiera.</p></article>
<article class="card"><h2>Atención organizada</h2><p>Las consultas y sus estados ayudan a dar seguimiento a las solicitudes y a mantener una comunicación clara entre cliente y contador.</p></article>
<article class="card"><h2>Uso responsable de la información</h2><p>El proyecto promueve el uso cuidadoso de la información compartida y el respeto por la privacidad de las personas.</p></article>
</section>
<section class="team">
<h2>Las personas detrás del proyecto</h2>
<p>Conoce al equipo creador de CreaContable.</p>
<div class="team-grid">
<article class="card person"><div class="initial" aria-hidden="true">JM</div><div><h3>Jonatan Moreno</h3><p>Co-creador de CreaContable</p></div></article>
<article class="card person"><img src="<?php echo URLROOT; ?>/public/img/juan-beltran.png" alt="Fotografía de Juan Beltrán"><div><h3>Juan Beltrán</h3><p>Co-creador de CreaContable</p></div></article>
</div>
</section>
</main>
<footer>© <?php echo date('Y'); ?> CreaContable · Proyecto académico</footer>
</body>
</html>
