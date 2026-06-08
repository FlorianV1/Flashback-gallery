<div>
    {{-- Hero --}}
    <div class="text-center pt-10 pb-14 mb-8">
        <p class="text-xs font-medium uppercase tracking-widest mb-4" style="color: #A89880; letter-spacing: 0.22em;">
            Flashback ONE35
        </p>
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2.8rem, 7vw, 5rem); color: #2C1810; line-height: 1.0; letter-spacing: -0.01em;" class="mb-6">
            Florian's Cameraatje
        </h1>
        <div class="flex items-center justify-center gap-3 mb-5" style="max-width: 18rem; margin-left: auto; margin-right: auto;">
            <div class="flex-1 h-px" style="background: linear-gradient(to right, transparent, #CEC6B5);"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full" style="background: #C8BFB0;"></div>
                <div class="w-2 h-2 rounded-full" style="background: #E85D04; opacity: 0.65;"></div>
                <div class="w-1.5 h-1.5 rounded-full" style="background: #C8BFB0;"></div>
            </div>
            <div class="flex-1 h-px" style="background: linear-gradient(to left, transparent, #CEC6B5);"></div>
        </div>
        <p style="color: #8B7355; max-width: 30rem; margin: 0 auto; font-size: 1.0rem;" class="leading-relaxed">
            Gave momenten vastgelegd op de camera
        </p>
    </div>

    @if($albums->isEmpty())
        <div class="text-center py-24" style="color: #8B7355;">
            <svg class="w-16 h-16 mx-auto mb-5 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-family: 'DM Serif Display', serif; font-size: 1.75rem; color: #2C1810;" class="mb-2">No albums yet.</p>
            <p class="text-sm">Check back after the next outing.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8 md:gap-10">
            @foreach($albums as $album)
                @php
                    $unlocked = $album->isUnlocked();
                    $isLocked = !$album->is_public && !$unlocked;
                    $rotations  = ['-2deg', '-1deg', '0.5deg', '1.5deg', '-0.5deg'];
                    $rotate     = $rotations[$album->id % 5];
                    $tapeLeft   = ['24%','38%','18%','44%','30%'][$album->id % 5];
                    $tapeAngle  = ['-3deg','2deg','-1.5deg','3.5deg','-2.5deg'][$album->id % 5];
                @endphp

                {{-- Outer wrapper --}}
                @if($isLocked)
                    <div wire:click="openUnlock({{ $album->id }})"
                         class="relative group cursor-pointer"
                         style="transform: rotate({{ $rotate }}); transition: transform 0.35s ease;">
                @else
                    <a href="{{ route('albums.show', $album) }}"
                       class="relative block group"
                       style="transform: rotate({{ $rotate }}); transition: transform 0.35s ease;">
                @endif

                    {{-- Masking tape strip --}}
                    <div class="absolute z-10 rounded-sm pointer-events-none"
                         style="
                             top: -10px;
                             left: {{ $tapeLeft }};
                             width: 42px;
                             height: 14px;
                             background: linear-gradient(135deg, rgba(255,248,205,0.88) 0%, rgba(240,228,170,0.80) 100%);
                             transform: rotate({{ $tapeAngle }});
                             box-shadow: 0 1px 3px rgba(0,0,0,0.10), inset 0 0 0 0.5px rgba(0,0,0,0.06);
                         "
                         aria-hidden="true"></div>

                    {{-- Card --}}
                    <div class="overflow-hidden"
                         style="
                             background: #FEFCF9;
                             padding: 10px 10px 50px;
                             border-radius: 1px;
                             box-shadow:
                                 0 1px 1px rgba(44,24,16,0.04),
                                 0 2px 4px rgba(44,24,16,0.07),
                                 0 6px 14px rgba(44,24,16,0.11),
                                 0 12px 28px rgba(44,24,16,0.07);
                             transition: transform 0.35s ease, box-shadow 0.35s ease;
                             {{ $isLocked ? 'border: 1.5px solid rgba(232,93,4,0.18);' : '' }}
                         "
                         onmouseenter="this.style.transform='rotate(0deg) translateY(-8px)'; this.style.boxShadow='0 2px 4px rgba(44,24,16,0.06), 0 8px 20px rgba(44,24,16,0.12), 0 24px 48px rgba(44,24,16,0.16)'"
                         onmouseleave="this.style.transform=''; this.style.boxShadow='0 1px 1px rgba(44,24,16,0.04), 0 2px 4px rgba(44,24,16,0.07), 0 6px 14px rgba(44,24,16,0.11), 0 12px 28px rgba(44,24,16,0.07)'"
                    >

                        {{-- Photo area --}}
                        <div class="relative aspect-square overflow-hidden" style="background: #EDE8DF;">

                            @if($album->coverPhoto)
                                <div class="skeleton-shimmer absolute inset-0 pointer-events-none"></div>
                                <img
                                    src="{{ $album->coverPhoto->getThumbnailUrl() }}"
                                    alt="{{ $album->title }}"
                                    class="w-full h-full object-cover block relative"
                                    style="opacity: 0; transition: opacity 0.3s ease, transform 0.4s ease; {{ $isLocked ? 'filter: brightness(0.6) saturate(0.5);' : '' }}"
                                    onload="this.style.opacity='1'; this.previousElementSibling.style.display='none';"
                                    onmouseenter="this.style.transform='scale(1.04)'"
                                    onmouseleave="this.style.transform='scale(1)'"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-10 h-10 opacity-20" fill="none" stroke="#2C1810" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Lock overlay for private albums --}}
                            @if($isLocked)
                                <div class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(232,93,4,0.15); border: 1px solid rgba(232,93,4,0.3);">
                                        <svg class="w-5 h-5" style="color: #E85D04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                {{-- Frame counter badge --}}
                                <div class="absolute bottom-1.5 right-1.5 text-xs font-mono leading-none px-1.5 py-0.5 rounded-sm"
                                     style="background: rgba(20,10,5,0.5); color: rgba(255,255,255,0.85); font-size: 0.6rem; letter-spacing: 0.05em;">
                                    {{ str_pad($album->photos_count, 2, '0', STR_PAD_LEFT) }}
                                </div>
                            @endif
                        </div>

                        {{-- Caption --}}
                        <div class="mt-3 px-0.5">
                            <p style="font-family: 'DM Serif Display', serif; color: #2C1810; font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3;">
                                {{ $album->title }}
                            </p>
                            <p style="color: #9B8570; font-size: 0.68rem; margin-top: 3px; font-variant-numeric: tabular-nums;">
                                {{ $album->date_of_outing->format('M j, Y') }}
                            </p>
                            @if($isLocked)
                                <p class="mt-1 text-xs font-medium" style="color: #E85D04; font-size: 0.65rem; letter-spacing: 0.04em;">
                                    ENTER CODE TO VIEW
                                </p>
                            @elseif(! $album->is_public && $unlocked)
                                <p class="mt-1 text-xs" style="color: #E85D04; font-size: 0.65rem; letter-spacing: 0.04em;">
                                    UNLOCKED
                                </p>
                            @else
                                <p style="color: #B0A090; font-size: 0.65rem; margin-top: 3px;">
                                    {{ $album->photos_count }} {{ $album->photos_count === 1 ? 'photo' : 'photos' }}
                                </p>
                            @endif
                        </div>
                    </div>

                @if($isLocked)
                    </div>
                @else
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Inline unlock modal --}}
    @if($unlockAlbumId)
        @php $modalAlbum = $albums->firstWhere('id', $unlockAlbumId); @endphp

        <div class="fixed inset-0 flex items-center justify-center z-50 px-4"
             style="background: rgba(28,14,8,0.6); backdrop-filter: blur(4px);"
             wire:click.self="closeUnlock">

            <div class="w-full max-w-sm rounded-sm shadow-2xl overflow-hidden" style="background: #ffffff;">

                {{-- Modal header accent --}}
                <div class="h-0.5 w-full" style="background: linear-gradient(90deg, #C84B00, #E85D04, #FF8C38);"></div>

                <div class="p-8">
                    <div class="flex justify-center mb-5">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background: #FEF3EC; border: 1.5px solid rgba(232,93,4,0.2);">
                            <svg class="w-6 h-6" style="color: #E85D04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>

                    <h2 style="font-family: 'DM Serif Display', serif; font-size: 1.6rem; color: #2C1810;" class="text-center mb-1">
                        Private Album
                    </h2>

                    @if($modalAlbum)
                        <p class="text-sm text-center mb-0.5 font-medium" style="color: #5C3010;">{{ $modalAlbum->title }}</p>
                        <p class="text-xs text-center mb-7" style="color: #9B8570;">{{ $modalAlbum->date_of_outing->format('F j, Y') }}</p>
                    @else
                        <div class="mb-7"></div>
                    @endif

                    <form wire:submit="submitUnlock" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: #8B7355; letter-spacing: 0.1em;">
                                Access Code
                            </label>
                            <input
                                wire:model="unlockCode"
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
                                    border-color: {{ $unlockError ? '#ef4444' : '#DDD5C5' }};
                                    letter-spacing: 0.45em;
                                    transition: border-color 0.15s ease;
                                "
                                onfocus="this.style.borderColor='#E85D04'"
                                onblur="this.style.borderColor='{{ $unlockError ? '#ef4444' : '#DDD5C5' }}'"
                                autofocus
                            >
                            @if($unlockError)
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
                        <button wire:click="closeUnlock"
                                class="text-sm transition-colors"
                                style="color: #9B8570;"
                                onmouseenter="this.style.color='#2C1810'"
                                onmouseleave="this.style.color='#9B8570'">
                            &larr; Back to Gallery
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
