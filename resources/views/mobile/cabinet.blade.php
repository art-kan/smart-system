@extends('mobile/components/layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cabinet.mobile.css') }}">
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; flex-grow: 1;">
        <h1>Открытые запросы</h1>
        <div class="welcome">
            <img src="/images/no-documents.png" alt="NoDocuments">
            <h2>Вы еще не создавали запросов, самое время начать!</h2>
            <button>Создать запрос</button>
        </div>
    </div>
@endsection
