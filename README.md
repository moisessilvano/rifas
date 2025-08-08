# 🎫 RifasPro - Plataforma de Rifas Online

Uma plataforma moderna e completa para gerenciamento de rifas online, desenvolvida em PHP com arquitetura MVC, Tailwind CSS e suporte a dark mode.

## ✨ Funcionalidades

### 🔐 Autenticação & Autorização
- ✅ Cadastro de usuário (nome, e-mail, senha)
- ✅ Login de usuário (front-end)
- ✅ Login de administrador (back-office)
- ✅ Controle de sessão e proteção de rotas

### 🎯 Gestão de Rifas (Admin)
- ✅ Criar nova rifa com título, descrição, imagem
- ✅ Configurar número total de cotas e valor por cota
- ✅ Definir data e local do sorteio
- ✅ Adicionar telefone de contato
- ✅ Editar rifa existente (campo a campo, inclusive trocar imagem)
- ✅ Listar rifas criadas (filtros por título/data)
- ✅ Publicar/Despublicar/Excluir rifa

### 🛒 Fluxo de Compra (Usuário)
- ✅ Listar rifas públicas (grid responsivo com imagem, título e botão "Ver Rifa")
- ✅ Página de detalhes da rifa
- ✅ UI de seleção de números (grade de cotas, cores para disponível/reservado/vendido)
- ✅ Formulário de reserva (números + e-mail)

### 💰 Gestão de Cotas & Pagamentos
- ✅ Geração automática de cotas ao criar a rifa
- ✅ Reserva de cotas (associa número + e-mail, status "reservado")
- ✅ Instruções de pagamento PIX automáticas
- ✅ Área de batimento manual (Admin)
- ✅ Botão "Marcar como pago" em cada reserva
- ✅ Filtro / busca por reservas pendentes de pagamento
- ✅ Status das cotas: disponível → reservado → pago

### 📊 Painel Admin & Relatórios
- ✅ Visão geral de rifas com resumo de vendas
- ✅ Total arrecadado (valor pago)
- ✅ Histórico de reservas
- ✅ Filtros por rifa, status, data, e-mail do comprador
- ✅ Exportar CSV de reservas/pagamentos
- ✅ Detalhes de cada reserva

### 📧 Notificações & Mensagens
- ✅ E-mail automático para o comprador ao concluir reserva
- ✅ E-mail automático quando pagamento confirmado
- ✅ Aviso interno (Admin) na dashboard para novas reservas

### 🏗️ Infraestrutura & Operação
- ✅ Front Controller (MVC) + Autoloading PSR-4
- ✅ Configuração de ambiente (.env)
- ✅ Logs básicos (erros de sistema, tentativas de login)
- ✅ Validação de formulários (servidor e cliente)
- ✅ Tratamento de erros (404, 500, mensagem amigável)
- ✅ Dark Mode com Tailwind CSS

## 🚀 Instalação e Configuração

### Pré-requisitos
- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Composer
- Servidor web (Apache/Nginx)

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/rifa-php.git
cd rifa-php
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o banco de dados
1. Crie um banco de dados MySQL:
```sql
CREATE DATABASE rifa_platform;
```

2. Importe o schema:
```bash
mysql -u root -p rifa_platform < database/schema.sql
```

### 4. Configure as variáveis de ambiente
1. Copie o arquivo de exemplo:
```bash
cp config.env.example config.env
```

2. Edite o arquivo `config.env` com suas configurações:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=rifa_platform
DB_USER=root
DB_PASSWORD=sua_senha

# Application Configuration
APP_URL=http://localhost/rifa-php
APP_ENV=development
APP_DEBUG=true

# Email Configuration (Gmail)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-app
MAIL_FROM=seu-email@gmail.com
MAIL_FROM_NAME="Plataforma de Rifas"

# PIX Configuration
PIX_KEY=sua-chave-pix@email.com
PIX_OWNER_NAME="Seu Nome"

# Security
SESSION_SECRET=gere-uma-chave-aleatoria-aqui
```

### 5. Configure o servidor web

#### Apache
Certifique-se de que o `mod_rewrite` está habilitado e aponte o DocumentRoot para a pasta `public/`.

#### Nginx
```nginx
server {
    listen 80;
    server_name localhost;
    root /caminho/para/rifa-php/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. Configuração de Email (Gmail)
1. Habilite a verificação em 2 etapas na sua conta Google
2. Gere uma senha de app específica para a aplicação
3. Use essa senha no campo `MAIL_PASSWORD`

### 7. Configuração de Permissões
```bash
chmod -R 755 public/uploads/
chmod -R 755 storage/logs/
```

## 🎯 Como Usar

### Primeira Execução
1. Acesse `http://localhost/rifa-php`
2. Faça login como admin:
   - Email: `admin@rifas.com`
   - Senha: `password`
3. Vá para o painel admin: `/admin/login`

### Criando uma Rifa
1. No painel admin, clique em "Nova Rifa"
2. Preencha os dados:
   - Título e descrição
   - Upload de imagem
   - Quantidade de números
   - Valor por número
   - Data e local do sorteio
   - Telefone de contato
3. Marque "Publicar rifa imediatamente" se quiser que fique visível
4. Clique em "Criar Rifa"

### Processo de Compra
1. Cliente acessa a rifa pública
2. Seleciona os números desejados
3. Preenche nome e email
4. Clica em "Reservar Números"
5. Recebe email com instruções de pagamento PIX
6. Realiza o pagamento e envia comprovante
7. Admin confirma o pagamento
8. Cliente recebe email de confirmação

### Gestão de Pagamentos
1. Acesse `/admin/reservations`
2. Veja todas as reservas pendentes
3. Para confirmar pagamento:
   - Clique na reserva
   - Verifique o comprovante
   - Clique em "Marcar como pago"
4. Cliente recebe email automático de confirmação

## 🎨 Interface

### Design Moderno
- ✨ Interface responsiva com Tailwind CSS
- 🌙 Dark mode com toggle automático
- 📱 Otimizado para mobile, tablet e desktop
- 🎯 UX intuitiva e moderna

### Cores do Sistema
- **Primária**: Azul (#3b82f6)
- **Sucesso**: Verde (#16a34a)
- **Aviso**: Amarelo (#eab308)
- **Erro**: Vermelho (#dc2626)

## 🛡️ Segurança

### Implementações de Segurança
- 🔒 CSRF Protection em todos os formulários
- 🛡️ SQL Injection Protection com PDO
- 🔐 Password Hashing com bcrypt
- 🚫 XSS Protection com sanitização
- 📊 Session Management seguro
- 🔍 Input Validation em frontend e backend

## 📱 Recursos Mobile

### Responsividade
- 📱 Grid adaptivo para todas as telas
- 👆 Touch-friendly para seleção de números
- 🔄 Swipe navigation
- ⚡ Performance otimizada

## 📧 Sistema de Emails

### Templates Responsivos
- 📬 Email de confirmação de reserva
- ✅ Email de confirmação de pagamento
- 🔔 Notificações para administradores
- 🎨 Design HTML responsivo

### Configuração SMTP
- 📮 Suporte a Gmail, Outlook, etc.
- 🔧 Configuração via variáveis de ambiente
- 📊 Logs de envio de emails

## 🚀 Funcionalidades Avançadas

### API REST (Futuro)
- 🔌 Endpoints para integração
- 📊 Webhook para notificações
- 🔐 Autenticação JWT

### Relatórios
- 📈 Dashboard com gráficos
- 📊 Exportação em CSV/PDF
- 📉 Análise de vendas

## 🐛 Troubleshooting

### Problemas Comuns

#### Erro de Conexão com Banco
```
Erro de conexão com banco de dados
```
**Solução**: Verifique as credenciais em `config.env`

#### Emails não enviados
```
Erro ao enviar email
```
**Solução**: 
1. Verifique configurações SMTP
2. Certifique-se de usar senha de app (Gmail)
3. Verifique logs em `storage/logs/`

#### Erro 404 em rotas
**Solução**: 
1. Verifique se `mod_rewrite` está habilitado
2. Confirme o arquivo `.htaccess` na pasta `public/`

#### Upload de imagens falha
**Solução**:
1. Verifique permissões da pasta `public/uploads/`
2. Aumente `upload_max_filesize` no PHP

## 🔧 Desenvolvimento

### Estrutura de Pastas
```
rifa-php/
├── app/
│   ├── Controllers/     # Controladores MVC
│   ├── Core/           # Classes principais do framework
│   ├── Models/         # Modelos de dados
│   └── Services/       # Serviços (Email, etc.)
├── database/           # Schema e migrations
├── public/            # Pasta pública (DocumentRoot)
├── routes/            # Definição de rotas
├── views/             # Templates PHP
└── vendor/            # Dependências Composer
```

### Adicionando Novas Funcionalidades
1. Crie o controlador em `app/Controllers/`
2. Crie o modelo em `app/Models/`
3. Adicione as rotas em `routes/web.php`
4. Crie as views em `views/`

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📞 Suporte

Para dúvidas e suporte:
- 📧 Email: suporte@rifaspro.com
- 💬 Issues: Abra uma issue no GitHub
- 📱 WhatsApp: (11) 99999-9999

## 🎉 Changelog

### v1.0.0 (2024-01-XX)
- ✨ Lançamento inicial
- 🎯 Sistema completo de rifas
- 📧 Notificações por email
- 🌙 Dark mode
- 📱 Interface responsiva

---

Desenvolvido com ❤️ por [Seu Nome]
