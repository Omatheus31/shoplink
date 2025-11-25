
# 🛍️ Shoplink - E-commerce

![Status](https://img.shields.io/badge/Status-Finalizado-success) ![PHP](https://img.shields.io/badge/PHP-8.0+-blue) ![MySQL](https://img.shields.io/badge/MySQL-Database-orange)

O **Shoplink** é uma plataforma web completa de e-commerce projetada para pequenos empreendedores gerenciarem suas vendas online. O sistema oferece uma experiência fluida de compra para o cliente e um painel administrativo robusto para o lojista.

---

## 🌐 Acesso Online (Publicado)

O projeto está hospedado e funcional. Você pode acessar a versão de produção através do link abaixo:

👉 **[Acessar Loja Shoplink (shoplink.rf.gd)](http://shoplink.rf.gd)**

---

## ✨ Funcionalidades Principais

### 👤 Área do Cliente
* **Cadastro e Segurança:** Criação de conta com checklist de senha forte, confirmação de e-mail e recuperação de senha ("Esqueci minha senha") via token.
* **Catálogo:** Visualização de produtos com filtros por categoria e busca por nome.
* **Carrinho de Compras:** Gestão de itens (adicionar, remover, aumentar/diminuir quantidade) com persistência local.
* **Checkout:** Simulação de pagamentos (PIX, Boleto e Formulário de Cartão de Crédito).
* **Meus Pedidos:** Histórico de compras com status em tempo real (Aguardando Pagamento, Enviado, etc).
* **Perfil:** Edição de dados pessoais e senha com validação de segurança.

### 🛡️ Painel Administrativo
* **Dashboard:** Visão geral com métricas de vendas, produtos ativos e pedidos pendentes.
* **Gestão de Pedidos:** Visualização detalhada e alteração de status do pedido (Fluxo: Aguardando -> Pago -> Enviado).
* **Gestão de Catálogo:** CRUD completo (Criar, Ler, Atualizar, Deletar) de Produtos (com upload de imagens) e Categorias.
* **Gestão de Clientes:** Visualização da base de clientes cadastrados.
* **Configurações:** Alteração dinâmica do nome da loja exibido no site.

---

## 🛠️ Tecnologias Utilizadas

* **Backend:** PHP 8 (Puro/Vanilla) com PDO.
* **Banco de Dados:** MySQL (Relacional).
* **Frontend:** HTML5, CSS3, JavaScript (ES6).
* **Framework UI:** Bootstrap 5 (Responsividade e Componentes).
* **E-mail:** PHPMailer (Biblioteca externa para envio via SMTP).
* **Segurança:**
    * Hash de senhas com `Bcrypt`.
    * Proteção contra SQL Injection (Prepared Statements).
    * Controle de Sessão e ACL (Níveis de acesso Admin vs Cliente).

---

## 🚀 Instalação e Execução Local

Siga estes passos para rodar o projeto em sua máquina (localhost).

### 1. Pré-requisitos
* **XAMPP** (ou qualquer servidor Apache + MySQL + PHP 8).
* **Git** instalado.

### 2. Clonar o Repositório
Abra o terminal na pasta `htdocs` do seu XAMPP:
```bash
git clone [https://github.com/Omatheus31/shoplink.git](https://github.com/Omatheus31/shoplink.git)
cd shoplink
```

### 3\. Banco de Dados

1.  Inicie o **Apache** e **MySQL** no painel de controle do XAMPP.
2.  Acesse `http://localhost/phpmyadmin` no seu navegador.
3.  Crie um novo banco de dados chamado **`shoplink_final`**.
4.  Clique na aba **Importar**, selecione o arquivo `database.sql` (localizado na raiz deste projeto) e clique em Executar.

### 4\. Configuração de E-mail (Opcional para Localhost)

O sistema utiliza **PHPMailer** para envio real de e-mails (Recuperação de senha e Boas-vindas).
Para que o envio funcione no seu PC:

1.  Abra o arquivo `includes/email.php` no seu editor de código.
2.  Insira seu e-mail Gmail e sua **Senha de App** (Gerada nas configurações de segurança do Google \> Senhas de App) nas linhas indicadas.

> *Nota: Caso não configure o SMTP, o sistema continuará funcionando e exibirá os links de recuperação simulados na tela (Modo Debug).*

### 5\. Acessar o Projeto

Abra no seu navegador:
👉 **`http://localhost/shoplink`**

-----

## 🔐 Credenciais de Acesso (Teste)

Você pode criar uma conta nova ou usar as credenciais pré-configuradas abaixo:

| Perfil | E-mail | Senha |
| :--- | :--- | :--- |
| **Administrador** | `admin@shoplink.com` | `123456` |
| **Cliente** | `cliente@teste.com` | `123456` |

### Como criar um NOVO Admin?

Como a loja é única (Single-Tenant), o cadastro público cria apenas contas de **Cliente**. Para criar um novo Administrador, execute o seguinte comando SQL no seu banco de dados (phpMyAdmin):

```sql
INSERT INTO usuarios (nome, email, senha_hash, role) 
VALUES ('Novo Admin', 'novo@admin.com', '$2y$10$a3cUsBb9Z1kpvc9kH1qQN.bp6YO5ZLmpPYV6WCCsT4PsZ3vAsmFD6', 'admin');
```

*(A senha criada será: **123456**)*

-----

## 📂 Estrutura de Pastas

```
shoplink/
├── admin/              # Painel Administrativo (Protegido)
├── assets/             # CSS, JS e Imagens estáticas
├── config/             # Conexão com Banco de Dados
├── includes/           # Componentes reutilizáveis (Header, Footer, Email)
├── libs/               # Bibliotecas externas (PHPMailer)
├── uploads/            # Imagens dos produtos (Dinâmico)
├── *.php               # Páginas públicas (index, carrinho, login...)
└── database.sql        # Script de criação do banco
```

-----

## 👨‍💻 Autor

Desenvolvido por **Matheus Farias** para a disciplina de Programação Web.
