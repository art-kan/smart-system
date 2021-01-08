<?php

namespace App\Http\Controllers;

use App\Extra\Privileges\Privilege;
use App\Http\Controllers\traits\CabinetTricks;
use App\Models\Report;
use App\Models\ReportRequest;
use Carbon\Carbon;
use Carbon\Exceptions\UnknownUnitException;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportsArchiveController extends Controller
{
    use CabinetTricks;

    public function index(Request $request)
    {
        $request->validate([
            'start-of' => ['string',
                Rule::in('week', 'month',
                    'quarter', 'half-year', 'year')],
            'sub' => ['string'],
            'from' => ['date'],
            'to' => ['date']
        ]);

        $dateRangeData = $this->determineDateRange($request);

        $reportRequests = $this
            ->reportRequestsBetween($dateRangeData['from'], $dateRangeData['to'])
            ->select(['id', 'title', 'created_at'])->get();

        $reportsCount = Report::whereIn('report_request_id', $reportRequests->pluck('id'))->count();
        $maxReportsCount = DB::table(Privilege::getTableNameByTargetType('report_request'))
            ->whereIn('target_id', $reportRequests->pluck('id'))
            ->where('response_priv', true)
            ->join('group_lists', 'group_lists.group_id',
                Privilege::getTableNameByTargetType('report_requests') . '.group_id')
            ->count();

        /** @var ReportRequest|null $activeReportRequest */
        $activeReportRequest = ReportRequest::find($request->input('report-request-id'));

        return viewMobileOrDesktop('reports-archive', [
            'from' => $dateRangeData['from'],
            'to' => $dateRangeData['to'],
            'dateRangeName' => $dateRangeData['dateRangeName'],
            'reportRequests' => $reportRequests,
            'activeReportRequest' => $activeReportRequest,
            'groupedReports' => isset($activeReportRequest) ? $activeReportRequest->responsesGroupedByDate() : null,
            'reportsCount' => $reportsCount,
            'maxReportsCount' => $maxReportsCount,
        ]);
    }

    private function reportRequestsBetween($from, $to = null)
    {
        $q = \Auth::user()->availableReportRequests(
            Privilege::fromAllowedList('report_request', ['inspect_priv'])
        )->where('created_at', '>=', $from);

        if (isset($to)) $q = $q->where('created_at', '<=', $to);
        return $q->orderBy('created_at', 'DESC');
    }

    private function determineDateRange(Request $request): array
    {
        if (!is_null($request->input('start-of'))) {
            $from = $this->getStartOf($request->input('start-of'));

            if (isset($from)) return [
                'dateRangeName' => 'current ' . $request->input('start-of'),
                'from' => $from,
                'to' => Carbon::now(),
            ];
        }

        if (!is_null($request->input('sub'))) {
            $from = @Carbon::now()->sub($request->input('sub'));

            if (isset($from)) return [
                'dateRangeName' => 'last ' . $request->input('sub'),
                'from' => $from,
                'to' => Carbon::now(),
            ];
        }

        if (!is_null($request->input('from'))) return [
            'dateRangeName' => 'custom range',
            'from' => $request->input('from'),
            'to' => $request->input('to') ?? Carbon::now(),
        ];

        return [
            'dateRangeName' => 'current week',
            'from' => Carbon::now()->startOfWeek(),
            'to' => Carbon::now(),
        ];
    }

    private function getStartOf($unit): ?Carbon
    {
        try {
            return Carbon::now()->startOf($unit);
        } catch (UnknownUnitException $e) {
        }

        if ($unit === 'half-year') {
            $now = Carbon::now();
            if ($now->month < 7) return Carbon::now()->startOfYear();
            return Carbon::parse('July 1');
        }

        return null;
    }
}
