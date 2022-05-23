<?php

namespace Tests\Unit;

use App\Models\Report;
use App\Models\ReportRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class ReportRequestModel extends TestCase
{
    public function testRelationWithCreator()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var ReportRequest $report_request */
        $report_request = ReportRequest::factory(['created_by' => $user->id])->create();

        self::assertEquals($user->id, $report_request->creator->id);
    }

    public function testRelationWithResponses()
    {
        /** @var ReportRequest $report_request */
        $report_request = ReportRequest::factory()->create();
        /** @var Collection|Report[] $responses */
        $responses = Report::factory(['report_request_id' => $report_request->id])->count(5)->create();

        self::assertEquals(
            Arr::sortRecursive($responses->pluck('id')->toArray()),
            Arr::sortRecursive($report_request->responses()->select('id')->pluck('id')->toArray())
        );
    }

    public function testSettingStatus()
    {
        /** @var ReportRequest $report_request */
        $report_request = ReportRequest::factory(['status' => 'PENDING'])->make();
        $report_request->setStatus('invalid value )*@#)!#@)#');
        self::assertFalse($report_request->isDirty('status'));
        $report_request->setStatus('REJECTED');
        self::assertEquals('REJECTED', $report_request->status);
        self::assertTrue($report_request->isDirty('status'));
    }

    public function testCheckCreatorDefaultPrivilegeOnReportRequest()
    {
        /** @var User $requester */
        $requester = User::factory()->createOne();

        /** @var ReportRequest $report_request */
        $report_request = ReportRequest::factory(['created_by' => $requester->id])->createOne();

        $record = DB::table('privileges_on_report_requests')
            ->where(['group_id' => $requester->primitiveGroup->id, 'target_id' => $report_request->id])
            ->first();

        foreach ($record as $column => $value) {
            if (Str::endsWith($column, '_priv')) {
                self::assertTrue($value);
            }
        }
    }
}
