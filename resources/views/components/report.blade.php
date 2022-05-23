<div class="response scrollable">
    <div class="response-doc">
        <div class="back-arrow">
            <a href="{{ route('cabinet.report-requests.show', $report->report_request_id)  }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="51" height="8" viewBox="0 0 51 8" fill="none">
                    <path
                        d="M0.646446 3.64645C0.451183 3.84171 0.451183 4.15829 0.646446 4.35355L3.82843 7.53553C4.02369 7.7308 4.34027 7.7308 4.53553 7.53553C4.7308 7.34027 4.7308 7.02369 4.53553 6.82843L1.70711 4L4.53553 1.17157C4.7308 0.976311 4.7308 0.659728 4.53553 0.464466C4.34027 0.269204 4.02369 0.269204 3.82843 0.464466L0.646446 3.64645ZM51 3.5L1 3.5V4.5L51 4.5V3.5Z"
                        fill="black"/>
                </svg>
            </a>
            <h1>{{ $report->creator->name }} школа</h1>
        </div>

        <div class="response-info">
            <table class="table-info">
                <tr>
                    <td><p>Создано:</p></td>
                    <td><p class="date">{{ formatDateAndTime($report->created_at) }}</p></td>
                </tr>
                <tr>
                    <td><p>От:</p></td>
                    <td><p>{{ $report->creator->name }} школа</p></td>
                </tr>
                <tr>
                    <td><p>На запрос:</p></td>
                    <td>
                        <a href="{{ route('cabinet.report-requests.show', $report->report_request_id) }}"><b>{{ $report->respondedTo->title }}</b>
                            (от <i>{{ $report->respondedTo->creator->name }}</i>)</a></td>
                </tr>
                <tr>
                    <td><p>Текущий статус:</p></td>
                    <td><p class="color-{{ $report->status }}">{{ $report->status }}</p></td>
                </tr>
            </table>
        </div>

        <div class="response-body editor-output">{!! purify($report->body) !!}</div>

        @if(!$report->getAttachments()->isEmpty())
            <ul class="attachments">
                @foreach($report->getAttachments() as $attachment)
                    <a href="{{ route('archive.file', $attachment->id) }}" class="attachment">
                        <img class="report-requests__attachment-icon"
                             src="{{ docIconByFilename($attachment->filename) }}"
                             alt="{{ pathinfo($attachment->filename)['extension'] }}"
                             title="{{ pathinfo($attachment->filename)['extension'] }}">
                        <p class="report-requests__attachment-name">{{ pathinfo($attachment->filename)['filename'] }}</p>
                    </a>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="response-button">
        @if($report->status === 'rejected' && Auth::id() == $report->created_by)
            <a href="{{ route('cabinet.reports.edit', $report->id) }}"
               class="button button-response">Править отчёт</a>
        @endif
        @if($report->status !== 'rejected')
            @can('rejectResponse', $report->respondedTo)
                <form method="POST" action="{{ route('cabinet.reports.change-status', $report->id) }}">
                    @csrf
                    <input type="text" name="_method" value="PUT" hidden>
                    <input type="text" name="status" value="rejected" hidden>
                    <button type="submit" class="button cancel">Отклонить</button>
                </form>
            @endcan
        @endif
        @if($report->status !== 'accepted')
            @can('acceptResponse', $report->respondedTo)
                <form method="POST" action="{{ route('cabinet.reports.change-status', $report->id) }}">
                    @csrf
                    <input type="text" name="_method" value="PUT" hidden>
                    <input type="text" name="status" value="accepted" hidden>
                    <button type="submit" class="button confirm">Утвердить</button>
                </form>
            @endcan
        @endif
    </div>

</div>
