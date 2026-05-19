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
        $error = "Usuario o contraseña incorrectos.";
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
            $mensaje = "Usuario creado correctamente.";
        } else {
            $error = "No se pudo crear el usuario.";
        }

        $stmt->close();
    } else {
        $error = "Debe llenar todos los campos.";
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
    <title>Sistema Web Docker AWS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #eef2f7;
            color: #1f2937;
        }

        header {
            background: linear-gradient(135deg, #0f172a, #1d4ed8);
            color: white;
            padding: 35px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 34px;
        }

        header p {
            margin-top: 10px;
            color: #dbeafe;
            font-size: 17px;
        }

        .contenedor {
            max-width: 1100px;
            margin: 35px auto;
            padding: 20px;
        }

        .login-box {
            max-width: 420px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.10);
        }

        .login-box h2 {
            margin-top: 0;
            color: #1d4ed8;
            text-align: center;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .card h2 {
            margin-top: 0;
            color: #1d4ed8;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
        }

        button, .boton {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 18px;
            background: #1d4ed8;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
        }

        .boton.salir {
            background: #991b1b;
        }

        .boton.oscuro {
            background: #0f172a;
        }

        .top-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .estado {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .ok {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .alerta {
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            overflow: hidden;
            border-radius: 10px;
        }

        th {
            background: #1d4ed8;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background: #f8fafc;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        @media (max-width: 850px) {
            .grid {
                grid-template-columns: 1fr;
            }

            header h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Sistema Web Dockerizado en AWS</h1>
    <p>PHP + MySQL + phpMyAdmin + Docker Compose en una instancia EC2</p>
</header>

<?php if (!isset($_SESSION["login"])) { ?>

    <div class="login-box">
        <h2>Iniciar sesión</h2>
        <p>Ingrese al sistema para ver y crear usuarios en la base de datos.</p>

        <?php if ($error != "") { ?>
            <div class="alerta error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <label>Usuario</label>
            <input type="text" name="usuario" placeholder="admin" required>

            <label>Contraseña</label>
            <input type="password" name="clave" placeholder="admin123" required>

            <button type="submit" name="login">Entrar al sistema</button>
        </form>

        <p style="margin-top: 20px; color: #64748b;">
            Usuario de prueba: <strong>admin</strong><br>
            Contraseña: <strong>admin123</strong>
        </p>
    </div>

<?php } else { ?>

    <main class="contenedor">

        <div class="top-panel">
            <div>
                <h2>Panel de administración</h2>
                <p>Bienvenido, <?php echo $_SESSION["usuario"]; ?>.</p>
            </div>

            <div>
                <?php if ($conexion_ok) { ?>
                    <span class="estado ok">Base de datos conectada</span>
                <?php } else { ?>
                    <span class="estado error">Error de conexión</span>
                <?php } ?>

                <a class="boton salir" href="index.php?logout=1">Cerrar sesión</a>
            </div>
        </div>

        <?php if ($mensaje != "") { ?>
            <div class="alerta ok"><?php echo $mensaje; ?></div>
        <?php } ?>

        <?php if ($error != "") { ?>
            <div class="alerta error"><?php echo $error; ?></div>
        <?php } ?>

        <?php if (!$conexion_ok) { ?>
            <div class="alerta error">
                Error de conexión: <?php echo $error_conexion; ?>
            </div>
        <?php } ?>

        <section class="grid">

            <div class="card">
                <h2>Crear usuario</h2>
                <p>Este formulario inserta datos directamente en la tabla <strong>usuarios</strong> de MySQL.</p>

                <form method="POST">
                    <label>Nombre</label>
                    <input type="text" name="nombre" placeholder="Ejemplo: Juan Pérez" required>

                    <label>Correo</label>
                    <input type="email" name="correo" placeholder="ejemplo@email.com" required>

                    <button type="submit" name="crear_usuario">Guardar usuario</button>
                </form>

                <a class="boton oscuro" href="http://localhost:8080" target="_blank">
                    Abrir phpMyAdmin
                </a>
            </div>

            <div class="card">
                <h2>Usuarios registrados</h2>
                <p>Listado cargado desde la base de datos MySQL.</p>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($usuarios as $usuario) { ?>
                            <tr>
                                <td><?php echo $usuario["id"]; ?></td>
                                <td><?php echo htmlspecialchars($usuario["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($usuario["correo"]); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>

<?php } ?>

<footer>
    Proyecto ejecutado en AWS EC2 usando Docker, PHP, MySQL y phpMyAdmin.
</footer>

</body>
</html>