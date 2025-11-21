# Projeto Final: Shoplink

![Status do Projeto](https://img.shields.io/badge/status-em%20desenvolvimento-blue)

Shoplink é uma plataforma SaaS (Software as a Service) de e-commerce ultraleve, projetada para micro e pequenos empreendedores que desejam criar um catálogo online com integração direta ao WhatsApp. O sistema é **multi-loja**, permitindo que qualquer pessoa se cadastre e crie sua própria vitrine digital.

---

## 📄 Documentação de Planejamento

Os documentos a seguir detalham o escopo, a arquitetura e o modelo de dados do projeto.

* **[📄 Documento de Visão do Projeto](./docs/documento_visao_projeto.pdf)**
* **[🧱 Diagrama de Componentes](./docs/diagrama_componentes.png)**
* **[🗃️ Modelo de Dados (DER)](./docs/modelo_dados.png)**

---

## ✨ Funcionalidades Atuais

* **Plataforma Multi-Loja (Multi-Tenant):**
    * ✅ **Cadastro de Lojistas:** Página de cadastro (`cadastro.php`) pública para que novos donos de loja possam se registrar.
    * ✅ **Isolamento de Dados:** Arquitetura de backend segura onde cada lojista só pode ver e gerenciar seus próprios dados (produtos, categorias e pedidos).
    * ✅ **Login Automático:** Após o cadastro, o novo lojista é logado automaticamente e direcionado ao seu painel.

* **Painel de Administração Seguro:**
    * ✅ **Sistema de Autenticação:** Acesso seguro ao painel com e-mail e senha. As senhas são 100% protegidas usando `password_hash()` (Bcrypt).
    * ✅ **Proteção de Rotas:** Todas as páginas do admin (`/admin`) são protegidas e redirecionam usuários não logados para o `login.php`.
    * ✅ **Redirecionamento Inteligente:** O usuário é levado para a página que tentava acessar após o login.
    * ✅ **Dashboard de Estatísticas:** Painel inicial com contagem de pedidos pendentes, produtos e categorias do lojista logado.

* **Gestão da Loja (CRUDs):**
    * ✅ **Gestão de Produtos (CRUD):** Interface completa para Adicionar, Listar, Editar e Excluir produtos.
    * ✅ **Gestão de Categorias (CRUD):** Interface completa para Adicionar, Listar, Editar e Excluir categorias.
    * ✅ **Associação de Produtos:** Os produtos podem ser associados às categorias do lojista.

* **Gestão de Vendas:**
    * ✅ **Listagem de Pedidos:** O painel lista todos os pedidos recebidos pela loja.

* **Catálogo Público (Vitrine da Loja Principal):**
    * ✅ **Filtro por Categorias:** O cliente pode filtrar a visualização do catálogo por categoria.
    * ✅ **Página de Detalhes:** O cliente pode clicar em um produto para ver uma página dedicada com mais informações.
    * ✅ **Carrinho de Compras:** Funcionalidade em JavaScript que utiliza `localStorage` para adicionar/remover produtos.

* **Checkout:**
    * ✅ **Novo Fluxo de Pedido:** O pedido é salvo no banco via AJAX (sem recarregar a página) e, em vez de redirecionar ao WhatsApp, exibe uma mensagem de sucesso ("Pedido Recebido! Entraremos em contato.").
    * ✅ **Registro no Banco de Dados:** O pedido é salvo nas tabelas `pedidos` e `pedido_itens`.

---

## 💻 Tecnologias Utilizadas

* **Backend:** PHP 8+ (puro) com Sessões (`$_SESSION`)
* **Frontend:** HTML5, CSS3, JavaScript (ES6) com AJAX (`fetch`)
* **Banco de Dados:** MySQL com PDO (para prevenção de SQL Injection)
* **Servidor Local:** XAMPP (Apache)
* **Versionamento:** Git & GitHub

---

## 🚀 Como Executar o Projeto

Siga os passos abaixo para executar o projeto em um ambiente local.

**1. Requisitos:**
* [XAMPP](https://www.apachefriends.org/pt_br/index.html) (ou ambiente similar com Apache, MySQL e PHP)
* [Git](https://git-scm.com/)

**2. Instalação:**
* Clone o repositório para a pasta `htdocs` do seu XAMPP:
    ```bash
    git clone [https://github.com/Omatheus31/shoplink.git](https://github.com/Omatheus31/shoplink.git)
    ```
* Navegue até a pasta do projeto:
    ```bash
    cd shoplink
    ```

**3. Configuração do Banco de Dados:**
* Inicie os módulos Apache e MySQL no painel de controle do XAMPP.
* Abra o phpMyAdmin (ou seu cliente de banco de dados preferido).
* Crie um novo banco de dados chamado `shoplink_db`.
* **Importante:** Importe o arquivo `database.sql` (localizado na raiz). Ele contém a estrutura multi-loja mais recente.
* Na tabela `configuracoes`, edite o valor da chave `whatsapp_numero` para o número que receberá os pedidos da loja principal.

**4. Acesso ao Sistema:**
* **Catálogo Público (Loja Principal):** Acesse `http://localhost/shoplink/`
* **Painel Administrativo:** Acesse `http://localhost/shoplink/login.php` para entrar ou `http://localhost/shoplink/cadastro.php` para criar uma nova conta de lojista.

### ⚙️ Configurações adicionais (Roles e usuário admin)

O projeto usa uma coluna `role` na tabela `usuarios` para distinguir administradores de clientes. Se o seu banco veio do arquivo `database.sql` deste repositório, é provável que ainda não exista a coluna `role`. Para garantir compatibilidade, execute (no phpMyAdmin ou via cliente SQL):

```sql
ALTER TABLE usuarios
    ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'cliente';
```

Depois disso, você pode criar um usuário admin (Admin da loja) de duas formas:

- Método A — Gerar o hash da senha com PHP e inserir manualmente (recomendado):

    1. No terminal (PowerShell) gere o hash da senha (substitua `SuaSenhaAqui` pela senha desejada):

         ```powershell
         php -r "echo password_hash('SuaSenhaAqui', PASSWORD_DEFAULT);"
         ```

         Copie o valor retornado (algo como `$2y$10$...`).

    2. No phpMyAdmin (ou cliente SQL) rode um INSERT usando o hash gerado:

         ```sql
         INSERT INTO usuarios (nome_loja, email, senha_hash, role)
         VALUES ('Nome Admin', 'admin@exemplo.com', '<HASH_AQUI>', 'admin_master');
         ```

- Método B — Transformar um usuário existente em admin:

    ```sql
    UPDATE usuarios SET role = 'admin_master' WHERE email = 'admin@exemplo.com';
    ```

Observação: o projeto trata dois tipos de administrador: `admin_master` (super admin) e `admin_loja` (administrador da loja). Clientes normais devem ter `role = 'cliente'`.

### Criar usuário admin via script PHP (alternativa)

Se preferir, você pode criar o usuário com um pequeno script PHP (`criar_admin.php`) colocado temporariamente na raiz do projeto. Exemplo de conteúdo:

```php
<?php
require 'config/database.php';
$nome = 'Nome Admin';
$email = 'admin@exemplo.com';
$senha = 'SuaSenhaAqui';
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO usuarios (nome_loja, email, senha_hash, role) VALUES (:n,:e,:s,:r)');
$stmt->execute([':n'=>$nome,':e'=>$email,':s'=>$hash,':r'=>'admin_master']);
echo "Admin criado\n";
```

Salve o arquivo e execute no terminal (no diretório do projeto):

```powershell
php criar_admin.php
```

Remova o script `criar_admin.php` depois de criar o usuário por segurança.


---

## 🗺️ Próximos Passos (Roadmap)

* [ ] **Detalhes do Pedido:** Criar a página de detalhes do pedido no painel admin.
* [ ] **Checkout com ViaCEP:** Implementar o formulário de endereço estruturado com preenchimento automático via API.
* [ ] **"Esqueci Minha Senha":** Implementar o fluxo de recuperação de senha.
* [ ] **Admin Master:** Criar um "papel" de administrador que possa ver os dados de *todas* as lojas.
* [ ] **Links Públicos por Loja:** Implementar roteamento para que cada loja tenha sua URL (ex: `shoplink/loja/mais-puff`).

---

## 👨‍💻 Autor

**Matheus Farias**

* [GitHub: @Omatheus31](https://github.com/Omatheus31)