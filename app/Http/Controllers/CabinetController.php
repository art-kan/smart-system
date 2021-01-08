<?php

namespace App\Http\Controllers;

use App\Extra\Privileges\Privilege;
use App\Http\Controllers\traits\CabinetTricks;
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
    use CabinetTricks;

    public function index(): RedirectResponse
    {
        return redirect()->intended(route('cabinet.report-requests.index'));
    }
}
