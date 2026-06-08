# KidTask — Fase 6: Guia Passo a Passo
# CRUD de Recompensas + Layout Base

---

## Parte A — Copiar os arquivos

```
fase6_kidtask/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Parent/
│   │   │   │   └── RewardController.php              ← novo
│   │   │   └── Child/
│   │   │       └── RewardController.php              ← novo
│   │   └── Requests/
│   │       ├── StoreRewardRequest.php                ← novo
│   │       └── UpdateRewardRequest.php               ← novo
├── routes/
│   └── web.php                                       ← substitui o existente
└── resources/views/
    ├── layouts/
    │   ├── app.blade.php                             ← substitui o do Breeze
    │   ├── guest.blade.php                           ← substitui o do Breeze
    │   └── partials/
    │       ├── nav.blade.php                         ← novo (crie a pasta partials/)
    │       └── alerts.blade.php                      ← novo
    ├── components/
    │   ├── empty-state.blade.php                     ← novo
    │   ├── badge.blade.php                           ← novo
    │   └── page-header.blade.php                     ← novo
    ├── auth/
    │   ├── login.blade.php                           ← substitui o existente
    │   └── register.blade.php                        ← substitui o existente
    ├── parent/
    │   ├── dashboard.blade.php                       ← substitui o existente
    │   ├── tasks/
    │   │   ├── index.blade.php                       ← substitui o existente
    │   │   ├── create.blade.php                      ← substitui o existente
    │   │   └── edit.blade.php                        ← substitui o existente
    │   ├── validations/
    │   │   └── index.blade.php                       ← substitui o existente
    │   └── rewards/                                  ← nova pasta
    │       ├── index.blade.php                       ← novo
    │       ├── create.blade.php                      ← novo
    │       └── edit.blade.php                        ← novo
    └── child/
        ├── dashboard.blade.php                       ← substitui o existente
        ├── points.blade.php                          ← substitui o existente
        └── rewards.blade.php                         ← novo
```

### Pastas novas a criar
- `resources/views/layouts/partials/`
- `resources/views/parent/rewards/`

---

## Parte B — Testar o sistema

### Teste 1 — Layout base funcionando

1. Acesse qualquer página autenticada (ex: `/parent/tasks`)
2. ✅ O menu mostra: Painel · Tarefas · Recompensas · Validações
3. O item ativo deve ficar com a cor indigo/negrito
4. Clique em cada item do menu e confirme que navega corretamente

### Teste 2 — CRUD de Recompensas (pai)

1. Clique em **Recompensas** no menu
2. Clique em **"+ Nova recompensa"**
3. Crie uma recompensa tipo **Prêmio**: "Sorvete" · 30 pts
4. Crie uma tipo **Mesada**: "Mesada quinzenal" · 150 pts
5. ✅ Ambas aparecem na lista ordenadas por pontos (menor primeiro)
6. Edite o "Sorvete" para 35 pontos → ✅ atualiza
7. Exclua a mesada → ✅ some da lista
8. Tente criar sem título → ✅ mensagem de erro em português

### Teste 3 — Recompensas para o filho

1. Entre com `filho@kidtask.com`
2. Clique em **Recompensas** no menu do filho
3. ✅ Lista aparece com saldo no canto superior
4. Recompensas que o filho **pode resgatar** têm botão "Resgatar" azul
5. Recompensas fora do alcance aparecem acinzentadas com "🔒" e "(faltam X pts)"

### Teste 4 — Resgatar recompensa

1. Garanta que o filho tem pontos suficientes (aprove algumas tarefas se necessário)
2. Clique em **Resgatar** em uma recompensa disponível
3. Confirme o diálogo
4. ✅ Mensagem de sucesso "Recompensa X resgatada com sucesso! 🎉"
5. O saldo diminuiu corretamente
6. Em **Meus Pontos**, o histórico mostra "🎁 Resgatou: X -N"

### Teste 5 — Tentar resgatar sem saldo

1. Tente resgatar uma recompensa cara (via URL direta se necessário)
2. ✅ Mensagem de erro: "Saldo insuficiente. Você tem X pontos e precisa de Y."

### Teste 6 — Mensagens flash via layout

1. Crie uma tarefa → ✅ mensagem verde com ✅ aparece no topo
2. Aprove uma conclusão → ✅ mensagem verde
3. Rejeite uma conclusão → ✅ mensagem verde (confirmação de rejeição)
4. As mensagens somem ao navegar para outra página

---

## Parte C — Checklist antes do commit

- [ ] Menu de navegação aparece igual em todas as páginas
- [ ] Item ativo no menu está destacado
- [ ] Contador vermelho em "Validações" aparece quando há pendentes
- [ ] CRUD completo de recompensas funciona (criar, listar, editar, excluir)
- [ ] Filho vê recompensas e resgata com saldo suficiente
- [ ] Filho sem saldo recebe mensagem de erro clara
- [ ] Mensagens flash (success/error) aparecem em todas as páginas
- [ ] Nenhum `dd()` ou debug no código

```bash
git add .
git commit -m "feat: implementar CRUD de recompensas e layout base com templates Blade"
```

---

## Possíveis erros e soluções

**`View [layouts.app] not found`**
→ O arquivo `resources/views/layouts/app.blade.php` não foi substituído ou está na pasta errada.

**`View [components.empty-state] not found`**
→ Os componentes `empty-state.blade.php`, `badge.blade.php` e `page-header.blade.php` precisam estar em `resources/views/components/`.

**`Route [parent.rewards.index] not defined`**
→ O `routes/web.php` não foi substituído.

**`Route [child.rewards] not defined`**
→ Mesma causa — substituir o `web.php`.

**Menu aparece mas sem o link "Recompensas" para o filho**
→ A rota `child.rewards` precisa existir no `web.php`. Confirme que o arquivo foi substituído.
