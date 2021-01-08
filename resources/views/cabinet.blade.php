@extends('components/layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cabinet.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/cabinet.js') }}"></script>
@endpush

@section('content')
    @if(empty($reportRequests) && !isset($report))
        <div style="display: flex; flex-direction: column; flex-grow: 1;">
            <h1>Открытые запросы</h1>
            <div class="welcome">
                <img src="/images/document-folder.png" alt="NoDocuments">
                <h2>Вы еще не создавали запросов, самое время начать!</h2>
                <a href="{{ route('cabinet.report-request.creator') }}">Создать запрос</a>
            </div>
        </div>
    @elseif(isset($report))
        <x-report :report="$report"></x-report>

        <x-chat :chat_data="$chatData"></x-chat>
    @else
        <x-report-requests-nav :reportRequests="$reportRequests"
                               :activeReportRequest="$activeReportRequest">
        </x-report-requests-nav>

        <x-report-request :report_request="$activeReportRequest"
                          :report_status="$reportStatus"
                          :response="$response"
                          :reports_grouped_by_date="$reportsGroupedByDate"
        ></x-report-request>

        @if(is_null($chatData))
            <x-responders-list :reporters="$reporters"></x-responders-list>
        @else
            <div class="chat-overlay" id="chat-overlay"></div>
            <x-chat :chat_data="$chatData"></x-chat>
        @endif
    @endif
@endsection
