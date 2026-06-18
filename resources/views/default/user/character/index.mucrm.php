<div class="bg-zinc-950 text-zinc-400">
    @view('components.web.title', ['title'=> __lang('user.character.manage')])

    <div class="flex justify-center my-6">
        <a
            href="{{ route('user.panel') }}"
            class="flex items-center gap-2 px-4 py-2 rounded-md border border-zinc-800 text-xs font-bold uppercase tracking-widest text-zinc-500 hover:text-zinc-100 hover:bg-zinc-900 transition-all"
        >
            <i data-lucide="arrow-left" class="w-4 h-4"></i> {{ __lang('user.character.back') }}
        </a>
    </div>

    <div class="px-4 mb-1">@view('components.web.alert-message')</div>

    <div class="p-4 grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 flex flex-col items-center">
                <div class="relative group">
                    <div class="absolute inset-0 bg-zinc-100/5 blur-2xl rounded-full"></div>
                    <img
                        src="{{ avatar($character->Avatar) }}"
                        alt="{{ $character->Name }}"
                        class="relative w-40 h-40 object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.8)]"
                    />
                </div>

                <div class="text-center mt-4">
                    <h2 class="text-zinc-100 font-bold text-xl tracking-tight">{{ $character->Name }}</h2>
                    <span
                        class="text-xs uppercase font-black tracking-widest text-zinc-500 bg-zinc-950 px-3 py-1 rounded-full border border-zinc-800 mt-2 inline-block"
                    >
                        {{ config('character.classes')[$character->Class]['name'] }}
                    </span>
                </div>
            </div>

            <div class="bg-zinc-900/50 border border-zinc-800 p-4 rounded-lg flex items-center gap-3">
                <i data-lucide="map-pin" class="w-5 h-5 text-zinc-500"></i>
                <div>
                    <p class="text-[10px] uppercase font-bold text-zinc-600 leading-none">{{ __lang('user.character.current_location') }}</p>
                    <p class="text-sm text-zinc-300 font-medium mt-1">
                        {{ config('map')[$character->MapNumber]['name'] }}
                        <span class="text-zinc-500 text-xs">[{{ $character->MapPosX }}x{{ $character->MapPosY }}]</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-6">
            @view('components.web.sub_title', ['title'=> __lang('user.character.base_attributes')])

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                    <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('user.character.strength') }}</p>
                    <p class="text-lg font-bold text-zinc-100">{{ $character->Strength }}</p>
                </div>
                <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                    <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('user.character.agility') }}</p>
                    <p class="text-lg font-bold text-zinc-100">{{ $character->Dexterity }}</p>
                </div>
                <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                    <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('user.character.vitality') }}</p>
                    <p class="text-lg font-bold text-zinc-100">{{ $character->Vitality }}</p>
                </div>
                <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                    <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('user.character.energy') }}</p>
                    <p class="text-lg font-bold text-zinc-100">{{ $character->Energy }}</p>
                </div>
                @if($character->class == 64 || $character->class == 65)
                <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center border-t-zinc-600">
                    <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('user.character.command') }}</p>
                    <p class="text-lg font-bold text-zinc-100">{{ $character->Leadership }}</p>
                </div>
                @endif
            </div>

            @view('components.web.sub_title', ['title'=> __lang('user.character.general_stats')])

            <div class="bg-zinc-900/30 border border-zinc-900 rounded-lg overflow-hidden divide-y divide-zinc-800">
                <div class="flex justify-between items-center p-3">
                    <span class="text-sm font-medium">{{ __lang('user.character.total_resets') }}</span>
                    <span class="text-zinc-100 font-bold bg-zinc-800 px-3 py-1 rounded text-xs">
                        {{ $character->{config('user.character.columns_profile.resets')} }}
                    </span>
                </div>
                <div class="flex justify-between items-center p-3">
                    <span class="text-sm font-medium">{{ __lang('user.character.master_resets') }}</span>
                    <span
                        class="text-amber-500 font-bold bg-amber-500/10 px-3 py-1 rounded text-xs border border-amber-500/20"
                    >
                        {{ $character->{config('user.character.columns_profile.mresets')} }}
                    </span>
                </div>
                <div class="flex justify-between items-center p-3">
                    <span class="text-sm font-medium">{{ __lang('user.character.pk_points') }}</span>
                    <span class="text-rose-500 font-bold">
                        {{ $character->{config('user.character.columns_profile.pk')} }} kills
                    </span>
                </div>
                <div class="flex justify-between items-center p-3">
                    <span class="text-sm font-medium">{{ __lang('user.character.hero_status') }}</span>
                    <span class="text-sky-500 font-bold uppercase text-[10px] tracking-widest">
                        {{ $character->{config('user.character.columns_profile.hero')} }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 p-4">
        @if(config('user.character.previlegy.avatar.active'))
        <a
            href="{{ route('user.character.profile.avatar', ['name'=> $character->Name]) }}"
            class="px-2.5 py-2 flex items-center justify-center gap-2 rounded border border-zinc-700 text-sm text-center block bg-zinc-800 hover:bg-zinc-700"
        >
            <i data-lucide="image-up" class="w-4 h-4"></i> {{ __lang('user.character.avatar') }}</a
        >
        @endif 
        @if(config('user.character.previlegy.nickname.active'))
        <a
            href="{{ route('user.character.profile.nickname', ['name'=> $character->Name]) }}"
            class="px-2.5 py-2 flex items-center justify-center gap-2 rounded border border-zinc-700 text-sm text-center block bg-zinc-800 hover:bg-zinc-700"
        >
            <i data-lucide="signature" class="w-4 h-4"></i> {{ __lang('user.character.nickname') }}</a
        >
        @endif 
        @if(config('user.character.previlegy.classe.active'))
        <a
            href="{{ route('user.character.profile.classe', ['name'=> $character->Name]) }}"
            class="px-2.5 py-2 flex items-center justify-center gap-2 rounded border border-zinc-700 text-sm text-center block bg-zinc-800 hover:bg-zinc-700"
        >
            <i data-lucide="user-star" class="w-4 h-4"></i> {{ __lang('user.character.class') }}</a
        >
        @endif 
        @if(config('user.character.previlegy.move.active'))
        <a
            href="{{ route('user.character.profile.move', ['name'=> $character->Name]) }}"
            class="px-2.5 py-2 flex items-center justify-center gap-2 rounded border border-zinc-700 text-sm text-center block bg-zinc-800 hover:bg-zinc-700"
        >
            <i data-lucide="arrow-right-left" class="w-4 h-4"></i> {{ __lang('user.character.move') }}</a
        >
        @endif
    </div>
</div>
