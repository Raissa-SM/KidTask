# KidTask — Fase 5: Guia Passo a Passo
# Fluxo de Conclusão & Pontos

---

## Parte A — Copiar os arquivos

```
fase5_kidtask/
├── app/
│   ├── Services/
│   │   ├── PointService.php                          ← novo
│   │   └── CompletionService.php                     ← novo
│   └── Http/Controllers/
│       ├── Child/
│       │   ├── DashboardController.php               ← substitui o existente
│       │   ├── TaskCompletionController.php          ← novo
│       │   └── PointController.php                   ← novo
│       └── Parent/
│           ├── DashboardController.php               ← substitui o existente
│           └── ValidationController.php              ← novo
├── routes/
│   └── web.php                                       ← substitui o existente
└── resources/views/
    ├── child/
    │   ├── dashboard.blade.php                       ← substitui o existente
    │   └── points.blade.php                          ← novo
    └── parent/
        ├── dashboard.blade.php                       ← substitui o existente
        └── validations/
            └── index.blade.php                       ← novo (crie a pasta validations/)
```

### Tabela de destinos

| Arquivo | Destino | Ação |
|---|---|---|
| `PointService.php` | `app/Services/` | Novo |
| `CompletionService.php` | `app/Services/` | Novo |
| `Child/DashboardController.php` | `app/Http/Controllers/Child/` | **Substitui** |
| `Child/TaskCompletionController.php` | `app/Http/Controllers/Child/` | Novo |
| `Child/PointController.php` | `app/Http/Controllers/Child/` | Novo |
| `Parent/DashboardController.php` | `app/Http/Controllers/Parent/` | **Substitui** |
| `Parent/ValidationController.php` | `app/Http/Controllers/Parent/` | Novo |
| `web.php` | `routes/` | **Substitui** |
| `child/dashboard.blade.php` | `resources/views/child/` | **Substitui** |
| `child/points.blade.php` | `resources/views/child/` | Novo |
| `parent/dashboard.blade.php` | `resources/views/parent/` | **Substitui** |
| `parent/validations/index.blade.php` | `resources/views/parent/validations/` | Novo (crie a pasta) |

> **Pasta nova:** `resources/views/parent/validations/`

---

## Parte B — Testar o sistema

Com `php artisan serve` e `npm run dev` rodando.

### Teste 1 — Dashboard da criança mostra tarefas do dia

1. Entre com `filho@kidtask.com` / `password`
2. ✅ As 4 tarefas do seed aparecem (3 diárias + 1 semanal de segunda)
3. Cada card mostra título, pontos e o botão "Fiz! ✓"
4. O saldo de pontos (0) aparece no canto superior direito

### Teste 2 — Marcar tarefa como concluída

1. Clique em "Fiz! ✓" em qualquer tarefa
2. ✅ Card muda para fundo amarelo com ícone ⏳ e texto "Aguardando validação"
3. O botão some para esta tarefa

### Teste 3 — Duplo clique não registra duas vezes

1. Tente marcar a mesma tarefa novamente (forçando via URL ou recarregando)
2. ✅ Mensagem: "Esta tarefa já foi marcada como concluída hoje."

### Teste 4 — Pai vê pendência no dashboard

1. Abra outra aba e entre com `pai@kidtask.com` / `password`
2. ✅ Dashboard mostra o número 1 no card "Aguardando validação"
3. O menu "Validações" também mostra um contador vermelho com "1"
4. Na seção "Conclusões pendentes" aparece a tarefa com o nome do filho e tempo relativo

### Teste 5 — Aprovar conclusão

1. Acesse Validações pelo menu
2. Veja o card da conclusão pendente com nome da tarefa, filho e horário
3. Opcionalmente escreva uma observação como "Ótimo trabalho!"
4. Clique em "✅ Aprovar"
5. ✅ Mensagem: "Arrumar a cama aprovada! 5 pontos creditados para Lucas Silva."
6. O card some da lista de pendentes e aparece no histórico como aprovada

### Teste 6 — Pontos creditados para o filho

1. Volte para a aba do filho e recarregue o dashboard
2. ✅ O saldo agora mostra os pontos da tarefa aprovada
3. O card da tarefa mostra fundo verde com ✅ e "Aprovada!"
4. Acesse "Meus Pontos" pelo menu
5. ✅ Histórico mostra: "⭐ Concluiu: Arrumar a cama +5"

### Teste 7 — Rejeitar conclusão

1. Filho marca outra tarefa como feita
2. No painel do pai, clique em "❌ Rejeitar" **sem preencher o motivo**
3. ✅ Mensagem: "Informe o motivo da rejeição."
4. Preencha o motivo e rejeite
5. ✅ Na aba do filho o card mostra fundo vermelho com ❌, "Rejeitada" e o motivo
6. O botão "Tentar de novo" aparece para poder submeter novamente

### Teste 8 — Segurança: filho não acessa validações

1. Com o filho logado, tente acessar `http://localhost:8000/parent/validations`
2. ✅ Erro 403

---

## Parte C — Checklist antes do commit

- [ ] Dashboard da criança mostra tarefas do dia corretamente
- [ ] "Fiz! ✓" registra a conclusão e muda o visual do card
- [ ] Duplo registro no mesmo dia retorna mensagem de erro
- [ ] Dashboard do pai mostra contador de pendências
- [ ] Aprovar credita os pontos automaticamente
- [ ] Rejeitar sem motivo retorna erro de validação
- [ ] Histórico de pontos da criança está correto
- [ ] Filho não acessa rotas de pai (403)
- [ ] Nenhum `dd()` ou debug no código

```bash
git add .
git commit -m "feat: implementar fluxo de conclusão, validação e pontos"
```

---

## Possíveis erros e soluções

**`Call to undefined method translatedFormat()`**
→ O Laravel precisa do locale PT-BR configurado. Abra `config/app.php` e ajuste:
```php
'locale' => 'pt_BR',
'faker_locale' => 'pt_BR',
```
Ou substitua `now()->translatedFormat('l, d \d\e F')` por `now()->format('d/m/Y')` no `child/dashboard.blade.php`.

**Saldo zerado mesmo após aprovar**
→ Confirme que `PointService` e `CompletionService` estão em `app/Services/` e não em subpastas.

**Erro: `Route [child.tasks.complete] not defined`**
→ O `routes/web.php` não foi substituído. Verifique o destino.

**Validações não aparecem para o pai**
→ O `Parent/DashboardController.php` e `Parent/ValidationController.php` precisam ter sido substituídos/criados corretamente.
