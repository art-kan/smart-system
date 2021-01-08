<?php

namespace App\Http\Controllers;

use App\Http\Controllers\traits\CabinetTricks;
use App\Models\DocumentSet;
use App\Models\Report;
use App\Models\ReportRequest;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportRequestController extends Controller
{
    use CabinetTricks;

    public function index(Request $request)
    {
        /** @var ReportRequest $toShow */
        $toShow = ReportRequest::latest()->first();

        if (is_null($toShow)) {
            return viewMobileOrDesktop('cabinet');
        }

        return $this->show($request, $toShow);
    }

    /**
     * @param Request $request
     * @param ReportRequest $reportRequest
     * @return Application|Factory|View
     */
    public function show(Request $request, ReportRequest $reportRequest)
    {
        $reportsStatus = Report::normalizeStatus($request->query('report-status'));

//        dd([
//            'response' => Auth::user()->can('response', $reportRequest)
//                ? $reportRequest->responseFrom(Auth::user()) : null,
//        ]);

        return viewMobileOrDesktop('cabinet', [
            'reportRequests' => $this->availableActiveReportRequests()->get(),
            'activeReportRequest' => $reportRequest,
            'reportStatus' => $reportsStatus,
            'response' => Auth::user()->can('response', $reportRequest)
                ? $reportRequest->responseFrom(Auth::user()) : null,
            'reportsGroupedByDate' => $reportRequest->responsesGroupedByDate($reportsStatus),
            'reporters' => $reportRequest
                ->respondersJoinedWithReports()
                ->select(['users.id', 'users.name', 'reports.status'])
                ->get()
                ->sort(function ($a, $b) {
                    $table = array_flip(Report::STATUSES);
                    $as = $table[$a->status ?? Report::DEFAULT_STATE];
                    $bs = $table[$b->status ?? Report::DEFAULT_STATE];
                    if ($as != $bs) return $as > $bs;
                    return $a->name > $b->name;
                }),
            'chatData' => $this->fetchChatData($request),
        ]);
    }

    public function create()
    {
        return viewMobileOrDesktop('editroom', [
            'title' => 'Создание запроса',
            'placeholder' => 'Напишите описание для запроса, чтобы администрация школы могла выполнить необходимые вам действия.',
            'useAttachments' => true,
            'actionURL' => route('cabinet.report-requests.store'),
            'actionMethod' => 'POST',
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'body' => ['required', 'string', 'min:1'],
            'attached' => ['array'],
            'attached.*' => ['file', 'max:16384'],
        ]);

        $set_id = $request->file('attached')
            ? DocumentSet::fromFiles($request->file('attached'))->id
            : null;

        $created = ReportRequest::create(array_merge(
            $request->only(['title', 'body']),
            ['created_by' => Auth::user()->id, 'document_set_id' => $set_id]
        ));

        return redirect()->intended(route('cabinet.report-requests.show', $created->id));
    }

    /**
     * @param ReportRequest $reportRequest
     * @return Application|Factory|View
     */
    public function edit(ReportRequest $reportRequest)
    {
        return viewMobileOrDesktop('editroom', [
            'title' => 'Редактирование запроса',
            'content' => $reportRequest,
            'attachments' => $reportRequest->getAttachments(),
            'placeholder' => 'Напишите описание для запроса, чтобы администрация школы могла выполнить необходимые вам действия.',
            'actionURL' => route('cabinet.report-requests.update', $reportRequest->id),
            'actionMethod' => 'PUT',
        ]);
    }

    /**
     * @param Request $request
     * @param ReportRequest $reportRequest
     * @return RedirectResponse
     */
    public function update(Request $request, ReportRequest $reportRequest): RedirectResponse
    {
        $request->validate([
            'title' => ['string', 'min:1', 'max:255'],
            'body' => ['string', 'min:1'],
            'attach' => ['array'],
            'attach.*' => ['file', 'max:16384'],
            'detach' => ['array'],
            'detach.*' => ['integer', 'exists:\App\Models\ArchiveDocument,id'],
        ]);


        $reportRequest->fill(array_filter($request->only(['title', 'body'])));
        $reportRequest->detachAttachments($request->input('detach'));
        $reportRequest->attachUploadedFiles($request->file('attach'));

        $reportRequest->save();

        return redirect()->route('cabinet.report-requests.show', $reportRequest->id);
    }

    public function changeStatus(Request $request, ReportRequest $reportRequest): RedirectResponse
    {
        $request->validate(['status' => ['required', 'string', Rule::in(ReportRequest::STATUSES)]]);

        if ($reportRequest->setStatus($request->input('status'))) {
            $reportRequest->save();
            return redirect()->intended(route('cabinet.report-requests.show', $reportRequest->id));
        }

        abort(500);
    }

    /**
     * @param ReportRequest $reportRequest
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(ReportRequest $reportRequest): RedirectResponse
    {
        $set = DocumentSet::find($reportRequest->document_set_id);
        $set->documents()->delete();
        $set->delete();
        $reportRequest->delete();
        return redirect()->intended(route('cabinet.report-requests'));
    }
}
