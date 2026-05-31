# KidTask — Plano de Implementação

> **Disciplina:** Programação Web 2 — Laravel | **Entrega final:** 23/06/2026
> 
> Este arquivo serve como guia de referência durante todo o desenvolvimento. Consulte antes de começar cada fase e siga as boas práticas listadas.

---

## Visão Geral

| Item | Detalhe |
|---|---|
| Framework | Laravel (PHP) |
| Banco de dados | MySQL |
| Frontend | Blade + Tailwind CSS |
| Autenticação | Laravel Breeze |
| Fases | 7 |
| Tempo estimado | ~19 horas de desenvolvimento |
| Entrega | 23/06/2026 via Google Classroom (link GitHub + vídeo) |

---

## Regras Gerais de Código

Estas regras se aplicam a **todas as fases** sem exceção.

### Nomenclatura

- **Inglês** para todo código: nomes de classes, variáveis, métodos, rotas, colunas de banco, arquivos.
- **Português** apenas para: mensagens ao usuário, labels de formulário e comentários explicativos.
- Models no **singular** (`Task`, `Family`, `Reward`).
- Tabelas no **plural** (`tasks`, `families`, `rewards`).
- Controllers no **singular** + sufixo (`TaskController`, `RewardController`).
- Migrations com prefixo descritivo: `create_task_completions_table`, `add_role_to_users_table`.

### Estrutura MVC — responsabilidades claras

```
Controller  →  recebe request, chama Service, retorna response
Service     →  lógica de negócio (nunca acessa request diretamente)
Model       →  relacionamentos, $fillable, $casts, scopes
View        →  só exibição: loops, condicionais simples, @components
Request     →  todas as regras de validação
Policy      →  todas as regras de autorização
```

> **Regra prática:** se um método no controller tiver mais de ~15 linhas de lógica, extraia para um Service.

### Commits

Fazer commit ao fim de cada sub-tarefa concluída e testada. Mensagem em português, no imperativo:

```
feat: criar migration da tabela tasks
feat: implementar CRUD de tarefas
fix: corrigir validação de recorrência semanal
refactor: extrair lógica de pontos para PointService
```

### Antes de avançar para a próxima fase

Verificar sempre:
- [ ] `php artisan migrate:fresh --seed` roda sem erros
- [ ] Nenhum `dd()`, `var_dump()` ou `dump()` esquecido no código
- [ ] Rotas protegidas pelo middleware/policy correto
- [ ] Commit feito com mensagem descritiva

---

## Fase 1 — Configuração do Projeto

**Tempo estimado:** ~2 horas

### Passos

1. Criar projeto Laravel:
   ```bash
   composer create-project laravel/laravel kidtask
   cd kidtask
   ```

2. Configurar `.env` com banco de dados e variáveis da aplicação:
   ```env
   APP_NAME=KidTask
   APP_ENV=local
   APP_DEBUG=true
   DB_CONNECTION=mysql
   DB_DATABASE=kidtask
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Instalar dependências frontend:
   ```bash
   composer require laravel/breeze --dev
   npm install
   npm run dev
   ```

4. Inicializar repositório Git:
   ```bash
   git init
   git add .
   git commit -m "feat: setup inicial do projeto KidTask"
   ```

5. Criar estrutura de pastas de serviços e requests (serão populadas nas fases seguintes):
   ```
   app/
   ├── Http/
   │   ├── Controllers/
   │   ├── Requests/        ← Form Requests de validação
   │   └── Middleware/      ← Middlewares customizados
   ├── Models/
   ├── Services/            ← Lógica de negócio
   └── Policies/            ← Regras de autorização
   ```

### Boas Práticas — Configuração

- **Nunca commitar o `.env`** — ele já está no `.gitignore` por padrão. Manter o `.env.example` sempre atualizado.
- Gerar a `APP_KEY` imediatamente: `php artisan key:generate`
- Usar `APP_DEBUG=true` apenas em desenvolvimento — nunca em produção.
- Testar conexão com o banco antes de continuar: `php artisan db:show`

---

## Fase 2 — Migrations & Models

**Tempo estimado:** ~3 horas

### Ordem de criação das migrations

Respeitar a ordem por causa das foreign keys:

```
1. families
2. users (alterar tabela existente — add family_id, role, avatar, birthdate)
3. tasks
4. task_assignments
5. task_completions
6. rewards
7. point_transactions
```

### Passos

1. Criar cada migration:
   ```bash
   php artisan make:migration create_families_table
   php artisan make:migration add_family_fields_to_users_table
   php artisan make:migration create_tasks_table
   php artisan make:migration create_task_assignments_table
   php artisan make:migration create_task_completions_table
   php artisan make:migration create_rewards_table
   php artisan make:migration create_point_transactions_table
   ```

2. Criar os Models com relacionamentos:
   ```bash
   php artisan make:model Family
   php artisan make:model Task
   php artisan make:model TaskAssignment
   php artisan make:model TaskCompletion
   php artisan make:model Reward
   php artisan make:model PointTransaction
   ```

3. Criar Factories e Seeders para dados de demonstração:
   ```bash
   php artisan make:factory FamilyFactory
   php artisan make:factory TaskFactory
   php artisan make:seeder DatabaseSeeder  # já existe, popular
   ```

4. Rodar e validar:
   ```bash
   php artisan migrate --seed
   ```

### Campos obrigatórios por tabela

**families:** `id`, `name`, `invite_code` (unique), `timestamps`

**users (alterações):** `family_id` (FK, nullable), `role` (enum: parent/child), `avatar` (nullable), `birthdate` (nullable)

**tasks:** `id`, `family_id` (FK), `created_by` (FK → users), `title`, `description` (nullable), `points` (default 1), `recurrence` (enum: none/daily/weekly/monthly), `recurrence_day` (nullable), `due_date` (nullable), `reminder_time` (nullable), `is_active` (default true), `timestamps`

**task_assignments:** `id`, `task_id` (FK), `user_id` (FK), `timestamps`

**task_completions:** `id`, `task_id` (FK), `user_id` (FK), `completed_at`, `status` (enum: pending_validation/approved/rejected, default pending_validation), `validated_by` (FK nullable), `validated_at` (nullable), `notes` (nullable), `timestamps`

**rewards:** `id`, `family_id` (FK), `title`, `description` (nullable), `points_required`, `type` (enum: allowance/prize), `timestamps`

**point_transactions:** `id`, `user_id` (FK), `task_completion_id` (FK nullable), `points`, `type` (enum: earned/redeemed), `description`, `timestamps`

### Relacionamentos nos Models

```php
// Family
public function users(): HasMany        // → User
public function tasks(): HasMany        // → Task
public function rewards(): HasMany      // → Reward

// User
public function family(): BelongsTo     // → Family
public function createdTasks(): HasMany // → Task (created_by)
public function assignments(): HasMany  // → TaskAssignment
public function completions(): HasMany  // → TaskCompletion
public function pointTransactions(): HasMany // → PointTransaction

// Task
public function family(): BelongsTo    // → Family
public function creator(): BelongsTo   // → User (created_by)
public function assignedUsers(): BelongsToMany // via task_assignments
public function completions(): HasMany // → TaskCompletion

// TaskCompletion
public function task(): BelongsTo      // → Task
public function user(): BelongsTo      // → User
public function validator(): BelongsTo // → User (validated_by)
public function pointTransaction(): HasOne // → PointTransaction
```

### Boas Práticas — Banco de Dados

- **Sempre usar migrations** — nunca alterar o banco manualmente (phpMyAdmin, etc.).
- Definir `$casts` nos Models para tipos corretos:
  ```php
  protected $casts = [
      'is_active'    => 'boolean',
      'due_date'     => 'date',
      'reminder_time'=> 'datetime',
      'completed_at' => 'datetime',
      'validated_at' => 'datetime',
      'recurrence'   => RecurrenceEnum::class, // se usar Enum PHP 8.1
  ];
  ```
- Definir `$fillable` **explicitamente** em todo Model — nunca usar `$guarded = []`.
- Adicionar índices nos campos usados em filtros e foreign keys:
  ```php
  $table->index(['family_id', 'status']);
  $table->index('completed_at');
  ```
- Usar `unsignedBigInteger` ou `foreignId` para foreign keys.

---

## Fase 3 — Autenticação & Famílias

**Tempo estimado:** ~2 horas | **Vale:** Extra (1 pt)

### Passos

1. Instalar Laravel Breeze com Blade:
   ```bash
   php artisan breeze:install blade
   php artisan migrate
   npm run dev
   ```

2. Customizar o `RegisterController` / `RegisteredUserController` para:
   - Campo `role` (parent/child) no formulário de cadastro.
   - Se `role = parent`: criar nova família e associar o usuário como admin.
   - Se `role = child`: campo `invite_code` para entrar na família existente.

3. Criar Middleware de proteção por perfil:
   ```bash
   php artisan make:middleware EnsureIsParent
   php artisan make:middleware EnsureIsSameFamily
   ```

4. Criar Policies para autorização por recurso:
   ```bash
   php artisan make:policy TaskPolicy --model=Task
   php artisan make:policy RewardPolicy --model=Reward
   ```

5. Definir grupos de rotas no `routes/web.php`:
   ```php
   // Rotas acessíveis por ambos os perfis
   Route::middleware(['auth'])->group(function () { ... });

   // Rotas exclusivas para pais
   Route::middleware(['auth', 'role:parent'])->prefix('parent')->group(function () { ... });

   // Rotas exclusivas para filhos
   Route::middleware(['auth', 'role:child'])->prefix('child')->group(function () { ... });
   ```

### Boas Práticas — Autenticação

- Usar **Policies** para autorização de recursos (não só middleware de role):
  ```php
  $this->authorize('update', $task); // no controller
  @can('update', $task) ... @endcan  // na view
  ```
- **Nunca** verificar `$user->role` diretamente na view — usar `@can` / `@cannot`.
- Centralizar lógica de família em um `FamilyService`:
  ```php
  // app/Services/FamilyService.php
  public function createForUser(User $user, string $name): Family
  public function joinByCode(User $user, string $code): Family
  ```
- **Sempre validar** se o recurso pertence à família do usuário logado antes de qualquer operação.

---

## Fase 4 — CRUD de Tarefas

**Tempo estimado:** ~4 horas | **Vale:** CRUD (2 pt) + Busca/Filtro (1 pt)

### Passos

1. Criar controller como resource:
   ```bash
   php artisan make:controller TaskController --resource --model=Task
   ```

2. Criar Form Requests de validação:
   ```bash
   php artisan make:request StoreTaskRequest
   php artisan make:request UpdateTaskRequest
   ```

3. Implementar lógica de recorrência em `app/Services/TaskService.php`:
   ```php
   public function store(array $data, User $creator): Task
   public function assignToChildren(Task $task, array $childrenIds): void
   public function getTasksForToday(User $child): Collection
   public function getFilteredTasks(User $parent, array $filters): Collection
   ```

4. Implementar filtros no `index()` do controller:
   ```php
   // Filtros aceitos: date, status, child_id, recurrence
   $tasks = $this->taskService->getFilteredTasks(auth()->user(), request()->only([
       'date', 'status', 'child_id', 'recurrence'
   ]));
   ```

5. Registrar rotas resource:
   ```php
   Route::resource('tasks', TaskController::class);
   ```

### Validações obrigatórias (StoreTaskRequest)

```php
'title'          => 'required|string|max:255',
'points'         => 'required|integer|min:1|max:100',
'recurrence'     => 'required|in:none,daily,weekly,monthly',
'recurrence_day' => 'nullable|integer|min:0|max:6', // obrigatório se weekly
'due_date'       => 'nullable|date|after_or_equal:today', // obrigatório se none
'reminder_time'  => 'nullable|date_format:H:i',
'children'       => 'required|array|min:1',
'children.*'     => 'exists:users,id',
```

### Boas Práticas — Controllers e CRUD

- **Controllers finos:** método `store()` do controller deve ter no máximo ~8 linhas.
- Usar **Route Model Binding** — o Laravel injeta o Model automaticamente:
  ```php
  public function edit(Task $task) { ... } // não precisa de Task::find($id)
  ```
- Usar **Form Requests** para toda validação — nunca `$request->validate()` direto no controller.
- Sempre verificar autorização explicitamente:
  ```php
  $this->authorize('update', $task);
  ```
- Retornar feedback claro ao usuário:
  ```php
  return redirect()->route('tasks.index')->with('success', 'Tarefa criada com sucesso!');
  return redirect()->back()->withErrors($validator)->withInput();
  ```
- Usar **scopes** no Model para filtros reutilizáveis:
  ```php
  // Task.php
  public function scopeForFamily(Builder $query, int $familyId): Builder
  public function scopeActive(Builder $query): Builder
  public function scopeForDate(Builder $query, Carbon $date): Builder
  ```

---

## Fase 5 — Fluxo de Conclusão & Pontos

**Tempo estimado:** ~3 horas | **Vale:** Relacionamentos (1 pt)

### Passos

1. Criar controller de conclusões:
   ```bash
   php artisan make:controller TaskCompletionController
   ```

2. Criar dashboard da criança — tarefas do dia:
   ```bash
   php artisan make:controller Child/DashboardController
   ```

3. Criar ação de validação para o pai (aprovar/rejeitar):
   ```bash
   php artisan make:controller Parent/ValidationController
   ```

4. Criar serviços especializados:
   ```bash
   # app/Services/PointService.php
   # app/Services/CompletionService.php
   ```

5. Implementar `PointService`:
   ```php
   public function credit(TaskCompletion $completion): PointTransaction
   // Cria o registro em point_transactions e associa à completion

   public function getBalance(User $child): int
   // Soma todos os pontos earned - redeemed da criança

   public function redeem(User $child, Reward $reward): PointTransaction
   // Verifica saldo e registra resgate
   ```

### Fluxo completo de uma tarefa

```
Criança acessa dashboard
    → vê tarefas do dia (TaskService::getTasksForToday)
    → clica "Marcar como feita"
    → POST /completions → CompletionService::markDone()
    → cria TaskCompletion com status = pending_validation
    → notifica pai (flash ou futura notificação)

Pai acessa painel de validação
    → vê lista de completions pendentes
    → clica "Aprovar" com nota opcional
    → POST /validations/{completion}/approve
    → CompletionService::approve()
    → PointService::credit() — cria PointTransaction
    → redirect com sucesso
```

### Boas Práticas — Serviços e Lógica de Negócio

- Usar **database transactions** ao aprovar (garante consistência):
  ```php
  DB::transaction(function () use ($completion) {
      $completion->update(['status' => 'approved', 'validated_at' => now()]);
      $this->pointService->credit($completion);
  });
  ```
- **Nunca duplicar lógica** — o método que calcula saldo fica só no `PointService`.
- Disparar **Events** ao aprovar para facilitar futuras notificações:
  ```php
  event(new TaskApproved($completion));
  ```
- Separar responsabilidades claramente:
  - `CompletionService` → gerencia o ciclo de vida da conclusão
  - `PointService` → gerencia pontos e resgates
  - `TaskService` → gerencia tarefas e recorrências

---

## Fase 6 — CRUD de Recompensas & Layout

**Tempo estimado:** ~3 horas | **Vale:** Layout (1 pt) + Template/Menu (2 pt)

### Passos

1. Criar controller de recompensas (segundo CRUD obrigatório):
   ```bash
   php artisan make:controller RewardController --resource --model=Reward
   php artisan make:request StoreRewardRequest
   php artisan make:request UpdateRewardRequest
   ```

2. Criar layout Blade base:
   ```
   resources/views/
   ├── layouts/
   │   ├── app.blade.php          ← layout principal com menu
   │   ├── guest.blade.php        ← layout de autenticação
   │   └── partials/
   │       ├── nav.blade.php      ← menu de navegação
   │       └── alerts.blade.php   ← mensagens flash
   ├── components/
   │   ├── card.blade.php         ← card reutilizável
   │   ├── badge.blade.php        ← badge de status
   │   ├── avatar.blade.php       ← avatar de usuário
   │   └── empty-state.blade.php  ← estado vazio
   ├── parent/                    ← views do painel do pai
   └── child/                     ← views do painel da criança
   ```

3. Estrutura do layout base (`layouts/app.blade.php`):
   ```blade
   <!DOCTYPE html>
   <html lang="pt-BR">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>@yield('title', 'KidTask') — KidTask</title>
       @vite(['resources/css/app.css', 'resources/js/app.js'])
   </head>
   <body class="bg-gray-50">
       @include('layouts.partials.nav')
       <main class="max-w-5xl mx-auto px-4 py-8">
           @include('layouts.partials.alerts')
           @yield('content')
       </main>
   </body>
   </html>
   ```

4. Usar nas views filhas:
   ```blade
   @extends('layouts.app')
   @section('title', 'Minhas Tarefas')
   @section('content')
       {{-- conteúdo aqui --}}
   @endsection
   ```

5. Menu com navegação diferente por perfil:
   ```blade
   @auth
       @if(auth()->user()->isParent())
           <a href="{{ route('parent.tasks.index') }}">Tarefas</a>
           <a href="{{ route('parent.rewards.index') }}">Recompensas</a>
           <a href="{{ route('parent.validations.index') }}">Validações</a>
       @else
           <a href="{{ route('child.dashboard') }}">Meu Dia</a>
           <a href="{{ route('child.points') }}">Meus Pontos</a>
       @endif
   @endauth
   ```

### Boas Práticas — Views e Templates Blade

- **Um layout base** para toda a aplicação — nunca repetir `<head>`, `<nav>` ou `<footer>`.
- Extrair **componentes Blade** para tudo que aparece em 2 ou mais views:
  ```bash
  php artisan make:component Card
  php artisan make:component Badge
  ```
- Usar `@csrf` em **todos os formulários** sem exceção.
- Usar `@method('PUT')` e `@method('DELETE')` onde necessário.
- Mensagens de feedback via **session flash**:
  ```php
  // controller
  return redirect()->route('tasks.index')->with('success', 'Tarefa criada!');
  ```
  ```blade
  {{-- partials/alerts.blade.php --}}
  @if(session('success'))
      <div class="alert-success">{{ session('success') }}</div>
  @endif
  ```
- **Nada de lógica** nas views além de loops e condicionais simples. Cálculos, formatações e queries ficam no controller/service.
- Usar **Tailwind utility classes** — não criar CSS customizado a menos que seja absolutamente necessário.

---

## Fase 7 — Testes, Revisão & Entrega

**Tempo estimado:** ~2 horas

### Checklist de testes manuais

Testar cada fluxo com dois usuários diferentes (pai e filho em abas separadas):

**Cadastro e autenticação**
- [ ] Pai consegue se cadastrar e cria família automaticamente
- [ ] Filho consegue entrar na família usando o código de convite
- [ ] Logout e login funcionam corretamente
- [ ] Usuário sem família não acessa área restrita

**Tarefas (perfil pai)**
- [ ] Criar tarefa diária atribuída a um filho
- [ ] Criar tarefa semanal com dia específico
- [ ] Criar evento único com data
- [ ] Editar tarefa existente
- [ ] Desativar tarefa (sem excluir histórico)
- [ ] Filtrar tarefas por status, data e filho

**Tarefas (perfil filho)**
- [ ] Dashboard mostra apenas as tarefas do dia
- [ ] Filho consegue marcar tarefa como feita
- [ ] Tarefa marcada aparece como "pendente de validação"
- [ ] Filho não vê tarefas de outros filhos

**Validação e pontos**
- [ ] Pai vê lista de tarefas pendentes de validação
- [ ] Pai consegue aprovar com nota opcional
- [ ] Pai consegue rejeitar com justificativa
- [ ] Ao aprovar, pontos são creditados corretamente
- [ ] Histórico de pontos do filho está correto

**Recompensas**
- [ ] Pai consegue criar recompensa com custo em pontos
- [ ] Filho vê recompensas disponíveis
- [ ] Filho com saldo suficiente consegue resgatar
- [ ] Filho sem saldo suficiente recebe mensagem de erro

### Checklist de qualidade do código

- [ ] Nenhum `dd()`, `dump()`, `var_dump()` ou `echo` de debug no código
- [ ] Nenhuma senha, chave ou dado sensível no código (só no `.env`)
- [ ] `.env.example` atualizado com todas as variáveis necessárias
- [ ] `README.md` com instruções de instalação (ver abaixo)
- [ ] Todas as rotas testadas com usuário sem permissão (deve retornar 403)
- [ ] Formulários com dados inválidos mostram mensagens de erro claras
- [ ] Páginas sem dados mostram estado vazio (não ficam em branco)

### Conteúdo do README.md

```markdown
# KidTask

Sistema de afazeres familiares gamificado. Pais cadastram tarefas,
filhos concluem, pais validam e pontos são creditados.

## Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+

## Instalação

1. Clone o repositório:
   git clone https://github.com/seu-usuario/kidtask.git
   cd kidtask

2. Instale as dependências:
   composer install
   npm install

3. Configure o ambiente:
   cp .env.example .env
   php artisan key:generate

4. Configure o banco de dados no .env:
   DB_DATABASE=kidtask
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha

5. Rode as migrations com dados de exemplo:
   php artisan migrate --seed

6. Compile os assets:
   npm run build

7. Inicie o servidor:
   php artisan serve

## Acesso de demonstração (após seed)
- Pai: pai@kidtask.com / password
- Filho: filho@kidtask.com / password
```

### Boas Práticas — Entrega

- Fazer o **push final** para o GitHub com todos os commits organizados.
- Gravar vídeo de até **3 minutos** mostrando: cadastro, criação de tarefa, conclusão pelo filho, validação pelo pai e pontos creditados.
- Verificar se o repositório é **público** no GitHub.
- Submeter no Google Classroom: link do GitHub + link do vídeo.

---

## Resumo dos Pontos da Rubrica

| Critério | Fase | Pontos |
|---|---|---|
| Laravel + Banco de Dados | 1, 2 | 2 pt |
| CRUD de 2 tabelas (tasks + rewards) | 4, 6 | 2 pt |
| Relacionamento entre tabelas | 2, 5 | 1 pt |
| Busca / Filtro | 4 | 1 pt |
| Menu de navegação | 6 | 1 pt |
| Layout atraente | 6 | 1 pt |
| Template / reutilização (Blade) | 6 | 1 pt |
| Extra — Autenticação | 3 | 1 pt |
| **Total** | | **10 pt** |

---

## Estrutura Final do Projeto

```
kidtask/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Parent/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── TaskController.php
│   │   │   │   ├── ValidationController.php
│   │   │   │   └── RewardController.php
│   │   │   └── Child/
│   │   │       ├── DashboardController.php
│   │   │       ├── TaskCompletionController.php
│   │   │       └── PointController.php
│   │   ├── Requests/
│   │   │   ├── StoreTaskRequest.php
│   │   │   ├── UpdateTaskRequest.php
│   │   │   ├── StoreRewardRequest.php
│   │   │   └── UpdateRewardRequest.php
│   │   └── Middleware/
│   │       ├── EnsureIsParent.php
│   │       └── EnsureIsSameFamily.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Family.php
│   │   ├── Task.php
│   │   ├── TaskAssignment.php
│   │   ├── TaskCompletion.php
│   │   ├── Reward.php
│   │   └── PointTransaction.php
│   ├── Services/
│   │   ├── FamilyService.php
│   │   ├── TaskService.php
│   │   ├── CompletionService.php
│   │   └── PointService.php
│   └── Policies/
│       ├── TaskPolicy.php
│       └── RewardPolicy.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── guest.blade.php
│       │   └── partials/
│       ├── components/
│       ├── parent/
│       └── child/
├── routes/
│   └── web.php
├── .env.example
└── README.md
```

---

*Gerado em 31/05/2026 — KidTask / PW2 — UNIDAVI*
