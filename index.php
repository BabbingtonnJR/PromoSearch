<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$sql = "SELECT U.nome AS nomeLoja, U.endereco, U.numero
        FROM Loja L
        JOIN Usuario U ON L.id_usuario = U.id";

$result = $conn->query($sql);

$lojas = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lojas[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            width: 100%;
            margin: 20px auto;
            border: 2px solid #333;
            border-radius: 8px;
        }

        #radiusSlider {
            width: 100%;
            margin-top: 5px;
        }

        .radius-control {
            position: absolute;
            top: 80px;
            left: 20px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            z-index: 1000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
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
                        <li><a href="#">Mapa</a></li>
                        <li><a href="#">Página 2</a></li>
                        <li><a href="#">Página 3</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil.php">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Mapa</h1>

        <div class="radius-control">
            <label for="radiusRange">Raio: <span id="radiusValue">5</span> km</label>
            <input type="range" id="radiusRange" min="1" max="50" value="5" step="1">
        </div>

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

    const lojas = <?php echo json_encode($lojas); ?>;
    let userLat = null;
    let userLon = null;
    let lojaMarkers = [];

    function calcularDistancia(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

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

    async function atualizarMarcadores(raio) {
        lojaMarkers.forEach(marker => map.removeLayer(marker));
        lojaMarkers = [];

        for (const loja of lojas) {
            const enderecoCompleto = `${loja.endereco}, ${loja.numero}, Paraná, Brasil`;
            const coords = await geocodeEndereco(enderecoCompleto);

            if (coords && userLat !== null && userLon !== null) {
                const distancia = calcularDistancia(userLat, userLon, coords.lat, coords.lon);
                if (distancia <= raio) {
                    const marker = L.marker([coords.lat, coords.lon])
                        .addTo(map)
                        .bindPopup(`<strong>${loja.nomeLoja}</strong><br>${enderecoCompleto}`);
                    lojaMarkers.push(marker);
                }
            }
        }
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            userLat = position.coords.latitude;
            userLon = position.coords.longitude;

            const userMarker = L.marker([userLat, userLon])
                .addTo(map)
                .bindPopup("Você está aqui!")
                .openPopup();

            map.setView([userLat, userLon], 14);

            const raioInicial = parseInt(document.getElementById('radiusRange').value);
            atualizarMarcadores(raioInicial);
        }, () => {
            alert("Não foi possível detectar sua localização.");
        });
    } else {
        alert("Geolocalização não é suportada pelo seu navegador.");
    }

    document.getElementById('radiusRange').addEventListener('input', function() {
        const raio = parseInt(this.value);
        document.getElementById('radiusValue').textContent = raio;
        if (userLat !== null && userLon !== null) {
            atualizarMarcadores(raio);
        }
    });
</script>
