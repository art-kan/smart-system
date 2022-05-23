<div class="request">
    <h1>Открытые запросы</h1>
    <ul>
        @foreach($reportRequests as $reportRequest)
            <li>
                <a class="request-box {{ $reportRequest->id == $activeReportRequest->id ? 'active' : '' }}"
                   href="{{ route('cabinet.report-requests.show', $reportRequest->id) }}"
                   title="{{ $reportRequest['title'] }}">
                    <svg class="request-box__folder-icon" width="18" height="15" viewBox="0 0 18 15" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M1 2.7931C1 1.8028 1.79594 1 2.77778 1H5.20022C5.85908 1 6.42635 1.46906 6.55556 2.12069C6.68476 2.77232 7.252 3.24138 7.91093 3.24138H15.2222C16.2041 3.24138 17 4.04418 17 5.03448V12.2069C17 13.1972 16.2041 14 15.2222 14H2.77778C1.79594 14 1 13.1972 1 12.2069V2.7931Z"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="request-box__title with-ellipsis">{{ $reportRequest->title }}</span>
                    <span
                        class="request-box__date">{{ \Carbon\Carbon::parse($reportRequest->created_at)->format('d.m.Y') }}</span>
                    <svg class="request-box__arrow-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.45459 1L11 6.00004L6.45459 11" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                        <path d="M1 1L5.54545 6.00004L1 11" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
        @endforeach
    </ul>
    @can('create', \App\Models\ReportRequest::class)
        <div class="btn-request">
            <a class="btn-request-create" href="{{ route('cabinet.report-requests.create') }}">Создать запрос</a>
        </div>
    @endcan
</div>
