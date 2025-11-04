# Projeto Final: Shoplink

![Status do Projeto](https://img.shields.io/badge/status-em%20desenvolvimento-blue)

Shoplink é um sistema de e-commerce ultraleve e de gestão de pedidos, projetado para micro e pequenos empreendedores que desejam estabelecer uma presença de vendas online de forma rápida e descomplicada. A plataforma permite a criação de um catálogo digital elegante e a finalização de pedidos através de uma integração direta e automatizada com o WhatsApp.

---

## 📄 Documentação de Planejamento

Os documentos a seguir detalham o escopo, a arquitetura e o modelo de dados do projeto.

* **[📄 Documento de Visão do Projeto](./docs/documento_visao_projeto.pdf)**
* **[🧱 Diagrama de Componentes](./docs/diagrama_componentes.png)**
* **[🗃️ Modelo de Dados (DER)](./docs/modelo_dados.png)**

---

## ✨ Funcionalidades Atuais

A versão atual do projeto inclui as seguintes funcionalidades principais:

* **Painel de Administração Seguro:**
    * ✅ **Sistema de Autenticação:** Acesso seguro ao painel com e-mail e senha. As senhas são 100% protegidas usando `password_hash()` (Bcrypt).
    * ✅ **Proteção de Rotas:** Todas as páginas do admin são protegidas e redirecionam usuários não logados para a página de login.
    * ✅ **Logout Seguro:** Funcionalidade de "Sair" que destrói a sessão.

* **Gestão da Loja (CRUDs):**
    * ✅ **Gestão de Produtos (CRUD):** Interface completa para Adicionar, Listar, **Editar** e **Excluir** produtos, incluindo upload de imagens.
    * ✅ **Gestão de Categorias (CRUD):** Interface completa para Adicionar, Listar, **Editar** e **Excluir** categorias de produtos.
    * ✅ **Associação de Produtos:** Os produtos podem ser associados a categorias no momento da criação ou edição.

* **Gestão de Vendas:**
    * ✅ **Visualização de Pedidos:** O painel lista todos os pedidos recebidos, com detalhes de cliente, valor total e data.

* **Catálogo Público (Vitrine):**
    * ✅ **Filtro por Categorias:** O cliente pode filtrar a visualização do catálogo por categoria ou ver todos os produtos.
    * ✅ **Carrinho de Compras:** Funcionalidade em JavaScript que utiliza `localStorage` para adicionar produtos a um carrinho persistente no navegador.

* **Checkout Híbrido:**
    * ✅ **Registro no Banco de Dados:** O pedido é salvo no banco de dados (`pedidos` e `pedido_itens`) via AJAX, sem recarregar a página.
    * ✅ **Integração com WhatsApp:** Após salvar, o sistema gera uma mensagem pré-formatada com o resumo completo e o **ID do Pedido** para ser enviada ao lojista.

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
* **Importante:** Importe o arquivo `database.sql` atualizado. Ele agora contém as tabelas `usuarios`, `categorias`, `pedidos` e `pedido_itens`.
* Na tabela `configuracoes`, edite o valor da chave `whatsapp_numero` para o número que receberá os pedidos.
* **(Primeiro Acesso)** Como não há página de cadastro, você deve criar seu usuário admin manualmente (ou usar um script temporário) na tabela `usuarios` usando `password_hash()`.

**4. Acesso ao Sistema:**
* **Catálogo Público:** Acesse `http://localhost/shoplink/` no seu navegador.
* **Painel Administrativo:** Acesse `http://localhost/shoplink/login.php` para entrar no painel.

---

## 🗺️ Próximos Passos (Roadmap)

* [ ] **Checkout Otimizado:** Implementar o formulário de endereço estruturado (CEP, Rua, Bairro) com preenchimento automático via API (ViaCEP).
* [ ] **Cadastro de Lojistas (Multi-loja):** Criar uma página de cadastro pública para que novos lojistas possam se registrar, implementando o isolamento de dados (um lojista só vê seus próprios produtos/pedidos).
* [ ] **Dashboard de Administrador:** Criar uma página inicial para o admin (`admin/index.php`) com estatísticas rápidas (ex: nº de pedidos, total de vendas).
* [ ] **Gestão de Pedidos (Avançado):** Criar uma página de "Detalhes do Pedido" e permitir a atualização do status (de "Pendente" para "Concluído").

---

## 👨‍💻 Autor

**Matheus Farias**

* [GitHub: @Omatheus31](https://github.com/Omatheus31)