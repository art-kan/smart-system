<?php

namespace App\Http\Controllers;

use App\Extra\Privileges\Privilege;
use App\Models\ArchiveDocument;
use App\Models\DocumentSet;
use App\Models\Report;
use App\Models\ReportRequest;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Comment\Doc;
use Throwable;

class ReportRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = Auth::user()->availableReportRequests(
            Privilege::fromAllowedList('report_requests', ['inspect_priv']));
        return response($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return viewMobileOrDesktop('editroom', [
            'title' => 'Создание запроса',
            'placeholder' => 'Напишите описание для запроса, чтобы администрация школы могла выполнить необходимые вам действия.',
            'useAttachments' => true,
            'actionURL' => route('cabinet.report-request.store'),
            'redirectURL' => route('cabinet.report-request'), // TODO: IT SHOULD BE DETERMINED AFTER ACTUAL STORING
            'actionMethod' => 'POST',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
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

        $insertedId = ReportRequest::insertGetId(array_merge(
            $request->only(['title', 'body']),
            ['created_by' => Auth::user()->id, 'document_set_id' => $set_id],
        ));

        return redirect()->intended(route('cabinet.report-request', $insertedId));
    }

    /**
     * Display the specified resource.
     *
     * @param ReportRequest $reportRequest
     * @return Application|Factory|View
     */
    public static function show(Request $request, ReportRequest $reportRequest)
    {
        $reportsStatus = Report::normalizeStatus($request->query('report-status'));
        $activeReportRequests = $this->getActiveReportRequests();

        abort_if(is_null($reportRequest) && $activeReportRequests->isEmpty(), 404);

        $reportRequest = $reportRequest ?? ReportRequest::find($activeReportRequests->get(0)->id);

        return viewMobileOrDesktop('cabinet', [
            'reportRequests' => $activeReportRequests,
            'activeReportRequest' => $reportRequest,
            'reportStatus' => $reportsStatus,
            'reportsGroupedByDate' => $reportRequest->responsesGroupedByDate($reportsStatus),
            'reporters' => $reportRequest->responders()->select(['id', 'name'])->get(),
            'chatData' => $this->fetchChatData($request),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ReportRequest $reportRequest
     * @return Response
     */
    public function edit(ReportRequest $reportRequest): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ReportRequest $reportRequest
     * @return RedirectResponse
     */
    public function update(Request $request, ReportRequest $reportRequest): RedirectResponse
    {
        $request->validate([
            'status' => ['string'],
            'title' => ['string', 'min:1', 'max:255'],
            'body' => ['string', 'min:1'],
            'attached' => ['array'],
            'attached.*' => ['file', 'max:16384'],
            'detached' => ['array'],
            'detached.*' => ['integer', 'exists:\App\Models\ArchiveDocument,id'],
        ]);

        $update = array_filter($request->only(['title', 'body']));

        if ($request->input('detached') && !empty($request->input('detached') && $reportRequest->document_set_id)) {
            $reportRequest->documentSet->documents()->detach($request->input('detached'));
            ArchiveDocument::deleteWithFiles($request->input('detached'));
        }

        if ($request->file('attached') && !empty($request->file('attached'))) {
            if (is_null($reportRequest->document_set_id)) {
                $update['document_set_id'] = DocumentSet::fromFiles($request->file('attached'))->id;
            } else {
                $reportRequest->documentSet->documents()
                    ->attach(ArchiveDocument::fromFiles($request->file('attached'))->pluck('id'));
            }
        }

        if (in_array($request->input('status'), ReportRequest::STATUSES)) {
            $update['status'] = $request->input('status');
        }

        $reportRequest->update($update);

        return redirect()->route('cabinet.report-request', $reportRequest->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ReportRequest $reportRequest
     * @return Response
     * @throws Exception
     */
    public function destroy(ReportRequest $reportRequest): Response
    {
        $set = DocumentSet::find($reportRequest->document_set_id);
        $set->documents()->delete();
        $set->delete();
        $reportRequest->delete();
    }
}
