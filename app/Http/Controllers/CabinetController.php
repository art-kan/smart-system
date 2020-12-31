<?php

namespace App\Http\Controllers;

use App\Extra\Privileges\Privilege;
use App\Models\ChatMessage;
use App\Models\DocumentSet;
use App\Models\Report;
use App\Models\ReportRequest;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CabinetController extends Controller
{


    /**
     * @return \Illuminate\Support\Collection|ReportRequest[]
     */
    private function getActiveReportRequests()
    {
        $required_priv = Auth::user()->role === 'Raino' ? 'inspect_priv' : 'response_priv';

        return Auth::user()
            ->availableReportRequests(Privilege::fromAllowedList('report_request', [$required_priv]))
            ->where(['status' => 'active'])
            ->orderBy('created_at', 'DESC')
            ->select(['id', 'title', 'created_at'])
            ->get();
    }

    private function fetchChatData(Request $request, int $userChatWithId = null): ?array
    {
        $userChatWith = is_null($userChatWithId) ? $this->getUserChatWith($request) : User::find($userChatWithId);
        if (is_null($userChatWith)) return null;

        $chatHistory = $this->fetchChatHistory($request, $userChatWith);
        $chatId = Auth::user()->chatWith($userChatWith)->id;

        return ['chatWith' => $userChatWith, 'chatMessages' => $chatHistory, 'chatId' => $chatId];
    }

    private function getUserChatWith(Request $request)
    {
        return Auth::user()->role == 'Raino'
            ? User::find((int)$request->query('chat-with'))
            : User::whereRole('raino')->first();
    }

    /**
     * @param Request $request
     * @param User $user_chat_with
     * @return Collection|ChatMessage[]
     */
    private function fetchChatHistory(Request $request, User $user_chat_with)
    {
        $before = $request->query('before');

        if ($user_chat_with && Auth::user()->id != $user_chat_with->id) {
            return Auth::user()
                ->chatWith($user_chat_with)
                ->messageHistoryGroupedByDate($before);
        }

        return null;
    }

    public function getReport(Request $request, Report $report)
    {
        return viewMobileOrDesktop('cabinet', [
            'report' => $report,
            'chatData' => $this->fetchChatData($request, $report->created_by),
        ]);
    }

    public function getReportRequestEditor(ReportRequest $reportRequest = null)
    {
        return viewMobileOrDesktop('editroom', [
            'title' => $reportRequest ? 'Редактирование запроса' : 'Создание запроса',
            'content' => $reportRequest,
            'attachments' => $reportRequest ? $reportRequest->getAttachments() : null,
            'placeholder' => 'Напишите описание для запроса, чтобы администрация школы могла выполнить необходимые вам действия.',
            'useAttachments' => true,
            'actionURL' => $reportRequest
                ? route('cabinet.report-request.edit', $reportRequest->id)
                : route('cabinet.report-request.create'),
            'redirectURL' => $reportRequest
                ? route('cabinet.report-request', $reportRequest->id)
                : route('cabinet.report-request'),
            'actionMethod' => $reportRequest ? 'PUT' : 'POST',
        ]);
    }

    public function createReportRequest(Request $request): Response
    {
        return (new ReportRequestController())->store($request);
    }

    public function updateReportRequest(Request $request, ReportRequest $reportRequest): RedirectResponse
    {
        return (new ReportRequestController())->update($request, $reportRequest);
    }

    public function getReportEditor(ReportRequest $reportRequest)
    {
        return viewMobileOrDesktop('editroom', [
            'title' => 'Создание отчета',
            'noTitleInput' => true,
            'useAttachments' => true,
            'actionURL' => route('cabinet.report.create', $reportRequest->id),
            'redirectURL' => route('home'),
            'actionMethod' => 'POST',
            'hint' => $reportRequest,
        ]);
    }

    /**
     * @param Request $request
     * @param ReportRequest $reportRequest
     * @return Application|ResponseFactory|Response
     * @throws Exception
     */
    public function createReport(Request $request, ReportRequest $reportRequest)
    {
        $request->validate([
            'body' => ['required', 'string', 'min:1'],
            'attached' => ['array'],
            'attached.*' => ['file', 'max:16384'],
        ]);

        Report::where(['created_by' => Auth::user()->id, 'report_request_id' => $reportRequest->id])->delete();

        $set_id = $request->file('attached')
            ? DocumentSet::fromFiles($request->file('attached'))->id
            : null;

        $created = Report::create(array_merge(
            $request->only(['body']),
            ['created_by' => Auth::user()->id, 'document_set_id' => $set_id, 'report_request_id' => $reportRequest->id],
        ));

        return response(['created' => $created]);
    }

    public function updateReport(Request $request, Report $report): RedirectResponse
    {
        $request->validate([
            'status' => ['string', 'min:1']
        ]);

        $update = [];

        if (in_array($request->input('status'), Report::STATUSES)) {
            $update['status'] = $request->input('status');
        }

        $report->update($update);
        return redirect()->route('cabinet.report-request', $report->report_request_id);
    }
}
