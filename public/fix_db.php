<?php
// fix_db.php
// Script para reparar el usuario root de XAMPP

echo "<h1>Intentando reparar base de datos...</h1>";

// Intentar conectar via localhost (Socket/Pipe) primero, que es lo que suele funcionar en PMA
$mysqli = new mysqli("localhost", "root", "");

if ($mysqli->connect_error) {
    die("<p style='color:red'>Error de conexión (Localhost): " . $mysqli->connect_error . "</p>");
} else {
    echo "<p style='color:green'>Conexión Exitosa vía Localhost.</p>";
}

// Comandos para arreglar el usuario root en ambas interfaces
$queries = [
    "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD ''",
    "ALTER USER 'root'@'127.0.0.1' IDENTIFIED VIA mysql_native_password USING PASSWORD ''",
    "FLUSH PRIVILEGES"
];

foreach ($queries as $sql) {
    echo "Ejecutando: <code>$sql</code> ... ";
    if ($mysqli->query($sql) === TRUE) {
        echo "<b style='color:green'>OK</b><br>";
    } else {
        echo "<b style='color:red'>Error: " . $mysqli->error . "</b><br>";
    }
}

$mysqli->close();

echo "<hr>";
echo "<h3>Prueba Final:</h3>";
echo "<p>Ahora intenta recargar <a href='/'>tu aplicación Laravel (Inicio)</a>.</p>";
