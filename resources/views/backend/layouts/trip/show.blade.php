@extends('backend.app')

@section('title', 'Trip Details')

@section('content')
    {{-- PAGE-HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Trip Details</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('trip.index') }}">Trip Manage</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trip Details</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER END --}}

    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <h4 class="mb-4">General Information</h4>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Traveler:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ optional($trip->user)->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Traveler Email:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ optional($trip->user)->email ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>From:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $trip->departure_city }},
                            {{ $trip->departure_country }} ({{ $trip->departure_country_code }})
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>To:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $trip->arrival_city }},
                            {{ $trip->arrival_country }} ({{ $trip->arrival_country_code }})
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ optional($trip->date)->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Time:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ optional($trip->time)->format('H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Available Weight:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $trip->available_weight }} kg
                        </div>
                    </div>



                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            @php
                                $status = $trip->status;
                                $badgeClass = 'badge bg-secondary';
                                if ($status instanceof \App\Enums\TripStatus) {
                                    $badgeClass = match($status) {
                                        \App\Enums\TripStatus::PENDING => 'badge bg-warning',
                                        \App\Enums\TripStatus::ACTIVE => 'badge bg-success',
                                        \App\Enums\TripStatus::DEACTIVATED => 'badge bg-secondary',
                                    };
                                }
                            @endphp
                            <span class="{{ $badgeClass }}">
                                {{ $status instanceof \App\Enums\TripStatus ? $status->label() : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('trip.index') }}" class="btn btn-secondary">Back to Trip List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

