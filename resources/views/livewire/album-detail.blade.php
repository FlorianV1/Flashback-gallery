<div x-data="{
    lb: false,
    idx: 0,
    photos: {{ Js::from($photos->map(fn($p) => [
        'url'  => $p->getUrl(),
        'dl'   => route('photos.download', ['album' => $album->id, 'photo' => $p->id]),
        'name' => $p->original_filename,
    ])->values()) }},
    open(i) { this.idx = i; this.lb = true; document.body.style.overflow = 'hidden'; },
    close() { this.lb = false; document.body.style.overflow = ''; },
    prev() { this.idx = (this.idx - 1 + this.photos.length) % this.photos.length; },
    next() { this.idx = (this.idx + 1) % this.photos.length; }
}"
@keydown.window.escape.prevent="lb && close()"
@keydown.window.left.prevent="lb && prev()"
@keydown.window.right.prevent="lb && next()">

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
                    {{ $photos->count() }} {{ $photos->count() === 1 ? 'photo' : 'photos' }}
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
        @if($photos->isNotEmpty())
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
    @if($photos->isEmpty())
        <div class="text-center py-24" style="color: #8B7355;">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-family: 'DM Serif Display', serif; font-size: 1.5rem; color: #2C1810;">No photos yet.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-1">
            @foreach($photos as $photo)
                <div class="relative group aspect-square overflow-hidden cursor-pointer"
                     style="background: #EDE8DF;"
                     @click="open({{ $loop->index }})">
                    <img
                        src="{{ $photo->getUrl() }}"
                        alt="{{ $photo->original_filename }}"
                        class="w-full h-full object-cover"
                        style="transition: transform 0.4s ease;"
                        onmouseenter="this.style.transform='scale(1.06)'"
                        onmouseleave="this.style.transform='scale(1)'"
                        loading="lazy"
                    >
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
         @click.self="close()">

        <button @click="close()"
                class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center z-10"
                style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); transition: background 0.15s ease;"
                onmouseenter="this.style.background='rgba(255,255,255,0.2)'"
                onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <button @click="prev()"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full flex items-center justify-center z-10"
                style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); transition: background 0.15s ease;"
                onmouseenter="this.style.background='rgba(255,255,255,0.2)'"
                onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="flex items-center justify-center px-16 sm:px-20 w-full h-full">
            <img :src="photos[idx]?.url"
                 :alt="photos[idx]?.name"
                 class="max-w-full max-h-[88vh] object-contain select-none"
                 style="box-shadow: 0 25px 80px rgba(0,0,0,0.8);">
        </div>

        <button @click="next()"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full flex items-center justify-center z-10"
                style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); transition: background 0.15s ease;"
                onmouseenter="this.style.background='rgba(255,255,255,0.2)'"
                onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <div class="absolute bottom-0 left-0 right-0 flex items-center justify-between px-5 py-4"
             style="background: linear-gradient(transparent, rgba(0,0,0,0.65));">
            <span x-text="photos[idx]?.name" class="text-sm truncate max-w-[50%]" style="color: rgba(255,255,255,0.55);"></span>
            <div class="flex items-center gap-4 shrink-0">
                <span x-text="`${idx + 1} / ${photos.length}`" class="text-sm font-mono" style="color: rgba(255,255,255,0.4); font-size: 0.75rem;"></span>
                <a :href="photos[idx]?.dl"
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
            </div>
        </div>
    </div>
</div>

<script>
function downloadAll(urls, btn) {
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
</script>
