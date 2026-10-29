# CRUD de Clientes em PHP (PDO + MySQL) com Docker

## Deploy no Ubuntu Server

### 1. Instalar Docker e Git (uma vez só)
```bash
sudo apt update && sudo apt install -y git ca-certificates curl
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER   # depois saia e entre de novo no SSH
```

### 2. Clonar e configurar
```bash
git clone https://github.com/SEU_USUARIO/SEU_REPO.git
cd SEU_REPO
cp .env.example .env
nano .env        # troque as senhas (e a porta, se quiser)
```

### 3. Subir
```bash
docker compose up -d --build
```
Acesse: **http://IP_DO_SERVIDOR:8080**

### 4. Liberar a porta (acesso externo)
```bash
sudo ufw allow 8080/tcp      # se o ufw estiver ativo
```
Em VPS/nuvem (AWS, Oracle, Azure, etc.), libere também a porta 8080 (TCP, entrada) no Security Group / firewall do provedor.

## Comandos úteis
```bash
docker compose ps                 # status
docker compose logs -f web        # logs do site
docker compose logs -f db         # logs do banco
docker compose down               # para tudo (mantém os dados)
docker compose down -v            # para e APAGA o banco
git pull && docker compose up -d --build   # atualizar após mudanças
```

> A tabela é criada automaticamente só na **primeira** vez que o banco sobe.
> Se mudar o `banco.sql` depois, rode `docker compose down -v` para recriar.
> Se mudar as senhas do `.env` depois da 1ª subida, também precisa do `down -v`.

## Sem Docker (local)
Importe o `banco.sql` no MySQL e rode `php -S localhost:8000`.
Os padrões do `config.php` são `root` com senha vazia.

## Arquivos
- `Dockerfile` / `docker-compose.yml` – ambiente (PHP+Apache e MySQL)
- `.env.example` – modelo das variáveis (o `.env` real não vai pro git)
- `config.php` – conexão PDO, sessão, CSRF, helpers
- `index.php` – listagem + busca
- `form.php` – cadastro e edição
- `excluir.php` – exclusão
- `style.css`, `banco.sql`

## Segurança aplicada
Prepared statements, `htmlspecialchars`, token CSRF, exclusão só via POST, validação no servidor, MySQL sem porta exposta.
