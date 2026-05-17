@extends('layouts.marketing')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ __('legal.terms_title') }}</h1>
    <p>{{ __('legal.terms_intro') }}</p>
    <h3>{{ __('legal.terms_s1_title') }}</h3>
    <ul>
        <li>{{ __('legal.terms_s1_item1') }}</li>
        <li>{{ __('legal.terms_s1_item2') }}</li>
        <li>{{ __('legal.terms_s1_item3') }}</li>
    </ul>
    <h3>{{ __('legal.terms_s2_title') }}</h3>
    <ul>
        <li>{{ __('legal.terms_s2_item1') }}</li>
        <li>{{ __('legal.terms_s2_item2') }}</li>
        <li>{{ __('legal.terms_s2_item3') }}</li>
    </ul>
    <h3>{{ __('legal.terms_s3_title') }}</h3>
    <ul>
        <li>{{ __('legal.terms_s3_item1') }}</li>
        <li>{{ __('legal.terms_s3_item2') }}</li>
    </ul>
    <h3>{{ __('legal.terms_s4_title') }}</h3>
    <ul>
        <li>{{ __('legal.terms_s4_item1') }}</li>
        <li>{{ __('legal.terms_s4_item2') }}</li>
    </ul>
    <h3>{{ __('legal.terms_s5_title') }}</h3>
    <p>{{ __('legal.terms_s5_body') }}</p>
    <h3>{{ __('legal.terms_contact_title') }}</h3>
    <p>{!! __('legal.terms_contact_body', ['email' => '<a href="mailto:support@noblenest.com">support@noblenest.com</a>']) !!}</p>
</div>
@endsection
