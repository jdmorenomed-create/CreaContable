<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">

    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/theme.css">
    <script src="<?php echo URLROOT; ?>/public/js/theme.js" defer></script>
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 40px auto; padding: 20px;">
        <h2>📅 Editar Cita</h2>
        <!-- Se corrigió /cita/ por /citas/ en el action del formulario -->
        <form action="<?php echo URLROOT; ?>/citas/editar/<?php echo $data['cita']['id']; ?>" method="POST">
            <div style="margin-bottom: 15px;">
                <label>Fecha:</label><br>
                <input type="date" name="fecha" value="<?php echo $data['cita']['fecha']; ?>" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label>Hora:</label><br>
                <input type="time" name="hora" value="<?php echo $data['cita']['hora']; ?>" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label>Motivo:</label><br>
                <input type="text" name="motivo" value="<?php echo htmlspecialchars($data['cita']['motivo']); ?>" required style="width: 100%; padding: 8px;">
            </div>

            <button type="submit" class="btn-primary" style="padding: 8px 16px; cursor: pointer;">Guardar Cambios</button>
            <a href="<?php echo URLROOT; ?>/dashboard" style="margin-left: 10px;">Cancelar</a>
        </form>
    </div>
</body>
</html>