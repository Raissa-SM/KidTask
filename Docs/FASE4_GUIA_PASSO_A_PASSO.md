# KidTask — Fase 4: Guia Passo a Passo
# CRUD de Tarefas + Busca/Filtro

> Siga na ordem exata. Esta fase não tem comandos Artisan de banco — só copiar arquivos e testar.

---

## Parte A — Copiar os arquivos

Você recebeu a pasta `fase4_kidtask/` com esta estrutura:

```
fase4_kidtask/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Parent/
│   │   │       └── TaskController.php          ← novo
│   │   └── Requests/
│   │       ├── StoreTaskRequest.php            ← novo
│   │       └── UpdateTaskRequest.php           ← novo
│   ├── Policies/
│   │   └── TaskPolicy.php                      ← substitui o existente
│   └── Services/
│       └── TaskService.php                     ← novo
├── routes/
│   └── web.php                                 ← substitui o existente
└── resources/views/parent/tasks/
    ├── index.blade.php                         ← novo (crie a pasta tasks/)
    ├── create.blade.php                        ← novo
    └── edit.blade.php                          ← novo
```

### Onde colocar cada arquivo

| Arquivo | Destino no projeto | Ação |
|---|---|---|
| `TaskController.php` | `app/Http/Controllers/Parent/` | Novo |
| `StoreTaskRequest.php` | `app/Http/Requests/` | Novo |
| `UpdateTaskRequest.php` | `app/Http/Requests/` | Novo |
| `TaskPolicy.php` | `app/Policies/` | **Substitui** o existente |
| `TaskService.php` | `app/Services/` | Novo |
| `web.php` | `routes/` | **Substitui** o existente |
| `index.blade.php` | `resources/views/parent/tasks/` | Novo (crie a pasta `tasks/`) |
| `create.blade.php` | `resources/views/parent/tasks/` | Novo |
| `edit.blade.php` | `resources/views/parent/tasks/` | Novo |

> **Pasta nova a criar:** `resources/views/parent/tasks/`

> **Por que a TaskPolicy foi substituída?** A versão anterior só deixava o criador da tarefa editá-la. Como a família pode ter múltiplos responsáveis (pai e mãe), corrigimos para que qualquer pai da família possa editar.

---

## Parte B — Testar o sistema

Com o servidor rodando (`php artisan serve` + `npm run dev`), acesse com o usuário pai (`pai@kidtask.com` / `password`).

### Teste 1 — Criar tarefa diária

1. Acesse `http://localhost:8000/parent/tasks`
2. Clique em **"+ Nova tarefa"**
3. Preencha:
   - Título: `Arrumar a cama`
   - Pontos: `5`
   - Recorrência: **Diária**
   - Atribuir a: marque o filho disponível
4. Clique em "Criar tarefa"
5. ✅ Deve voltar para a lista com a mensagem "Tarefa criada com sucesso!"

### Teste 2 — Criar tarefa semanal

1. Nova tarefa com recorrência **Semanal**
2. O campo "Dia da semana" deve aparecer automaticamente
3. Os campos "Data" e "Dia do mês" devem ficar ocultos
4. ✅ Salvar e confirmar na lista com badge "Semanal"

### Teste 3 — Criar evento único

1. Nova tarefa com recorrência **Evento único**
2. O campo "Data" deve aparecer
3. Informe uma data futura
4. ✅ Badge "Evento único" na lista

### Teste 4 — Filtros

Na listagem de tarefas:
1. Digite parte de um título no campo "Buscar" e clique em Filtrar
2. ✅ Apenas tarefas com aquele texto no título aparecem
3. Filtre por filho específico
4. ✅ Apenas tarefas atribuídas a ele aparecem
5. Filtre por recorrência "Diária"
6. ✅ Apenas tarefas diárias aparecem
7. Clique em "Limpar"
8. ✅ Todos os filtros são removidos e lista volta ao normal

### Teste 5 — Editar tarefa

1. Clique em "Editar" em qualquer tarefa
2. Altere o título e os pontos
3. ✅ Salvar e confirmar as alterações na lista

### Teste 6 — Desativar / Excluir

**Tarefa sem histórico (recém criada):**
1. Clique em "Excluir"
2. Confirme o dialog
3. ✅ Tarefa some da lista

**Tarefa com histórico (do seed):**
1. Clique em "Desativar"
2. ✅ Mensagem "Tarefa desativada (possui histórico de conclusões)"
3. Filtro por **Status: Inativas** mostra a tarefa com badge vermelho "Inativa"

### Teste 7 — Proteção de rotas

1. Faça logout e entre com `filho@kidtask.com` / `password`
2. Tente acessar `http://localhost:8000/parent/tasks`
3. ✅ Deve retornar erro 403

---

## Parte C — Checklist antes de fechar a fase

- [ ] CRUD completo funciona: criar, listar, editar, excluir/desativar
- [ ] Campos condicionais funcionam: data (none), dia da semana (weekly), dia do mês (monthly)
- [ ] Filtros por busca, filho, recorrência e status funcionam
- [ ] Erro de validação (ex: sem título, sem filho) mostra mensagem em português
- [ ] Filho não consegue acessar rotas do pai (403)
- [ ] Nenhum `dd()` ou debug no código
- [ ] Commit feito:

```bash
git add .
git commit -m "feat: implementar CRUD de tarefas com filtros e validações"
```

---

## Possíveis erros e soluções

**Erro: `Route [parent.tasks.index] not defined`**
→ O `routes/web.php` não foi substituído. Confirme o destino.

**Erro: `Class "App\Http\Controllers\Parent\TaskController" not found`**
→ O arquivo foi colocado em `Controllers/` e não em `Controllers/Parent/`. Mova para a subpasta.

**Erro: `Target class [App\Http\Requests\StoreTaskRequest] does not exist`**
→ O arquivo `StoreTaskRequest.php` está faltando em `app/Http/Requests/`.

**Erro: `This action is unauthorized` ao acessar a lista de tarefas**
→ O usuário logado é filho (role=child). A rota `/parent/tasks` é exclusiva para pais.

**Campo "Dia da semana" não aparece ao selecionar Semanal**
→ O JavaScript depende do `npm run dev` estar rodando para o Tailwind processar as classes. Confirme que o terminal com `npm run dev` está ativo.

**Formulário enviado sem o campo `recurrence_day` (campo requerido)**
→ Ao trocar a recorrência, o JS desabilita os campos ocultos antes do submit. Se isso não estiver funcionando, confirme que o `npm run dev` compilou os assets mais recentes.
