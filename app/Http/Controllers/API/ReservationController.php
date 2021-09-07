<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use App\Rules\OpeningTimeRule;
use Carbon\Carbon;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;

class ReservationController extends ApiController
{
    /**
     * ReservationController construct
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'show', 'today']]);
        $this->middleware('isAdmin', ['only' => ['destroy', 'index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index(Request $request)
    {
        $reservations = Reservation::query()->paginate();

        return $this->success($reservations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        if ($reason = $this->validateReservation($request)) return $reason;

        $reservation = Reservation::query()->create($request->all());

        return $this->success($reservation);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function show($id)
    {
        $reservation = Reservation::query()->findOrFail($id);

        if (!$reservation) return $this->notFound('User Not Found');

        return $this->success($reservation);
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
        $this->validateRequest($request);

        if ($reason = $this->validateReservation($request)) return $reason;

        $reservation = Reservation::query()->find($id);

        $reservation->update($request->all());

        return $this->success($reservation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return string
     */
    public function destroy($id)
    {
        $reservation = Reservation::query()->find($id);

        if (!$reservation) return $this->notFound('Reservation Not Found');

        $reservation->delete();

        return $this->success('Reservation Deleted!');
    }

    /**
     * Get reservations for today.
     */
    public function today(Request $request)
    {
        $reservations = Reservation::query()
            ->whereDate('start_date', '<=', Carbon::now()->endOfDay())
            ->paginate();

        return $this->success($reservations);
    }

    /**
     * validate request
     *
     * @param Request $request
     */
    private function validateRequest(Request $request)
    {
        $this->validator($request, [
            'table_id' => 'required|numeric|exists:tables,id',
            'start_date' => ['required', 'date', new OpeningTimeRule()],
            'end_date' => ['required', 'date', 'after:start_date', new OpeningTimeRule()],
            'customer_seat' => 'required|numeric',
        ]);
    }

    /**
     * check if Reservation has valid data
     *
     * @param Request $request
     * @return string|void
     */
    private function validateReservation(Request $request)
    {
        $table = Table::find($request->table_id);

        if ($table->seats < $request->customer_seat) return $this->badRequest('Number Of customer more the table seats');

        $reservation = Reservation::query()
            ->where('table_id', $request->table_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
                $query->orwhereBetween('end_date', [$request->start_date, $request->end_date]);
            })->exists();

        if ($reservation) return $this->badRequest('Can\'t Reservation this table in this time');
    }
}
