# KidTask — Fase 2: Guia Passo a Passo

> Siga **na ordem exata** abaixo. Cada seção tem os arquivos a criar/substituir e os comandos a rodar.

---

## Parte A — Configurar o banco de dados no XAMPP

### 1. Iniciar o XAMPP
Abra o painel do XAMPP e clique em **Start** nos serviços **Apache** e **MySQL**.

### 2. Criar o banco de dados
Abra o navegador e acesse: **http://localhost/phpmyadmin**

No phpMyAdmin:
1. Clique em **"Novo"** na barra lateral esquerda
2. No campo "Nome do banco de dados" digite: `kidtask`
3. No seletor de collation escolha: `utf8mb4_unicode_ci`
4. Clique em **"Criar"**

O banco está criado. Não precisa criar nenhuma tabela manualmente — as migrations fazem isso.

### 3. Confirmar o `.env`
Abra o arquivo `.env` na raiz do projeto e confirme que está assim:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kidtask
DB_USERNAME=root
DB_PASSWORD=
```

> **Atenção:** O XAMPP por padrão não tem senha no MySQL (campo `DB_PASSWORD` fica vazio). Se você configurou uma senha, coloque-a aqui.

---

## Parte B — Adicionar os arquivos ao projeto

Você vai receber uma pasta `fase2_kidtask` com a seguinte estrutura. Copie cada arquivo para o lugar indicado no projeto:

```
fase2_kidtask/
├── database/
│   ├── migrations/
│   │   ├── 2026_06_01_000001_create_families_table.php
│   │   ├── 2026_06_01_000002_add_family_fields_to_users_table.php
│   │   ├── 2026_06_01_000003_create_tasks_table.php
│   │   ├── 2026_06_01_000004_create_task_assignments_table.php
│   │   ├── 2026_06_01_000005_create_task_completions_table.php
│   │   ├── 2026_06_01_000006_create_rewards_table.php
│   │   └── 2026_06_01_000007_create_point_transactions_table.php
│   ├── factories/
│   │   ├── FamilyFactory.php
│   │   └── TaskFactory.php
│   └── seeders/
│       └── DatabaseSeeder.php
└── app/
    └── Models/
        ├── Family.php
        ├── User.php            ← substitui o existente
        ├── Task.php
        ├── TaskAssignment.php
        ├── TaskCompletion.php
        ├── Reward.php
        └── PointTransaction.php
```

### Onde colocar cada arquivo

| Arquivo | Destino no projeto |
|---|---|
| `2026_06_01_000001_create_families_table.php` | `database/migrations/` |
| `2026_06_01_000002_add_family_fields_to_users_table.php` | `database/migrations/` |
| `2026_06_01_000003_create_tasks_table.php` | `database/migrations/` |
| `2026_06_01_000004_create_task_assignments_table.php` | `database/migrations/` |
| `2026_06_01_000005_create_task_completions_table.php` | `database/migrations/` |
| `2026_06_01_000006_create_rewards_table.php` | `database/migrations/` |
| `2026_06_01_000007_create_point_transactions_table.php` | `database/migrations/` |
| `FamilyFactory.php` | `database/factories/` |
| `TaskFactory.php` | `database/factories/` |
| `DatabaseSeeder.php` | `database/seeders/` (**substitui** o existente) |
| `Family.php` | `app/Models/` |
| `User.php` | `app/Models/` (**substitui** o existente) |
| `Task.php` | `app/Models/` |
| `TaskAssignment.php` | `app/Models/` |
| `TaskCompletion.php` | `app/Models/` |
| `Reward.php` | `app/Models/` |
| `PointTransaction.php` | `app/Models/` |

> **Atenção nos substitutos:** `User.php` e `DatabaseSeeder.php` precisam **substituir** os arquivos que já existem no projeto. Não crie duplicatas.

---

## Parte C — Rodar as migrations

Abra o terminal na pasta do projeto (`kidtask/`) e rode:

```bash
php artisan migrate
```

Você deve ver uma saída parecida com:

```
INFO  Running migrations.

  2026_06_01_000001_create_families_table ................. 12ms DONE
  2026_06_01_000002_add_family_fields_to_users_table ...... 18ms DONE
  2026_06_01_000003_create_tasks_table .................... 22ms DONE
  2026_06_01_000004_create_task_assignments_table ......... 14ms DONE
  2026_06_01_000005_create_task_completions_table ......... 16ms DONE
  2026_06_01_000006_create_rewards_table .................. 12ms DONE
  2026_06_01_000007_create_point_transactions_table ....... 10ms DONE
```

> Se aparecer erro de conexão com o banco: confirme que o MySQL do XAMPP está rodando e que o `.env` está correto.

> Se aparecer erro de "foreign key constraint": certifique-se de que não rodou as migrations fora de ordem. Se precisar, use `php artisan migrate:fresh` para apagar tudo e recomeçar do zero.

---

## Parte D — Popular o banco com dados de demonstração

```bash
php artisan db:seed
```

Isso vai criar:
- Uma família chamada **"Família Silva"** com código de convite `DEMO01`
- Um usuário **pai**: `pai@kidtask.com` / senha `password`
- Um usuário **filho**: `filho@kidtask.com` / senha `password`
- 4 tarefas de exemplo já atribuídas ao filho
- 3 recompensas de exemplo

---

## Parte E — Verificar no phpMyAdmin

Abra **http://localhost/phpmyadmin** e clique no banco `kidtask` na barra lateral.

Você deve ver as seguintes tabelas criadas:

| Tabela | O que contém |
|---|---|
| `families` | A família "Família Silva" |
| `users` | Os dois usuários de demo + novos campos (family_id, role...) |
| `tasks` | As 4 tarefas de demonstração |
| `task_assignments` | Atribuições das tarefas ao filho |
| `task_completions` | (vazio por enquanto — preenchido quando filho marcar tarefa) |
| `rewards` | As 3 recompensas de demonstração |
| `point_transactions` | (vazio por enquanto — preenchido após aprovação) |
| `migrations` | Registro das migrations executadas |
| `cache`, `jobs`, `sessions` | Tabelas do Laravel (geradas na Fase 1) |

---

## Parte F — Checklist antes de fechar a fase

Execute cada item e só avance para a Fase 3 quando tudo estiver marcado:

- [ ] `php artisan migrate:fresh --seed` roda sem nenhum erro vermelho
- [ ] phpMyAdmin mostra as 7 tabelas novas criadas com as colunas certas
- [ ] Tabela `users` tem os campos: `family_id`, `role`, `avatar`, `birthdate`
- [ ] Tabela `tasks` tem o campo `recurrence` com os valores corretos
- [ ] Dados de seed estão visíveis no phpMyAdmin (família, usuários, tarefas, recompensas)
- [ ] Nenhum `dd()` ou debug esquecido no código
- [ ] Commit feito:

```bash
git add .
git commit -m "feat: criar migrations, models, factories e seeder da fase 2"
```

---

## Possíveis erros e soluções

**Erro: `SQLSTATE[HY000] [2002] No connection`**
→ O MySQL do XAMPP não está rodando. Abra o painel do XAMPP e clique em Start no MySQL.

**Erro: `Access denied for user 'root'@'localhost'`**
→ O XAMPP tem senha no MySQL. Abra o phpMyAdmin, vá em Contas de usuário, e veja a senha do root. Coloque-a no `DB_PASSWORD` do `.env`.

**Erro: `Base table or view already exists`**
→ Existe uma migration travada ou tabela manual. Rode `php artisan migrate:fresh --seed` para recriar tudo do zero. **Atenção:** isso apaga todos os dados existentes.

**Erro: `Cannot add foreign key constraint`**
→ As migrations estão sendo executadas fora de ordem. Verifique se os nomes dos arquivos têm os timestamps corretos (`2026_06_01_000001`, `000002`, etc.) para garantir a ordem.

**Erro: `Class "App\Models\Family" not found`**
→ O arquivo `Family.php` não foi colocado em `app/Models/`. Confirme o destino.
