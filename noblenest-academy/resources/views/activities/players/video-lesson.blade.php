{{--
    Player: video-lesson  (Phase 2 polish: inline video + transcript drawer)

    Renders the activity's video inline with a transcript drawer (derived
    from step instructions) and a captions hint. The dedicated route still
    handles bookmarking + watch-time analytics.
--}}
<x-ui.card padding="md" class="space-y-3">
    @if(!empty($activity->video_url))
        <div class="rounded-2xl overflow-hidden bg-black aspect-video shadow-sm">
            <video
                src="{{ $activity->video_url }}"
                controls
                playsinline
                preload="metadata"
                @if(!empty($activity->subtitle_url)) crossorigin="anonymous" @endif
                class="w-full h-full object-contain"
                aria-label="{{ $activity->title }}"
            >
                @if(!empty($activity->subtitle_url))
                    <track src="{{ $activity->subtitle_url }}" kind="captions" srclang="en" label="English" default>
                @endif
                Your browser does not support inline video. Use the full lesson page.
            </video>
        </div>

        @if($activity->steps && $activity->steps->count() > 0)
            <details class="rounded-lg border border-gray-200 bg-white">
                <summary class="cursor-pointer px-4 py-2 text-sm font-semibold text-[var(--color-text)] flex items-center gap-2 list-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                    <x-ui.icon name="list-ordered" class="w-4 h-4" /> Transcript &amp; step bookmarks
                </summary>
                <ol class="px-4 pb-3 pt-1 text-sm text-[var(--color-text-muted)] space-y-1">
                    @foreach($activity->steps as $step)
                        <li>
                            <strong class="text-[var(--color-text)]">Step {{ $step->step_number }}:</strong>
                            {{ $step->instruction }}
                        </li>
                    @endforeach
                </ol>
            </details>
        @endif
    @else
        <x-ui.button
            variant="primary"
            href="{{ route('activities.video', $activity) . ($childQuery ?? '') }}"
            icon="play"
            size="lg"
            class="w-full justify-center"
        >
            Watch Video 🎬
        </x-ui.button>
    @endif

    <div class="text-center">
        <a href="{{ route('activities.video', $activity) . ($childQuery ?? '') }}"
           class="text-sm text-violet-600 hover:underline inline-flex items-center gap-1">
            <x-ui.icon name="circle-play" class="w-4 h-4" /> Open the full lesson page →
        </a>
    </div>
</x-ui.card>
