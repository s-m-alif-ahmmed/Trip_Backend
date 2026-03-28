@extends('backend.app')

@section('title', 'Trip Edit')

@section('content')
    {{-- PAGE-HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Trip Form</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('trip.index') }}">Trip</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Trip</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER END --}}

    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <form method="post" action="{{ route('trip.update', ['id' => $trip->id]) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departure_city" class="form-label">Departure City:</label>
                                    <input
                                        type="text"
                                        class="form-control @error('departure_city') is-invalid @enderror"
                                        name="departure_city"
                                        id="departure_city"
                                        placeholder="Enter departure city"
                                        value="{{ old('departure_city', $trip->departure_city) }}"
                                    >
                                    @error('departure_city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="departure_country" class="form-label">Departure Country:</label>
                                    <input
                                        type="text"
                                        class="form-control @error('departure_country') is-invalid @enderror"
                                        name="departure_country"
                                        id="departure_country"
                                        placeholder="Enter departure country"
                                        value="{{ old('departure_country', $trip->departure_country) }}"
                                    >
                                    @error('departure_country')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="departure_country_code" class="form-label">Departure Country Code:</label>
                                    <input
                                        type="text"
                                        class="form-control @error('departure_country_code') is-invalid @enderror"
                                        name="departure_country_code"
                                        id="departure_country_code"
                                        placeholder="Code"
                                        value="{{ old('departure_country_code', $trip->departure_country_code) }}"
                                    >
                                    @error('departure_country_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="arrival_city" class="form-label">Arrival City:</label>
                                    <input
                                        type="text"
                                        class="form-control @error('arrival_city') is-invalid @enderror"
                                        name="arrival_city"
                                        id="arrival_city"
                                        placeholder="Enter arrival city"
                                        value="{{ old('arrival_city', $trip->arrival_city) }}"
                                    >
                                    @error('arrival_city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="arrival_country" class="form-label">Arrival Country:</label>
                                    <input
                                        type="text"
                                        class="form-control @error('arrival_country') is-invalid @enderror"
                                        name="arrival_country"
                                        id="arrival_country"
                                        placeholder="Enter arrival country"
                                        value="{{ old('arrival_country', $trip->arrival_country) }}"
                                    >
                                    @error('arrival_country')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="arrival_country_code" class="form-label">Arrival Country Code:</label>
                                    <input
                                        type="text"
                                        class="form-control @error('arrival_country_code') is-invalid @enderror"
                                        name="arrival_country_code"
                                        id="arrival_country_code"
                                        placeholder="Code"
                                        value="{{ old('arrival_country_code', $trip->arrival_country_code) }}"
                                    >
                                    @error('arrival_country_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date" class="form-label">Date:</label>
                                    <input
                                        type="date"
                                        class="form-control @error('date') is-invalid @enderror"
                                        name="date"
                                        id="date"
                                        value="{{ old('date', optional($trip->date)->format('Y-m-d')) }}"
                                    >
                                    @error('date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="time" class="form-label">Time:</label>
                                    <input
                                        type="time"
                                        class="form-control @error('time') is-invalid @enderror"
                                        name="time"
                                        id="time"
                                        value="{{ old('time', optional($trip->time)->format('H:i')) }}"
                                    >
                                    @error('time')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="available_weight" class="form-label">Available Weight (kg):</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-control @error('available_weight') is-invalid @enderror"
                                        name="available_weight"
                                        id="available_weight"
                                        value="{{ old('available_weight', $trip->available_weight) }}"
                                    >
                                    @error('available_weight')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">

                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status:</label>
                                    <select
                                        name="status"
                                        id="status"
                                        class="form-select @error('status') is-invalid @enderror"
                                    >
                                        @foreach($statuses as $status)
                                            <option
                                                value="{{ $status->value }}"
                                                {{ old('status', $trip->status?->value) === $status->value ? 'selected' : '' }}
                                            >
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('trip.index') }}" class="btn btn-danger me-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

