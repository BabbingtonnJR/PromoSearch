<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include 'connection.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="produtos.css">
    <title>Promoções</title>
    <style>
        .product-card.selected {
            border: 3px solid #4CAF50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }
        .selection-controls {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 8px;
            display: none;
        }

        .edit-button, .delete-button {
            display: inline-block;
            padding: 6px 12px;
            margin: 5px 4px 0 0;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .edit-button {
            background-color: #007bff;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #b02a37;
        }

        .product-actions {
            margin-top: 10px;
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
                        <li><a href="produtos.php">Promoções</a></li>
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
        <div class="products-header">
            <h2>Promoções Cadastradas</h2>
            <br>
            <a href="cadastrar_promocao.php" class="add-promotion-btn">Adicionar Promoção</a>
            <button id="toggleSelection" class="add-promotion-btn">Selecionar Promoções</button>
        </div>
        
        <div class="selection-controls" id="selectionControls">
            <span id="selectedCount">0 selecionadas</span>
            <button id="publishBtn" class="add-promotion-btn" disabled>Publicar Promoções</button>
            <button id="cancelSelection" class="delete-button">Cancelar</button>
        </div>
        <br>

        <div class="product-grid">
            <?php
            $query = "SELECT * FROM Promocao";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card" data-id="' . $row['id'] . '">';
                    echo '<div class="product-image">';
                    echo '<img src="https://via.placeholder.com/250" alt="' . $row['nomeProduto'] . '">';
                    echo '</div>';
                    echo '<div class="product-info">';
                    echo '<div class="product-name">' . $row['nomeProduto'] . '</div>';
                    echo '<div class="product-price">De: R$ ' . number_format($row['precoInicial'], 2, ',', '.') . '</div>';
                    echo '<div class="product-promo-price">Por: R$ ' . number_format($row['precoPromocional'], 2, ',', '.') . '</div>';
                    echo '<div class="product-quantity">Quantidade: ' . $row['quantidade'] . '</div>';
                    echo '<div class="product-category">Categoria: ' . ucfirst($row['tipo']) . '</div>';

                    echo '<div class="product-actions">';
                    echo '<a href="editar_promocao.php?id=' . $row['id'] . '" class="edit-button">Editar</a>';
                    echo '<form action="excluir_promocao.php" method="POST" style="display:inline;">';
                    echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="delete-button" onclick="return confirm(\'Tem certeza que deseja excluir esta promoção?\')">Excluir</button>';
                    echo '</form>';
                    echo '</div>';

                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhuma promoção cadastrada ainda.</p>';
            }
            ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSelection = document.getElementById('toggleSelection');
            const selectionControls = document.getElementById('selectionControls');
            const selectedCount = document.getElementById('selectedCount');
            const publishBtn = document.getElementById('publishBtn');
            const cancelSelection = document.getElementById('cancelSelection');
            const productCards = document.querySelectorAll('.product-card');
            
            let selectedProducts = [];
            let isSelectionMode = false;

            toggleSelection.addEventListener('click', function() {
                isSelectionMode = !isSelectionMode;
                selectionControls.style.display = isSelectionMode ? 'block' : 'none';
                selectedProducts = [];
                updateSelectionUI();
                
                productCards.forEach(card => {
                    if (isSelectionMode) {
                        card.style.cursor = 'pointer';
                    } else {
                        card.classList.remove('selected');
                        card.style.cursor = '';
                    }
                });
            });

            cancelSelection.addEventListener('click', function() {
                isSelectionMode = false;
                selectionControls.style.display = 'none';
                selectedProducts = [];
                updateSelectionUI();
                
                productCards.forEach(card => {
                    card.classList.remove('selected');
                    card.style.cursor = '';
                });
            });

            productCards.forEach(card => {
                card.addEventListener('click', function() {
                    if (!isSelectionMode) return;
                    
                    const productId = card.dataset.id;
                    const index = selectedProducts.indexOf(productId);
                    
                    if (index === -1) {
                        if (selectedProducts.length < 3) {
                            selectedProducts.push(productId);
                            card.classList.add('selected');
                        }
                    } else {
                        selectedProducts.splice(index, 1);
                        card.classList.remove('selected');
                    }
                    
                    updateSelectionUI();
                });
            });

            publishBtn.addEventListener('click', function() {
                if (selectedProducts.length === 0) return;
                
                fetch('publicar_promocoes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        promocoes: selectedProducts
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Promoções publicadas com sucesso!');
                        window.location.reload();
                    } else {
                        alert('Erro ao publicar promoções: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao publicar promoções');
                });
            });

            function updateSelectionUI() {
                selectedCount.textContent = `${selectedProducts.length} selecionada(s) (máx. 3)`;
                publishBtn.disabled = selectedProducts.length === 0;
            }
        });
    </script>
</body>
</html>