@extends('layouts.marketing')

@section('meta_title', 'Apply for a Scholarship – NobleNest Global Academy')
@section('meta_description', 'Apply for a NobleNest Academy scholarship. We offer need-based and merit scholarships globally to ensure every child has access to world-class education.')

@section('content')
<div class="container py-5" style="max-width:680px">
    <div class="text-center mb-5">
        <div style="font-size:3rem;line-height:1">🎓</div>
        <h1 class="font-bold mt-2 mb-1">Apply for a Scholarship</h1>
        <p class="text-[var(--color-text-muted)]">We believe every child deserves quality education regardless of financial circumstance.</p>
    </div>

    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800 shadow-sm">
            <x-ui.icon name="check-circle" class="me-2" />{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
        <div class="p-5 p-4 p-md-5">
            <form method="POST" action="{{ route('scholarship.apply') }}" autocomplete="on">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Parent / Guardian Name <span class="text-red-600">*</span></label>
                    <input type="text" name="parent_name" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('parent_name') is-invalid @enderror"
                           value="{{ old('parent_name', auth()->user()->name ?? '') }}" required>
                    @error('parent_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Email Address <span class="text-red-600">*</span></label>
                    <input type="email" name="email" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('email') is-invalid @enderror"
                           value="{{ old('email', auth()->user()->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Child's Name <span class="text-red-600">*</span></label>
                    <input type="text" name="child_name" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('child_name') is-invalid @enderror"
                           value="{{ old('child_name') }}" required>
                    @error('child_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Child's Age (months) <span class="text-red-600">*</span></label>
                    <input type="number" name="child_age_months" min="0" max="180" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('child_age_months') is-invalid @enderror"
                           value="{{ old('child_age_months') }}" required>
                    @error('child_age_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Country <span class="text-red-600">*</span></label>
                    <input type="text" name="country" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('country') is-invalid @enderror"
                           value="{{ old('country') }}" required placeholder="e.g. Nigeria, Pakistan, Indonesia">
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Why does your family need financial assistance?</label>
                    <textarea name="need_statement" rows="4" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('need_statement') is-invalid @enderror"
                              placeholder="Please describe your household situation in a few sentences.">{{ old('need_statement') }}</textarea>
                    @error('need_statement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Why should your child receive this scholarship?</label>
                    <textarea name="merit_statement" rows="4" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg @error('merit_statement') is-invalid @enderror"
                              placeholder="Tell us about your child's curiosity, progress, or learning goals.">{{ old('merit_statement') }}</textarea>
                    @error('merit_statement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="flex items-center gap-2 mb-4">
                    <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="agree_terms" id="agree_terms" required>
                    <label class="text-sm" for="agree_terms">
                        I confirm this information is accurate and I agree to the
                        <a href="{{ route('terms') }}" target="_blank">Terms of Service</a>.
                    </label>
                </div>

                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-900 text-white hover:bg-gray-800 rounded-full px-5 font-bold">
                    Submit Application
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
