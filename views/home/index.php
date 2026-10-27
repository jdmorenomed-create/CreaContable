<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId     = $_SESSION['user_id'] ?? null;
$userNombre = $_SESSION['user_nombre'] ?? 'Usuario';
$userEmail  = $_SESSION['user_email'] ?? '';
$rol        = $_SESSION['user_rol'] ?? $_SESSION['rol_id'] ?? null;

$inicial = !empty($userNombre) ? strtoupper(substr(trim($userNombre), 0, 1)) : 'U';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CreaContable - Asesoría Contable en Línea</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: #0b0c10;
            color: #f3f4f6;
        }

        /* BARRA SUPERIOR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
            gap: 14px;
            padding: 16px 30px;
            background-color: #121318;
            border-bottom: 1px solid #1f222e;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            position: sticky;
            top: 0;
            z-index: 1000;
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

        .logo-arrow-line.top { width: 100%; }
        .logo-arrow-line.bottom { width: 80%; }

        .nav-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            list-style: none;
            flex: 1 1 auto;
            min-width: 0;
            white-space: nowrap;
        }

        .nav-menu a {
            text-decoration: none;
            color: #9ca3af;
            font-weight: 500;
            font-size: 0.80rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .nav-menu a:hover { color: #ffffff; }

        .user-section {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .user-section .btn-dark,
        .user-section .btn-gold {
            padding: 9px 11px;
            font-size: 0.78rem;
            gap: 5px;
        }

        .navbar .privacy-menu-link {
            padding: 8px 10px;
            font-size: 0.78rem;
        }

        @media (max-width: 1180px) {
            .navbar { flex-wrap: wrap; padding: 14px 22px; }
            .nav-menu { order: 3; flex: 1 0 100%; flex-wrap: wrap; gap: 12px 18px; }
            .user-section { margin-left: auto; }
        }

        @media (max-width: 620px) {
            .navbar { padding: 12px 14px; }
            .nav-menu { justify-content: flex-start; }
            .user-section { width: 100%; margin-left: 0; }
        }

        .btn-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            transition: opacity 0.2s, transform 0.2s;
        }

        .btn-gold:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-dark {
            background-color: #1a1d26;
            color: #ffffff;
            border: 1px solid #2d3245;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .btn-dark:hover {
            background-color: #242938;
            border-color: #f59e0b;
            color: #f59e0b;
        }

        /* 1. BANNER AZUL DE HERO */
        .hero-blue {
            background-color: #0d6efd;
            color: white;
            padding: 60px 20px 80px 20px;
            text-align: center;
        }

        .badge-cert {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.82rem;
            margin-bottom: 20px;
        }

        .hero-blue h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero-blue h1 span {
            color: #ffc107;
            display: block;
        }

        .hero-blue p {
            font-size: 1.05rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 30px auto;
        }

        /* 2. LOS 3 CUADROS SUPERPUESTOS */
        .cuadros-section {
            max-width: 1150px;
            margin: -50px auto 60px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            position: relative;
            z-index: 10;
        }

        .cuadro-card {
            background-color: #161820;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #252836;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }

        .cuadro-card h3 {
            color: #f59e0b;
            font-size: 1.15rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cuadro-card p {
            color: #8b92a5;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* 3. SERVICIOS CONTAINER */
        .servicios-container {
            max-width: 1150px;
            margin: 40px auto 60px auto;
            padding: 0 20px;
        }

        .servicios-card {
            background-color: #161820;
            border-radius: 16px;
            padding: 35px;
            border: 1px solid #252836;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .servicios-card h2 {
            color: #f59e0b;
            font-size: 1.6rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .servicios-card .subtitulo {
            color: #8b92a5;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .uvt-badge-info {
            display: inline-block;
            background-color: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 10px;
            font-size: 0.95rem;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .servicios-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .servicios-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            background-color: #10121a;
            border-radius: 10px;
            border: 1px solid #1f222e;
            border-left: 4px solid #f59e0b;
        }

        .servicios-item .nombre-servicio {
            font-size: 0.95rem;
            color: #e5e7eb;
            font-weight: 500;
        }

        .servicios-item .precio-uvt {
            background-color: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            font-weight: bold;
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid rgba(245, 158, 11, 0.3);
            white-space: nowrap;
        }

        /* 4. PASO A PASO / PASOS DE INTEGRACIÓN */
        .paso-paso-section {
            max-width: 1150px;
            margin: 60px auto 40px auto;
            padding: 0 20px;
        }

        .paso-paso-tag {
            color: #f59e0b;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .paso-paso-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .paso-paso-subtitle {
            color: #8b92a5;
            font-size: 1rem;
            margin-bottom: 45px;
            max-width: 600px;
        }

        .paso-paso-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        @media (max-width: 900px) {
            .paso-paso-grid { grid-template-columns: 1fr; }
        }

        .pasos-timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
            position: relative;
        }

        .paso-item {
            display: flex;
            gap: 20px;
            position: relative;
            padding-bottom: 30px;
        }

        .paso-item:last-child { padding-bottom: 0; }

        .paso-number-column {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .paso-number {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background-color: #161820;
            border: 2px solid #f59e0b;
            color: #f59e0b;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
            z-index: 2;
        }

        .paso-line {
            width: 2px;
            background-color: #252836;
            flex-grow: 1;
            margin-top: 6px;
            margin-bottom: -6px;
        }

        .paso-item:last-child .paso-line { display: none; }

        .paso-content { padding-top: 5px; }

        .paso-content h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .paso-content p {
            font-size: 0.88rem;
            color: #8b92a5;
            line-height: 1.5;
        }

        /* DASHBOARD INTERACTIVO MOCKUP (ADAPTADO A CREACONTABLE) */
        .preview-container {
            background-color: #161820;
            border-radius: 16px;
            border: 1px solid #252836;
            padding: 22px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .preview-header-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .preview-small-card {
            background-color: #10121a;
            border: 1px solid #252836;
            border-radius: 10px;
            padding: 12px 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-card-icon { font-size: 1.2rem; }

        .preview-card-info h4 {
            font-size: 0.78rem;
            color: #ffffff;
            font-weight: 700;
        }

        .preview-card-info span {
            font-size: 0.7rem;
            color: #10b981;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .preview-dashboard-mock {
            background-color: #0b0c10;
            border-radius: 12px;
            border: 1px solid #1f222e;
            padding: 20px;
        }

        .mock-status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid #1f222e;
        }

        .mock-status-item {
            font-size: 0.8rem;
            color: #8b92a5;
        }

        .mock-status-item strong {
            color: #f59e0b;
            display: block;
            font-size: 0.95rem;
        }

        .mock-chart-bars {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 120px;
            gap: 10px;
            padding-top: 15px;
        }

        .mock-bar-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            height: 100%;
            justify-content: flex-end;
        }

        .mock-bar {
            width: 100%;
            background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%);
            border-radius: 4px 4px 0 0;
            transition: height 0.3s ease;
        }

        .mock-bar-label {
            font-size: 0.68rem;
            color: #8b92a5;
        }

        /* 4.1 SEGURIDAD Y CONTINUIDAD OPERATIVA */
        .seguridad-section {
            max-width: 1150px;
            margin: 0 auto 70px auto;
            padding: 0 20px;
        }

        .seguridad-title {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .seguridad-subtitle {
            color: #8b92a5;
            font-size: 0.98rem;
            margin-bottom: 30px;
        }

        .seguridad-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .seguridad-card {
            background-color: #161820;
            border-radius: 16px;
            padding: 30px 25px;
            border: 1px solid #252836;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .seguridad-card:hover {
            transform: translateY(-4px);
            border-color: rgba(245, 158, 11, 0.4);
        }

        .seguridad-icon-box {
            width: 44px;
            height: 44px;
            background-color: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }

        .seguridad-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .seguridad-card p {
            font-size: 0.88rem;
            color: #8b92a5;
            line-height: 1.55;
        }

        /* 5. PLANES DE SUSCRIPCIÓN */
        .planes-container {
            max-width: 1150px;
            margin: 40px auto 60px auto;
            padding: 0 20px;
        }

        .planes-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .planes-header h2 {
            font-size: 2.2rem;
            color: #ffffff;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .planes-header p {
            color: #8b92a5;
            font-size: 1rem;
        }

        .planes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            align-items: stretch;
        }

        .plan-card {
            background-color: #161820;
            border-radius: 16px;
            padding: 35px 25px;
            border: 1px solid #252836;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            border-color: rgba(245, 158, 11, 0.5);
        }

        .plan-card.popular {
            border: 2px solid #f59e0b;
            background: linear-gradient(180deg, #1c1a24 0%, #161820 100%);
            box-shadow: 0 15px 35px rgba(245, 158, 11, 0.15);
        }

        .badge-popular {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0c10;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .plan-nombre { font-size: 1.4rem; font-weight: bold; color: #ffffff; margin-bottom: 10px; }
        .plan-precio { font-size: 2.2rem; font-weight: 800; color: #f59e0b; margin-bottom: 5px; }
        .plan-precio span { font-size: 0.9rem; color: #8b92a5; font-weight: normal; }

        .plan-uvt {
            font-size: 0.82rem;
            color: #8b92a5;
            margin-bottom: 20px;
            background-color: #0f1015;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .plan-features li {
            font-size: 0.92rem;
            color: #d1d5db;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .plan-features li::before { content: '✓'; color: #f59e0b; font-weight: bold; }

        .plan-btn {
            width: 100%;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95rem;
            display: block;
        }

        /* 6. SECCIÓN SOBRE NOSOTROS */
        .sobre-nosotros-container {
            max-width: 1150px;
            margin: 50px auto 80px auto;
            padding: 0 20px;
        }

        .sobre-nosotros-card {
            background-color: #161820;
            border-radius: 20px;
            padding: 50px 40px;
            border: 1px solid #252836;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .sobre-nosotros-header {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 45px auto;
        }

        .sobre-nosotros-header h2 {
            font-size: 2.2rem;
            color: #ffffff;
            font-weight: 800;
            margin: 15px 0;
        }

        .sobre-nosotros-header p {
            color: #8b92a5;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .pilares-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .pilar-item {
            background-color: #10121a;
            border-radius: 14px;
            padding: 25px;
            border: 1px solid #1f222e;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .pilar-item:hover {
            transform: translateY(-5px);
            border-color: #f59e0b;
        }

        .pilar-icon {
            font-size: 2.2rem;
            margin-bottom: 15px;
        }

        .pilar-item h3 {
            color: #ffffff;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .pilar-item p {
            color: #8b92a5;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* AVATAR DE USUARIO */
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
            border: 2px solid #252836;
            cursor: pointer;
        }

        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 55px;
            background-color: #161820;
            width: 280px;
            border-radius: 14px;
            padding: 20px;
            z-index: 1000;
            border: 1px solid #252836;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
        }

        .user-dropdown.show { display: block; }

        /* Ajustes de lectura y adaptación a móviles */
        html { scroll-behavior: smooth; scroll-padding-top: 90px; }
        body { overflow-x: hidden; line-height: 1.6; }
        p { line-height: 1.65; }
        a:focus-visible, button:focus-visible { outline: 3px solid #f59e0b; outline-offset: 3px; }
        .navbar { flex-wrap: wrap; gap: 16px; padding: 14px clamp(16px, 4vw, 40px); }
        .nav-menu { flex-wrap: wrap; gap: 12px 20px; }
        .nav-menu a { min-height: 40px; }
        .hero-blue h1 { font-size: clamp(2rem, 5vw, 3rem); line-height: 1.15; }
        .hero-blue { padding: clamp(42px, 7vw, 76px) 20px; }
        .cuadros-section, .servicios-container, .paso-paso-section, .planes-container, .sobre-nosotros-container { width: min(100% - 32px, 1150px); }
        .servicios-card, .sobre-nosotros-card { padding: clamp(20px, 4vw, 36px); }
        .team-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:24px; margin-top:28px; }
        .team-card { background:#191c25; border:1px solid #303544; border-radius:12px; padding:24px; text-align:center; }
        .team-photo { width:150px; height:150px; margin:0 auto 16px; border-radius:50%; border:2px dashed #687084; background:#222633; display:flex; align-items:center; justify-content:center; color:#c6cad4; overflow:hidden; }
        .team-photo img { width:100%; height:100%; object-fit:cover; }
        .team-card h3 { color:#f3f4f6; margin-bottom:6px; }
        .team-card p, .legal-section p, .legal-section li { color:#c2c7d2; }
        .legal-section { width:min(100% - 32px, 1050px); margin:36px auto 60px; padding:clamp(22px,4vw,36px); background:#161820; border:1px solid #303544; border-radius:12px; }
        .legal-section h2, .legal-section h3 { color:#f3f4f6; margin:18px 0 8px; }
        .legal-section ul { padding-left:22px; margin:10px 0; }
        .site-footer { padding:24px 16px; text-align:center; color:#aeb4c2; border-top:1px solid #303544; font-size:.92rem; }
        .site-footer a { color:#f5b942; margin:0 8px; }
        @media (max-width: 900px) {
            .navbar { justify-content:center; }
            .nav-menu { order:3; width:100%; justify-content:center; }
            .user-section { margin-left:auto; }
            .cuadros-section { grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr)); }
        }
        @media (max-width: 600px) {
            .navbar { justify-content:space-between; padding:12px 16px; }
            .logo-brand-text { font-size:1.35rem; }
            .nav-menu { justify-content:flex-start; gap:4px 12px; }
            .nav-menu a { font-size:.9rem; }
            .user-section { margin-left:0; }
            .hero-blue p { font-size:1rem; }
            .cuadros-section { margin-top:20px; }
            .servicios-item { align-items:flex-start; gap:12px; flex-direction:column; }
            .planes-grid, .team-grid { grid-template-columns:1fr !important; }
            .paso-paso-title { font-size:1.7rem; }
            .team-photo { width:130px; height:130px; }
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

    <!-- NAVBAR -->
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

        <ul class="nav-menu">
            <?php if (!empty($userId)): ?>
                <li><a href="<?php echo URLROOT; ?>/dashboard#consultas">Consultas</a></li>
                <li><a href="<?php echo URLROOT; ?>/dashboard#citas">Agendar citas</a></li>
            <?php endif; ?>
            <li><a href="#nuestros-servicios">Servicios</a></li>
            <li><a href="#paso-a-paso">Cómo funciona</a></li>
            <li><a href="#nuestros-planes">Planes</a></li>
            <li><a href="<?php echo URLROOT; ?>/home/quienesSomos">Quiénes somos</a></li>
            <li><a href="#" class="privacy-menu-link" onclick="abrirModalTratamiento(event)">Tratamiento de datos</a></li>
        </ul>

        <div class="user-section">
            <?php if (!empty($userId)): ?>
                <div class="user-avatar-container">
                    <button type="button" class="avatar-btn" id="avatarHomeBtn" onclick="toggleHomeDropdown()" title="Perfil de usuario">
                        <?php echo $inicial; ?>
                    </button>

                    <div class="user-dropdown" id="userHomeDropdown">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <strong><?php echo htmlspecialchars($userNombre); ?></strong>
                            <p style="font-size: 0.8rem; color: #8b92a5;"><?php echo htmlspecialchars($userEmail); ?></p>
                        </div>
                        <a href="<?php echo URLROOT; ?>/auth/logout" style="color: #ef4444; text-decoration: none; font-weight: bold; display: block; text-align: center;">🚪 Cerrar sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo URLROOT; ?>/auth/login" class="btn-dark">🔑 Iniciar Sesión</a>
                <a href="<?php echo URLROOT; ?>/auth/register" class="btn-gold">📝 Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- 1. HERO BLUE BANNER -->
    <section class="hero-blue">
        <div class="badge-cert">
            🛡️ Plataforma Contable y Tributaria Certificada
        </div>
        <h1>
            Asesoría Contable en Línea
            <span>Rápida y Segura</span>
        </h1>
        <p>Conecta con contadores profesionales, resuelve tus dudas tributarias, agenda citas y gestiona todos los trámites financieros de tu empresa o emprendimiento en un solo lugar.</p>
        <a href="<?php echo !empty($userId) ? URLROOT . '/dashboard' : URLROOT . '/auth/register'; ?>" class="btn-gold" style="padding: 12px 30px; font-size: 1rem;">Comenzar Ahora ➔</a>
    </section>

    <!-- 2. LOS 3 CUADROS SUPERPUESTOS -->
    <section class="cuadros-section">
        <div class="cuadro-card">
            <h3>Consultas en Línea</h3>
            <p>Escribe tus dudas contables y recibe orientación experta de contadores certificados en tiempo real.</p>
        </div>

        <div class="cuadro-card">
            <h3>📅 Agendamiento de Citas</h3>
            <p>Programa sesiones personalizadas según la disponibilidad del profesional de tu preferencia.</p>
        </div>

        <div class="cuadro-card">
            <h3>📄 Servicios Especializados</h3>
            <p>Contrata servicios contables y tributarios adaptados a las necesidades específicas de tu negocio.</p>
        </div>
    </section>

    <!-- 3. NUESTROS SERVICIOS -->
    <section class="servicios-container" id="nuestros-servicios">
        <div class="servicios-card">
            <h2>💼 Nuestros Servicios</h2>
            <p class="subtitulo">
                Portafolio integral de soluciones contables con tarifas estandarizadas en Unidades de Valor Tributario (UVT):<br>
                <span class="uvt-badge-info">📌 Valor UVT Vigente: COP $52.374</span>
            </p>
            
            <ul class="servicios-list">
                <li class="servicios-item">
                    <span class="nombre-servicio">🏢 Creación de empresas</span>
                    <span class="precio-uvt">10 - 25 UVT</span>
                </li>
                <li class="servicios-item">
                    <span class="nombre-servicio">📑 Inscripción Cámara de Comercio personas naturales y establecimientos de comercio</span>
                    <span class="precio-uvt">3 - 8 UVT</span>
                </li>
                <li class="servicios-item">
                    <span class="nombre-servicio">🆔 Inscripción, Actualización y cancelación de RUT</span>
                    <span class="precio-uvt">1 - 3 UVT</span>
                </li>
                <li class="servicios-item">
                    <span class="nombre-servicio">📲 Facturación y nómina electrónica</span>
                    <span class="precio-uvt">5 - 15 UVT / mes</span>
                </li>
                <li class="servicios-item">
                    <span class="nombre-servicio">💻 Digitalización de transacciones contables</span>
                    <span class="precio-uvt">8 - 20 UVT / mes</span>
                </li>
                <li class="servicios-item">
                    <span class="nombre-servicio">⚖️ Asesorías laborales</span>
                    <span class="precio-uvt">2 - 6 UVT</span>
                </li>
                <li class="servicios-item">
                    <span class="nombre-servicio">📜 Certificados de ingresos</span>
                    <span class="precio-uvt">3 - 6 UVT</span>
                </li>
                <li class="servicios-item">
                    <span class="nombre-servicio">📊 Liquidación y presentación de declaraciones tributarias</span>
                    <span class="precio-uvt">5 - 18 UVT</span>
                </li>
            </ul>
        </div>
    </section>

    <!-- 4. PASO A PASO CON MOCKUP TRIBUTARIO DE CREACONTABLE -->
    <section class="paso-paso-section" id="paso-a-paso">
        <span class="paso-paso-tag">PASO A PASO · CREACONTABLE CONNECT</span>
        <h2 class="paso-paso-title">¿Cómo gestionamos tu contabilidad en CreaContable?</h2>
        <p class="paso-paso-subtitle">Nuestro equipo de contadores certificados se encarga de todo el proceso de tu empresa o perfil independiente, de principio a fin.</p>

        <div class="paso-paso-grid">
            <div class="pasos-timeline">
                <div class="paso-item">
                    <div class="paso-number-column">
                        <div class="paso-number">1</div>
                        <div class="paso-line"></div>
                    </div>
                    <div class="paso-content">
                        <h3>Abrimos tu solicitud</h3>
                        <p>Confirmamos la información de tu empresa, tu régimen tributario, documentos disponibles y el servicio exacto a realizar.</p>
                    </div>
                </div>

                <div class="paso-item">
                    <div class="paso-number-column">
                        <div class="paso-number">2</div>
                        <div class="paso-line"></div>
                    </div>
                    <div class="paso-content">
                        <h3>Recibimos y revisamos tus archivos</h3>
                        <p>Compartes tus extractos, facturas o soportes por nuestra plataforma segura y un contador asignado valida que todo esté en orden.</p>
                    </div>
                </div>

                <div class="paso-item">
                    <div class="paso-number-column">
                        <div class="paso-number">3</div>
                        <div class="paso-line"></div>
                    </div>
                    <div class="paso-content">
                        <h3>Procesamos y organizamos</h3>
                        <p>Digitalizamos tus movimientos, liquidamos tus tributos y preparamos tus balances bajo normativa vigentes.</p>
                    </div>
                </div>

                <div class="paso-item">
                    <div class="paso-number-column">
                        <div class="paso-number">4</div>
                        <div class="paso-line"></div>
                    </div>
                    <div class="paso-content">
                        <h3>Validamos con la DIAN / Entidades</h3>
                        <p>Comprobamos que las declaraciones, reportes y certificados cumplan strictly con las fechas del calendario tributario.</p>
                    </div>
                </div>

                <div class="paso-item">
                    <div class="paso-number-column">
                        <div class="paso-number">5</div>
                        <div class="paso-line"></div>
                    </div>
                    <div class="paso-content">
                        <h3>Agendamos tu sesión personalizada</h3>
                        <p>Coordinamos una cita virtual mediante la plataforma para explicarte los resultados y resolver cualquier inquietud.</p>
                    </div>
                </div>

                <div class="paso-item">
                    <div class="paso-number-column">
                        <div class="paso-number">6</div>
                        <div class="paso-line"></div>
                    </div>
                    <div class="paso-content">
                        <h3>Acompañamiento continuo</h3>
                        <p>Mantienes acceso permanente a nuestro chat en vivo y al historial completo de tus trámites en tu panel de cliente.</p>
                    </div>
                </div>
            </div>

            <!-- DASHBOARD MOCKUP TOTALMENTE ADAPTADO A CREACONTABLE -->
            <div class="preview-container">
                <div class="preview-header-cards">
                    <div class="preview-small-card">
                        <div class="preview-card-icon">🏛️</div>
                        <div class="preview-card-info">
                            <h4>Estado DIAN</h4>
                            <span>✓ Al Día</span>
                        </div>
                    </div>
                    <div class="preview-small-card">
                        <div class="preview-card-icon">📲</div>
                        <div class="preview-card-info">
                            <h4>Factura Elec.</h4>
                            <span>✓ Emitiendo</span>
                        </div>
                    </div>
                    <div class="preview-small-card">
                        <div class="preview-card-icon">📜</div>
                        <div class="preview-card-info">
                            <h4>Certificados</h4>
                            <span>✓ Firmados</span>
                        </div>
                    </div>
                </div>

                <div class="preview-dashboard-mock">
                    <div class="mock-status-row">
                        <div class="mock-status-item">
                            Impuesto Liquidado
                            <strong>IVA & Renta (UVT)</strong>
                        </div>
                        <div class="mock-status-item" style="text-align: right;">
                            Contador Asignado
                            <strong>TP #28491-T</strong>
                        </div>
                    </div>
                    
                    <div class="mock-chart-bars">
                        <div class="mock-bar-wrapper">
                            <div class="mock-bar" style="height: 40%;"></div>
                            <span class="mock-bar-label">Ene</span>
                        </div>
                        <div class="mock-bar-wrapper">
                            <div class="mock-bar" style="height: 65%;"></div>
                            <span class="mock-bar-label">Feb</span>
                        </div>
                        <div class="mock-bar-wrapper">
                            <div class="mock-bar" style="height: 50%;"></div>
                            <span class="mock-bar-label">Mar</span>
                        </div>
                        <div class="mock-bar-wrapper">
                            <div class="mock-bar" style="height: 85%;"></div>
                            <span class="mock-bar-label">Abr</span>
                        </div>
                        <div class="mock-bar-wrapper">
                            <div class="mock-bar" style="height: 70%;"></div>
                            <span class="mock-bar-label">May</span>
                        </div>
                        <div class="mock-bar-wrapper">
                            <div class="mock-bar" style="height: 95%;"></div>
                            <span class="mock-bar-label">Jun</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4.1 SEGURIDAD Y CONTINUIDAD OPERATIVA -->
    <section class="seguridad-section">
        <h2 class="seguridad-title">Seguridad y continuidad operativa</h2>
        <p class="seguridad-subtitle">Tu información viaja y se resguarda con estándares certificados, mientras tu operación sigue en marcha.</p>

        <div class="seguridad-grid">
            <div class="seguridad-card">
                <div class="seguridad-icon-box">📜</div>
                <h3>Certificaciones SSL & DIAN</h3>
                <p>Cifrado de grado bancario para la protección de tus datos financieros e información tributaria confidencial.</p>
            </div>

            <div class="seguridad-card">
                <div class="seguridad-icon-box">🔄</div>
                <h3>Copias de seguridad</h3>
                <p>Respaldo en tiempo real: resguardamos tus declaraciones, facturas e historial de solicitudes para consulta inmediata.</p>
            </div>

            <div class="seguridad-card">
                <div class="seguridad-icon-box">🔒</div>
                <h3>Acceso restringido</h3>
                <p>Solo tú y el contador público asignado a tu caso pueden revisar y gestionar la información financiera de tu empresa.</p>
            </div>
        </div>
    </section>

    <!-- 5. PLANES DE SUSCRIPCIÓN -->
    <section class="planes-container" id="nuestros-planes">
        <div class="planes-header">
            <h2>Planes de Suscripción</h2>
            <p>Elige el plan que mejor se adapte a tu empresa o actividad independiente</p>
        </div>

        <div class="planes-grid">
            <div class="plan-card">
                <div>
                    <div class="plan-nombre">Emprendedor</div>
                    <div class="plan-precio">$149.000 <span>/ mes</span></div>
                    <div class="plan-uvt">Equivalente a ~3 UVT/mes</div>
                    <ul class="plan-features">
                        <li>Consultas ilimitadas por chat</li>
                        <li>Asesoría para actualización del RUT</li>
                        <li>1 Certificado de ingresos anual</li>
                        <li>1 Cita virtual con contador al mes (30 min)</li>
                        <li>Historial de trámites centralizado</li>
                    </ul>
                </div>
                <a href="<?php echo !empty($userId) ? URLROOT . '/pagos/index.php?plan=emprendedor' : URLROOT . '/auth/register?plan=emprendedor'; ?>" class="btn-dark plan-btn">
                    Elegir Plan
                </a>
            </div>

            <div class="plan-card popular">
                <div class="badge-popular">★ MÁS POPULAR</div>
                <div>
                    <div class="plan-nombre">PYME Pro</div>
                    <div class="plan-precio">$390.000 <span>/ mes</span></div>
                    <div class="plan-uvt">Equivalente a ~7.5 UVT/mes</div>
                    <ul class="plan-features">
                        <li>Atención prioritaria por chat</li>
                        <li>Facturación y nómina electrónica</li>
                        <li>Digitalización de transacciones mensuales</li>
                        <li>Liquidación de impuestos (IVA, Retención, ICA)</li>
                        <li>2 Citas virtuales al mes con contador</li>
                    </ul>
                </div>
                <a href="<?php echo !empty($userId) ? URLROOT . '/pagos/index.php?plan=pyme' : URLROOT . '/auth/register?plan=pyme'; ?>" class="btn-gold plan-btn">
                    Elegir Plan Pro
                </a>
            </div>

            <div class="plan-card">
                <div>
                    <div class="plan-nombre">Corporativo</div>
                    <div class="plan-precio">$790.000 <span>/ mes</span></div>
                    <div class="plan-uvt">Equivalente a ~15 UVT/mes</div>
                    <ul class="plan-features">
                        <li>Contador público exclusivo asignado</li>
                        <li>Estados financieros y Declaración de Renta</li>
                        <li>Asesoría laboral y liquidación de nómina</li>
                        <li>Facturación y Nómina electrónica ilimitada</li>
                        <li>Citas virtuales programadas ilimitadas</li>
                    </ul>
                </div>
                <a href="<?php echo !empty($userId) ? URLROOT . '/pagos/index.php?plan=corporativo' : URLROOT . '/auth/register?plan=corporativo'; ?>" class="btn-dark plan-btn">
                    Elegir Plan
                </a>
            </div>
        </div>
    </section>

    <section class="legal-section" id="tratamiento-datos">
        <h2>Tratamiento de datos personales</h2>
        <p><strong>Nota del proyecto:</strong> CreaContable es un proyecto académico. Los datos usados durante el desarrollo, las pruebas y las demostraciones son ficticios y se destinan exclusivamente a fines de simulación. No se deben ingresar datos personales reales de estudiantes, docentes o terceros.</p>
        <h3>¿Qué información contempla la plataforma?</h3>
        <p>En un entorno real, la plataforma podría tratar datos de contacto, información de la cuenta, consultas, solicitudes de citas y documentos que el usuario decida compartir para recibir orientación contable.</p>
        <h3>Finalidad y uso</h3>
        <p>La información se usaría para gestionar el acceso, responder consultas, coordinar citas y prestar los servicios solicitados. No debe utilizarse para fines distintos sin informar al titular y contar con la autorización que corresponda.</p>
        <h3>Responsabilidad y derechos</h3>
        <p>Antes de operar con datos reales, el proyecto deberá identificar al responsable del tratamiento, publicar canales de contacto, definir tiempos de conservación y establecer procedimientos para que las personas puedan consultar, actualizar, corregir o solicitar la supresión de sus datos, según la normativa aplicable en Colombia.</p>
        <p>Esta sección es una base informativa para el prototipo y no reemplaza una política jurídica revisada. Antes de publicar el servicio o recolectar información real, debe completarse y validarse con asesoría adecuada.</p>
    </section>
    <footer class="site-footer">
        <p>© <?php echo date('Y'); ?> CreaContable · Proyecto académico</p>
        <p><a href="<?php echo URLROOT; ?>/home/quienesSomos">Quiénes somos</a><a href="#tratamiento-datos">Tratamiento de datos</a></p>
    </footer>

    <script>
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