CREATE DATABASE PromoSearch;
USE PromoSearch;

CREATE TABLE Usuario(
	id INT NOT NULL AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL,
    senha VARCHAR(144) NOT NULL,
    nome VARCHAR (100) NOT NULL,
    email VARCHAR(100) NOT NULL,
	telefone VARCHAR(11) NOT NULL,
    endereco VARCHAR(100) NOT NULL,
    numero INT NOT NULL,
    UNIQUE (login, email, telefone, endereco, numero),
    PRIMARY KEY (id)
);

CREATE TABLE Cliente(
	id INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    UNIQUE (cpf),
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
);

CREATE TABLE Loja(
	id INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    cnpj INT(14) NOT NULL,
    proprietario VARCHAR(100),
    UNIQUE (cnpj),
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
);

CREATE TABLE Administrador(
	id INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    UNIQUE (cpf),
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
);


CREATE TABLE Promocao(
	id INT NOT NULL AUTO_INCREMENT,
    nomeProduto VARCHAR(50) NOT NULL,
    precoInicial DOUBLE NOT NULL,
    precoPromocional DOUBLE NOT NULL,
    quantidade INT NOT NULL,
    tipo VARCHAR(30) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE ListaPromocao(
	id INT NOT NULL AUTO_INCREMENT,
    id_promocao INT NOT NULL,
	PRIMARY KEY (id),
    FOREIGN KEY (id_promocao) REFERENCES Promocao(id)
);

CREATE TABLE Historico(
	id INT NOT NULL AUTO_INCREMENT,
    id_listaPromocao INT NOT NULL,
    id_loja INT NOT NULL,
	PRIMARY KEY (id),
    FOREIGN KEY (id_listaPromocao) REFERENCES ListaPromocao(id),
    FOREIGN KEY (id_loja) REFERENCES Loja(id)
);

CREATE TABLE PromocoesSalvas(
	id INT NOT NULL AUTO_INCREMENT,
    id_promocao INT NOT NULL,
	PRIMARY KEY (id),
    FOREIGN KEY (id_promocao) REFERENCES Promocao(id)
);

CREATE TABLE Denuncia(
	id INT NOT NULL AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_promocao INT NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    estado BOOLEAN,
    dataDenuncia DATE,
    PRIMARY KEY (id),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id),
    FOREIGN KEY (id_promocao) REFERENCES Promocao(id)
);

CREATE TABLE Registro(
	id INT NOT NULL AUTO_INCREMENT,
    id_denuncia INT NOT NULL,
    id_administrador INT NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    dataBanimento DATE,
    duracao DATE,
    tipoBanimento VARCHAR(30) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_denuncia) REFERENCES Denuncia(id),
    FOREIGN KEY (id_administrador) REFERENCES Administrador(id)
);