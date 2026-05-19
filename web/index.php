<?php
session_start();

$host = "mysql";
$user = "root";
$password = "root123";
$database = "mi_base";

$conn = new mysqli($host, $user, $password, $database);

$conexion_ok = true;
$error_conexion = "";

if ($conn->connect_error) {
    $conexion_ok = false;
    $error_conexion = $conn->connect_error;
}

$mensaje = "";
$error = "";

// Cerrar sesión
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Login simple
if (isset($_POST["login"])) {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    if ($usuario === "admin" && $clave === "admin123") {
        $_SESSION["login"] = true;
        $_SESSION["usuario"] = "Administrador";
        header("Location: index.php");
        exit;
    } else {
        $error = "Credenciales inválidas. Intente nuevamente.";
    }
}

// Crear usuario en la base de datos
if (isset($_POST["crear_usuario"]) && isset($_SESSION["login"]) && $conexion_ok) {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);

    if ($nombre != "" && $correo != "") {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $correo);

        if ($stmt->execute()) {
            $mensaje = "El usuario ha sido registrado con éxito.";
        } else {
            $error = "Ocurrió un error al registrar el usuario.";
        }

        $stmt->close();
    } else {
        $error = "Por favor, complete todos los campos requeridos.";
    }
}

// Obtener usuarios
$usuarios = [];

if ($conexion_ok && isset($_SESSION["login"])) {
    $resultado = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");

    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control | EC2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* NUEVO ESTILO VISUAL */
        :root {
            --color-primario: #059669; /* Verde esmeralda */
            --color-secundario: #1e293b; /* Gris oscuro pizarron */
            --color-fondo: #f1f5f9;
            --color-texto: #334155;
            --radio-bordes: 8px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--color-fondo);
            color: var(--color-texto);
        }

        .navbar {
            background-color: var(--color-secundario);
            color: white;
            padding: 25px 20px;
            text-align: center;
            border-bottom: 5px solid var(--color-primario);
        }

        .navbar h1 {
            margin: 0;
            font-size: 30px;
            letter-spacing: 1px;
        }

        .navbar p {
            margin-top: 8px;
            color: #cbd5e1;
            font-size: 16px;
        }

        .contenedor-principal {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Estilos del Login */
        .caja-login {
            max-width: 400px;
            margin: 60px auto;
            background: white;
            padding: 40px 30px;
            border-radius: var(--radio-bordes);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--color-primario);
        }

        .caja-login h2 {
            margin-top: 0;
            color: var(--color-secundario);
            text-align: center;
            margin-bottom: 25px;
        }

        /* Estructura del Panel */
        .layout-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .columna-formulario {
            flex: 1;
            min-width: 300px;
        }

        .columna-tabla {
            flex: 2;
            min-width: 300px;
        }

        .tarjeta {
            background: white;
            padding: 30px;
            border-radius: var(--radio-bordes);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .tarjeta h3 {
            margin-top: 0;
            color: var(--color-secundario);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Formularios y Botones */
        label {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-secundario);
        }

        input {
            width: 100%;
            padding: 12px 15px;
            margin-top: 8px;
            border: 1px solid #cbd5e1;
            border-radius: var(--radio-bordes);
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--color-primario);
        }

        .btn {
            display: block;
            width: 100%;
            margin-top: 25px;
            padding: 14px;
            background-color: var(--color-primario);
            color: white;
            border: none;
            border-radius: var(--radio-bordes);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #047857;
        }

        .btn-peligro {
            background-color: #ef4444;
            display: inline-block;
            width: auto;
            margin-top: 0;
            padding: 10px 20px;
            font-size: 14px;
        }

        .btn-peligro:hover {
            background-color: #dc2626;
        }

        .btn-secundario {
            background-color: var(--color-secundario);
        }

        .btn-secundario:hover {
            background-color: #0f172a;
        }

        /* Cabecera del Panel */
        .barra-superior {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: var(--radio-bordes);
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .barra-superior h2 {
            margin: 0;
            color: var(--color-secundario);
        }

        .etiqueta {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 15px;
        }

        .etiqueta-exito { background: #d1fae5; color: #065f46; }
        .etiqueta-error { background: #fee2e2; color: #991b1b; }

        .notificacion {
            padding: 15px;
            border-radius: var(--radio-bordes);
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .notificacion-exito { background: #f0fdf4; border-color: #16a34a; color: #166534; }
        .notificacion-error { background: #fef2f2; border-color: #ef4444; color: #991b1b; }

        /* Tabla */
        .contenedor-tabla {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: var(--color-secundario);
            padding: 15px;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        .pie-pagina {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="navbar">
    <h1>Administración de Servidor Cloud</h1>
    <p>Infraestructura AWS EC2 | Base de Datos MySQL | Entorno Docker</p>
</div>

<?php if (!isset($_SESSION["login"])) { ?>

    <div class="caja-login">
        <h2>Acceso Restringido</h2>
        
        <?php if ($error != "") { ?>
            <div class="notificacion notificacion-error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <label>Nombre de Usuario</label>
            <input type="text" name="usuario" placeholder="Ej: admin" required>

            <label>Clave de Seguridad</label>
            <input type="password" name="clave" placeholder="••••••••" required>

            <button type="submit" name="login" class="btn">Ingresar al Sistema</button>
        </form>
    </div>

<?php } else { ?>

    <div class="contenedor-principal">

        <div class="barra-superior">
            <div>
                <h2>Panel Principal</h2>
                <span style="color: #64748b; font-size: 14px;">Sesión iniciada como: <strong><?php echo $_SESSION["usuario"]; ?></strong></span>
            </div>

            <div style="display: flex; align-items: center;">
                <?php if ($conexion_ok) { ?>
                    <span class="etiqueta etiqueta-exito">MySQL Conectado</span>
                <?php } else { ?>
                    <span class="etiqueta etiqueta-error">Fallo en DB</span>
                <?php } ?>

                <a class="btn btn-peligro" href="index.php?logout=1">Cerrar Sesión</a>
            </div>
        </div>

        <?php if ($mensaje != "") { ?>
            <div class="notificacion notificacion-exito"><?php echo $mensaje; ?></div>
        <?php } ?>

        <?php if ($error != "") { ?>
            <div class="notificacion notificacion-error"><?php echo $error; ?></div>
        <?php } ?>

        <?php if (!$conexion_ok) { ?>
            <div class="notificacion notificacion-error">
                <strong>Error crítico:</strong> <?php echo $error_conexion; ?>
            </div>
        <?php } ?>

        <div class="layout-panel">

            <div class="columna-formulario">
                <div class="tarjeta">
                    <h3>Nuevo Registro</h3>
                    <p style="font-size: 14px; color: #64748b;">Añada nuevos perfiles a la base de datos.</p>

                    <form method="POST">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" placeholder="Ingrese el nombre" required>

                        <label>Dirección de Correo</label>
                        <input type="email" name="correo" placeholder="usuario@dominio.com" required>

                        <button type="submit" name="crear_usuario" class="btn">Registrar Usuario</button>
                    </form>
                    
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">
                    
                    <a class="btn btn-secundario" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>:8080" target="_blank" style="margin-top: 0;">
                        Acceder a phpMyAdmin
                    </a>
                </div>
            </div>

            <div class="columna-tabla">
                <div class="tarjeta">
                    <h3>Directorio de Usuarios</h3>
                    
                    <div class="contenedor-tabla">
                        <table>
                            <thead>
                                <tr>
                                    <th>Identificador</th>
                                    <th>Nombre del Perfil</th>
                                    <th>Contacto</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($usuarios) > 0) { ?>
                                    <?php foreach ($usuarios as $usuario) { ?>
                                        <tr>
                                            <td><strong>#<?php echo $usuario["id"]; ?></strong></td>
                                            <td><?php echo htmlspecialchars($usuario["nombre"]); ?></td>
                                            <td><?php echo htmlspecialchars($usuario["correo"]); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: #94a3b8;">No hay registros en la base de datos.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

<?php } ?>

<div class="pie-pagina">
    Despliegue realizado en AWS EC2 | <?php echo date("Y"); ?>
</div>

</body>
</html>
