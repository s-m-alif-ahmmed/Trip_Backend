<?php

namespace App\Repositories;

use App\Models\Trip;
use Illuminate\Support\Facades\Auth;

class TripRepository
{
    protected Trip $model;

    public function __construct(Trip $trip)
    {
        $this->model = $trip;
    }

    public function all()
    {
        return $this->model->latest()->get();
    }

    public function forUser(int $userId, int $perPage = 10)
    {
        return $this->model
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Trip $trip, array $data)
    {
        $trip->update($data);
        return $trip;
    }

    public function delete(Trip $trip)
    {
        return $trip->delete();
    }

    public function getBookingsForUser(int $userId, int $perPage = 10)
    {
        return \App\Models\TripBooking::whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })
            ->with('trip')
            ->latest()
            ->paginate($perPage);
    }
}
