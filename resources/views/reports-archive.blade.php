@extends('components/layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports-archive.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/reports-archive.js') }}"></script>
@endpush

@section('content')
    <div class="library">
        <h1 style="line-height: 44px;">Библиотека</h1>
        <div class="library-request">
            <div class="period-request">
                <div class="range-select">
                    <div id="quick-range-picker"
                         class="quick-range @if($dateRangeName === 'custom range') hidden @endif">
                        <a @if($dateRangeName == 'current week') class="active"
                           @endif href="?start-of=week">неделя</a>
                        <a @if($dateRangeName == 'current month') class="active"
                           @endif href="?start-of=month">месяц</a>
                        <a @if($dateRangeName == 'current quarter') class="active"
                           @endif href="?start-of=quarter">квартал</a>
                        <a @if($dateRangeName == 'current half-year') class="active"
                           @endif href="?start-of=half-year">полугодие</a>
                        <a @if($dateRangeName == 'current year') class="active"
                           @endif href="?start-of=year">год</a>
                        <a @if($dateRangeName == 'last 3 years') class="active"
                           @endif href="?sub=3 years">трехлетие</a>
                        <a @if($dateRangeName == 'last 5 years') class="active"
                           @endif href="?sub=5 years">пятилетка</a>
                    </div>
                    <form id="custom-range-picker"
                          class="custom-range-select @if($dateRangeName !== 'custom range') hidden @endif">
                        <h3>Выберите период</h3>
                        <input type="date" value="{{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '' }}"
                               name="from">
                        <input type="date" value="{{ $to ? \Carbon\Carbon::parse($to)->format('Y-m-d') : '' }}"
                               name="to">
                        <input type="submit" hidden>
                    </form>
                </div>
                <button id="toggle-custom-range-picker">
                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                        <path
                            d="M23.625 13.5C23.625 7.90812 19.0919 3.375 13.5 3.375C7.90812 3.375 3.375 7.90812 3.375 13.5C3.375 19.0919 7.90812 23.625 13.5 23.625C19.0919 23.625 23.625 19.0919 23.625 13.5Z"
                            fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.5 13.5V9" fill="none" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                        <path d="M9 9L13.5 13.5" fill="none" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="library-body">
                <div class="library-list">
                    <ul>
                        @foreach($reportRequests as $reportRequest)
                            <li>
                                <a @if($activeReportRequest && $reportRequest->id === $activeReportRequest->id)
                                   class="active" href="{{ route('cabinet.report-requests.show', $reportRequest->id) }}"
                                   @else
                                   href="?{{ Request::getQueryString() }}&report-request-id={{$reportRequest->id}}"
                                    @endif>
                                    <svg class="folder-icon" width="18" height="15" viewBox="0 0 18 15"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1 2.7931C1 1.8028 1.79594 1 2.77778 1H5.20022C5.85908 1 6.42635 1.46906 6.55556 2.12069C6.68476 2.77232 7.252 3.24138 7.91093 3.24138H15.2222C16.2041 3.24138 17 4.04418 17 5.03448V12.2069C17 13.1972 16.2041 14 15.2222 14H2.77778C1.79594 14 1 13.1972 1 12.2069V2.7931Z"
                                            fill="none" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                    </svg>
                                    {{ $reportRequest->title }}
                                    <span>{{ formatDate($reportRequest->created_at) }}</span>
                                    <svg class="rotate-on-active" width="12" height="12" viewBox="0 0 12 12"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.4541 1L10.9996 6.00004L6.4541 11" fill="none" stroke-width="2"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M1 1L5.54545 6.00004L1 11" fill="none" stroke-width="2"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                @if($activeReportRequest && $reportRequest->id === $activeReportRequest->id)
                                    <div class="responses-list">
                                        @foreach($groupedReports as $date => $reports)
                                            <span>{{ $date }}</span>
                                            @foreach($reports as $report)
                                                <div class="school-library">
                                                    <a href="{{ route('cabinet.reports.show', $report->id) }}">{{ $report->creator->name }}
                                                        школа</a>
                                                    <span>{{ timeFromDate($report->created_at) }}</span>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="last-stat">
                    <h3>{{ $dateRangeName ?? 'Custom range' }}</h3>
                    <span>{{ formatDate($from) }} &mdash; {{ formatDate($to ?? now()) }}</span>
                    <div>
                        <div class="last-stat-text"><p>отчетов сдано:</p>
                            <div><span class="red">{{ $reportsCount }}</span> /{{ $maxReportsCount }}</div>
                        </div>
                        <div class="last-stat-text"><p>созданно запросов:</p>
                            <div><span class="red">5</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
