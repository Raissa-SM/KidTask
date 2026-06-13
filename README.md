# KidTask

Sistema de afazeres familiares gamificado. Pais cadastram tarefas, filhos concluem, pais validam e pontos são creditados. Filhos trocam pontos por recompensas criadas pelos pais.

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+

## Instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/seu-usuario/kidtask.git
   cd kidtask
   ```

2. **Instale as dependências e configure o ambiente em um único comando:**
   ```bash
   composer run setup
   ```
   Isso instala dependências PHP e Node, copia o `.env`, gera a chave da aplicação, roda as migrations e compila os assets.

   > **Ou passo a passo:**
   > ```bash
   > composer install
   > cp .env.example .env
   > php artisan key:generate
   > npm install
   > npm run build
   > ```

3. **Configure o banco de dados no `.env`:**
   ```env
   DB_DATABASE=kidtask
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

4. **Rode as migrations com dados de exemplo:**
   ```bash
   php artisan migrate --seed
   ```

5. **Inicie o servidor de desenvolvimento:**
   ```bash
   composer run dev
   ```
   Isso inicia simultaneamente: servidor Laravel, worker de filas, visualizador de logs e Vite.

   Acesse em: `http://localhost:8000`

## Acesso de demonstração

Após rodar `php artisan migrate --seed`, use as credenciais abaixo:

| Perfil | E-mail | Senha |
|---|---|---|
| Pai | pai@kidtask.com | password |
| Filho | filho@kidtask.com | password |

## Funcionalidades

- **Famílias** — pai cria família e gera código de convite; filhos entram com o código
- **Tarefas** — CRUD completo com recorrência (diária / semanal / mensal / evento único), atribuição a filhos e filtros
- **Conclusões** — filho marca tarefa como feita; pai aprova ou rejeita com justificativa
- **Pontos** — crédito automático ao aprovar; saldo calculado por `ganho − resgatado`; histórico auditável
- **Recompensas** — CRUD de recompensas com custo em pontos; filho resgata; pai marca como entregue

## Tecnologias

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Blade, Tailwind CSS 3, Alpine.js
- **Banco:** MySQL (sessão, cache e filas também no banco)
- **Build:** Vite 7

## Comandos úteis

```bash
composer run test          # roda a suíte de testes
php artisan migrate:fresh --seed  # recria o banco com dados de exemplo
./vendor/bin/pint          # formata o código PHP
php artisan route:list     # lista todas as rotas
```
