<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id_user = $_SESSION['id_usuario'];

$sqlLoja = "SELECT id FROM Loja WHERE id_usuario = $id_user";
$resultLoja = $conn->query($sqlLoja);
$id = null;

if ($resultLoja && $resultLoja->num_rows > 0) {
    $row = $resultLoja->fetch_assoc();
    $id = (int)$row['id'];
}

$sql = " SELECT U.endereco, U.numero, U.nome as nomeLoja
FROM Loja L
JOIN Usuario U ON L.id_usuario = U.id
WHERE L.id = $id";

$result = $conn->query($sql);
$loja = null;

if ($result && $result->num_rows > 0) {
    $loja = $result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromoSearch - Mapa</title>
    <link rel="stylesheet" href="styles_loja.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            width: 100%;
            margin: 20px auto;
            border: 2px solid #333;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <h2>PromoSearch</h2>
            </div>
            <ul class="nav-links">
                <li class="dropdown">
                    <button class="dropdown-btn">Menu</button>
                    <ul class="dropdown-content">
                        <li><a href="produtos.php">Produtos</a></li>
                        <li><a href="index_loja.php">Mapa</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </li>
                <li class="profile">
                <a href="perfil_loja.php">
                    <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil">
                </a>
            </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Mapa</h1>
        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([-23.5, -51.5], 6);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://carto.com/">CARTO</a> | Dados do <a href="https://openstreetmap.org">OpenStreetMap</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        const loja = {
            nomeLoja: "<?php echo $loja['nomeLoja'] ?? 'Loja desconhecida'; ?>",
            endereco: "<?php echo $loja['endereco'] ?? ''; ?>",
            numero: "<?php echo $loja['numero'] ?? ''; ?>"
        };

        const enderecoCompleto = `${loja.endereco}, ${loja.numero}, Paraná, Brasil`;

        async function geocodeEndereco(endereco) {
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`;
            const response = await fetch(url, {
                headers: {
                    'User-Agent': 'PromoSearchApp/1.0 (email@example.com)',
                    'Referer': window.location.href
                }
            });
            const data = await response.json();
            if (data.length > 0) {
                return {
                    lat: parseFloat(data[0].lat),
                    lon: parseFloat(data[0].lon)
                };
            }
            return null;
        }

        geocodeEndereco(enderecoCompleto).then(coords => {
            if (coords) {
                L.marker([coords.lat, coords.lon])
                    .addTo(map)
                    .bindPopup(`
                        <strong>Loja:</strong> ${loja.nomeLoja}<br>
                        <strong>Endereço:</strong> ${enderecoCompleto}
                    `)
                    .openPopup();

                map.setView([coords.lat, coords.lon], 16);
            } else {
                alert("Endereço não encontrado.");
            }
        });
    </script>
</body>
</html>