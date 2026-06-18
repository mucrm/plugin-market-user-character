<div class="bg-zinc-950 text-zinc-400">
    @view('components.web.title', ['title'=> __lang('plugin.market.character.index')])

    <div class="px-4 mt-4">@view('components.web.alert-message')</div>

    <form method="GET" class="px-4 my-6 grid grid-cols-1 sm:grid-cols-12 gap-4">
        <div class="relative sm:col-span-7">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="{{ __lang('web.shop.search_item') }}"
                class="w-full py-2.5 pl-10 pr-4 text-sm bg-zinc-900/30 border border-zinc-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-zinc-500/50 transition-colors text-zinc-100" />
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500"></i>
        </div>
        <div class="sm:col-span-3">
            <select name="order" class="w-full py-2.5 px-3 text-sm bg-zinc-900/30 border border-zinc-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-zinc-500/50 transition-colors text-zinc-300">
                <option class="bg-zinc-950" value="price_asc" {{ request('order') == 'price_asc' ? 'selected' : ''}}>{{ __lang('web.shop.price_asc') }}</option>
                <option class="bg-zinc-950" value="price_desc" {{ request('order') == 'price_desc' ? 'selected' : ''}}>{{ __lang('web.shop.price_desc') }}</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            @view('components.web.button-submit', ['title' => __lang('web.shop.search_btn')])
        </div>
    </form>

    <div class="p-4 grid grid-cols-1 lg:grid-cols-12 gap-6">
        @forelse($characters as $character)
            <div x-data="{ open: false }" class="lg:col-span-12 grid grid-cols-1 lg:grid-cols-12 gap-6 border-b border-zinc-900 pb-6 last:border-0">
                
                <div class="lg:col-span-4 space-y-4">
                    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 flex flex-col items-center">
                        <div class="relative group">
                            <div class="absolute inset-0 bg-zinc-100/5 blur-2xl rounded-full"></div>
                            <img
                                src="{{ avatar($character->character->Avatar) }}"
                                alt="{{ $character->character->Name }}"
                                class="relative w-40 h-40 object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.8)]"
                            />
                        </div>

                        <div class="text-center mt-4 w-full">
                            <h2 class="text-zinc-100 font-bold text-xl tracking-tight">{{ $character->character->Name }}</h2>
                            <span
                                class="text-xs uppercase font-black tracking-widest text-zinc-500 bg-zinc-950 px-3 py-1 rounded-full border border-zinc-800 mt-2 inline-block"
                            >
                                {{ config('character.classes')[$character->character->Class]['name'] }}
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
                    
                    <div class="space-y-6">
                        @view('components.web.sub_title', ['title'=> __lang('plugin.market.character.base_attributes')])

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.strength') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->character->Strength }}</p>
                            </div>
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.agility') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->character->Dexterity }}</p>
                            </div>
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.vitality') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->character->Vitality }}</p>
                            </div>
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.energy') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->character->Energy }}</p>
                            </div>
                            @if($character->character->Class == 64 || $character->character->Class == 65)
                            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md text-center border-t-zinc-600">
                                <p class="text-[10px] uppercase font-black text-zinc-500">{{ __lang('plugin.market.character.command') }}</p>
                                <p class="text-lg font-bold text-zinc-100">{{ $character->character->Leadership }}</p>
                            </div>
                            @endif
                        </div>

                        <div x-show="open" x-collapse  class="space-y-3">
                            @view('components.web.sub_title', ['title'=> __lang('plugin.market.character.general_stats')])

                        <div  class="bg-zinc-900/30 border border-zinc-900 rounded-lg overflow-hidden divide-y divide-zinc-800">
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.total_resets') }}</span>
                                <span class="text-zinc-100 font-bold bg-zinc-800 px-3 py-1 rounded text-xs">
                                    {{ $character->character->{config('user.character.columns_profile.resets')} }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.master_resets') }}</span>
                                <span class="text-amber-500 font-bold bg-amber-500/10 px-3 py-1 rounded text-xs border border-amber-500/20">
                                    {{ $character->character->{config('user.character.columns_profile.mresets')} }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.pk_points') }}</span>
                                <span class="text-rose-500 font-bold">
                                    {{ $character->character->{config('user.character.columns_profile.pk')} }} kills
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-sm font-medium">{{ __lang('plugin.market.character.hero_status') }}</span>
                                <span class="text-sky-500 font-bold uppercase text-[10px] tracking-widest">
                                    {{ $character->character->{config('user.character.columns_profile.hero')} }}
                                </span>
                            </div>
                        </div></div>
                    </div>

                    @view('components.web.sub_title', ['title'=> __lang('plugin.market.character.sale_settings')])

                    <form action="{{ route('plugins.market.character.buy', ['id' => $character->id]) }}" method="POST" class="bg-zinc-900 border border-zinc-800 rounded-lg p-5 space-y-4">
                        @csrf
                        
                        <div class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="flex-1 w-full">
                                <label class="block text-xs uppercase font-black text-zinc-500 mb-2">
                                    {{ __lang('plugin.market.character.price') }}
                                </label>
                                <div class="relative">
                                    <p 
                                        class="bg-zinc-950 border border-zinc-800 rounded-md px-4 py-2.5 text-zinc-100 text-sm"
                                    >{{ number_format($character->price, 0, ',', '.') }} {{ $coin['name']}}
                                </p>
                                </div>
                            </div>
                            @if(auth_check())
                            <button 
                                type="submit" 
                                class="w-full sm:w-auto bg-zinc-100 hover:bg-zinc-200 text-zinc-950 font-bold text-xs uppercase tracking-widest px-6 py-3 rounded-md transition-all whitespace-nowrap"
                            >
                                {{ __lang('plugin.market.character.buy_btn') }}
                            </button>
                            @else
                                <p
                                    class="group/tool relative flex items-center justify-center w-32 py-2.5 gap-2 text-xs font-bold uppercase tracking-widest text-zinc-600 bg-zinc-800 rounded-xl cursor-not-allowed">
                                    <i data-lucide="lock" class="w-4 h-4"></i>

                                    <span
                                        class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 pointer-events-none hidden group-hover/tool:block bg-red-950 text-[10px] text-red-300 px-3 py-1.5 rounded-lg border border-red-800 shadow-2xl whitespace-nowrap z-50">
                                        {{ __lang('web.shop.must_be_logged') }}
                                    </span>

                                    <span
                                        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 pointer-events-none hidden group-hover/tool:block border-8 border-transparent border-t-zinc-800 z-50"></span>
                                </p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="lg:col-span-12 text-center py-12 bg-zinc-900 border border-zinc-800 rounded-lg">
                <p class="text-zinc-500 text-sm font-medium">{{ __lang('plugin.market.character.no_characters') }}</p>
            </div>
        @endforelse
    </div>

    <div class="p-4 flex justify-end border-t border-zinc-900 mt-4">
        {!! $characters->links() !!}
    </div>
</div>