@extends('layouts.parent')

@section('title', __('Refer Friends') . ' — Noble Nest Academy')

@section('content')
<div class="mx-auto max-w-2xl px-4 sm:px-6 py-8">

    {{-- Hero --}}
    <x-ui.section class="pt-0 text-center">
        <div class="mb-4 w-20 h-20 mx-auto rounded-full bg-[var(--color-primary-soft)] flex items-center justify-center" aria-hidden="true">
            <x-ui.icon name="gift" class="w-10 h-10 text-[var(--color-primary)]" />
        </div>
        <h1 class="text-3xl font-bold text-[var(--color-text)] mb-2">{{ __('Invite Friends, Earn Rewards') }}</h1>
        <p class="text-[var(--color-text-muted)] max-w-sm mx-auto leading-relaxed">
            {{ __('When a friend signs up with your link, you both get 1 month free!') }}
        </p>
    </x-ui.section>

    {{-- Referral link --}}
    <x-ui.card variant="clay" padding="md" class="mb-6">
        <label class="block text-sm font-semibold text-[var(--color-text)] mb-1">
            {{ __('Your referral link') }}
        </label>
        <div
            x-data="{ copied: false }"
            class="flex items-center gap-2"
        >
            <input
                id="refLink"
                type="text"
                readonly
                value="{{ url('/ref/' . Auth::user()->referral_code) }}"
                class="flex-1 block rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] text-sm py-2.5 px-4 focus:outline-none focus:border-[var(--color-brand-500)] select-all cursor-text"
                aria-label="{{ __('Referral link') }}"
                onclick="this.select()"
            />
            <x-ui.button
                type="button"
                variant="primary"
                size="md"
                icon="copy"
                x-bind:aria-label="copied ? '{{ __('Copied!') }}' : '{{ __('Copy link') }}'"
                @click="
                    navigator.clipboard.writeText(document.getElementById('refLink').value);
                    copied = true;
                    setTimeout(() => copied = false, 2000)
                "
            >
                <span x-show="!copied">{{ __('Copy') }}</span>
                <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
            </x-ui.button>
        </div>
        <p class="text-xs text-[var(--color-text-muted)] mt-2">
            {{ __('Share via WhatsApp, email, or social media') }}
        </p>

        {{-- Share shortcuts --}}
        <div class="flex flex-wrap gap-2 mt-3">
            <x-ui.button
                variant="secondary"
                size="sm"
                href="https://wa.me/?text={{ urlencode(__('Join me on Noble Nest Academy — learning made magical for kids! Use my link: ') . url('/ref/' . Auth::user()->referral_code)) }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                📱 {{ __('WhatsApp') }}
            </x-ui.button>
            <x-ui.button
                variant="secondary"
                size="sm"
                href="mailto:?subject={{ urlencode(__('Join Noble Nest Academy')) }}&body={{ urlencode(__("I've been using Noble Nest Academy with my kids and thought you'd love it! Sign up here: ") . url('/ref/' . Auth::user()->referral_code)) }}"
                icon="mail"
            >
                {{ __('Email') }}
            </x-ui.button>
        </div>
    </x-ui.card>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <x-ui.stat
            :label="__('Invites Sent')"
            :value="$referrals->total()"
            icon="send"
        />
        <x-ui.stat
            :label="__('Converted')"
            :value="$referrals->where('converted_at', '!=', null)->count()"
            icon="check-circle"
        />
        <x-ui.stat
            :label="__('Rewards Earned')"
            :value="$referrals->where('reward_granted', true)->count()"
            icon="award"
        />
    </div>

    {{-- How it works --}}
    <x-ui.section :title="__('How It Works')">
        <ol class="space-y-4">
            @foreach([
                ['icon' => 'copy',       'step' => '1', 'title' => __('Copy your link'),    'desc' => __('Share your unique referral link with friends and family.')],
                ['icon' => 'users',      'step' => '2', 'title' => __('Friend signs up'),   'desc' => __('Your friend creates an account using your referral link.')],
                ['icon' => 'award',      'step' => '3', 'title' => __('Both get rewarded'), 'desc' => __('You both receive 1 month free when they subscribe.')],
            ] as $step)
                <li class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-[var(--color-primary-soft)] flex items-center justify-center shrink-0 mt-0.5">
                        <x-ui.icon :name="$step['icon']" class="w-5 h-5 text-[var(--color-primary)]" />
                    </div>
                    <div>
                        <p class="font-bold text-[var(--color-text)] text-sm">{{ $step['step'] }}. {{ $step['title'] }}</p>
                        <p class="text-sm text-[var(--color-text-muted)] mt-0.5 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </x-ui.section>

    {{-- Referral History --}}
    <x-ui.section :title="__('Referral History')">
        @if($referrals->isNotEmpty())
            <x-ui.table :striped="true">
                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-start">{{ __('Friend') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('Status') }}</th>
                    </tr>
                </x-slot:head>

                @foreach($referrals as $ref)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-[var(--color-text)]">
                                {{ $ref->referred->name ?? __('Pending…') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[var(--color-text-muted)]">
                            {{ $ref->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <x-ui.badge :tone="$ref->converted_at ? 'success' : 'neutral'" size="sm">
                                @if($ref->converted_at)
                                    <x-ui.icon name="check" class="w-3 h-3" />
                                    {{ __('Converted') }}
                                @else
                                    {{ __('Pending') }}
                                @endif
                            </x-ui.badge>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-4">
                {{ $referrals->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="send"
                :title="__('No referrals yet')"
                :description="__('Share your link above to start inviting friends!')"
            />
        @endif
    </x-ui.section>

</div>
@endsection
