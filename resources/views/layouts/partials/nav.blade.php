{{--
    Navbar principal do KidTask.
    Incluído automaticamente pelo layouts/app.blade.php.
    Adapta os links conforme o perfil do usuário logado (pai ou filho).
    Menu hambúrguer para mobile via Alpine.js.
--}}
<header x-data="{ open: false }" class="bg-white border-b border-gray-200">

    <div class="px-4 sm:px-6 py-3 flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ auth()->check() && auth()->user()->isParent() ? route('parent.dashboard') : route('child.dashboard') }}"
           class="text-xl font-bold text-indigo-600">
            KidTask
        </a>

        @auth
            {{-- Nav desktop --}}
            <nav class="hidden sm:flex items-center gap-6 text-sm">

                @if(auth()->user()->isParent())
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
                        @php
                            $pendingRewardsCount = \App\Models\PointTransaction::where('type', 'redeemed')
                                ->whereNull('delivered_at')
                                ->whereHas('user', fn($q) => $q->where('family_id', auth()->user()->family_id))
                                ->count();
                        @endphp
                        @if($pendingRewardsCount > 0)
                            <span class="ml-1 bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5">
                                {{ $pendingRewardsCount }}
                            </span>
                        @endif
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

            {{-- Info + logout desktop --}}
            <div class="hidden sm:flex items-center gap-4">
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition">
                        Sair
                    </button>
                </form>
            </div>

            {{-- Botão hambúrguer (mobile) --}}
            <button @click="open = !open"
                    class="sm:hidden p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 transition"
                    aria-label="Menu">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endauth

    </div>

    {{-- Menu mobile expandido --}}
    @auth
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="sm:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">

        @if(auth()->user()->isParent())
            <a href="{{ route('parent.dashboard') }}" @click="open = false"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('parent.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Painel
            </a>

            <a href="{{ route('parent.tasks.index') }}" @click="open = false"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('parent.tasks.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Tarefas
            </a>

            <a href="{{ route('parent.rewards.index') }}" @click="open = false"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('parent.rewards.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <span>Recompensas</span>
                @if(($pendingRewardsCount ?? 0) > 0)
                    <span class="bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $pendingRewardsCount }}</span>
                @endif
            </a>

            <a href="{{ route('parent.validations.index') }}" @click="open = false"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('parent.validations.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <span>Validações</span>
                @if(($pendingNavCount ?? 0) > 0)
                    <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $pendingNavCount }}</span>
                @endif
            </a>

        @else
            <a href="{{ route('child.dashboard') }}" @click="open = false"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('child.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Meu Dia
            </a>

            <a href="{{ route('child.points') }}" @click="open = false"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('child.points') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Meus Pontos
            </a>

            <a href="{{ route('child.rewards') }}" @click="open = false"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('child.rewards') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Recompensas
            </a>
        @endif

        {{-- Divisor + nome + sair --}}
        <div class="pt-3 mt-1 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500 px-3">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="text-sm text-red-500 hover:text-red-700 font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition">
                    Sair
                </button>
            </form>
        </div>

    </div>
    @endauth

</header>
