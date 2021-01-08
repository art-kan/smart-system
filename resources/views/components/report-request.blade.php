<div class="inbox scrollable">
    <div class="top-request">
        <span>{{ \Carbon\Carbon::parse($reportRequest['created_at'])->format('d.m.Y') }}</span>
        <h2>{{ $reportRequest['title'] }}</h2>

        <table class="table-info">
            <tr>
                <td><p>Создано:</p></td>
                <td><p class="date"> {{ formatDateAndTime($reportRequest->created_at) }}</p></td>
            </tr>
            <tr>
                <td><p>Состояние:</p></td>
                <td><p class="color-{{ $reportRequest->status }}"> {{ $reportRequest->status }}</p></td>
            </tr>
            @can('response', $reportRequest)
                <tr>
                    <td><p>Ваш отчет:</p></td>
                    @if (isset($response))
                        <td><p class="color-{{ $response->status }}"> {{ $response->status }}</p></td>
                    @else
                        <td><p class="color-not-yet">Отсутствует</p></td>
                    @endif
                </tr>
            @endif
        </table>
        <div class="top-request__text-body editor-output @can('inspect', $reportRequest)scrollable @endcan">
            {!! purify($reportRequest['body']) !!}
        </div>
    </div>

    @can('inspect', $reportRequest)
        <div class="middle-request">
            <ul>
                <li>
                    <a href="?report-status=pending" class="{{ $reportStatus == 'pending' ? 'active' : '' }}">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1.5835 9.5H5.93766L6.81594 10.8174C7.25642 11.4781 7.99798 11.875 8.7921 11.875H9.50016H10.2082C11.0024 11.875 11.7439 11.4781 12.1844 10.8174L13.0627 9.5H17.4168V14.6458C17.4168 15.0831 17.0624 15.4375 16.6252 15.4375H2.37516C1.93794 15.4375 1.5835 15.0831 1.5835 14.6458V9.5Z"
                                fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path
                                d="M17.4168 9.49996L15.4271 4.19401C15.1953 3.57604 14.6046 3.16663 13.9446 3.16663H5.05575C4.39574 3.16663 3.80497 3.57604 3.57322 4.19401L1.5835 9.49996"
                                fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="reports__status-name reports__status-name_pending">Несортированные</span>
                    </a>
                </li>
                <li>
                    <a href="?report-status=accepted"
                       class="{{ $reportStatus == 'accepted' ? 'active' : '' }}">
                        <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3.375 7.875C3.375 6.63236 4.38236 5.625 5.625 5.625H8.69091C9.52477 5.625 10.2427 6.21358 10.4062 7.03125C10.5698 7.84892 11.2877 8.4375 12.1217 8.4375H21.375C22.6177 8.4375 23.625 9.44486 23.625 10.6875V19.6875C23.625 20.9302 22.6177 21.9375 21.375 21.9375H5.625C4.38236 21.9375 3.375 20.9302 3.375 19.6875V7.875Z"
                                fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.75 13.7812L11.8125 17.1562" fill="none" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M10.125 15.4688L11.8125 17.1562" fill="none" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="reports__status-name reports__status-name_accepted">Одобренные</span>
                    </a>
                </li>
                <li>
                    <a href="?report-status=rejected"
                       class="{{ $reportStatus == 'rejected' ? 'active' : '' }}">
                        <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.9819 13.7411H20.8125"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.18753 13.7411H4.01807"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.7412 4.01831V6.18777"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.7412 20.8123V22.9817"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.96582 6.62476L8.49986 8.15881"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.8408 18.5L20.3749 20.034"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.625 20.0337L8.15904 18.4996"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.5 8.15879L20.034 6.62476"
                                  fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="reports__status-name reports__status-name_rejected">Переработка</span>
                    </a>
                </li>
            </ul>
            <div class="request-list">
                @if($reportsGroupedByDate->isEmpty())
                    <div class="request-empty">
                        <img src="/images/no-documents.png" alt="">
                        <h2>Здесь пока ничего нет.</h2>
                    </div>
                @else
                    @foreach($reportsGroupedByDate as $date => $reports)
                        <div class="responses-in-date">
                            <div class="date">{{ $date }}</div>
                            <div class="responses-list">
                                @foreach($reports as $report)
                                    <a href="{{ route('cabinet.reports.show', $report->id) }}" class="responses-item">
                                        <div class="responses-list-author">
                                            {{ $report->creator->name }} Школа
                                        </div>
                                        <div
                                            class="responses-list-text with-ellipsis">{!! strip_tags(purify($report->body)) !!}
                                            ...
                                        </div>
                                        <div class="responses-list-time">{{ timeFromDate($report->created_at) }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @elsecan('response', $reportRequest)
        @if(!$reportRequest->getAttachments()->isEmpty())
            <ul class="report-requests__attachments">
                @foreach($reportRequest->getAttachments() as $attachment)
                    <li class="report-requests__attachment">
                        <img class="report-requests__attachment-icon"
                             src="{{ docIconByFilename($attachment->filename) }}"
                             alt="{{ pathinfo($attachment->filename)['extension'] ?? '' }}"
                             title="{{ pathinfo($attachment->filename)['extension'] ?? '(без расширения)' }}">
                        <a class="report-requests__attachment-name"
                           href="{{ route('archive.file', $attachment->id) }}">{{ pathinfo($attachment->filename)['basename'] }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endcan
    <div class="bottom-request">
        @can('response', $reportRequest)
            @if($reportRequest->isActive())
                @if(isset($response))
                    <a href="{{ route('cabinet.reports.show', $response->id) }}"
                       class="button button-show-own-response">Просмотреть свой отчет</a>

                    @if($response->status === 'rejected')
                        <a href="{{ route('cabinet.reports.edit', $response->id) }}"
                           class="button button-response">Править отчёт</a>
                    @endif
                @else
                    <a href="{{ route('cabinet.report-requests.response', $reportRequest->id) }}"
                       class="button button-response">Отправить отчёт</a>
                @endif
            @endif
        @endcan
        @if($reportRequest->isEditable())
            @can('update', $reportRequest)
                <a href="{{ route('cabinet.report-requests.edit', $reportRequest->id) }}" class="button button-edit">Редактировать</a>
            @endcan
        @endif
        @if($reportRequest->isClosable())
            @can('close', $reportRequest)
                <form action="{{ route('cabinet.report-requests.change-status', $reportRequest->id) }}" method="POST">
                    @csrf
                    <input type="text" name="_method" value="PUT" hidden>
                    <input type="text" name="status" value="closed" hidden>
                    <button class="button button-close" type="submit">Закрыть</button>
                </form>
            @endcan
        @endif
        @if($reportRequest->isOpenable())
            @can('open', $reportRequest)
                <form action="{{ route('cabinet.report-requests.change-status', $reportRequest->id) }}" method="POST">
                    @csrf
                    <input type="text" name="_method" value="PUT" hidden>
                    <input type="text" name="status" value="active" hidden>
                    <button class="button button-open" type="submit">Возобновить</button>
                </form>
            @endcan
        @endif
    </div>
</div>
