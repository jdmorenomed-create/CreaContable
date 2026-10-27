<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Consulta</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">

    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/theme.css">
    <script src="<?php echo URLROOT; ?>/public/js/theme.js" defer></script>
</head>
<body>
    <div class="container">
        <h2>Editar Consulta</h2>
        <form action="<?php echo URLROOT; ?>/consulta/editar/<?php echo $data['consulta']['id']; ?>" method="POST">
            <label>Título:</label><br>
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($data['consulta']['titulo']); ?>" required><br><br>
            
            <label>Detalle:</label><br>
            <textarea name="detalle" required><?php echo htmlspecialchars($data['consulta']['detalle']); ?></textarea><br><br>
            
            <button type="submit">Guardar Cambios</button>
            <a href="<?php echo URLROOT; ?>/dashboard">Cancelar</a>
        </form>
    </div>
</body>
</html>