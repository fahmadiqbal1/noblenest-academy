@extends('layouts.marketing')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ __('legal.privacy_title') }}</h1>
    <p>{{ __('legal.privacy_intro') }}</p>
    <h3>{{ __('legal.privacy_collect_title') }}</h3>
    <ul>
        <li>{{ __('legal.privacy_collect_item1') }}</li>
        <li>{{ __('legal.privacy_collect_item2') }}</li>
        <li>{{ __('legal.privacy_collect_item3') }}</li>
    </ul>
    <h3>{{ __('legal.privacy_use_title') }}</h3>
    <ul>
        <li>{{ __('legal.privacy_use_item1') }}</li>
        <li>{{ __('legal.privacy_use_item2') }}</li>
        <li>{{ __('legal.privacy_use_item3') }}</li>
    </ul>
    <h3>{{ __('legal.privacy_protection_title') }}</h3>
    <ul>
        <li>{{ __('legal.privacy_protection_item1') }}</li>
        <li>{{ __('legal.privacy_protection_item2') }}</li>
        <li>{{ __('legal.privacy_protection_item3') }}</li>
    </ul>
    <h3>{{ __('legal.privacy_children_title') }}</h3>
    <p>{{ __('legal.privacy_children_body') }}</p>
    <h3>{{ __('legal.privacy_contact_title') }}</h3>
    <p>{!! __('legal.privacy_contact_body', ['email' => '<a href="mailto:support@noblenest.com">support@noblenest.com</a>']) !!}</p>
</div>
@endsection
