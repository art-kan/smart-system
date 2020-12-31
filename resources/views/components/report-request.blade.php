<div class="inbox">
    <div class="top-request">
        <span>{{ \Carbon\Carbon::parse($reportRequest['created_at'])->format('d.m.Y') }}</span>
        <h2>{{ $reportRequest['title'] }}</h2>
        <div class="top-request__text-body">
            {!! purify($reportRequest['body']) !!}
        </div>
    </div>

    @can('inspect', $reportRequest)
        <div class="middle-request">
            <ul>
                <li>
                    <a href="?report-status=pending" class="{{ $reportStatus == 'pending' ? 'active' : '' }}">
                        <img src="/images/unsorted.svg" alt="">Несортированные
                    </a>
                </li>
                <li>
                    <a href="?report-status=accepted"
                       class="{{ $reportStatus == 'accepted' ? 'active' : '' }}">
                        <img src="/images/accepted.svg" alt="">Одобренные
                    </a>
                </li>
                <li>
                    <a href="?report-status=rejected"
                       class="{{ $reportStatus == 'rejected' ? 'active' : '' }}">
                        <img src="/images/reworking.svg" alt="">Переработка
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
                                    <a href="{{ route('cabinet.report', $report->id) }}" class="responses-item">
                                        <div class="responses-list-author">
                                            {{ $report->creator->name }} Школа
                                        </div>
                                        <div
                                            class="responses-list-text with-ellipsis">{!! strip_tags(purify($report->body)) !!}
                                            ...
                                        </div>
                                        <div class="responses-list-time">12:00</div>
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
                             alt="{{ pathinfo($attachment->filename)['extension'] }}"
                             title="{{ pathinfo($attachment->filename)['extension'] }}">
                        <a class="report-requests__attachment-name"
                           href="{{ route('archive.file', $attachment->id) }}">{{ pathinfo($attachment->filename)['filename'] }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endcan
    <div class="bottom-request">
        @if($reportRequest->isActive() && Auth::user()->id !== $reportRequest->created_by)
            @can('response', $reportRequest)
                <a href="{{ route('cabinet.report.creator', $reportRequest->id) }}" class="button button-response">Отправить отчет</a>
            @endcan
        @endif
        @if($reportRequest->isEditable())
            @can('update', $reportRequest)
                <a href="{{ route('cabinet.report-request.editor', $reportRequest->id) }}" class="button button-edit">Редактировать</a>
            @endcan
        @endif
        @if($reportRequest->isClosable())
            @can('close', $reportRequest)
                <form action="{{ route('cabinet.report-request.edit', $reportRequest->id) }}" method="POST">
                    @csrf
                    <input type="text" name="_method" value="PUT" hidden>
                    <input type="text" name="status" value="closed" hidden>
                    <button class="button button-close" type="submit">Закрыть</button>
                </form>
            @endcan
        @endif
        @if($reportRequest->isOpenable())
            @can('open', $reportRequest)
                <button class="button button-open">Возобновить</button>
            @endcan
        @endif
    </div>
</div>
