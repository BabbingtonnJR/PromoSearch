<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="produtos.css">
    <title>Cadastrar Produtos</title>
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
        <div class="product-form">
            <h2>Cadastrar Novo Produto</h2>
            <form action="cadastrar_produto.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product-image">Imagem do Produto</label>
                    <input type="file" id="product-image" name="product-image" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="product-name">Nome do Produto</label>
                    <input type="text" id="product-name" name="product-name" required>
                </div>
                <div class="form-group">
                    <label for="product-description">Descrição</label>
                    <textarea id="product-description" name="product-description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="product-price">Preço do Produto</label>
                    <input type="number" id="product-price" name="product-price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="product-category">Categoria do Produto</label>
                    <select id="product-category" name="product-category" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="eletronicos">Eletrônicos</option>
                        <option value="roupas">Roupas</option>
                        <option value="alimentos">Alimentos</option>
                        <option value="moveis">Móveis</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-add">ADICIONAR PRODUTO</button>
                    <button type="reset" class="btn btn-cancel">CANCELAR CADASTRO</button>
                </div>
            </form>
        </div>

        <div class="products-list">
            <h2>Produtos Cadastrados</h2>
            <div class="product-grid">
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250" alt="Produto 1">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Smartphone XYZ</div>
                        <div class="product-price">R$ 1.999,90</div>
                        <div class="product-category">Eletrônicos</div>
                        <div class="product-description">Smartphone com câmera de 48MP, 128GB de armazenamento e tela de 6.5 polegadas.</div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250" alt="Produto 2">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Notebook ABC</div>
                        <div class="product-price">R$ 3.499,90</div>
                        <div class="product-category">Eletrônicos</div>
                        <div class="product-description">Notebook com processador i7, 16GB RAM, SSD 512GB e placa de vídeo dedicada.</div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250" alt="Produto 3">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Camiseta Básica</div>
                        <div class="product-price">R$ 49,90</div>
                        <div class="product-category">Roupas</div>
                        <div class="product-description">Camiseta 100% algodão, disponível em várias cores e tamanhos.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>