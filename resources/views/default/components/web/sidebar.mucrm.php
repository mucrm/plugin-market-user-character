<div class="w-[310px] space-y-6">
    <div class="overflow-hidden border border-zinc-800 bg-zinc-950 shadow-2xl">
        <div class="bg-zinc-900/50 border-b border-zinc-800 p-4">
            <h3 class="text-zinc-100 font-semibold text-sm uppercase flex items-center gap-2">
                <i data-lucide="lock" class="w-4 h-4 text-zinc-500"></i>
                {{ __lang('web.sidebar.control_panel') }}
            </h3>
        </div>
        @auth
        <div>
            <div class="p-5 space-y-3 border-b border-white/10">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.name') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ auth()->memb_name }}</span>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.plan') }}</span>
                    <span
                        class="px-2 py-0.5 bg-amber-500/10 text-amber-500 rounded text-xs font-semibold ring-1 ring-amber-500/20">
                        {{ ucfirst(config('app.vip.types')[auth()->accountPlan()]) }}
                    </span>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.expires_at') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ auth()->AccountExpireDate->toDateBr() }}</span>
                </div>

                <div class="pt-2 mt-2 border-t border-white/5 space-y-2">
                    @foreach(auth()->getCoins() as $coin)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-400">{{ $coin['name'] }}:</span>
                        <span class="text-zinc-400 font-semibold">{{ $coin['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <nav class="p-2">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('user.panel') }}"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg transition-all group">
                            <i data-lucide="user" class="w-4 h-4 text-zinc-500 group-hover:text-zinc-400"></i>
                            <span class="font-semibold">{{ __lang('web.sidebar.my_account') }}</span>
                        </a>
                    </li>
                    @if(config('user.exchange.active'))
                        <li>
                            <a href="{{ route('plugins.coin.index') }}"
                                class="flex items-center gap-3 px-3 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg transition-all group">
                                <i data-lucide="coins" class="w-4 h-4 text-zinc-500 group-hover:text-zinc-400"></i>
                                <span class="font-semibold">{{ __lang('user.exchange.title') }}</span>
                            </a>
                        </li>
                    @endif
                    @if(config('app.plugins.market_character.active'))
                        <li>
                            <a href="{{ route('plugins.market.character.index') }}"
                                class="flex items-center gap-3 px-3 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg transition-all group">
                                <i data-lucide="store" class="w-4 h-4 text-zinc-500 group-hover:text-zinc-400"></i>
                                <span class="font-semibold">{{ __lang('plugin.market.character.sell_character') }}</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('user.logout') }}"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-red-400 hover:bg-red-400/10 rounded-lg transition-all group">
                            <i data-lucide="log-out" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                            <span class="font-semibold">{{ __lang('web.sidebar.logout') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        @else
        <div class="p-5">
            <form action="{{ route('user.auth') }}" method="POST" class="space-y-4">
                @csrf @view('components.web.alert-message')

                <div class="space-y-1.5">
                    <label for="username" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 ml-1">
                        {{ __lang('web.sidebar.username') }}
                    </label>
                    <div class="relative">
                        <i data-lucide="user"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600"></i>
                        <input type="text" name="username" id="username" placeholder="{{ __lang('web.sidebar.login_placeholder') }}"
                            autocomplete="off"
                            class="w-full pl-10 pr-4 py-2.5 rounded bg-zinc-900 border border-zinc-800 text-zinc-100 text-sm outline-none transition-all focus:border-zinc-600 focus:bg-zinc-900/80 placeholder:text-zinc-700" />
                    </div>
                    @view('components.web.input-error', ['name' => 'username'])
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 ml-1">
                        {{ __lang('web.sidebar.password') }}
                    </label>
                    <div class="relative">
                        <i data-lucide="key-round"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600"></i>
                        <input type="password" name="upassword" id="password" placeholder="********" autocomplete="off"
                            class="w-full pl-10 pr-4 py-2.5 rounded bg-zinc-900 border border-zinc-800 text-zinc-100 text-sm outline-none transition-all focus:border-zinc-600 focus:bg-zinc-900/80 placeholder:text-zinc-700" />
                    </div>
                    @view('components.web.input-error', ['name' => 'upassword'])
                </div>

                <div class="pt-2">@view('components.web.button-submit', ['title'=> __lang('web.sidebar.login_button')])</div>

                <div class="flex justify-between items-center mt-4 pt-4 border-t border-zinc-900">
                    <a href="{{route('register')}}"
                        class="text-[10px] text-zinc-600 hover:text-zinc-300 transition-colors uppercase font-bold">{{ __lang('web.sidebar.register_link') }}</a>
                    <a href="{{route('recover.password')}}"
                        class="text-[10px] text-zinc-600 hover:text-zinc-300 transition-colors uppercase font-bold">{{ __lang('web.sidebar.forgot_password') }}</a>
                </div>
            </form>
        </div>
        @endauth
    </div>

    <div class="overflow-hidden border border-zinc-800 bg-zinc-950 shadow-2xl">
        <div class="bg-zinc-900/50 border-b border-zinc-800 p-4">
            <h3 class="text-zinc-100 font-semibold text-sm uppercase flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-zinc-500"></i>
                {{ __lang('web.sidebar.server_info') }}
            </h3>

            <div class="py-5 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.name') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ config('app.server.name') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.version') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ config('app.server.version') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.experience') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ config('app.server.experience') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.drop') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ config('app.server.bug_bless') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.max_level') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ config('app.server.max_level') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{ __lang('web.sidebar.max_points') }}</span>
                    <span class="text-zinc-100 font-semibold">{{ config('app.server.max_points') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden border border-zinc-800 bg-zinc-950 shadow-2xl">
        <div class="bg-zinc-900/50 border-b border-zinc-800 p-4">
            <h3 class="text-zinc-100 font-semibold text-sm uppercase flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-zinc-500"></i>
                {{ __lang('web.sidebar.server_team') }}
            </h3>

            <div class="py-5 space-y-3">
                @forelse(getStaffs() as $staff)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-400">{{$staff['name']}}:</span>
                    <span class="font-semibold {{ $staff['status'] == 'offline'? 'text-red-500':'text-green-500' }}">{{
                        ucfirst($staff['status']) }}</span>
                </div>
                @empty
                <div class="bg-yellow-500/10 border border-yellow-800 rounded-xl p-2">
                    <p class="text-center text-yellow-100 text-sm font-semibold flex items-center justify-center gap-2">
                        {{ __lang('web.sidebar.no_members') }}
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>