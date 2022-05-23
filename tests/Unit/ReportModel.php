<?php

namespace Tests\Unit;

use App\Models\Report;
use App\Models\ReportRequest;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class ReportModel extends TestCase
{
    public function testRelationWithCreator()
    {
        /** @var User $creator */
        $creator = User::factory()->createOne();

        /** @var Report $report */
        $report = Report::factory(['created_by' => $creator->id])->createOne();

        self::assertEquals($creator->id, $report->creator->id);
    }

    public function testRelationWithReportRequest()
    {
        /** @var ReportRequest $report_request */
        $report_request = ReportRequest::factory()->createOne();

        /** @var Report $report */
        $report = Report::factory()->createOne();

        self::assertEquals($report_request->id, $report->respondedTo->id);
    }

    public function testSettingStatus()
    {
        /** @var Report $report */
        $report = Report::factory()->makeOne();

        $report->setStatus('invalid value ()*#$#*(@&$');
        self::assertFalse($report->isDirty());

        $report->setStatus(Report::STATUSES[0]);
        self::assertTrue($report->isDirty());
    }
}
