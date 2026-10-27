<?php
class MarketplaceController extends Controller {
    private $db;
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) { header('Location: '.URLROOT.'/auth/login'); exit; }
        $this->db = Database::connect();
        // Tablas auxiliares para chat privado por contratación y notificaciones.
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS contratacion_mensajes (id INT AUTO_INCREMENT PRIMARY KEY, contratacion_id INT NOT NULL, remitente_id INT NOT NULL, mensaje TEXT NOT NULL, creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_chat_contratacion (contratacion_id), CONSTRAINT fk_cm_contratacion FOREIGN KEY (contratacion_id) REFERENCES contrataciones(id) ON DELETE CASCADE, CONSTRAINT fk_cm_remitente FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $this->db->exec("CREATE TABLE IF NOT EXISTS contratacion_notificaciones (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NOT NULL, contratacion_id INT NOT NULL, titulo VARCHAR(180) NOT NULL, detalle VARCHAR(500) NOT NULL, leida TINYINT(1) NOT NULL DEFAULT 0, creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_notif_usuario (usuario_id, leida), CONSTRAINT fk_cn_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE, CONSTRAINT fk_cn_contratacion FOREIGN KEY (contratacion_id) REFERENCES contrataciones(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $this->db->exec("CREATE TABLE IF NOT EXISTS servicio_notificaciones (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NOT NULL, servicio_id INT NOT NULL, titulo VARCHAR(180) NOT NULL, detalle VARCHAR(500) NOT NULL, leida TINYINT(1) NOT NULL DEFAULT 0, creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_sn_usuario (usuario_id, leida), CONSTRAINT fk_sn_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE, CONSTRAINT fk_sn_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_contables(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $this->db->exec("CREATE TABLE IF NOT EXISTS servicios_metodos_pago (id INT AUTO_INCREMENT PRIMARY KEY, servicio_id INT NOT NULL, metodo VARCHAR(80) NOT NULL, datos_cuenta VARCHAR(255) NOT NULL, titular VARCHAR(180) NULL, activo TINYINT(1) NOT NULL DEFAULT 1, creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_smp_servicio (servicio_id), CONSTRAINT fk_smp_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_contables(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $cols=$this->db->query("SHOW COLUMNS FROM contrataciones")->fetchAll(PDO::FETCH_COLUMN);
            foreach (["metodo_pago_cliente VARCHAR(80) NULL","referencia_pago_cliente VARCHAR(180) NULL","fecha_pago_cliente DATE NULL","banco_origen VARCHAR(120) NULL","comprobante_url VARCHAR(255) NULL","notas_pago TEXT NULL","pago_enviado_en DATETIME NULL"] as $def) { $name=strtok($def,' '); if (!in_array($name,$cols,true)) $this->db->exec("ALTER TABLE contrataciones ADD COLUMN $def"); }
            $this->db->exec("CREATE TABLE IF NOT EXISTS solicitudes_explicacion_rechazo (id INT AUTO_INCREMENT PRIMARY KEY, contador_id INT NOT NULL, asunto VARCHAR(180) NOT NULL, mensaje TEXT NOT NULL, respuesta_admin TEXT NULL, estado ENUM('pendiente','respondida') NOT NULL DEFAULT 'pendiente', creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP, respondido_en TIMESTAMP NULL, INDEX idx_solicitud_estado (estado), CONSTRAINT fk_ser_contador FOREIGN KEY (contador_id) REFERENCES usuarios(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) { error_log('No se pudieron preparar tablas del marketplace: '.$e->getMessage()); }
    }
    private function role() { return (int)($_SESSION['user_rol'] ?? 2); }
    private function dashboard($msg='') {
        if ($msg !== '') $_SESSION['marketplace_msg'] = $msg;
        $destino = ($this->role() === 3) ? '/marketplace/admin' : '/marketplace/servicios';
        header('Location: '.URLROOT.$destino); exit;
    }
    public function servicios() {
        $rol = $this->role();
        if ($rol === 3) { header('Location: '.URLROOT.'/marketplace/admin'); exit; }
        $db = $this->db;
        $servicios = [];
        $contrataciones = [];
        $misServicios = [];
        $historialPagos = [];
        $perfil = null;
        $notificaciones = []; $notificacionesNoLeidas = 0;
        try {
            $servicios = $db->query("SELECT s.*,u.nombre AS contador_nombre,u.apellido AS contador_apellido,p.matricula_profesional,p.experiencia_anos,p.especialidades,p.estado_verificacion FROM servicios_contables s JOIN usuarios u ON u.id=s.contador_id JOIN contador_perfiles p ON p.usuario_id=u.id WHERE s.estado IN ('publicado','rechazado') AND p.estado_verificacion='aprobado' ORDER BY CASE WHEN s.estado='publicado' THEN 0 ELSE 1 END, s.creado_en DESC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { $servicios = []; }
        if ($rol === 1) {
            try { $st=$db->prepare("SELECT id,titulo,especialidad,descripcion,precio,estado,creado_en FROM servicios_contables WHERE contador_id=? ORDER BY creado_en DESC"); $st->execute([$_SESSION['user_id']]); $misServicios=$st->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { $misServicios=[]; }
            try { $st=$db->prepare("SELECT * FROM contador_perfiles WHERE usuario_id=? LIMIT 1"); $st->execute([$_SESSION['user_id']]); $perfil=$st->fetch(PDO::FETCH_ASSOC) ?: null; } catch (PDOException $e) { $perfil=null; }
            try { $st=$db->prepare("SELECT c.id,c.monto,c.moneda,c.estado_pago,c.metodo_pago_cliente,c.referencia_pago_cliente,c.fecha_pago_cliente,c.banco_origen,c.pago_enviado_en,c.creado_en,s.titulo,cl.nombre AS cliente_nombre,cl.apellido AS cliente_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios cl ON cl.id=c.cliente_id WHERE c.contador_id=? AND (c.pago_enviado_en IS NOT NULL OR c.estado_pago IN ('pagado','procesando','reembolsado','fallido')) ORDER BY COALESCE(c.pago_enviado_en,c.creado_en) DESC LIMIT 300"); $st->execute([$_SESSION['user_id']]); $historialPagos=$st->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { $historialPagos=[]; }
        }
        try {
            if ($rol === 2) {
                $st=$db->prepare("SELECT c.*,s.titulo,u.nombre AS contador_nombre,u.apellido AS contador_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios u ON u.id=c.contador_id WHERE c.cliente_id=? ORDER BY c.creado_en DESC");
                $st->execute([$_SESSION['user_id']]); $contrataciones=$st->fetchAll(PDO::FETCH_ASSOC);
            } elseif ($rol === 1) {
                $st=$db->prepare("SELECT c.*,s.titulo,u.nombre AS cliente_nombre,u.apellido AS cliente_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios u ON u.id=c.cliente_id WHERE c.contador_id=? ORDER BY c.creado_en DESC");
                $st->execute([$_SESSION['user_id']]); $contrataciones=$st->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) { $contrataciones=[]; }
        try {
            // Mostrar una sola notificación de mensajes por contrato, incluso si existen duplicados antiguos.
            // Las notificaciones de pagos se mantienen independientes.
            $st=$db->prepare("SELECT n.id,n.contratacion_id,n.titulo,n.detalle,n.leida,n.creado_en
                FROM contratacion_notificaciones n
                WHERE n.usuario_id=? AND n.leida=0 AND ( ? = 1 OR n.titulo NOT LIKE 'Nuevo comprobante de pago%' )
                  AND (n.titulo NOT LIKE 'Nuevo mensaje sobre %' OR n.id=(
                    SELECT MAX(n2.id) FROM contratacion_notificaciones n2
                    WHERE n2.usuario_id=n.usuario_id AND n2.contratacion_id=n.contratacion_id
                      AND n2.leida=0 AND n2.titulo LIKE 'Nuevo mensaje sobre %'
                  ))
                ORDER BY n.creado_en DESC LIMIT 100");
            $st->execute([$_SESSION['user_id'], $rol === 1 ? 1 : 0]); $notificaciones=$st->fetchAll(PDO::FETCH_ASSOC);
            // Avisos de moderación del administrador sobre servicios del contador.
            if ($rol === 1) {
                try {
                    $sn=$db->prepare("SELECT id,servicio_id AS contratacion_id,titulo,detalle,leida,creado_en,'servicio' AS tipo FROM servicio_notificaciones WHERE usuario_id=? AND leida=0 ORDER BY creado_en DESC LIMIT 100");
                    $sn->execute([$_SESSION['user_id']]);
                    foreach ($sn->fetchAll(PDO::FETCH_ASSOC) as $aviso) { $aviso['tipo']='servicio'; $notificaciones[]=$aviso; }
                } catch (PDOException $e) {}
            }
            usort($notificaciones, function($a,$b){ return strcmp($b['creado_en']??'', $a['creado_en']??''); });
            // El indicador refleja notificaciones visibles, no cada mensaje individual.
            $notificacionesNoLeidas=count($notificaciones);
        } catch (PDOException $e) { $notificaciones=[]; $notificacionesNoLeidas=0; }
        $this->view('marketplace/servicios', ['rol'=>$rol,'servicios'=>$servicios,'misServicios'=>$misServicios,'contrataciones'=>$contrataciones,'historialPagos'=>$historialPagos,'perfil'=>$perfil,'notificaciones'=>$notificaciones,'notificacionesNoLeidas'=>$notificacionesNoLeidas]);
    }

    /** Oculta una notificación para el usuario autenticado. */
    public function quitarNotificacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->dashboard(); }
        $uid=(int)($_SESSION['user_id'] ?? 0);
        $id=(int)($_POST['id'] ?? 0);
        $tipo=($_POST['tipo'] ?? 'contratacion') === 'servicio' ? 'servicio' : 'contratacion';
        if ($uid > 0 && $id > 0) {
            try {
                if ($tipo === 'servicio') {
                    $st=$this->db->prepare('DELETE FROM servicio_notificaciones WHERE id=? AND usuario_id=?');
                } else {
                    $st=$this->db->prepare('DELETE FROM contratacion_notificaciones WHERE id=? AND usuario_id=?');
                }
                $st->execute([$id,$uid]);
            } catch (PDOException $e) { error_log('No se pudo quitar la notificación: '.$e->getMessage()); }
        }
        header('Location: '.URLROOT.'/marketplace/servicios#notificaciones'); exit;
    }

    /** El contador confirma manualmente el pago solo después de verificar el ingreso real. */
    public function confirmarPago($id=null) {
        if ($this->role() !== 1) { http_response_code(403); exit('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->dashboard(); }
        $contratoId=(int)$id;
        if ($contratoId < 1) { $this->dashboard('Contratación no válida.'); }
        try {
            $st=$this->db->prepare("SELECT c.id,c.cliente_id,c.contador_id,c.estado_pago,s.titulo FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id WHERE c.id=? AND c.contador_id=? LIMIT 1");
            $st->execute([$contratoId,(int)$_SESSION['user_id']]);
            $contrato=$st->fetch(PDO::FETCH_ASSOC);
            if (!$contrato) { $this->dashboard('No puedes modificar el pago de esta contratación.'); }
            if ($contrato['estado_pago'] === 'pagado') { $this->dashboard('Este pago ya estaba confirmado.'); }
            if ($contrato['estado_pago'] !== 'procesando') { $this->dashboard('Solo puedes confirmar pagos que el cliente haya reportado.'); }
            $up=$this->db->prepare("UPDATE contrataciones SET estado_pago='pagado' WHERE id=? AND contador_id=? AND estado_pago='procesando'");
            $up->execute([$contratoId,(int)$_SESSION['user_id']]);
            if ($up->rowCount() > 0) {
                $n=$this->db->prepare("INSERT INTO contratacion_notificaciones (usuario_id,contratacion_id,titulo,detalle) VALUES (?,?,?,?)");
                $n->execute([(int)$contrato['cliente_id'],$contratoId,'Pago confirmado','El contador confirmó el pago del servicio: '.$contrato['titulo'].'.']);
                $this->dashboard('Pago confirmado. Se notificó al cliente.');
            }
            $this->dashboard('No se pudo confirmar el pago. Actualiza la página e inténtalo de nuevo.');
        } catch (PDOException $e) {
            error_log('Error al confirmar pago de contratación: '.$e->getMessage());
            $this->dashboard('No se pudo confirmar el pago.');
        }
    }

    public function crearServicio() {
        if ($this->role() !== 1) { http_response_code(403); exit('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->dashboard(); }
        $titulo=trim($_POST['titulo'] ?? ''); $descripcion=trim($_POST['descripcion'] ?? '');
        $especialidad=trim($_POST['especialidad'] ?? ''); $precio=(float)($_POST['precio'] ?? 0);
        $mat=trim($_POST['matricula_profesional']??''); $exp=(int)($_POST['experiencia_anos']??-1);
        $uni=trim($_POST['universidad']??''); $esp=trim($_POST['especialidades']??'');
        $bio=trim($_POST['biografia']??''); $doc=trim($_POST['documento_verificacion']??'');
        if (!$mat || $exp<0 || $exp>70 || !$uni || !$esp || !$bio) $this->dashboard('Para crear un servicio debes completar todos los datos de verificación profesional.');
        if ($titulo==='' || $descripcion==='' || $especialidad==='' || $precio<1000) $this->dashboard('Completa los datos del servicio y un precio válido.');
        $q=$this->db->prepare("SELECT estado_verificacion FROM contador_perfiles WHERE usuario_id=?");
        $q->execute([$_SESSION['user_id']]); $perfil=$q->fetch(PDO::FETCH_ASSOC);
        $estadoActual=$perfil['estado_verificacion']??'';
        // Cada envío de los datos profesionales se guarda junto con el formulario del servicio.
        $estadoNuevo='aprobado';
        $q=$this->db->prepare("INSERT INTO contador_perfiles (usuario_id,matricula_profesional,experiencia_anos,universidad,especialidades,biografia,documento_verificacion,estado_verificacion) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE matricula_profesional=VALUES(matricula_profesional),experiencia_anos=VALUES(experiencia_anos),universidad=VALUES(universidad),especialidades=VALUES(especialidades),biografia=VALUES(biografia),documento_verificacion=VALUES(documento_verificacion),estado_verificacion=VALUES(estado_verificacion)");
        $q->execute([$_SESSION['user_id'],$mat,$exp,$uni,$esp,$bio,$doc,$estadoNuevo]);
        $q=$this->db->prepare("INSERT INTO servicios_contables (contador_id,titulo,descripcion,especialidad,precio,estado) VALUES (?,?,?,?,?,'pendiente_revision')");
        $q->execute([$_SESSION['user_id'],$titulo,$descripcion,$especialidad,$precio]);
        $servicioId=(int)$this->db->lastInsertId();
        $metodos=$_POST['metodo_pago']??[]; $datos=$_POST['datos_pago']??[]; $titulares=$_POST['titular_pago']??[];
        if (is_array($metodos)) { $ins=$this->db->prepare('INSERT INTO servicios_metodos_pago (servicio_id,metodo,datos_cuenta,titular) VALUES (?,?,?,?)'); foreach($metodos as $i=>$m){ $m=trim((string)$m); $d=trim((string)($datos[$i]??'')); $t=trim((string)($titulares[$i]??'')); if($m!=='' && $d!=='') $ins->execute([$servicioId,mb_substr($m,0,80),mb_substr($d,0,255),mb_substr($t,0,180)]); } }
        $this->dashboard('Servicio enviado a revisión administrativa. Los métodos de pago quedaron guardados si completaste sus datos.');
    }
    public function contratar($id=null) {
        if ($this->role() !== 2) { http_response_code(403); exit('Solo los clientes pueden contratar servicios.'); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->dashboard();
        $servicioId=(int)$id;
        $q=$this->db->prepare("SELECT id,contador_id,precio FROM servicios_contables WHERE id=? AND estado='publicado' LIMIT 1");
        $q->execute([$servicioId]); $servicio=$q->fetch(PDO::FETCH_ASSOC);
        if (!$servicio) $this->dashboard('Este servicio ya no está disponible. Actualiza el Centro de servicios.');
        if ((int)$servicio['contador_id']===(int)$_SESSION['user_id']) $this->dashboard('No puedes contratar tu propio servicio.');
        $q=$this->db->prepare("INSERT INTO contrataciones (servicio_id,cliente_id,contador_id,monto,moneda,estado_pago) VALUES (?,?,?,?,'COP','pendiente')");
        $q->execute([$servicioId,$_SESSION['user_id'],$servicio['contador_id'],$servicio['precio']]);
        $contratacionId=(int)$this->db->lastInsertId();
        // No se notifica al contador como contratado/pagado hasta que una pasarela confirme el pago.
        header('Location: '.URLROOT.'/marketplace/pago/'.$contratacionId); exit;
    }
    public function pago($id=null) {
        if ($this->role() !== 2) { http_response_code(403); exit('Solo los clientes pueden pagar contrataciones.'); }
        $id=(int)$id;
        $st=$this->db->prepare("SELECT c.*,s.titulo FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id WHERE c.id=? AND c.cliente_id=? LIMIT 1");
        $st->execute([$id,(int)$_SESSION['user_id']]); $contrato=$st->fetch(PDO::FETCH_ASSOC);
        if (!$contrato) { http_response_code(404); exit('Contratación no encontrada.'); }
        $metodo=''; $metodos=[];
        try { $m=$this->db->prepare("SELECT DISTINCT TRIM(metodo) AS metodo,TRIM(datos_cuenta) AS datos_cuenta,TRIM(COALESCE(titular,'')) AS titular FROM servicios_metodos_pago WHERE servicio_id=? AND activo=1 AND TRIM(metodo)<>'' AND TRIM(datos_cuenta)<>'' ORDER BY metodo"); $m->execute([(int)$contrato['servicio_id']]); $metodos=$m->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) { $metodos=[]; }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $metodo=trim((string)($_POST['metodo_pago']??''));
            $referencia=trim((string)($_POST['referencia_pago']??'')); $fecha=trim((string)($_POST['fecha_pago']??'')); $banco=trim((string)($_POST['banco_origen']??'')); $comprobante=trim((string)($_POST['comprobante_url']??'')); $notas=trim((string)($_POST['notas_pago']??''));
            $permitidos=array_column($metodos,'metodo');
            if (!$metodos || !in_array($metodo,$permitidos,true)) { $_SESSION['marketplace_msg']='Selecciona uno de los métodos de pago configurados por el contador.'; header('Location: '.URLROOT.'/marketplace/pago/'.$id); exit; }
            if ($referencia==='' || $fecha==='' || $banco==='') { $_SESSION['marketplace_msg']='Indica la referencia, fecha de transferencia y banco de origen.'; header('Location: '.URLROOT.'/marketplace/pago/'.$id); exit; }
            if ($comprobante!=='' && !filter_var($comprobante,FILTER_VALIDATE_URL)) { $_SESSION['marketplace_msg']='El enlace del comprobante no es válido.'; header('Location: '.URLROOT.'/marketplace/pago/'.$id); exit; }
            $st=$this->db->prepare("UPDATE contrataciones SET proveedor_pago=?,metodo_pago_cliente=?,referencia_pago_cliente=?,fecha_pago_cliente=?,banco_origen=?,comprobante_url=?,notas_pago=?,pago_enviado_en=NOW(),estado_pago='procesando' WHERE id=? AND cliente_id=? AND estado_pago IN ('pendiente','procesando')");
            $st->execute([$metodo,$metodo,$referencia,$fecha,$banco,$comprobante?:null,$notas?:null,$id,(int)$_SESSION['user_id']]);
            if ($st->rowCount()>0) { $n=$this->db->prepare("INSERT INTO contratacion_notificaciones (usuario_id,contratacion_id,titulo,detalle) VALUES (?,?,?,?)"); $n->execute([(int)$contrato['contador_id'],$id,'Nuevo comprobante de pago','El cliente envió datos de transferencia para '.$contrato['titulo'].'. Verifica el ingreso en tu cuenta antes de iniciar el trabajo.']); }
            $_SESSION['marketplace_msg']='Comprobante enviado. Ya puedes conversar con el contador por el chat privado de esta contratación. El pago quedará confirmado cuando el contador verifique el ingreso.';
            header('Location: '.URLROOT.'/marketplace/chat/'.$id); exit;
        }
        $this->view('marketplace/pago',['contrato'=>$contrato,'metodo'=>$metodo,'metodos'=>$metodos]);
    }

    public function crearAdministrador() {
        if ($this->role() !== 3) { http_response_code(403); exit('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->dashboard();
        $nombre=trim($_POST['nombre']??''); $apellido=trim($_POST['apellido']??'');
        $email=filter_var(trim($_POST['email']??''),FILTER_VALIDATE_EMAIL); $password=$_POST['password']??'';
        if (!$nombre || !$email || strlen($password)<10 || !preg_match('/[A-Z]/',$password) || !preg_match('/[0-9]/',$password) || !preg_match('/[^A-Za-z0-9]/',$password)) $this->dashboard('Completa nombre y correo válido. La contraseña debe tener 10 caracteres, mayúscula, número y símbolo.');
        $q=$this->db->prepare("INSERT INTO usuarios (rol_id,rol,nombre,apellido,email,password,estado) VALUES (3,'admin',?,?,?,?,'activo')");
        try { $q->execute([$nombre,$apellido,$email,password_hash($password,PASSWORD_DEFAULT)]); }
        catch (PDOException $e) { $this->dashboard('No se pudo crear el administrador. Comprueba si el correo ya está registrado.'); }
        $this->dashboard('Cuenta de administrador creada correctamente.');
    }
    public function moderarServicio($id=null) {
        if ($this->role() !== 3) { http_response_code(403); exit('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->dashboard();
        $estado=$_POST['estado']??'';
        if (!in_array($estado,['publicado','rechazado'],true)) $this->dashboard('Estado de servicio no válido.');
        $q=$this->db->prepare("SELECT id,contador_id,titulo,estado FROM servicios_contables WHERE id=? LIMIT 1");
        $q->execute([(int)$id]); $servicio=$q->fetch(PDO::FETCH_ASSOC);
        if (!$servicio) $this->dashboard('No se encontró el servicio.');
        $q=$this->db->prepare("UPDATE servicios_contables SET estado=? WHERE id=?"); $q->execute([$estado,(int)$id]);
        // Crear aviso solo cuando cambia realmente el estado. Evita que reaparezca al volver a guardar el mismo estado.
        if (($servicio['estado'] ?? '') !== $estado) {
            $tituloEstado=($estado==='publicado')?'Servicio profesional aprobado':'Servicio profesional rechazado';
            $detalleEstado=($estado==='publicado')
                ? 'El administrador aprobó tu servicio «'.$servicio['titulo'].'». Ya puede aparecer como disponible para contratación.'
                : 'El administrador rechazó tu servicio «'.$servicio['titulo'].'». No se podrá contratar mientras esté rechazado.';
            $n=$this->db->prepare("INSERT INTO servicio_notificaciones (usuario_id,servicio_id,titulo,detalle) VALUES (?,?,?,?)");
            $n->execute([(int)$servicio['contador_id'],(int)$servicio['id'],$tituloEstado,$detalleEstado]);
        }
        $this->dashboard('Estado del servicio actualizado.');
    }

    public function guardarPerfil() {
        if ($this->role() !== 1) { http_response_code(403); exit('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->dashboard();
        $mat=trim($_POST['matricula_profesional']??''); $exp=(int)($_POST['experiencia_anos']??-1);
        $uni=trim($_POST['universidad']??''); $esp=trim($_POST['especialidades']??'');
        $bio=trim($_POST['biografia']??''); $doc=trim($_POST['documento_verificacion']??'');
        if (!$mat || $exp<0 || $exp>70 || !$uni || !$esp || !$bio) $this->dashboard('Completa los campos obligatorios de verificación.');
        $q=$this->db->prepare("INSERT INTO contador_perfiles (usuario_id,matricula_profesional,experiencia_anos,universidad,especialidades,biografia,documento_verificacion,estado_verificacion) VALUES (?,?,?,?,?,?,?,'pendiente') ON DUPLICATE KEY UPDATE matricula_profesional=VALUES(matricula_profesional),experiencia_anos=VALUES(experiencia_anos),universidad=VALUES(universidad),especialidades=VALUES(especialidades),biografia=VALUES(biografia),documento_verificacion=VALUES(documento_verificacion),estado_verificacion='pendiente'");
        $q->execute([$_SESSION['user_id'],$mat,$exp,$uni,$esp,$bio,$doc]);
        $this->dashboard('Perfil enviado a validación. No podrás publicar hasta que la administración lo apruebe.');
    }

    public function chat($id=null) {
        $contratacionId=(int)$id; $uid=(int)$_SESSION['user_id']; $rol=$this->role();
        $st=$this->db->prepare("SELECT c.*,s.titulo,cl.nombre AS cliente_nombre,cl.apellido AS cliente_apellido,co.nombre AS contador_nombre,co.apellido AS contador_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios cl ON cl.id=c.cliente_id JOIN usuarios co ON co.id=c.contador_id WHERE c.id=? LIMIT 1");
        $st->execute([$contratacionId]); $contrato=$st->fetch(PDO::FETCH_ASSOC);
        if (!$contrato || !in_array($uid,[(int)$contrato['cliente_id'],(int)$contrato['contador_id']],true)) { http_response_code(403); exit('No tienes acceso a este chat privado.'); }
        try { $st=$this->db->prepare("UPDATE contratacion_notificaciones SET leida=1 WHERE usuario_id=? AND contratacion_id=? AND titulo LIKE 'Nuevo mensaje sobre %'"); $st->execute([$uid,$contratacionId]); } catch(PDOException $e) {}
        $st=$this->db->prepare('SELECT m.*,u.nombre,u.apellido FROM contratacion_mensajes m JOIN usuarios u ON u.id=m.remitente_id WHERE m.contratacion_id=? ORDER BY m.creado_en ASC LIMIT 500');
        $st->execute([$contratacionId]); $mensajes=$st->fetchAll(PDO::FETCH_ASSOC);
        $this->view('marketplace/chat',['contrato'=>$contrato,'mensajes'=>$mensajes,'uid'=>$uid,'rol'=>$rol]);
    }

    public function enviarMensaje($id=null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->dashboard(); }
        $contratacionId=(int)$id; $uid=(int)$_SESSION['user_id']; $mensaje=trim((string)($_POST['mensaje']??''));
        $st=$this->db->prepare('SELECT id,cliente_id,contador_id,servicio_id FROM contrataciones WHERE id=? LIMIT 1');
        $st->execute([$contratacionId]); $c=$st->fetch(PDO::FETCH_ASSOC);
        if (!$c || !in_array($uid,[(int)$c['cliente_id'],(int)$c['contador_id']],true)) { http_response_code(403); exit('No tienes acceso a esta contratación.'); }
        if ($mensaje==='' || strlen($mensaje)>5000) { $_SESSION['marketplace_msg']='Escribe un mensaje de hasta 5000 caracteres.'; header('Location: '.URLROOT.'/marketplace/chat/'.$contratacionId); exit; }
        $st=$this->db->prepare('INSERT INTO contratacion_mensajes (contratacion_id,remitente_id,mensaje) VALUES (?,?,?)');
        $st->execute([$contratacionId,$uid,$mensaje]);
        $destino=$uid===(int)$c['cliente_id']?(int)$c['contador_id']:(int)$c['cliente_id'];
        $st=$this->db->prepare('SELECT titulo FROM servicios_contables WHERE id=?'); $st->execute([(int)$c['servicio_id']]); $titulo=(string)$st->fetchColumn();
        // Agrupar mensajes: si ya existe una notificación pendiente de mensajes para este
        // destinatario y contrato, actualizarla en vez de crear otra.
        $buscarNotif=$this->db->prepare("SELECT id FROM contratacion_notificaciones WHERE usuario_id=? AND contratacion_id=? AND leida=0 AND titulo LIKE 'Nuevo mensaje sobre %' ORDER BY id DESC LIMIT 1");
        $buscarNotif->execute([$destino,$contratacionId]);
        $notifId=$buscarNotif->fetchColumn();
        if ($notifId) {
            $actualizarNotif=$this->db->prepare("UPDATE contratacion_notificaciones SET titulo=?,detalle='Tienes nuevos mensajes en el chat privado de esta contratación.',creado_en=CURRENT_TIMESTAMP WHERE id=?");
            $actualizarNotif->execute(['Nuevo mensaje sobre '.$titulo,(int)$notifId]);
        } else {
            $crearNotif=$this->db->prepare('INSERT INTO contratacion_notificaciones (usuario_id,contratacion_id,titulo,detalle) VALUES (?,?,?,?)');
            $crearNotif->execute([$destino,$contratacionId,'Nuevo mensaje sobre '.$titulo,'Tienes nuevos mensajes en el chat privado de esta contratación.']);
        }
        $clear=$this->db->prepare('UPDATE contratacion_notificaciones SET leida=1 WHERE usuario_id=? AND contratacion_id=?');
        $clear->execute([$uid,$contratacionId]);
        header('Location: '.URLROOT.'/marketplace/chat/'.$contratacionId); exit;
    }

    public function admin() {
        if ($this->role() !== 3) { http_response_code(403); exit('Acceso denegado'); }
        $section = trim($_GET['seccion'] ?? 'usuarios');
        $allowed = ['usuarios','detalle_usuario','historial_usuario','comentarios','foros','servicios','pagos'];
        if (!in_array($section, $allowed, true)) $section = 'usuarios';

        $q = trim($_GET['q'] ?? '');
        $commentQ = trim($_GET['comentario'] ?? '');
        $forumQ = trim($_GET['foro'] ?? '');
        $profileQ = trim($_GET['contador'] ?? '');
        $serviceQ = trim($_GET['servicio'] ?? '');
        $paymentQ = trim($_GET['pago'] ?? '');

        $users = $profiles = $payments = $services = $comments = $forums = $requests = []; $userDetail=null; $userProfile=null; $historyConsultas=$historyForums=$historyComments=$historyServices=$historyContracts=$historyClientContracts=$historyPayments=[]; $historyPaidTotal=0.0;
        if ($section === 'detalle_usuario') { try { $st=$this->db->prepare('SELECT id,nombre,apellido,email,telefono,rol,rol_id,estado,created_at FROM usuarios WHERE id=? LIMIT 1'); $st->execute([(int)($_GET['id']??0)]); $userDetail=$st->fetch(PDO::FETCH_ASSOC) ?: null; if($userDetail && ($userDetail['rol']==='contador' || (int)$userDetail['rol_id']===1)){ $st=$this->db->prepare('SELECT matricula_profesional,experiencia_anos,universidad,especialidades,biografia,documento_verificacion FROM contador_perfiles WHERE usuario_id=? LIMIT 1'); $st->execute([(int)$userDetail['id']]); $userProfile=$st->fetch(PDO::FETCH_ASSOC) ?: null; } } catch(PDOException $e) {} }

        if ($section === 'historial_usuario') {
            $historyId=(int)($_GET['id']??0);
            try {
                $st=$this->db->prepare('SELECT id,nombre,apellido,email,telefono,rol,rol_id,estado,created_at FROM usuarios WHERE id=? LIMIT 1');
                $st->execute([$historyId]); $userDetail=$st->fetch(PDO::FETCH_ASSOC) ?: null;
                if ($userDetail) {
                    try { $st=$this->db->prepare('SELECT id,titulo,detalle,estado,fecha_creacion FROM consultas WHERE usuario_id=? ORDER BY fecha_creacion DESC'); $st->execute([$historyId]); $historyConsultas=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                    try { $st=$this->db->prepare('SELECT p.id,p.titulo,p.categoria,p.contenido,p.fecha_creacion,(SELECT COUNT(*) FROM foro_comentarios c WHERE c.publicacion_id=p.id) total_comentarios FROM foro_publicaciones p WHERE p.usuario_id=? ORDER BY p.fecha_creacion DESC'); $st->execute([$historyId]); $historyForums=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                    try { $st=$this->db->prepare('SELECT c.id,c.contenido,c.fecha_creacion,p.titulo FROM foro_comentarios c LEFT JOIN foro_publicaciones p ON p.id=c.publicacion_id WHERE c.usuario_id=? ORDER BY c.fecha_creacion DESC'); $st->execute([$historyId]); $historyComments=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                    try { $st=$this->db->prepare('SELECT id,titulo,descripcion,especialidad,precio,estado,creado_en FROM servicios_contables WHERE contador_id=? ORDER BY creado_en DESC'); $st->execute([$historyId]); $historyServices=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                    try { $st=$this->db->prepare("SELECT c.id,c.creado_en,c.estado_pago,c.monto,c.moneda,s.titulo,s.contador_id,cl.nombre cliente_nombre,cl.apellido cliente_apellido,co.nombre contador_nombre,co.apellido contador_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios cl ON cl.id=c.cliente_id JOIN usuarios co ON co.id=c.contador_id WHERE c.cliente_id=? OR c.contador_id=? ORDER BY c.creado_en DESC"); $st->execute([$historyId,$historyId]); $historyContracts=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                    $isCounter = (($userDetail['rol'] ?? '') === 'contador' || (int)($userDetail['rol_id'] ?? 0) === 1);
                    if ($isCounter) {
                        try { $st=$this->db->prepare("SELECT c.id,c.creado_en,c.estado_pago,c.monto,c.moneda,s.titulo,cl.nombre cliente_nombre,cl.apellido cliente_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios cl ON cl.id=c.cliente_id WHERE c.contador_id=? ORDER BY c.creado_en DESC"); $st->execute([$historyId]); $historyContracts=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                        try { $st=$this->db->prepare("SELECT COALESCE(SUM(monto),0) FROM contrataciones WHERE contador_id=? AND estado_pago='pagado'"); $st->execute([$historyId]); $historyPaidTotal=(float)$st->fetchColumn(); } catch(PDOException $e) {}
                    } else {
                        try { $st=$this->db->prepare("SELECT c.id,c.creado_en,c.estado_pago,c.monto,c.moneda,s.titulo,co.nombre contador_nombre,co.apellido contador_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios co ON co.id=c.contador_id WHERE c.cliente_id=? ORDER BY c.creado_en DESC"); $st->execute([$historyId]); $historyClientContracts=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                        try { $st=$this->db->prepare("SELECT c.id,c.creado_en,c.estado_pago,c.monto,c.moneda,c.proveedor_pago,c.referencia_pago,s.titulo FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id WHERE c.cliente_id=? ORDER BY c.creado_en DESC"); $st->execute([$historyId]); $historyPayments=$st->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) {}
                    }
                }
            } catch(PDOException $e) {}
        }
        try {
            $like = '%'.$q.'%';
            $sql = "SELECT id,nombre,apellido,email,rol,rol_id,telefono,estado,created_at FROM usuarios WHERE (nombre LIKE ? OR apellido LIKE ? OR email LIKE ?)";
            $args = [$like,$like,$like];
            $rol = trim($_GET['rol'] ?? '');
            if (in_array($rol,['cliente','contador','admin'],true)) { $sql .= " AND rol=?"; $args[]=$rol; }
            $sql .= " ORDER BY id DESC LIMIT 300";
            $st=$this->db->prepare($sql); $st->execute($args); $users=$st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { $rol=''; }

        try {
            $like='%'.$profileQ.'%';
            $st=$this->db->prepare("SELECT p.*,u.nombre,u.apellido,u.email,u.telefono FROM contador_perfiles p JOIN usuarios u ON u.id=p.usuario_id WHERE (u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ? OR p.matricula_profesional LIKE ? OR p.especialidades LIKE ?) ORDER BY p.creado_en DESC LIMIT 300");
            $st->execute([$like,$like,$like,$like,$like]); $profiles=$st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {}

        try {
            $like='%'.$serviceQ.'%';
            $st=$this->db->prepare("SELECT s.*,u.nombre,u.apellido,u.email FROM servicios_contables s JOIN usuarios u ON u.id=s.contador_id WHERE (s.titulo LIKE ? OR s.especialidad LIKE ? OR s.descripcion LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ?) ORDER BY s.creado_en DESC LIMIT 300");
            $st->execute([$like,$like,$like,$like,$like]); $services=$st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {}

        try {
            $like='%'.$paymentQ.'%';
            $st=$this->db->prepare("SELECT c.*,s.titulo,u.nombre AS cliente_nombre,u.apellido AS cliente_apellido,co.nombre AS contador_nombre,co.apellido AS contador_apellido FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios u ON u.id=c.cliente_id JOIN usuarios co ON co.id=c.contador_id WHERE (s.titulo LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ? OR co.nombre LIKE ? OR co.apellido LIKE ? OR c.estado_pago LIKE ? OR c.referencia_pago LIKE ?) ORDER BY c.creado_en DESC LIMIT 300");
            $st->execute([$like,$like,$like,$like,$like,$like,$like,$like]); $payments=$st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {}

        try {
            $like='%'.$commentQ.'%';
            $st=$this->db->prepare("SELECT c.id,c.contenido,c.fecha_creacion,u.nombre,u.apellido,u.email,p.titulo FROM foro_comentarios c JOIN usuarios u ON u.id=c.usuario_id JOIN foro_publicaciones p ON p.id=c.publicacion_id WHERE c.contenido LIKE ? OR u.nombre LIKE ? OR u.email LIKE ? OR p.titulo LIKE ? ORDER BY c.fecha_creacion DESC LIMIT 300");
            $st->execute([$like,$like,$like,$like]); $comments=$st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {}

        try {
            $like='%'.$forumQ.'%';
            $st=$this->db->prepare("SELECT p.id,p.titulo,p.categoria,p.contenido,p.fecha_creacion,u.nombre,u.apellido,u.email,(SELECT COUNT(*) FROM foro_comentarios c WHERE c.publicacion_id=p.id) AS total_comentarios FROM foro_publicaciones p JOIN usuarios u ON u.id=p.usuario_id WHERE p.titulo LIKE ? OR p.categoria LIKE ? OR p.contenido LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ? ORDER BY p.fecha_creacion DESC LIMIT 300");
            $st->execute([$like,$like,$like,$like,$like]); $forums=$st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {}

        $this->view('marketplace/admin', [
            'section'=>$section,'users'=>$users,'profiles'=>$profiles,'payments'=>$payments, 'userDetail'=>$userDetail,'userProfile'=>$userProfile,
            'services'=>$services,'comments'=>$comments,'forums'=>$forums,'requests'=>$requests, 'historyConsultas'=>$historyConsultas,'historyForums'=>$historyForums,'historyComments'=>$historyComments,'historyServices'=>$historyServices,'historyContracts'=>$historyContracts,'historyClientContracts'=>$historyClientContracts,'historyPayments'=>$historyPayments,'historyPaidTotal'=>$historyPaidTotal,
            'q'=>$q,'rol'=>$rol ?? '', 'comentario'=>$commentQ,'foro'=>$forumQ,
            'contador'=>$profileQ,'servicio'=>$serviceQ,'pago'=>$paymentQ
        ]);
    }
    public function validarPerfil($id=null) {
        if ($this->role() !== 3) { http_response_code(403); exit('Acceso denegado'); }
        $id=(int)$id; $estado=$_POST['estado']??'';
        if (!in_array($estado,['aprobado','rechazado','pendiente'],true)) $this->dashboard('Estado de validación no válido.');
        $stmt=$this->db->prepare("UPDATE contador_perfiles SET estado_verificacion=?, observaciones_admin=? WHERE usuario_id=?");
        $stmt->execute([$estado,trim($_POST['observaciones']??''),$id]);
        $this->dashboard('Se actualizó el estado del perfil profesional.');
    }
}
