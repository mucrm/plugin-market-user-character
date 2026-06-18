<div class="bg-zinc-950 text-zinc-400">
    @view('components.web.title', ['title'=> __lang('plugin.market.character.sell_character')])

    <div class="flex items-center gap-2 justify-center my-6">
        <a
            href="{{ route('user.panel') }}"
            class="flex items-center gap-2 px-4 py-2 rounded-md border border-zinc-800 text-xs font-bold uppercase tracking-widest text-zinc-500 hover:text-zinc-100 hover:bg-zinc-900 transition-all"
        >
            <i data-lucide="arrow-left" class="w-4 h-4"></i> {{ __lang('plugin.market.character.back') }}
        </a>
         <a href="{{ route('plugins.market.character.ads') }}"
        class="flex items-center gap-2 px-4 py-2 rounded-md border border-zinc-800 text-xs font-bold uppercase tracking-widest text-zinc-500 hover:text-zinc-100 hover:bg-zinc-900 transition-all"
    >
         {{ __lang('plugin.market.character.my_ads') }}<i data-lucide="arrow-right" class="w-4 h-4"></i>
    </a>
    </div>

    <div class="px-4 mb-1">@view('components.web.alert-message')</div>



    <div class="p-4 grid grid-cols-1 lg:grid-cols-12 gap-6">
        @forelse($characters as $character)
            <div x-data="{ open: false }" class="lg:col-span-12 grid grid-cols-1 lg:grid-cols-12 gap-6 border-b border-zinc-900 pb-6 last:border-0">
                
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

                        <div class="text-center mt-4 w-full">
                            <h2 class="text-zinc-100 font-bold text-xl tracking-tight">{{ $character->Name }}</h2>
                            <span
                                class="text-xs uppercase font-black tracking-widest text-zinc-500 bg-zinc-950 px-3 py-1 rounded-full border border-zinc-800 mt-2 inline-block"
                            >
                                {{ config('character.classes')[$character->Class]['name'] }}
                            </span>

                            <button 
                                @click="open = !open"
                                type="button"
                                class="mt-4 w-full block text-center text-[10px] uppercase font-black tracking-wider text-zinc-500 hover:text-zinc-300 border border-zinc-800 bg-zinc-950/50 py-2 rounded transition-all"
                            >
                                <span x-show="!open">{{ __lang('plugin.market.character.expand') }}</span>
                                <span x-show="open">{{ __lang('plugin.market.character.hide') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8 space-y-6">
                    
                    <div class="space-y-6 mb-6">
                        @view('components.web.sub_title', ['title'=> __lang('plugin.market.character.base_attributes')])

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.strength') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->Strength }}</p>
                            </div>
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.agility') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->Dexterity }}</p>
                            </div>
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.vitality') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->Vitality }}</p>
                            </div>
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.energy') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->Energy }}</p>
                            </div>
                            @if($character->class == 64 || $character->class == 65)
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center border-t-zinc-600">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.command') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->Leadership }}</p>
                            </div>
                            @endif
                        </div>

                        <div  x-show="open" x-collapse>
                        @view('components.web.sub_title', ['title'=> __lang('plugin.market.character.general_stats')])
                        

                        <div class="bg-zinc-900/30 border border-zinc-900 rounded-lg overflow-hidden divide-y divide-zinc-800">
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.total_resets') }}</span>
                                <span class="text-zinc-100 font-bold bg-zinc-800 px-3 py-1 rounded text-xs">
                                    {{ $character->{config('user.character.columns_profile.resets')} }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.master_resets') }}</span>
                                <span
                                    class="text-amber-500 font-bold bg-amber-500/10 px-3 py-1 rounded text-xs border border-amber-500/20"
                                >
                                    {{ $character->{config('user.character.columns_profile.mresets')} }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.pk_points') }}</span>
                                <span class="text-rose-500 font-bold">
                                    {{ $character->{config('user.character.columns_profile.pk')} }} kills
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.hero_status') }}</span>
                                <span class="text-sky-500 font-bold uppercase text-[10px] tracking-widest">
                                    {{ $character->{config('user.character.columns_profile.hero')} }}
                                </span>
                            </div>
                        </div>
                        </div>
                    </div>

                    @view('components.web.sub_title', ['title'=> __lang('plugin.market.character.sale_settings')])

                    <form action="#" method="POST" class="bg-zinc-900 border border-zinc-800 rounded-lg p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="character" value="{{ $character->Name }}">
                        
                        <div class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="flex-1 w-full">
                                <label for="price" class="block text-xs uppercase font-black text-zinc-500 mb-2">
                                    {{ __lang('plugin.market.character.price') }}
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="price" 
                                        id="price" 
                                        min="1" 
                                        step="1"
                                        placeholder="0"
                                        value=""
                                        class="w-full bg-zinc-950 border border-zinc-800 rounded-md px-4 py-2.5 text-zinc-100 text-sm focus:outline-none focus:border-zinc-600 transition-all"
                                        required
                                    >
                                </div>
                            </div>
                            <button 
                                type="submit" 
                                class="w-full sm:w-auto bg-zinc-100 hover:bg-zinc-200 text-zinc-950 font-bold text-xs uppercase tracking-widest px-6 py-3 rounded-md transition-all whitespace-nowrap"
                            >
                                {{ __lang('plugin.market.character.sell') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            {{-- Vazio --}}
        @endforelse
    </div>
</div>