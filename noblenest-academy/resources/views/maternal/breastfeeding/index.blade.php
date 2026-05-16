@extends('layouts.maternal')

@section('title', 'Breastfeeding Education — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <x-ui.icon name="activity" class="me-2" style="color:#EC4899;" /> Breastfeeding Education
            </h3>
            <p class="text-[var(--color-text-muted)] mb-4">Evidence-based guidance on breastfeeding — its profound benefits for both mother and child, including reduced breast cancer risk, immune strength transfer, and bonding.</p>

            {{-- Key benefits banner --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:linear-gradient(135deg, #FDF2F8, #FCE7F3); border-radius:1rem;">
                <div class="p-5 p-4">
                    <h6 class="font-semibold mb-3">Why Breastfeed?</h6>
                    <div class="flex flex-wrap gap-3">
                        <div class="md:w-4/12">
                            <div class="text-center">
                                <div class="mb-2" style="font-size:1.5rem;">🛡️</div>
                                <h6 class="text-sm font-semibold">Immune Transfer</h6>
                                <p class="text-sm text-[var(--color-text-muted)] mb-0">Antibodies pass directly to baby, building immunity from day one.</p>
                            </div>
                        </div>
                        <div class="md:w-4/12">
                            <div class="text-center">
                                <div class="mb-2" style="font-size:1.5rem;">💖</div>
                                <h6 class="text-sm font-semibold">Reduced Cancer Risk</h6>
                                <p class="text-sm text-[var(--color-text-muted)] mb-0">Every 12 months of breastfeeding reduces breast cancer risk by ~4.3%.</p>
                            </div>
                        </div>
                        <div class="md:w-4/12">
                            <div class="text-center">
                                <div class="mb-2" style="font-size:1.5rem;">🤱</div>
                                <h6 class="text-sm font-semibold">Bonding</h6>
                                <p class="text-sm text-[var(--color-text-muted)] mb-0">Oxytocin release strengthens the mother-child emotional bond.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                @forelse($content as $item)
                <div class="md:w-6/12">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="w-full rounded-t-xl" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="p-5 p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="text-sm text-[var(--color-text-muted)] mb-2">{{ Str::limit($item->benefit_explanation, 100) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                Read More <x-ui.icon name="arrow-right" class="ms-1" />
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-full">
                    <p class="text-[var(--color-text-muted)] text-center py-4">Breastfeeding guides coming soon.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $content->links() }}</div>
        </div>
    </div>
</div>
@endsection
