# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Full project setup (first run)
composer run setup

# Development (starts Laravel server, queue worker, log viewer, and Vite dev server concurrently)
composer run dev

# Run tests
composer run test
# or
php artisan test

# Build assets for production
npm run build

# Code formatting
./vendor/bin/pint

# Migrations
php artisan migrate
php artisan migrate:fresh --seed   # reset DB with demo data
```

## Architecture

**KidTask** is a Laravel 12 family chore management app. Families are the top-level organizational unit. Parents create and assign tasks to their children; children complete tasks, earn points, and redeem rewards.

### Role System

Every `User` has a `role` field (`'parent'` or `'child'`). All routes are protected by `auth`, and further divided by:
- `EnsureIsParent` middleware — aborts 403 if the user is not a parent
- `EnsureIsSameFamily` middleware — aborts 403 if `family_id` is null (user hasn't joined a family yet)

Parent routes live under `/parent/*`, child routes under `/child/*`.

### Task Workflow

1. Parent creates a `Task` and assigns it to one or more children (via `task_assignments` pivot).
2. Child marks task complete → a `TaskCompletion` record is created with status `pending_validation`.
3. Parent approves → status becomes `approved`, a `PointTransaction` (type `earned`) is created atomically via `CompletionService`.
4. Parent rejects → status becomes `rejected`, no points are awarded.

Point balance is derived by summing `point_transactions`: `sum(earned) - sum(redeemed)`. All transactions are append-only for audit purposes.

### Task Recurrence

Tasks have a `recurrence` enum: `none | daily | weekly | monthly`.
- `none`: uses `due_date` for a one-time task
- `daily`: appears every day
- `weekly` / `monthly`: appear on `recurrence_day` (0–6 day-of-week or 1–28 day-of-month)

The `Task` model has a `forDate($date)` scope that applies this logic.

### Soft-Delete Pattern

Tasks with existing completion history are deactivated (`is_active = false`) instead of hard-deleted, to preserve the point audit trail. Tasks without history are hard-deleted. This logic lives in `TaskService`.

### Service Layer

Business logic is extracted into four services injected via constructor:

| Service | Responsibility |
|---|---|
| `TaskService` | CRUD for tasks, syncing assignments, filtering by date/recurrence |
| `CompletionService` | Mark done, approve/reject (DB transaction wraps point crediting) |
| `PointService` | Credit points, calculate balance, redeem rewards |
| `FamilyService` | Create family, generate/join by invite code |

### Authorization

- `TaskPolicy` — parents see all family tasks; creation/editing scoped to `family_id`
- `RewardPolicy` — all family members view; only parents create/edit/delete

### Frontend

Blade templates with Tailwind CSS 3 and Alpine.js. Assets bundled via Vite. No SPA framework — server-rendered pages with Alpine.js for interactivity.

### Key Relationships

```
Family ──< User (parent or child)
Family ──< Task ──< TaskAssignment >── User (child)
                 ──< TaskCompletion ──< PointTransaction
Family ──< Reward
```

### Database

MySQL (configured in `.env`). Sessions, cache, and queue all use the `database` driver by default. Locale is `pt_BR` with timezone `America/Sao_Paulo`.
