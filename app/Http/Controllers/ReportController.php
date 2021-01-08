<?php

namespace App\Http\Controllers;

use App\Http\Controllers\traits\CabinetTricks;
use App\Models\DocumentSet;
use App\Models\Report;
use App\Models\ReportRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    use CabinetTricks;

    public function create(ReportRequest $reportRequest)
    {
        return viewMobileOrDesktop('editroom', [
            'title' => 'Создание отчета',
            'noTitleField' => true,
            'useAttachments' => true,
            'actionMethod' => 'POST',
            'actionURL' => route('cabinet.reports.store', $reportRequest->id),
            'hint' => $reportRequest,
        ]);
    }

    public function store(Request $request, ReportRequest $reportRequest)
    {
        $request->validate([
            'body' => ['required', 'string', 'min:1'],
            'attached' => ['array'],
            'attached.*' => ['file', 'max:16384'],
        ]);

        if (Report::where(['report_request_id' => $reportRequest->id, 'created_by' => Auth::id()])->exists()) {
            return response(['message' => 'Создание отчета повторно невозможно'], 422);
        }

        $set_id = $request->file('attached')
            ? DocumentSet::fromFiles($request->file('attached'))->id
            : null;

        $created = Report::create(array_merge(
            $request->only(['body']),
            ['created_by' => Auth::user()->id, 'document_set_id' => $set_id, 'report_request_id' => $reportRequest->id],
        ));

        return redirect()->intended(route('cabinet.reports.show', $created->id));
    }

    /**
     * @param Request $request
     * @param Report $report
     * @return Application|Factory|View
     */
    public function show(Request $request, Report $report)
    {
        return viewMobileOrDesktop('cabinet', [
            'report' => $report,
            'chatData' => $this->fetchChatData($request, $report->created_by),
        ]);
    }

    public function edit(Report $report)
    {
        return viewMobileOrDesktop('editroom', [
            'title' => 'Создание отчета',
            'content' => $report,
            'noTitleField' => true,
            'attachments' => $report->getAttachments(),
            'actionURL' => route('cabinet.reports.update', $report->id),
            'actionMethod' => 'PUT',
            'hint' => $report->respondedTo,
            'additional_input' => ['report_request_id' => $report->report_request_id],
        ]);
    }

    /**
     * @param Request $request
     * @param Report $report
     * @return Application|ResponseFactory|RedirectResponse|Response
     */
    public function update(Request $request, Report $report)
    {
        if (!$report->isEditable())
            return response(['message' => 'Невозможно изменение отчета, если он не отклонен.']);

        $request->validate([
            'body' => ['string', 'min:1'],
            'attach' => ['array'],
            'attach.*' => ['file', 'max:16384'],
            'detach' => ['array'],
            'detach.*' => ['exists:\App\Models\ArchiveDocument,id'],
        ]);

        $report->detachAttachments($request->input('detach'));
        $report->attachUploadedFiles($request->input('attach'));
        $report->fill(array_filter($request->only('body')));
        $report->status = Report::DEFAULT_STATE;
        $report->save();

        return redirect()->intended(route('cabinet.reports.show', $report->id));
    }

    public function changeStatus(Request $request, Report $report): RedirectResponse
    {
        $request->validate(['status' => ['required', 'string', Rule::in(Report::STATUSES)]]);

        if ($report->setStatus($request->input('status'))) {
            $report->save();
            return redirect()->intended(route('cabinet.reports.show', $report->id));
        }

        abort(500);
    }
}
