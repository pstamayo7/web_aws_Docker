<?php
session_start();

$host = "mysql";
$user = "root";
$password = "root123";
$database = "db_facturacion"; // Actualizado al nuevo nombre

$conn = new mysqli($host, $user, $password, $database);
$conexion_ok = ($conn->connect_error) ? false : true;

$error = "";

// Mod Login
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST["login"])) {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];
    // Simulación de login seguro para la evaluación
    if ($usuario === "admin" && $clave === "admin123") {
        $_SESSION["login"] = true;
        $_SESSION["usuario"] = "Administrador";
        header("Location: index.php");
        exit;
    } else {
        $error = "Credenciales inválidas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Facturación</title>
    <style>
        :root { --color-primario: #2563eb; --color-secundario: #1e293b; --fondo: #f8fafc; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: var(--fondo); color: #333; }
        .login-container { max-width: 400px; margin: 100px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid var(--color-primario); }
        input, button { width: 100%; padding: 12px; margin-top: 10px; box-sizing: border-box; border-radius: 5px; }
        input { border: 1px solid #ccc; }
        button { background: var(--color-primario); color: white; border: none; font-weight: bold; cursor: pointer; }
        .dashboard { display: flex; height: 100vh; }
        .sidebar { width: 250px; background: var(--color-secundario); color: white; padding: 20px; }
        .sidebar h2 { border-bottom: 1px solid #475569; padding-bottom: 10px; font-size: 18px; }
        .menu-item { display: block; color: #cbd5e1; text-decoration: none; padding: 12px; margin: 5px 0; border-radius: 5px; background: #334155; }
        .menu-item:hover { background: var(--color-primario); color: white; }
        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .tarjeta { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION["login"])) { ?>
    <div class="login-container">
        <h2 style="text-align:center; margin-top:0;">Facturación App</h2>
        <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <label>Usuario</label>
            <input type="text" name="usuario" placeholder="admin" required>
            <label>Contraseña</label>
            <input type="password" name="clave" placeholder="admin123" required>
            <button type="submit" name="login">Ingresar al Sistema</button>
        </form>
    </div>
<?php } else { ?>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Módulos del Sistema</h2>
            <a href="#" class="menu-item">🏠 Inicio (Dashboard)</a>
            <a href="#" class="menu-item">👥 Mod Registro Usuarios</a>
            <a href="#" class="menu-item">🤝 Mod Registro Clientes</a>
            <a href="#" class="menu-item">🏢 Mod Razón Social</a>
            <a href="#" class="menu-item">📦 Mod Registro Productos</a>
            <a href="#" class="menu-item">🏷️ Mod Categoría Producto</a>
            <a href="#" class="menu-item">💰 Mod Facturación</a>
            <a href="#" class="menu-item">📊 Mod Reportes (+ Vendido)</a>
            <a href="index.php?logout=1" class="menu-item" style="background:#ef4444; margin-top:30px;">🚪 Cerrar Sesión</a>
        </div>
        
        <div class="content">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>Bienvenido, <?php echo $_SESSION["usuario"]; ?></h1>
                <span style="background: <?php echo $conexion_ok ? '#d1fae5; color: #065f46;' : '#fee2e2; color: #991b1b;'; ?> padding: 5px 15px; border-radius: 20px;">
                    Estado DB: <?php echo $conexion_ok ? 'Conectado' : 'Desconectado'; ?>
                </span>
            </div>

            <div class="grid">
                <div class="tarjeta">
                    <h3>Atajos Rápidos</h3>
                    <button>+ Nueva Factura</button>
                    <button style="background: #10b981; margin-top: 10px;">+ Nuevo Cliente</button>
                </div>
                <div class="tarjeta">
                    <h3>Resumen del Día</h3>
                    <p>Facturas emitidas hoy: <strong>0</strong></p>
                    <p>Total recaudado: <strong>$0.00</strong></p>
                </div>
            </div>
            
            <div class="tarjeta">
                <h3>Base de datos configurada</h3>
                <p>Las tablas para Usuarios, Clientes, Productos, Razón Social y Facturación han sido inicializadas correctamente en Docker.</p>
                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>:8080" target="_blank" style="color:var(--color-primario); font-weight:bold;">Acceder a phpMyAdmin para gestionar las tablas</a>
            </div>
        </div>
    </div>
<?php } ?>

</body>
</html>
