{{--
    Player: song-and-movement
    Looping video / audio with lyric overlay and tap-along beat tracker.
    Phase 2: embed the video (if any) or audio; lyric overlay / beat tracker
    arrive in a later commit. Renders the existing video player route as a
    fallback when both video_url and audio_url are absent.
--}}
@if(!empty($activity->video_url))
    <x-ui.card padding="md">
        <div class="aspect-video w-full rounded-lg overflow-hidden bg-black">
            <video
                src="{{ $activity->video_url }}"
                controls
                loop
                playsinline
                preload="metadata"
                class="w-full h-full object-contain"
                aria-label="{{ $activity->title }}"
            ></video>
        </div>
    </x-ui.card>
@elseif(!empty($activity->audio_url))
    <x-ui.card padding="md" class="space-y-3 text-center">
        <div class="text-5xl" aria-hidden="true">🎶</div>
        <audio src="{{ $activity->audio_url }}" controls loop preload="metadata" class="w-full"
               aria-label="{{ $activity->title }}"></audio>
    </x-ui.card>
@else
    <x-ui.button
        variant="primary"
        href="{{ route('activities.video', $activity) . ($childQuery ?? '') }}"
        icon="play"
        size="lg"
        class="w-full justify-center"
    >
        Play song 🎵
    </x-ui.button>
@endif
