# Projeto Final: Shoplink

![Status do Projeto](https://img.shields.io/badge/status-protótipo%20funcional-green)

Shoplink é um sistema de e-commerce ultraleve e de gestão de pedidos, projetado para micro e pequenos empreendedores que desejam estabelecer uma presença de vendas online de forma rápida e descomplicada. A plataforma permite a criação de um catálogo digital elegante e a finalização de pedidos através de uma integração direta e automatizada com o WhatsApp.

---

## 📄 Documentação de Planejamento (Entrega 1)

Os documentos a seguir detalham o escopo, a arquitetura e o modelo de dados do projeto, conforme solicitado na primeira fase de entrega.

* **[📄 Documento de Visão do Projeto](./docs/documento_visao_projeto.pdf)**
* **[🧱 Diagrama de Componentes](./docs/diagrama_componentes.png)**
* **[🗃️ Modelo de Dados (DER)](./docs/modelo_dados.png)**

---

## ✨ Funcionalidades do Protótipo

A versão atual do projeto inclui as seguintes funcionalidades principais:

* **Painel de Administração:**
    * ✅ **Cadastro de Produtos:** Interface para adicionar novos produtos com nome, descrição, preço e upload de imagem.
    * ✅ **Listagem de Produtos:** Visualização de todos os produtos cadastrados em uma tabela.
* **Catálogo Público (Vitrine):**
    * ✅ **Exibição de Produtos:** Layout de grid responsivo e mobile-first que exibe todos os produtos.
    * ✅ **Carrinho de Compras:** Funcionalidade em JavaScript que utiliza `localStorage` para adicionar produtos a um carrinho persistente no navegador.
* **Checkout:**
    * ✅ **Integração com WhatsApp:** Geração de uma mensagem pré-formatada com o resumo completo do pedido (itens, total, dados do cliente) para ser enviada diretamente ao número do lojista.

---

## 💻 Tecnologias Utilizadas

* **Backend:** PHP 8+ (puro)
* **Frontend:** HTML5, CSS3, JavaScript (ES6)
* **Banco de Dados:** MySQL
* **Servidor Local:** XAMPP (Apache)
* **Versionamento:** Git & GitHub

---

## 🚀 Como Executar o Protótipo

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
* Importe o arquivo `database.sql` (localizado na raiz do projeto) para dentro do banco `shoplink_db`. Isso criará as tabelas necessárias.
* **Importante:** Na tabela `configuracoes`, edite o valor da chave `whatsapp_numero` para o número de WhatsApp que receberá os pedidos.

**4. Acesso ao Sistema:**
* **Catálogo Público:** Acesse `http://localhost/shoplink/` no seu navegador.
* **Painel Administrativo:** Acesse `http://localhost/shoplink/admin/adicionar_produto.php` para começar a cadastrar produtos.

---

## 🗺️ Próximos Passos (Roadmap)

* [ ] **CRUD Completo de Produtos:** Implementar as funcionalidades de Editar e Excluir.
* [ ] **Sistema de Autenticação:** Criar área restrita com login e senha para o painel administrativo.
* [ ] **Registro de Pedidos:** Salvar os pedidos no banco de dados, além de enviar via WhatsApp.
* [ ] **Sistema de Categorias:** Permitir a organização dos produtos por categorias.

---

## 👨‍💻 Autor

**Matheus Farias**

* [GitHub: @Omatheus31](https://github.com/Omatheus31)