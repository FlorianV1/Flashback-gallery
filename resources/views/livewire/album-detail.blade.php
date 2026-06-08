<div x-data="{
    lb: false,
    idx: 0,
    lbLoading: false,
    showIOSTip: false,
    sticky: false,
    showLightboxHint: false,
    hintShown: false,
    isIOS: /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
    swipeStartX: 0,
    swipeStartY: 0,
    onSwipeStart(e) { this.swipeStartX = e.touches[0].clientX; this.swipeStartY = e.touches[0].clientY; },
    onSwipeEnd(e) {
        const dx = e.changedTouches[0].clientX - this.swipeStartX;
        const dy = e.changedTouches[0].clientY - this.swipeStartY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) { dx < 0 ? this.next() : this.prev(); }
    },
    photos: {{ Js::from($photos->getCollection()->map(fn($p) => [
        'url'  => $p->getWebUrl(),
        'dl'   => route('photos.download', ['album' => $album->id, 'photo' => $p->id]),
        'name' => $p->original_filename,
    ])->values()) }},
    open(i) {
        this.idx = i; this.lb = true; this.lbLoading = true;
        document.body.style.overflow = 'hidden';
        if (!this.hintShown) {
            this.hintShown = true; this.showLightboxHint = true;
            setTimeout(() => { this.showLightboxHint = false; }, 2500);
        }
    },
    close() { this.lb = false; this.showIOSTip = false; document.body.style.overflow = ''; },
    prev() { this.idx = (this.idx - 1 + this.photos.length) % this.photos.length; this.lbLoading = true; },
    next() { this.idx = (this.idx + 1) % this.photos.length; this.lbLoading = true; }
}"
@scroll.window="sticky = window.scrollY > 160"
@keydown.window.escape.prevent="lb && close()"
@keydown.window.left.prevent="lb && prev()"
@keydown.window.right.prevent="lb && next()">

    {{-- Sticky title bar --}}
    <div x-show="sticky"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="fixed top-16 left-0 right-0 z-40 flex items-center gap-3 px-4 sm:px-6 lg:px-8 h-10"
         style="background: rgba(250,247,242,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid #DDD5C5;">
        <a href="{{ route('gallery') }}" class="text-xs shrink-0 transition-colors" style="color: #9B8570;" onmouseenter="this.style.color='#2C1810'" onmouseleave="this.style.color='#9B8570'">&#8592; Albums</a>
        <span style="color: #DDD5C5; font-size: 0.65rem;">&#9679;</span>
        <span class="text-sm truncate" style="font-family: 'DM Serif Display', serif; color: #2C1810;">{{ $album->title }}</span>
        <span class="ml-auto text-xs shrink-0" style="color: #A89880;">{{ $album->date_of_outing->format('M j, Y') }}</span>
    </div>

    {{-- Back link --}}
    <div class="mb-8">
        <a href="{{ route('gallery') }}"
           class="inline-flex items-center gap-1.5 text-sm transition-colors group"
           style="color: #9B8570;"
           onmouseenter="this.style.color='#2C1810'"
           onmouseleave="this.style.color='#9B8570'">
            <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transition: transform 0.2s ease;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            All Albums
        </a>
    </div>

    {{-- Album header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-10 pb-8" style="border-bottom: 1px solid #E8E0D4;">
        <div>
            <p class="text-xs font-medium uppercase tracking-widest mb-2" style="color: #A89880; letter-spacing: 0.18em;">
                {{ $album->date_of_outing->format('F j, Y') }}
            </p>
            <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2rem, 5vw, 3.5rem); color: #2C1810; line-height: 1.05; letter-spacing: -0.01em;">
                {{ $album->title }}
            </h1>
            <div class="flex flex-wrap items-center gap-2 mt-3">
                <span class="inline-flex items-center gap-1.5 text-sm px-2.5 py-1 rounded-sm" style="background: #F0EBE2; color: #6B4226;">
                    <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $photos->total() }} {{ $photos->total() === 1 ? 'photo' : 'photos' }}
                </span>
                @if(!$album->is_public)
                    <span class="inline-flex items-center gap-1.5 text-sm px-2.5 py-1 rounded-sm" style="background: #FEF0E7; color: #C84B00;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        Private
                    </span>
                @endif
            </div>
            @if($album->description)
                <p class="mt-4 text-sm leading-relaxed max-w-prose" style="color: #7A6050; font-style: italic;">
                    {{ $album->description }}
                </p>
            @endif
        </div>

        {{-- Download All --}}
        @if($photos->total() > 0)
            <button
                type="button"
                id="download-all-btn"
                onclick="downloadAll({{ Js::from($downloadUrls) }}, this)"
                class="inline-flex items-center gap-2 font-medium px-5 py-2.5 rounded-sm text-sm shrink-0"
                style="background: #2C1810; color: #FAF7F2; transition: background 0.15s ease;"
                onmouseenter="this.style.background='#1a0e08'"
                onmouseleave="this.style.background='#2C1810'"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download All
            </button>
        @endif
    </div>

    {{-- Photo grid --}}
    @if($photos->total() === 0)
        <div class="text-center py-24" style="color: #8B7355;">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-family: 'DM Serif Display', serif; font-size: 1.5rem; color: #2C1810;">No photos yet.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-1">
            @foreach($photos as $photo)
                <div class="relative group aspect-square overflow-hidden cursor-pointer photo-cell"
                     style="background: #EDE8DF;"
                     @click="open({{ $loop->index }})">
                    <img
                        src="{{ $photo->getThumbnailUrl() }}"
                        data-src="{{ $photo->getThumbnailUrl() }}"
                        alt="{{ $photo->original_filename }}"
                        class="w-full h-full object-cover block"
                        style="transition: transform 0.4s ease, opacity 0.25s ease; opacity: 0;"
                        onmouseenter="this.style.transform='scale(1.06)'"
                        onmouseleave="this.style.transform='scale(1)'"
                        loading="lazy"
                        onload="this.style.opacity='1'"
                        onerror="photoError(this)"
                    >
                    {{-- Error state --}}
                    <div class="photo-error absolute inset-0 hidden items-center justify-center flex-col gap-2" style="background: #EDE8DF;">
                        <svg class="w-7 h-7 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #6B4226;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <button class="text-xs px-2.5 py-1 rounded-sm"
                                style="background: rgba(107,66,38,0.12); color: #6B4226; border: none; cursor: pointer;"
                                onclick="event.stopPropagation(); photoRetry(this.closest('.photo-cell'))">
                            Retry
                        </button>
                    </div>
                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100"
                         style="background: rgba(20,10,5,0.35); transition: opacity 0.2s ease;">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background: rgba(255,255,255,0.92);">
                            <svg class="w-4 h-4" style="color: #2C1810;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($photos->hasPages())
            <div class="mt-10 flex items-center justify-center gap-1">
                {{-- Previous --}}
                @if($photos->onFirstPage())
                    <span class="px-3 py-1.5 text-sm rounded-sm opacity-30 cursor-not-allowed" style="color: #8B7355;">&#8592; Prev</span>
                @else
                    <button wire:click="previousPage" onclick="window.scrollTo({top:0,behavior:'smooth'})" class="px-3 py-1.5 text-sm rounded-sm transition-colors" style="color: #6B4226; background: #F0EBE2;" onmouseenter="this.style.background='#E8DFD3'" onmouseleave="this.style.background='#F0EBE2'">&#8592; Prev</button>
                @endif

                {{-- Page numbers --}}
                @foreach($photos->getUrlRange(1, $photos->lastPage()) as $page => $url)
                    @if($page === $photos->currentPage())
                        <span class="px-3 py-1.5 text-sm rounded-sm font-medium" style="background: #2C1810; color: #FAF7F2;">{{ $page }}</span>
                    @elseif(abs($page - $photos->currentPage()) <= 2 || $page === 1 || $page === $photos->lastPage())
                        <button wire:click="gotoPage({{ $page }})" onclick="window.scrollTo({top:0,behavior:'smooth'})" class="px-3 py-1.5 text-sm rounded-sm transition-colors" style="color: #6B4226;" onmouseenter="this.style.background='#F0EBE2'" onmouseleave="this.style.background='transparent'">{{ $page }}</button>
                    @elseif(abs($page - $photos->currentPage()) === 3)
                        <span class="px1 text-sm" style="color: #A89880;">…</span>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($photos->hasMorePages())
                    <button wire:click="nextPage" onclick="window.scrollTo({top:0,behavior:'smooth'})" class="px-3 py-1.5 text-sm rounded-sm transition-colors" style="color: #6B4226; background: #F0EBE2;" onmouseenter="this.style.background='#E8DFD3'" onmouseleave="this.style.background='#F0EBE2'">Next &#8594;</button>
                @else
                    <span class="px-3 py-1.5 text-sm rounded-sm opacity-30 cursor-not-allowed" style="color: #8B7355;">Next &#8594;</span>
                @endif
            </div>
        @endif
    @endif

    {{-- Lightbox --}}
    <div x-show="lb"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="background: rgba(12,6,3,0.96);"
         @click.self="close()"
         @touchstart.passive="onSwipeStart"
         @touchend.passive="onSwipeEnd">

        {{-- Controls hint (fades out after first open) --}}
        <div x-show="showLightboxHint"
             x-transition:leave="transition ease-in duration-700"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute top-5 left-1/2 -translate-x-1/2 z-10 pointer-events-none whitespace-nowrap">
            <div class="text-xs px-3 py-1.5 rounded-sm" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.55); backdrop-filter: blur(6px);">
                <span x-show="!isIOS">&#8592; &#8594; navigate &nbsp;·&nbsp; ESC close</span>
                <span x-show="isIOS">Swipe to navigate</span>
            </div>
        </div>

        {{-- Close --}}
        <button @click="close()"
                class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center z-10"
                style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); transition: background 0.15s ease;"
                onmouseenter="this.style.background='rgba(255,255,255,0.2)'"
                onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Prev --}}
        <button @click="prev()"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full flex items-center justify-center z-10"
                style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); transition: background 0.15s ease;"
                onmouseenter="this.style.background='rgba(255,255,255,0.2)'"
                onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Image --}}
        <div class="flex items-center justify-center px-16 sm:px-20 w-full h-full relative">
            <div x-show="lbLoading" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <svg class="w-8 h-8 animate-spin" style="color: rgba(255,255,255,0.35);" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            <img :src="photos[idx]?.url"
                 :alt="photos[idx]?.name"
                 class="max-w-full max-h-[88vh] object-contain select-none"
                 :class="{ 'opacity-0': lbLoading }"
                 style="box-shadow: 0 25px 80px rgba(0,0,0,0.8); transition: opacity 0.2s ease;"
                 @load="lbLoading = false"
                 x-on:error="lbLoading = false">
        </div>

        {{-- Next --}}
        <button @click="next()"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full flex items-center justify-center z-10"
                style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); transition: background 0.15s ease;"
                onmouseenter="this.style.background='rgba(255,255,255,0.2)'"
                onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Bottom bar --}}
        <div class="absolute bottom-0 left-0 right-0 flex items-center justify-between px-5 pt-4"
             style="background: linear-gradient(transparent, rgba(0,0,0,0.65)); padding-bottom: max(1rem, env(safe-area-inset-bottom));">
            <span x-text="photos[idx]?.name" class="text-sm truncate max-w-[50%]" style="color: rgba(255,255,255,0.55);"></span>
            <div class="flex items-center gap-4 shrink-0">
                <span x-text="`${idx + 1} / ${photos.length}`" class="text-sm font-mono" style="color: rgba(255,255,255,0.4); font-size: 0.75rem;"></span>

                {{-- Standard download for non-iOS --}}
                <a x-show="!isIOS"
                   :href="photos[idx]?.dl"
                   :download="photos[idx]?.name"
                   class="inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-sm"
                   style="background: rgba(232,93,4,0.85); color: #ffffff; transition: background 0.15s ease;"
                   onmouseenter="this.style.background='rgba(200,75,0,0.95)'"
                   onmouseleave="this.style.background='rgba(232,93,4,0.85)'"
                   @click.stop>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Save
                </a>

                {{-- iOS: open image in new tab so user can long-press to save --}}
                <a x-show="isIOS"
                   :href="photos[idx]?.url"
                   target="_blank"
                   rel="noopener"
                   class="inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-sm"
                   style="background: rgba(232,93,4,0.85); color: #ffffff; transition: background 0.15s ease;"
                   @click.stop="showIOSTip = true; setTimeout(() => showIOSTip = false, 4000)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Save
                </a>
            </div>
        </div>

        {{-- iOS long-press hint --}}
        <div x-show="showIOSTip"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute bottom-24 left-0 right-0 flex justify-center pointer-events-none">
            <div class="text-xs px-3 py-2 rounded-sm" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); backdrop-filter: blur(8px);">
                Press &amp; hold the image to save to your Photos
            </div>
        </div>
    </div>
</div>

<script>
function downloadAll(urls, btn) {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) ||
                  (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    if (isIOS) {
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Open each photo and long-press to save';
        setTimeout(() => { btn.disabled = false; btn.innerHTML = original; }, 3500);
        return;
    }

    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin inline-block mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Downloading…';
    urls.forEach((url, i) => {
        setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            if (i === urls.length - 1) {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = original;
                }, 600);
            }
        }, i * 650);
    });
}

function photoError(img) {
    img.style.display = 'none';
    const error = img.closest('.photo-cell').querySelector('.photo-error');
    error.classList.remove('hidden');
    error.classList.add('flex');
}

function photoRetry(cell) {
    const img = cell.querySelector('img');
    const error = cell.querySelector('.photo-error');
    error.classList.add('hidden');
    error.classList.remove('flex');
    img.style.display = '';
    img.style.opacity = '0';
    const src = img.dataset.src;
    img.src = '';
    setTimeout(() => { img.src = src; }, 50);
}
</script>
