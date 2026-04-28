@extends('layouts.parent')

@section('title', 'Edit ' . $child->name . ' — Noble Nest Academy')

@section('content')
<div class="max-w-xl mx-auto">

    <x-ui.page-header
        title="{{ I18n::get('edit_child') }}"
        subtitle="Update {{ $child->name }}'s learning profile"
    >
        <x-slot name="actions">
            <x-ui.button variant="ghost" href="{{ route('parent.child', $child) }}" icon="chevron-left" size="sm">
                Back
            </x-ui.button>
        </x-slot>
    </x-ui.page-header>

    @if($errors->any())
        <x-ui.alert tone="danger" class="mb-6">
            <ul class="space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    <x-ui.card variant="clay" padding="lg">
        <form method="POST" action="{{ route('children.update', $child) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="space-y-5">

                <x-ui.field
                    label="{{ I18n::get('child_name') }}"
                    name="name"
                    :error="$errors->first('name')"
                    required
                >
                    <x-ui.input
                        type="text"
                        name="name"
                        :value="old('name', $child->name)"
                        placeholder="e.g. Amira"
                        :invalid="$errors->has('name')"
                        autocomplete="off"
                    />
                </x-ui.field>

                <x-ui.field
                    label="Date of Birth"
                    name="date_of_birth"
                    :error="$errors->first('date_of_birth')"
                    required
                >
                    <x-ui.input
                        type="date"
                        name="date_of_birth"
                        :value="old('date_of_birth', $child->date_of_birth?->format('Y-m-d'))"
                        max="{{ now()->toDateString() }}"
                        :invalid="$errors->has('date_of_birth')"
                    />
                </x-ui.field>

                <x-ui.field
                    label="Gender"
                    name="gender"
                    :error="$errors->first('gender')"
                    required
                >
                    <x-ui.select
                        name="gender"
                        :value="old('gender', $child->gender)"
                        placeholder="Select..."
                        :options="['male' => 'Boy', 'female' => 'Girl', 'other' => 'Other']"
                        :invalid="$errors->has('gender')"
                    />
                </x-ui.field>

                <x-ui.field
                    label="{{ I18n::get('preferred_language') }}"
                    name="preferred_language"
                    :error="$errors->first('preferred_language')"
                >
                    <x-ui.select
                        name="preferred_language"
                        :value="old('preferred_language', $child->preferred_language)"
                        placeholder="{{ I18n::get('select_language') }}"
                        :options="['en' => 'English', 'fr' => 'French', 'ru' => 'Russian', 'zh' => 'Mandarin', 'es' => 'Spanish', 'ko' => 'Korean', 'ur' => 'Urdu', 'ar' => 'Arabic']"
                        :invalid="$errors->has('preferred_language')"
                    />
                </x-ui.field>

                {{-- Muslim family toggle --}}
                <fieldset>
                    <legend class="block text-sm font-semibold text-[var(--color-text)] mb-2">
                        Is your family Muslim?
                    </legend>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                name="is_muslim"
                                value="1"
                                @if(old('is_muslim', $child->is_muslim)) checked @endif
                                class="w-4 h-4 accent-[var(--color-brand-600)] cursor-pointer"
                            >
                            <span class="text-sm font-medium text-[var(--color-text)]">Yes</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                name="is_muslim"
                                value="0"
                                @if(!old('is_muslim', $child->is_muslim)) checked @endif
                                class="w-4 h-4 accent-[var(--color-brand-600)] cursor-pointer"
                            >
                            <span class="text-sm font-medium text-[var(--color-text)]">No</span>
                        </label>
                    </div>
                    <p class="text-xs text-[var(--color-text-muted)] mt-1.5">This helps us include Quran and Islamic studies content tailored for your child.</p>
                </fieldset>

            </div>

            <div class="flex gap-3 mt-8">
                <x-ui.button type="submit" variant="primary" icon="check" class="flex-1">
                    {{ I18n::get('update_child') }}
                </x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('children.index') }}">
                    {{ I18n::get('cancel') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

</div>
@endsection
