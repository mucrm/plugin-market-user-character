# 🛒 Market Character - MUCRM

Permita que os jogadores coloquem seus personagens à venda diretamente em um mercado integrado ao site, possibilitando negociações seguras utilizando moedas do sistema.

---

# 📜 Licença

Este plugin é destinado exclusivamente aos usuários da **MUCRM**.

### ❗ Restrições

* Proibida a revenda deste plugin.
* Proibida a redistribuição em fóruns, grupos ou sites de terceiros.
* Proibido remover créditos ou reivindicar autoria.
* Permitido apenas em projetos que utilizem a plataforma **MUCRM**.

Todos os direitos reservados.

---

# 📦 Instalação

## 1. Baixe o plugin

Baixe o arquivo compactado (.zip).

## 2. Extraia os arquivos

Extraia todo o conteúdo na pasta raiz do seu site.

---

# ⚙️ Configuração

### Arquivo

```php
bootstrap/config/app.php
```

Adicione ao final do array plugins:

```php
'market_character' => [
    'active'              => true,
    'discount_percentage' => 10, // Taxa do sistema em porcentagem
    'coin'                => 0,  // Verificar a moeda em user.php
],
```

---

# 💰 Configuração da Moeda

O sistema utiliza as moedas configuradas em:

```php
bootstrap/config/user.php
```

No parâmetro:

```php
'coins'
```

Exemplo:

```php
'coins' => [
    0 => [
        'name' => 'Cash',
        ...
    ],
],
```

Defina no plugin qual moeda será utilizada:

```php
'coin' => 0,
```

---

# 🏠 Funcionalidades

### Venda de Personagens

Os jogadores podem colocar seus personagens à venda diretamente pelo painel.

### Compra de Personagens

Outros jogadores podem adquirir personagens disponíveis utilizando a moeda configurada.

### Taxa Automática

O sistema pode descontar automaticamente uma porcentagem da venda.

Exemplo:

```php
'discount_percentage' => 10,
```

Uma venda de:

```text
10.000 Cash
```

Com taxa de:

```text
10%
```

Resultará em:

```text
9.000 Cash para o vendedor
1.000 Cash para o sistema
```

---

# ✨ Recursos

* Mercado integrado ao site.
* Compra e venda de personagens.
* Taxa configurável por porcentagem.
* Escolha da moeda utilizada.
* Transferência automática do personagem.
* Sistema seguro e automatizado.
* Interface integrada à MUCRM.
* Instalação simples e rápida.

---

# 📋 Requisitos

* MUCRM atualizado.
* Sistema de moedas configurado.
* Pelo menos uma moeda cadastrada em:

```php
bootstrap/config/user.php
```

---

# 👨‍💻 Desenvolvido para MUCRM

Plugin criado para expandir os recursos da plataforma **MUCRM**, oferecendo um sistema moderno e seguro para comercialização de personagens entre os jogadores.

### Versão

**1.0.0**

---

© MUCRM - Todos os direitos reservados.
