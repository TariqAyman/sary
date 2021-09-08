<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;

class TableController extends ApiController
{
    /**
     * TableController construct
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
        $this->middleware('isAdmin', ['only' => ['store', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $tables = Table::query()->get();

        return $this->success($tables);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $this->validator($request, [
            'number' => 'required|numeric|unique:tables',
            'seats' => 'required|numeric|min:1|max:12',
        ]);

        $user = Table::query()->create($request->all());

        return $this->success($user);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function show($id)
    {
        $table = Table::query()->findOrFail($id);

        if (!$table) return $this->notFound('Table Not Found');

        return $this->success($table);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return string
     */
    public function update(Request $request, $id)
    {
        $table = Table::query()->find($id);

        if (!$table) return $this->notFound('Table Not Found');

        $this->validator($request, [
            'number' => "numeric|unique:tables,number,$id",
            'seats' => 'numeric|min:1|max:12',
        ]);

        $table->update($request->all());

        return $this->success($table->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return string
     */
    public function destroy($id)
    {
        $table = Table::query()->find($id);

        if (!$table) return $this->notFound('Table Not Found');

        $reservations = Reservation::query()->where('table_id', $table->id)
            ->whereDate('start_date', '>=', Carbon::now())->exists();

        if ($reservations) return $this->badRequest('Can\'t delete table has reservations');

        $table->delete();

        return $this->success('Table Deleted!');
    }

    /**
     *
     *
     * @param Request $request
     */
    private function tableAvailability(Request $request)
    {

    }
}
