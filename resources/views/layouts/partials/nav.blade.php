{{--
    Navbar principal do KidTask.
    Incluído automaticamente pelo layouts/app.blade.php.
    Adapta os links conforme o perfil do usuário logado (pai ou filho).
--}}
<header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">

    <a href="{{ auth()->check() && auth()->user()->isParent() ? route('parent.dashboard') : route('child.dashboard') }}"
       class="text-xl font-bold text-indigo-600">
        KidTask
    </a>

    @auth
        <nav class="flex items-center gap-6 text-sm">

            @if(auth()->user()->isParent())
                {{-- Menu do pai --}}
                <a href="{{ route('parent.dashboard') }}"
                   class="{{ request()->routeIs('parent.dashboard') ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-indigo-600' }}">
                    Painel
                </a>

                <a href="{{ route('parent.tasks.index') }}"
                   class="{{ request()->routeIs('parent.tasks.*') ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-indigo-600' }}">
                    Tarefas
                </a>

                <a href="{{ route('parent.rewards.index') }}"
                   class="{{ request()->routeIs('parent.rewards.*') ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-indigo-600' }}">
                    Recompensas
                </a>

                <a href="{{ route('parent.validations.index') }}"
                   class="{{ request()->routeIs('parent.validations.*') ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-indigo-600' }}">
                    Validações
                    @php
                        $pendingNavCount = \App\Models\TaskCompletion::where('status', 'pending_validation')
                            ->whereHas('task', fn($q) => $q->where('family_id', auth()->user()->family_id))
                            ->count();
                    @endphp
                    @if($pendingNavCount > 0)
                        <span class="ml-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
                            {{ $pendingNavCount }}
                        </span>
                    @endif
                </a>

            @else
                {{-- Menu do filho --}}
                <a href="{{ route('child.dashboard') }}"
                   class="{{ request()->routeIs('child.dashboard') ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-indigo-600' }}">
                    Meu Dia
                </a>

                <a href="{{ route('child.points') }}"
                   class="{{ request()->routeIs('child.points') ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-indigo-600' }}">
                    Meus Pontos
                </a>

                <a href="{{ route('child.rewards') }}"
                   class="{{ request()->routeIs('child.rewards') ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-indigo-600' }}">
                    Recompensas
                </a>
            @endif

        </nav>

        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600 hidden sm:block">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition">
                    Sair
                </button>
            </form>
        </div>
    @endauth

</header>
