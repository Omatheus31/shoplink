# 🛍️ Shoplink - E-commerce Single-Tenant

![Status](https://img.shields.io/badge/Status-Finalizado-success) ![PHP](https://img.shields.io/badge/PHP-8.0+-blue) ![MySQL](https://img.shields.io/badge/MySQL-Database-orange)

O **Shoplink** é uma plataforma web completa de e-commerce projetada para pequenos empreendedores gerenciarem suas vendas online. O sistema oferece uma experiência fluida de compra para o cliente e um painel administrativo robusto para o lojista.

---

## ✨ Funcionalidades Principais

### 👤 Área do Cliente (Frontend)
* **Catálogo Dinâmico:** Visualização de produtos com filtros inteligentes (busca por nome e categorias).
* **Carrinho de Compras:** Gestão de itens em tempo real (armazenamento local e persistência no checkout).
* **Cadastro e Perfil:** Criação de conta segura com validação de senha forte e edição de dados pessoais.
* **Checkout Simulado:** Escolha de meios de pagamento (PIX, Boleto e Cartão de Crédito) com interfaces visuais responsivas.
* **Meus Pedidos:** Histórico completo de compras com status colorido e detalhes dos itens.

### 🛡️ Painel Administrativo (Backend)
* **Dashboard:** Visão geral com métricas de vendas, produtos ativos e pedidos pendentes.
* **Gestão de Pedidos:** Visualização detalhada de pedidos e **alteração de status** (Aguardando Pagamento -> Enviado -> Concluído).
* **Gestão de Catálogo:** CRUD completo (Criar, Ler, Atualizar, Deletar) de Produtos (com upload de imagens) e Categorias.
* **Gestão de Clientes:** Visualização da base de clientes cadastrados.
* **Configurações:** Alteração dinâmica do nome da loja exibido no site.

---

## 🛠️ Tecnologias e Arquitetura

* **Linguagem:** PHP 8 (Puro/Vanilla) com PDO.
* **Banco de Dados:** MySQL (Relacional).
* **Frontend:** HTML5, CSS3, JavaScript (ES6).
* **Framework UI:** Bootstrap 5 (Responsividade e Componentes).
* **Segurança:**
    * Hash de senhas com `Bcrypt`.
    * Proteção contra SQL Injection (Prepared Statements).
    * Controle de Sessão e ACL (Níveis de acesso Admin vs Cliente).
    * Prevenção de XSS com sanitização de saída.

---

## 🚀 Como Rodar o Projeto Localmente

### 1. Pré-requisitos
* [XAMPP](https://www.apachefriends.org/pt_br/index.html) (ou WAMP/Laragon) instalado.
* Navegador Web moderno.

### 2. Instalação
1.  Clone este repositório na pasta `htdocs` do seu servidor local:
    ```bash
    git clone [https://github.com/Omatheus31/shoplink.git](https://github.com/Omatheus31/shoplink.git)
    ```
2.  Inicie o **Apache** e o **MySQL** no painel do XAMPP.

### 3. Banco de Dados
1.  Acesse `http://localhost/phpmyadmin`.
2.  Crie um banco de dados chamado **`shoplink_final`**.
3.  Importe o arquivo `database.sql` (localizado na raiz do projeto).

### 4. Configuração
1.  Verifique o arquivo `config/database.php`. Se você usa XAMPP padrão, as credenciais já estão corretas:
    * Host: `localhost`
    * User: `root`
    * Pass: `` (vazio)

### 5. Acesso
Abra no navegador: **`http://localhost/shoplink`**

---

## 🔐 Credenciais de Acesso (Demo)

Para testar o sistema, utilize os usuários pré-cadastrados ou crie novos.

| Papel | E-mail | Senha |
| :--- | :--- | :--- |
| **Administrador** | `admin@shoplink.com` | `123456` |
| **Cliente** | `cliente@teste.com` | `123456` |

---

## 📂 Estrutura de Pastas

shoplink/ ├── admin/ # Painel Administrativo (Protegido) ├── assets/ # CSS, JS e Imagens estáticas ├── config/ # Conexão com Banco de Dados ├── includes/ # Headers e Footers reutilizáveis ├── uploads/ # Imagens dos produtos (Dinâmico) ├── *.php # Páginas públicas (index, carrinho, login...) └── database.sql # Script de criação do banco


---

## 👨‍💻 Autor
Desenvolvido por **Matheus Farias** para a disciplina de Programação Web.