<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$sql = "SELECT DISTINCT U.nome AS nomeLoja, U.endereco, U.numero, L.id AS id_loja
        FROM Loja L
        JOIN Usuario U ON L.id_usuario = U.id
        JOIN Historico H ON L.id = H.id_loja
        JOIN ListaPromocao LP ON H.id_listaPromocao = LP.id
        JOIN Promocao P ON LP.id_promocao = P.id
        WHERE P.quantidade > 0";

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

        .navbar {
            background-color: #333;
            padding: 10px 20px;
            color: white;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1001;
        }

        .controls-container {
            position: absolute;
            top: 80px;
            width: 100%;
            display: flex;
            justify-content: start;
            gap: 20px;
            z-index: 1000;
            flex-wrap: wrap;
        }


        .radius-control, .filter-control {
            background: white;
            padding: 10px 15px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radius-control label,
        .filter-control label {
            font-weight: bold;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .radius-control input[type=range] {
            width: 150px;
        }

        .filter-control select {
            padding: 6px 10px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .filter-control select:focus {
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        .filter-control select:hover {
            background-color: #fff;
        }

        .form-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        background-color: #fff;
        padding: 10px 15px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        max-width: 400px;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .form-group select {
            padding: 8px 12px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group select:focus {
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        .form-group select:hover {
            background-color: #fff;
        }

        #formDenuncia {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fefefe;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 10001;
            width: 400px;
            max-width: 90%;
            font-family: Arial, sans-serif;
        }

        #formDenuncia h3 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        #formDenuncia p {
            text-align: center;
            margin-bottom: 15px;
            color: #555;
            font-weight: bold;
        }

        #formDenuncia textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: none;
            font-size: 14px;
        }

        #formDenuncia button {
            padding: 10px 18px;
            margin-right: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        #formDenuncia button[type="submit"] {
            background-color: #d9534f;
            color: white;
        }

        #formDenuncia button[type="submit"]:hover {
            background-color: #c9302c;
        }

        #formDenuncia button[type="button"] {
            background-color: #6c757d;
            color: white;
        }

        #formDenuncia button[type="button"]:hover {
            background-color: #5a6268;
        }

        #map {
            z-index: 1;
            height: 600px;
            width: 100%;
            margin: 20px auto;
            border: 2px solid #333;
            border-radius: 8px;
        }


        ul li a{
            font-family: arial;
            text-decoration: none;
            color: white;
        }

        li {
            list-style-type: none;
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
                        <li><a href="promocoes_salvas.php">Salvos</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil.php">
                        <img src="exibir_foto.php" alt="Foto de Perfil" style="width: 40px; height: 40px; border-radius: 50%;">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Mapa</h1>
        <div class="controls-container">
            <div class="radius-control">
                <label for="radiusRange">Raio:</label>
                <span id="radiusValue">5</span> km
                <input type="range" id="radiusRange" min="1" max="50" value="5" step="1">
            </div>

            <div class="filter-control">
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo">
                    <option value="">Todos</option>
                    <option value="eletronicos">Eletrônicos</option>
                    <option value="roupas">Roupas</option>
                    <option value="alimentos">Alimentos</option>
                    <option value="moveis">Móveis</option>
                    <option value="outros">Outros</option>
                </select>
            </div>
        </div>


        <div id="map"></div>
    </div>

<div id="formDenuncia">
    <h3>Denunciar Loja</h3>
    <p id="nomeLojaDenuncia"></p>
    <form id="denunciaForm" method="POST" action="registrar_denuncia.php">
        <input type="hidden" name="id_loja" id="id_loja">
        <label for="descricao">Descrição do problema:</label><br>
        <textarea name="descricao" id="descricao" required rows="4" placeholder="Descreva o motivo da denúncia..."></textarea><br><br>
        <button type="submit">Enviar Denúncia</button>
        <button type="button" onclick="fecharFormularioDenuncia()">Cancelar</button>
    </form>
</div>


<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([-23.5, -51.5], 6);

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://carto.com/">CARTO</a> | Dados do <a href="https://openstreetmap.org">OpenStreetMap</a>',
    subdomains: 'abcd',
    maxZoom: 19
}).addTo(map);

const userIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const lojaIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});




const lojas = <?php echo json_encode($lojas); ?>;
let userLat = null;
let userLon = null;
let lojaMarkers = [];

const coordenadasCache = {};

function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

async function geocodeEndereco(endereco) {
    if (coordenadasCache[endereco]) {
        return coordenadasCache[endereco];
    }

    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`;
    const response = await fetch(url, {
        headers: {
            'User-Agent': 'PromoSearchApp/1.0 (email@example.com)',
            'Referer': window.location.href
        }
    });

    const data = await response.json();
    if (data.length > 0) {
        const coords = {
            lat: parseFloat(data[0].lat),
            lon: parseFloat(data[0].lon)
        };
        coordenadasCache[endereco] = coords;
        return coords;
    }
    return null;
}

async function atualizarMarcadores(raio) {
    lojaMarkers.forEach(marker => map.removeLayer(marker));
    lojaMarkers = [];

    const tipoSelecionado = document.getElementById('tipo').value;

    for (const loja of lojas) {
        const enderecoCompleto = `${loja.endereco}, ${loja.numero}, Paraná, Brasil`;
        const coords = await geocodeEndereco(enderecoCompleto);

        if (coords && userLat !== null && userLon !== null) {
            const distancia = calcularDistancia(userLat, userLon, coords.lat, coords.lon);
            if (distancia <= raio) {
                const promocoes = await obterPromocoesDaLoja(loja.nomeLoja, loja.endereco, loja.numero);
                const promocoesAtivas = promocoes.filter(p => p.quantidade > 0);

                const promocoesFiltradas = tipoSelecionado
                    ? promocoesAtivas.filter(p => p.tipo.toLowerCase() === tipoSelecionado.toLowerCase())
                    : promocoesAtivas;

                if (promocoesFiltradas.length > 0) {
                    let popupContent = `<strong><a href="produtos_loja.php?id_loja=${loja.id_loja}" style="color:black; text-decoration:none;">${loja.nomeLoja}</a></strong><br>${enderecoCompleto}<br>`;
                    popupContent += '<h4>Promoções Ativas:</h4><ul>';

                    promocoesFiltradas.forEach(promo => {
                        popupContent += `<li>${promo.nomeProduto} - De: R$ ${promo.precoInicial} Por: R$ ${promo.precoPromocional}</li>`;
                    });

                    popupContent += '</ul>';
                    popupContent += `<strong><a href="produtos_loja.php?id_loja=${loja.id_loja}" style="color:black; text-decoration:none;">Ver Produtos</a></strong><br>`;
                    popupContent += `<button 
    style="padding: 10px 18px; margin-right: 10px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; background-color: #d9534f; color: white; transition: background-color 0.3s ease;" 
    onmouseover="this.style.backgroundColor='#c9302c'" 
    onmouseout="this.style.backgroundColor='#d9534f'" 
    onclick="abrirFormularioDenuncia('${loja.nomeLoja}', '${loja.endereco}', '${loja.numero}')">
    Denunciar
</button>`;

                    const marker = L.marker([coords.lat, coords.lon], { icon: lojaIcon })
                        .addTo(map)
                        .bindPopup(popupContent);
                    lojaMarkers.push(marker);
                }
            }
        }
    }
}



function abrirFormularioDenuncia(nome, endereco, numero) {
    const loja = lojas.find(l =>
        l.nomeLoja === nome &&
        l.endereco === endereco &&
        l.numero.toString() === numero.toString()
    );

    if (!loja) {
        alert("Loja não encontrada.");
        return;
    }

    document.getElementById('nomeLojaDenuncia').innerText = `Loja: ${nome}`;

    fetch(`buscar_id_loja.php?endereco=${encodeURIComponent(endereco)}&numero=${encodeURIComponent(numero)}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.id_loja) {
                document.getElementById('id_loja').value = data.id_loja;
                document.getElementById('formDenuncia').style.display = 'block';
            } else {
                alert("Não foi possível localizar a loja.");
            }
        });
}

function fecharFormularioDenuncia() {
    document.getElementById('formDenuncia').style.display = 'none';
    document.getElementById('descricao').value = '';
    document.getElementById('nomeLojaDenuncia').innerText = '';
}


async function obterPromocoesDaLoja(nomeLoja, endereco, numero) {
    try {
        const response = await fetch(`obter_promocoes_loja.php?nome=${encodeURIComponent(nomeLoja)}&endereco=${encodeURIComponent(endereco)}&numero=${encodeURIComponent(numero)}`);
        const data = await response.json();
        return data.promocoes || [];
    } catch (error) {
        console.error('Erro ao obter promoções:', error);
        return [];
    }
}

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
        userLat = position.coords.latitude;
        userLon = position.coords.longitude;

        const userMarker = L.marker([userLat, userLon], { icon: userIcon })
            .addTo(map)
            .bindPopup("Você está aqui!")
            .openPopup();

        map.setView([userLat, userLon], 14);

        const raioInicial = parseInt(document.getElementById('radiusRange').value);
        atualizarMarcadores(raioInicial);

    }, error => {
        alert("Não foi possível detectar sua localização.");
        console.error(error);
    }, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
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

document.getElementById('tipo').addEventListener('change', function() {
    const raio = parseInt(document.getElementById('radiusRange').value);
    if (userLat !== null && userLon !== null) {
        atualizarMarcadores(raio);
    }
});

</script>
</body>
</html>