# asesoria_contable

## Cambios incluidos en esta versión

- El botón **Responder** del panel de contador abre la sección Chat y carga la conversación del cliente asociado a la consulta.
- El chat tiene listas separadas de **Pendientes** y **Respondidos**.
- Enviar con Enter o con el botón vacía el campo al comenzar el envío; si el servidor falla, el texto vuelve al campo para no perderlo. Se eliminó el doble manejador de envío.
- Roles oficiales: `1 = contador`, `2 = cliente`, `3 = admin`. El inicio de sesión y el dashboard vuelven a leer el rol desde la base de datos.
- Se incluye `config/Basededatos/migracion_roles_contador_admin.sql` para bases ya existentes. Antes de ejecutarlo, exporta una copia de seguridad en phpMyAdmin. La migración sincroniza `usuarios.rol` con `usuarios.rol_id`.

### Para una base ya existente

1. Haz una copia de seguridad de la base de datos.
2. En phpMyAdmin selecciona la base que usa CreaContable y ejecuta `config/Basededatos/migracion_roles_contador_admin.sql` una sola vez.
3. Reemplaza los archivos de la aplicación con los de este ZIP.
4. Cierra sesión y vuelve a entrar para que se lea el rol actualizado.

Si cambias el rol directamente en la base de datos, modifica `rol_id` (1 contador, 2 cliente, 3 admin) o `rol`; el trigger de la migración mantiene ambos campos sincronizados.


## Actualización de roles (importante)
En phpMyAdmin selecciona la base de datos `creacontable` y ejecuta una sola vez el archivo
`config/Basededatos/migracion_roles_contador_admin.sql`. La tabla `roles` debe quedar:
- ID 1: `contador`
- ID 2: `cliente`
- ID 3: `admin`

La migración sincroniza `usuarios.rol` y `usuarios.rol_id`; por eso, si cambias el rol en phpMyAdmin,
ambos campos quedan coordinados. La aplicación vuelve a leer el rol de la base de datos al entrar
a la página principal y al abrir el panel. Cierra sesión y vuelve a entrar si tenías una sesión abierta.


## Corrección del chat (cliente y contador)
- Los mensajes propios aparecen a la derecha y los recibidos a la izquierda en ambos roles.
- Para instalaciones donde `mensajes` todavía no tiene `consulta_id`, el modelo intenta añadir la columna automáticamente.
- Si el chat sigue sin mostrar mensajes, revisa en phpMyAdmin que la tabla `mensajes` tenga la columna `consulta_id`. El archivo `config/Basededatos/corregir_chat_consultas.sql` explica la migración manual; ejecútala solo si esa columna aún no existe.


## Foro profesional para contadores
- Se agregó `/foro`, accesible únicamente para usuarios con rol de contador o administrador.
- Los contadores pueden publicar temas y responder publicaciones de otros miembros.
- Categorías disponibles: cambios normativos, impuestos y DIAN, facturación, contabilidad y general.
- Se usa protección CSRF en los formularios y consultas preparadas para guardar publicaciones y respuestas.
- **Instalación necesaria:** antes de usar el foro, importa `config/Basededatos/foro_contadores.sql` desde phpMyAdmin sobre la base `creacontable`. Haz una copia de seguridad antes de ejecutar cambios en la base de datos.
- El acceso al foro aparece como **Foro profesional** en la barra superior del panel de contador. Los clientes no pueden entrar directamente al foro.


## Corregir relaciones (claves foráneas) en phpMyAdmin

Se agregó `config/Basededatos/corregir_relaciones_tablas.sql`. Antes de ejecutarlo, haz una copia de seguridad de `creacontable`. Luego abre phpMyAdmin, selecciona la base `creacontable`, entra en la pestaña **SQL**, pega/abre el contenido de ese archivo y ejecútalo. El script crea las relaciones que puede crear sin borrar datos y avisa si encuentra registros que apuntan a usuarios o consultas inexistentes. En ese caso, revisa los registros señalados y vuelve a ejecutar el script.

La tabla `notas` que aparece en algunas instalaciones no está definida en el SQL incluido en este ZIP; por eso no se agregó una relación inventada para ella.


### Corregir relaciones de `citas` y `consultas`

Si esas dos tablas siguen sin líneas en el Diseñador de phpMyAdmin, ejecuta `config/Basededatos/conectar_citas_consultas.sql`. Primero muestra registros cuyo `usuario_id` no existe en `usuarios`; debes asignarlos al usuario correcto antes de crear las claves foráneas. El script no reasigna datos automáticamente.


### Relaciones entre citas, consultas, mensajes y usuarios

Para una base de datos ya creada, haz una copia de seguridad y ejecuta en phpMyAdmin > `creacontable` > SQL el archivo `config/Basededatos/conectar_citas_consultas.sql`. El script conecta `citas.usuario_id`, `consultas.usuario_id`, `mensajes.remitente_id` y `mensajes.destinatario_id` con `usuarios.id`. Si detecta IDs históricos que no existen en `usuarios`, crea registros inactivos de referencia sin acceso para conservar los mensajes/citas/consultas y permitir la relación; revisa esos registros después para asociarlos a las personas correctas.


## Nuevas mejoras: modo claro/oscuro y búsqueda del foro
- Se añadió un botón flotante para alternar entre modo claro y oscuro. La preferencia se conserva en el navegador y el botón aparece en las páginas principales de la aplicación.
- En el Foro profesional hay un buscador que filtra temas por título, contenido, categoría o nombre/apellido del autor. La búsqueda usa consultas preparadas y permite limpiar el filtro.
- Para aplicar estos cambios, reemplaza los archivos del proyecto por los incluidos en este ZIP. No requiere cambios en la base de datos.


## Base de datos unificada (un solo archivo SQL)

Toda la estructura necesaria para una instalación nueva, incluidos los métodos de pago y los campos de comprobantes, está reunida en `config/creacontable.sql`. No necesitas importar archivos SQL separados.

**Instalación nueva:** abre phpMyAdmin, entra en la pestaña Importar y selecciona `config/creacontable.sql`. El archivo crea/selecciona la base `creacontable` e instala las tablas.

**Si ya tienes datos:** primero exporta una copia de seguridad. No importes el volcado completo sobre una base con información que quieras conservar; en ese caso, usa la función Exportar de phpMyAdmin para guardar toda la base en un único archivo `.sql` (método Personalizado, todas las tablas seleccionadas, formato SQL, guardar salida en un archivo).

El administrador puede iniciar sesión con una cuenta cuyo rol sea `admin`. Una vez dentro, puede crear administradores adicionales desde el panel. Por seguridad, el formulario público de registro solo permite cliente o contador; para el primer administrador usa una cuenta administrativa creada por el propietario de la instalación.

Las contrataciones crean solicitudes con pago pendiente. La plataforma no confirma cobros reales hasta que se configure la pasarela de pago y su notificación de confirmación.

## Historial de usuario y flujo de pago digital

El historial de administración distingue los datos del contador (foros publicados, servicios ofrecidos, contrataciones recibidas y suma de contrataciones con estado `pagado`) de los datos del cliente (consultas realizadas, servicios contratados e historial de pagos). Los importes recibidos solo suman registros cuyo `estado_pago` es `pagado`.

Al pulsar «Contratar y pagar», se crea una contratación y se redirige a la pantalla de selección de PSE, Nequi, Daviplata o tarjeta. En esta entrega se registra el método elegido y el estado queda `procesando`; no se confirma el pago ni se envía notificación de servicio pagado al contador porque aún hace falta integrar una pasarela real y validar su webhook/credenciales. No se deben almacenar números de tarjeta ni CVV. Cuando se configure el proveedor, su notificación de servidor debe validar firma, importe, moneda y referencia antes de cambiar `estado_pago` a `pagado` y generar la notificación al contador.


Nota de mantenimiento: se retiró del archivo SQL la definición de `metodos_pago_tokens`, porque no se encontraron referencias a ella en el código PHP del proyecto. Para eliminarla de una base de datos ya instalada, primero confirma que no contenga datos que necesites.
