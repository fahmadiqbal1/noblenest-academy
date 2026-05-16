{{--
    Player: guided-steps
    Default narrative player for hands_on / craft / routine / mindfulness /
    discussion / outdoor / observation / interactive / creative / worksheet /
    experiment / vocal / sensory / movement / play / reading / story /
    flashcard / game.

    Renders the step-by-step interactive viewer when the activity has steps,
    otherwise an emoji-scene fallback so the player is never blank.

    Scope: $activity (Activity), $child (Child|null), $childQuery (string|''),
           $isPlayful (bool|null).
--}}
@if($activity->steps && $activity->steps->count() > 0)
    <x-step-player :activity="$activity" :child="$child ?? null" />
@else
    <x-ui.card variant="clay" padding="lg" class="text-center space-y-3">
        <div class="text-5xl" aria-hidden="true">{{ $activity->emoji ?: '🌟' }}</div>
        <h3 class="font-display font-bold text-xl text-[var(--color-text)]">Let's get started!</h3>
        <p class="text-sm text-[var(--color-text-muted)] max-w-md mx-auto">{{ $activity->description ?: "Talk through this activity together. Use the instructions and learning notes above to guide your play." }}</p>
    </x-ui.card>
@endif
