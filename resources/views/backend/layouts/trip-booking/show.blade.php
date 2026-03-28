@extends('backend.app')

@section('title', 'Trip Booking Details')

@section('content')
    {{-- PAGE-HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Trip Booking Details</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('trip-booking.index') }}">Trip Booking Manage</a></li>
                <li class="breadcrumb-item active" aria-current="page">Booking Details</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER END --}}

    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-4">Passenger Information</h4>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Full Name:</strong></div>
                                <div class="col-md-8">{{ $booking->full_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Email:</strong></div>
                                <div class="col-md-8">{{ $booking->email }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Phone:</strong></div>
                                <div class="col-md-8">{{ $booking->phone_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Address:</strong></div>
                                <div class="col-md-8">{{ $booking->address }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4 class="mb-4">Trip & Transporter Information</h4>
                            @if($booking->trip)
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Transporter:</strong></div>
                                    <div class="col-md-8">{{ optional($booking->trip->user)->name ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Transporter Email:</strong></div>
                                    <div class="col-md-8">{{ optional($booking->trip->user)->email ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Transporter Phone:</strong></div>
                                    <div class="col-md-8">{{ optional($booking->trip->user)->number ?? 'N/A' }}</div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Departure:</strong></div>
                                    <div class="col-md-8">
                                        {{ $booking->trip->departure_city }}, {{ $booking->trip->departure_country }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Destination:</strong></div>
                                    <div class="col-md-8">
                                        {{ $booking->trip->arrival_city }}, {{ $booking->trip->arrival_country }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Travel Date:</strong></div>
                                    <div class="col-md-8">{{ optional($booking->trip->date)->format('Y-m-d') }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Departure Time:</strong></div>
                                    <div class="col-md-8">{{ $booking->trip->time ?? 'N/A' }}</div>
                                </div>
                            @else
                                <div class="text-danger">Trip information not available.</div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-4">Payment Breakdown</h4>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Charged Weight:</strong></div>
                                <div class="col-md-8">{{ $booking->weight }} kg</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Price per Kg:</strong></div>
                                <div class="col-md-8">€{{ number_format($booking->weight_per_kg_price, 2) }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Transport Amount:</strong></div>
                                <div class="col-md-8">€{{ number_format($booking->weight * $booking->weight_per_kg_price, 2) }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Service Fee:</strong></div>
                                <div class="col-md-8">€{{ number_format($booking->service_fee, 2) }}</div>
                            </div>
                            @if($booking->pickup_service_status)
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Pickup Fee:</strong></div>
                                    <div class="col-md-8">€{{ number_format($booking->pickup_fee, 2) }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Pickup Address:</strong></div>
                                    <div class="col-md-8">{{ $booking->pickup_address }}</div>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Total Paid:</strong></div>
                                <div class="col-md-8 text-primary"><strong>€{{ number_format($booking->total, 2) }}</strong></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4 class="mb-4">Status & Payment</h4>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Payment Status:</strong></div>
                                <div class="col-md-8">
                                    <span class="badge bg-{{ $booking->is_paid ? 'success' : 'danger' }}">
                                        {{ $booking->is_paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Transaction ID:</strong></div>
                                <div class="col-md-8">{{ $booking->transaction_id ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Order Status:</strong></div>
                                <div class="col-md-8">
                                    @php
                                        $status = $booking->status;
                                        $badgeClass = 'badge bg-secondary';
                                        if ($status instanceof \App\Enums\TripBookingStatus) {
                                            $badgeClass = match($status) {
                                                \App\Enums\TripBookingStatus::PENDING => 'badge bg-warning',
                                                \App\Enums\TripBookingStatus::COMPLETED => 'badge bg-success',
                                                \App\Enums\TripBookingStatus::CANCELLED => 'badge bg-danger',
                                            };
                                        }
                                    @endphp
                                    <span class="{{ $badgeClass }}">
                                        {{ $status instanceof \App\Enums\TripBookingStatus ? $status->label() : ($status ?? 'N/A') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Booking Date:</strong></div>
                                <div class="col-md-8">{{ $booking->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('trip-booking.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
