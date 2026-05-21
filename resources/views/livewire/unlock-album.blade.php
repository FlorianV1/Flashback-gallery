<div class="flex items-center justify-center py-16 min-h-[60vh]">
    <div class="w-full max-w-sm">
        <div class="rounded-sm shadow-xl overflow-hidden" style="background: #ffffff;">

            {{-- Top accent --}}
            <div class="h-0.5 w-full" style="background: linear-gradient(90deg, #C84B00, #E85D04, #FF8C38);"></div>

            <div class="p-8 pt-9">
                {{-- Lock icon --}}
                <div class="flex justify-center mb-6">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background: #FEF0E7; border: 1.5px solid rgba(232,93,4,0.2);">
                        <svg class="w-6 h-6" style="color: #E85D04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>

                <h1 style="font-family: 'DM Serif Display', serif; font-size: 1.75rem; color: #2C1810;" class="text-center mb-1">
                    Private Album
                </h1>
                <p class="text-sm text-center mb-0.5 font-medium" style="color: #5C3010;">{{ $album->title }}</p>
                <p class="text-xs text-center mb-7" style="color: #9B8570;">{{ $album->date_of_outing->format('F j, Y') }}</p>

                <form wire:submit="unlock" class="text-left space-y-4">
                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: #8B7355; letter-spacing: 0.1em;">
                            Access Code
                        </label>
                        <input
                            wire:model="code"
                            type="text"
                            maxlength="8"
                            placeholder="— — — — — —"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="characters"
                            spellcheck="false"
                            oninput="this.value = this.value.toUpperCase()"
                            class="w-full text-center font-mono text-2xl uppercase py-3 px-4 rounded-sm border-2 outline-none"
                            style="
                                background: #FAF7F2;
                                color: #2C1810;
                                border-color: {{ $error ? '#ef4444' : '#DDD5C5' }};
                                letter-spacing: 0.45em;
                                transition: border-color 0.15s ease;
                            "
                            onfocus="this.style.borderColor='#E85D04'"
                            onblur="this.style.borderColor='{{ $error ? '#ef4444' : '#DDD5C5' }}'"
                        >
                        @if($error)
                            <p class="mt-2 text-sm text-center" style="color: #ef4444;">
                                Incorrect code — please try again.
                            </p>
                        @endif
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full font-semibold py-3 rounded-sm disabled:opacity-60"
                        style="background: #E85D04; color: #ffffff; transition: background 0.15s ease;"
                        onmouseenter="if(!this.disabled) this.style.background='#C84B00'"
                        onmouseleave="this.style.background='#E85D04'"
                    >
                        <span wire:loading.remove>Unlock Album</span>
                        <span wire:loading>Checking&hellip;</span>
                    </button>
                </form>

                <div class="mt-6 pt-5 text-center" style="border-top: 1px solid #EDE8DF;">
                    <a href="{{ route('gallery') }}"
                       class="text-sm transition-colors"
                       style="color: #9B8570;"
                       onmouseenter="this.style.color='#2C1810'"
                       onmouseleave="this.style.color='#9B8570'">
                        &larr; Back to Gallery
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
