# Clean Earth

## Descrição do Projeto

O **Clean Earth** é um aplicativo web desenvolvido para monitorar a qualidade do ar, solo e temperatura usando um sensor. O sistema é equipado com funcionalidades de login e cadastro seguros, protegidos contra SQL Injection. O painel administrativo permite o acesso a informações detalhadas de monitoramento e a possibilidade de adicionar novos sensores para expandir as capacidades de monitoramento.

## Funcionalidades Principais

- **Login e Cadastro**: Sistema robusto com proteção contra SQL Injection.
- **Painel do Admin**: Acesso a informações de monitoramento ambiental.
- **Monitoramento em Tempo Real**: Sensor para qualidade do ar, solo e temperatura.
- **Escalabilidade**: Capacidade de adicionar novos sensores.
- **Recuperação de Senha**: Implementado com segurança.
- **Integração com API do Google**: Para login e outras funcionalidades.
- **Páginas Informativas**: Incluindo página sobre e outras atualizações.

## Estrutura do Projeto

A estrutura do projeto é organizada da seguinte forma:

.
├── .github/workflows
│ └── static.yml
├── PHP
├── img
├── sistema
├── .env
├── README.md
├── cadastro.php
├── composer.json
├── composer.lock
├── index.css
├── index.php
├── recuperação-de-senha.html
├── scripts.js
├── styles.css
└── vendor.zip


### Descrição dos Arquivos e Pastas

- **.github/workflows**: Contém configurações para CI/CD.
- **PHP**: Scripts PHP para funcionalidades backend.
- **img**: Imagens usadas no site.
- **sistema**: Arquivos principais do sistema.
- **.env**: Variáveis de ambiente para configuração.
- **README.md**: Documentação do projeto.
- **cadastro.php**: Script para cadastro de novos usuários.
- **composer.json**: Dependências do PHP.
- **composer.lock**: Arquivo de lock do Composer.
- **index.css**: Estilos principais do site.
- **index.php**: Página inicial do site.
- **recuperação-de-senha.html**: Página para recuperação de senha.
- **scripts.js**: Scripts JavaScript.
- **styles.css**: Estilos adicionais.
- **vendor.zip**: Dependências de terceiros.

## Tecnologias Utilizadas

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Banco de Dados**: MongoDB
- **Autenticação**: Integração com API do Google para login
- **Segurança**: Proteção contra SQL Injection

## Como Executar o Projeto

1. **Clone o repositório**:
   ```sh
   git clone https://github.com/seu-usuario/projeto-pi-3sm.git
Instale as dependências:

sh

composer install
Configure as variáveis de ambiente:
Crie um arquivo .env na raiz do projeto e configure conforme necessário.

Inicie o servidor:

sh
php -S localhost:8000

Acesse o aplicativo:
Abra o navegador e vá para http://localhost.
Contribuição
Faça um fork do projeto.
Crie uma nova branch: git checkout -b minha-nova-feature.
Faça suas alterações e commit: git commit -m 'Adiciona nova feature'.
Envie para o repositório original: git push origin minha-nova-feature.
Crie uma solicitação de pull.


Licença
Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE.md para detalhes.

Contato
Para mais informações, entre em contato com:

Kaique Mendess - GitHub
Clean Earth - Monitorando o meio ambiente para um futuro melhor.

Este README.md cobre todos os aspectos importantes do seu projeto, desde a descrição, funcionalidades, estrutura do projeto, tecnologias utilizadas, instruções de execução, até informações sobre contribuição e contato. Você pode ajustar ou expandir conforme necessário para se adequar às especificidades do seu projeto.
