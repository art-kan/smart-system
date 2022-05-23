<?php

namespace App\Http\Controllers;

use App\Extra\Privileges\Privilege;
use App\Models\Group;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $groups = Auth::user()->availableGroups(
            Privilege::fromAllowedList('group', ['inspect_priv']))->get();

        return response($groups);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['string', 'min:1'],
            'password' => ['string', 'min:1', 'max:255'],
        ]);

        // TODO: HASH PASSWORD
        $group = Group::create(array_merge(
            ['organization_id' => Auth::user()->organization_id],
            $request->only(['name', 'description', 'password'])
        ));

        return response(['created' => $group ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Group $group
     * @return Response
     */
    public function show(Group $group): Response
    {
        return response($group);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Group $group
     * @return Response
     */
    public function edit(Group $group): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Group $group
     * @return Response
     */
    public function update(Request $request, Group $group): Response
    {
        $request->validate([
            'name' => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'min:1'],
            'password' => ['string', 'min:1', 'max:255'],
        ]);

        // TODO: HASH PASSWORD
        $group->update($request->only(['name', 'description', 'password']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Group $group
     * @return Response
     * @throws Exception
     */
    public function destroy(Group $group): Response
    {
        foreach (Privilege::PRIVILEGE_TABLES as $table)
        {
            if ($table === Privilege::getTableNameByTargetType('group')) {
                DB::table($table)
                    ->where(['group_id' => $group->id])
                    ->orWhere(['target_id' => $group->id])
                    ->delete();
            } else {
                DB::table($table)->where(['group_id' => $group->id])->delete();
            }
        }

        $group->delete();

        return response(['deleted' => $group]);
    }
}
