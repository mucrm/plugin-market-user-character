<div class="fixed top-0 bg-white p-2 left-0 w-full flex-items justify-center gap-8 z-[99]">
    <nav class="flex items-center justify-between max-w-7xl w-full mx-auto gap-8">
        <img src="{{ asset('images/icon.png') }}" class="h-12" />
        <ul class="flex items-center gap-8">
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('home') }}"
                    >{{ __lang('web.nav.home') }}</a
                >
            </li>
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('register') }}"
                    >{{ __lang('web.nav.register') }}</a
                >
            </li>
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('download') }}"
                    >{{ __lang('web.nav.download') }}</a
                >
            </li>
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('info') }}"
                    >{{ __lang('web.nav.info') }}</a
                >
            </li>
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('rankings') }}"
                    >{{ __lang('web.nav.rankings') }}</a
                >
            </li>
            @if(config('app.plugins.shopping'))
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('shoppings') }}"
                    >{{ __lang('web.nav.shoppings') }}</a
                >
            </li>
            @endif
            @if(config('app.plugins.shop_guardian'))
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('guardian.packages.index') }}"
                    >{{ __lang('web.nav.promo_packages') }}</a
                >
            </li>
            @endif
            @if(config('app.plugins.market_character.active'))
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('plugins.market.character') }}"
                    >{{ __lang('plugin.market.character.index') }}</a
                >
            </li>
            @endif
            <li>
                <a
                    class="text-xs font-semibold text-zinc-600 hover:text-zinc-400 uppercase tracking-widest transition"
                    href="{{ route('donation') }}"
                    >{{ __lang('web.nav.donation') }}</a
                >
            </li>
        </ul>
         <div>
            <ul class="flex gap-3">
                @foreach(config('system.languages') as $language => $data)
                <li>
                    <a href="?lang={{ $language }}" class="relative group inline-block">
                        <img src="{{ asset("images/flags/{$data['icon']}.png") }}"
                            class="w-7 h-5 rounded object-cover transition-transform duration-200 group-hover:scale-105" alt="{{ $data['name'] }}" />

                        <span class="absolute hidden group-hover:block top-7 left-1/2 -translate-x-1/2 w-2 h-2 bg-zinc-950 rotate-45 z-20"></span>

                        <span class="absolute hidden group-hover:block top-8 left-1/2 -translate-x-1/2 rounded-md px-3 py-1.5 bg-zinc-950 text-xs text-white font-medium whitespace-nowrap shadow-xl z-20">
                            {{ $data['name'] }}
                        </span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</div>
