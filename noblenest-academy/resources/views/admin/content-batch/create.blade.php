@extends('layouts.admin')
@section('meta_title', __('admin.content_batch.meta_title'))

@section('content')
<div class="w-full px-4 py-4">
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.orchestrator.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm rounded-full">
            <x-ui.icon name="arrow-left" /> {{ __('admin.common.back') }}
        </a>
        <h1 class="h4 font-bold mb-0">{{ __('admin.content_batch.title') }}</h1>
    </div>

    <div class="flex flex-wrap gap-4">
        <div class="lg:w-7/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="p-5 p-4">
                    <form method="POST" action="{{ route('admin.content-batch.store') }}">
                        @csrf

                        <div class="flex flex-wrap gap-3">
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('admin.content_batch.subject') }}</label>
                                <select name="subject" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('subject') is-invalid @enderror">
                                    @foreach(['literacy','numeracy','creativity','stem','social','motor'] as $s)
                                        <option value="{{ $s }}" @selected(old('subject') === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('admin.content_batch.age_tier') }}</label>
                                <select name="age_tier" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('age_tier') is-invalid @enderror">
                                    <option value="baby">{{ __('admin.content_batch.age_baby') }}</option>
                                    <option value="toddler">{{ __('admin.content_batch.age_toddler') }}</option>
                                    <option value="preschool">{{ __('admin.content_batch.age_preschool') }}</option>
                                    <option value="school" @selected(old('age_tier')==='school')>{{ __('admin.content_batch.age_school') }}</option>
                                </select>
                            </div>

                            <div class="md:w-4/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('admin.content_batch.count') }}</label>
                                <input type="number" name="count" min="1" max="50" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg"
                                       value="{{ old('count', 10) }}" required>
                            </div>

                            <div class="md:w-4/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('admin.content_batch.language') }}</label>
                                <select name="language" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg">
                                    <option value="en">{{ __('admin.content_batch.lang_en') }}</option>
                                    <option value="fr">{{ __('admin.content_batch.lang_fr') }}</option>
                                    <option value="ar">{{ __('admin.content_batch.lang_ar') }}</option>
                                    <option value="ur">{{ __('admin.content_batch.lang_ur') }}</option>
                                    <option value="es">{{ __('admin.content_batch.lang_es') }}</option>
                                    <option value="zh">{{ __('admin.content_batch.lang_zh') }}</option>
                                    <option value="ko">{{ __('admin.content_batch.lang_ko') }}</option>
                                    <option value="ru">{{ __('admin.content_batch.lang_ru') }}</option>
                                </select>
                            </div>

                            <div class="md:w-4/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('admin.content_batch.free_tier') }}</label>
                                <div class="flex items-center gap-2 mt-2">
                                    <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="is_free" id="is_free" value="1"
                                           @checked(old('is_free'))>
                                    <label class="text-sm" for="is_free">{{ __('admin.content_batch.mark_free') }}</label>
                                </div>
                            </div>

                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('admin.content_batch.activity_types') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['tracing','puzzle','drawing','quiz','matching','story'] as $type)
                                        <div class="flex items-center gap-2">
                                            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox"
                                                   name="activity_types[]" value="{{ $type }}"
                                                   id="type_{{ $type }}"
                                                   @checked(in_array($type, old('activity_types', ['tracing','quiz'])))>
                                            <label class="text-sm" for="type_{{ $type }}">{{ ucfirst($type) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('activity_types')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-900 text-white hover:bg-gray-800 rounded-full">
                            <x-ui.icon name="zap" class="me-1" /> {{ __('admin.content_batch.queue_job') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:w-5/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 bg-gray-50">
                <div class="p-5 p-4">
                    <h5 class="font-bold mb-3">{{ __('admin.content_batch.how_it_works') }}</h5>
                    <ol class="ps-3 mb-0" style="font-size:0.875rem;line-height:2">
                        <li>{{ __('admin.content_batch.how_step_1') }}</li>
                        <li>{{ __('admin.content_batch.how_step_2') }}</li>
                        <li>{{ __('admin.content_batch.how_step_3_pre') }} <strong>{{ __('admin.content_batch.how_step_3_strong') }}</strong> {{ __('admin.content_batch.how_step_3_post') }}</li>
                        <li>{{ __('admin.content_batch.how_step_4') }}</li>
                        <li>{{ __('admin.content_batch.how_step_5') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
