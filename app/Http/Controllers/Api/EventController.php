<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $query = $this->loadRelationships(Event::query()); 
       
        return EventResource::collection(
            $query->latest()->paginate()->withQueryString()
        );
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $event = Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time'
            ]),
            'user_id' => $request->user()->id
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // load the user and attendees relationships
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        /**
         * Gate::denies is a method that checks if the user is not authorized to update the event.
         */
        // if(Gate::denies('update-event', $event )) {
        //     abort(403, 'You are not authorized to update this event.');
        // }

        /** 
         * Using Policy instead of Gate::denies
         * Gate::authorize is a method that authorizes the user to update the event.
         */
        Gate::authorize('update', $event);

        // $this->authorize('update-event', $event);

        $event->update(
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time'
            ])
        );

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        /**
         * Using Policy instead of Gate::denies
         * Gate::authorize is a method that authorizes the user to delete the event.
         */
        Gate::authorize('delete', $event);

        $event->delete();

        // return response(status: 204);
        
        return response()->json([
            "message" => "Event Deleted Successfully"
        ]);
    }
}
