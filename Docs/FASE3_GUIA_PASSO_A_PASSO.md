# KidTask — Fase 3: Guia Passo a Passo
# Autenticação & Famílias

> Siga **na ordem exata** abaixo. A fase tem 4 partes: instalar o Breeze, copiar os arquivos, registrar os controllers no Breeze e testar.

---

## Parte A — Instalar o Laravel Breeze

Com o XAMPP rodando e no terminal dentro da pasta do projeto (`kidtask/`):

```bash
php artisan breeze:install blade
```

Quando perguntar sobre dark mode ou outros temas, pode aceitar os padrões (Enter).

Depois:

```bash
npm install
npm run dev
```

> Deixe o `npm run dev` rodando em um terminal separado. Abra outro terminal para os próximos comandos.

**O que o Breeze cria automaticamente:**
- `app/Http/Controllers/Auth/` — vários controllers de autenticação padrão
- `app/Http/Requests/Auth/LoginRequest.php` — validação do login
- `resources/views/auth/` — views padrão de login, registro, etc.
- Rotas de autenticação no `routes/auth.php`

Na próxima parte você vai **substituir** as views e controllers que interessam pelo código customizado da fase.

---

## Parte B — Copiar os arquivos

Você recebeu a pasta `fase3_kidtask/` com esta estrutura:

```
fase3_kidtask/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── RegisteredUserController.php   ← substitui o do Breeze
│   │   │   │   └── AuthenticatedSessionController.php ← substitui o do Breeze
│   │   │   ├── Parent/
│   │   │   │   └── DashboardController.php        ← novo
│   │   │   └── Child/
│   │   │       └── DashboardController.php        ← novo
│   │   └── Middleware/
│   │       ├── EnsureIsParent.php                 ← novo
│   │       └── EnsureIsSameFamily.php             ← novo
│   ├── Policies/
│   │   ├── TaskPolicy.php                         ← novo
│   │   └── RewardPolicy.php                       ← novo
│   └── Services/
│       └── FamilyService.php                      ← novo
├── bootstrap/
│   └── app.php                                    ← substitui o existente
├── routes/
│   └── web.php                                    ← substitui o existente
└── resources/views/
    ├── auth/
    │   ├── register.blade.php                     ← substitui a do Breeze
    │   └── login.blade.php                        ← substitui a do Breeze
    ├── parent/
    │   └── dashboard.blade.php                    ← novo (crie a pasta)
    └── child/
        └── dashboard.blade.php                    ← novo (crie a pasta)
```

### Onde colocar cada arquivo

| Arquivo | Destino no projeto | Ação |
|---|---|---|
| `Auth/RegisteredUserController.php` | `app/Http/Controllers/Auth/` | **Substitui** |
| `Auth/AuthenticatedSessionController.php` | `app/Http/Controllers/Auth/` | **Substitui** |
| `Parent/DashboardController.php` | `app/Http/Controllers/Parent/` | Novo (crie a pasta) |
| `Child/DashboardController.php` | `app/Http/Controllers/Child/` | Novo (crie a pasta) |
| `EnsureIsParent.php` | `app/Http/Middleware/` | Novo |
| `EnsureIsSameFamily.php` | `app/Http/Middleware/` | Novo |
| `TaskPolicy.php` | `app/Policies/` | Novo |
| `RewardPolicy.php` | `app/Policies/` | Novo |
| `FamilyService.php` | `app/Services/` | Novo |
| `bootstrap/app.php` | `bootstrap/` | **Substitui** |
| `routes/web.php` | `routes/` | **Substitui** |
| `auth/register.blade.php` | `resources/views/auth/` | **Substitui** |
| `auth/login.blade.php` | `resources/views/auth/` | **Substitui** |
| `parent/dashboard.blade.php` | `resources/views/parent/` | Novo (crie a pasta) |
| `child/dashboard.blade.php` | `resources/views/child/` | Novo (crie a pasta) |

> **Pastas novas a criar:**
> - `app/Http/Controllers/Parent/`
> - `app/Http/Controllers/Child/`
> - `resources/views/parent/`
> - `resources/views/child/`

---

## Parte C — Rodar as migrations do Breeze

O Breeze adiciona algumas tabelas novas. Rode:

```bash
php artisan migrate
```

> Se aparecer aviso de "Nothing to migrate", tudo bem — significa que já estavam criadas.

---

## Parte D — Testar o sistema

Abra o servidor se ainda não estiver rodando:

```bash
php artisan serve
```

E acesse **http://localhost:8000**. Você deve ser redirecionado para a tela de login.

### Teste 1 — Cadastro do pai

1. Acesse `http://localhost:8000/register`
2. Preencha nome, e-mail e senha
3. Selecione **"Pai / Mãe"**
4. No campo "Nome da família" digite: `Família Teste`
5. Clique em "Criar conta"
6. ✅ Deve redirecionar para `/parent/dashboard` com a mensagem de boas-vindas e o código de convite

### Teste 2 — Cadastro do filho

1. Abra uma aba anônima (ou outro navegador) para não fazer logout do pai
2. Acesse `http://localhost:8000/register`
3. Preencha nome, e-mail e senha **diferentes** do pai
4. Selecione **"Filho / Filha"**
5. Pegue o código de convite que apareceu no dashboard do pai
6. Digite o código no campo "Código de convite"
7. Clique em "Criar conta"
8. ✅ Deve redirecionar para `/child/dashboard`

### Teste 3 — Proteção das rotas

1. Ainda na aba do filho logado, tente acessar: `http://localhost:8000/parent/dashboard`
2. ✅ Deve retornar erro 403 (Acesso restrito a responsáveis)
3. Faça logout e tente acessar `http://localhost:8000/parent/dashboard` sem estar logado
4. ✅ Deve redirecionar para `/login`

### Teste 4 — Código inválido

1. Tente se cadastrar como filho com o código `XXXXXX`
2. ✅ Deve mostrar a mensagem "Código de convite inválido. Verifique com seu responsável."

### Teste 5 — Login e logout

1. Faça logout do pai
2. Acesse `/login`, entre com `pai@kidtask.com` / `password` (dados do seed)
3. ✅ Deve ir para `/parent/dashboard`
4. Faça logout, entre com `filho@kidtask.com` / `password`
5. ✅ Deve ir para `/child/dashboard`

---

## Parte E — Checklist antes de fechar a fase

- [ ] `npm run dev` está compilando sem erros
- [ ] Cadastro de pai cria família e redireciona para `/parent/dashboard`
- [ ] Código de convite aparece no dashboard do pai
- [ ] Cadastro de filho com código válido funciona e vai para `/child/dashboard`
- [ ] Cadastro de filho com código inválido mostra mensagem de erro
- [ ] Acesso de filho a rota do pai retorna 403
- [ ] Acesso de visitante a rota protegida redireciona para login
- [ ] Login do seed funciona (pai@kidtask.com e filho@kidtask.com)
- [ ] Logout funciona e volta para `/login`
- [ ] Commit feito:

```bash
git add .
git commit -m "feat: implementar autenticação, famílias, middlewares e policies"
```

---

## Possíveis erros e soluções

**Erro: `Class "App\Http\Controllers\Parent\DashboardController" not found`**
→ A pasta `app/Http/Controllers/Parent/` não foi criada. Crie a pasta e coloque o arquivo dentro.

**Erro: `Route [parent.dashboard] not defined`**
→ O `routes/web.php` não foi substituído corretamente. Verifique se o arquivo novo está no lugar certo.

**Erro: `Target class [parent] does not exist`**
→ O `bootstrap/app.php` não foi substituído. O alias do middleware `parent` é registrado lá.

**Erro: `View [parent.dashboard] not found`**
→ A pasta `resources/views/parent/` ou o arquivo `dashboard.blade.php` não foi criado. Verifique o destino.

**Erro: `Class "App\Http\Requests\Auth\LoginRequest" not found`**
→ O Breeze não foi instalado ainda. Rode `php artisan breeze:install blade` antes de copiar os arquivos.

**Tela de login do Breeze aparece em vez da customizada**
→ O arquivo `resources/views/auth/login.blade.php` não foi substituído. Confirme que o novo arquivo está no lugar correto.
