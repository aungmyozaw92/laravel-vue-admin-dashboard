<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\Admin\Appointment\AppointmentResource;
use App\Http\Resources\Admin\Appointment\AppointmentCollection;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $appointments = Appointment::filter(request()->all())->with(['client'])
                    ->orderBy('id','desc')
                    ->get();
        return new AppointmentCollection($appointments);
    }

    public function getStatusWithCount()
    {
        $cases = AppointmentStatus::cases();

        return collect($cases)->map(function ($status) {
            return [
                'name' => $status->name,
                'value' => $status->value,
                'count' => Appointment::where('status', $status->value)->count(),
                'color' => AppointmentStatus::from($status->value)->color(),
            ];
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAppointmentRequest $request)
    {
         Appointment::create([
            'title' => $request['title'],
            'client_id' => $request['client_id'],
            'start_time' => $request['start_time'],
            'end_time' => $request['end_time'],
            'description' => $request['description'],
            'status' => AppointmentStatus::SCHEDULED,
        ]);

        return response()->json(['message' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment->load(['client']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->title = $request['title'];
        $appointment->client_id = $request['client_id'];
        $appointment->start_time = $request['start_time'];
        $appointment->end_time = $request['end_time'];
        $appointment->description = $request['description'];
        $appointment->save();
        return response()->json(['message' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
