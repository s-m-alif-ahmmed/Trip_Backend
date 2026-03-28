<?php

namespace App\Http\Controllers\API\Booking;

use App\Enums\TripBookingStatus;
use App\Http\Controllers\Controller;
use App\Models\PriceManage;
use App\Models\Trip;
use App\Models\TripBooking;
use App\Traits\AllTraits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;

class TripBookingController extends Controller
{
    use AllTraits;

    /**
     * Create a booking and initiate Stripe Checkout Session.
     * No authentication required.
     */
    public function booking(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'trip_id'               => 'required|exists:trips,id',
                'date'                  => 'required|date',
                'time'                  => 'required',
                'full_name'             => 'required|string|max:255',
                'email'                 => 'required|email|max:255',
                'phone_number'          => 'required|string|max:255',
                'address'               => 'required|string',
                'pickup_address'        => 'required_if:pickup_service_status,true|nullable|string',
                'weight'                => 'required|integer|min:0',
                'pickup_service_status' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed', 422, $validator->errors()->toArray());
            }

            // Check if trip is active and date is available
            $trip = Trip::findOrFail($request->trip_id);

            if ($trip->status !== \App\Enums\TripStatus::ACTIVE) {
                return $this->error('This trip is not active.', 400);
            }

            if ($trip->date->lt(now()->startOfDay())) {
                return $this->error('This trip date has already passed.', 400);
            }

            // Check weight capacity
            $requestedWeight = (int)$request->weight;
            $alreadyBookedWeight = TripBooking::where('trip_id', $request->trip_id)
                ->where('status', '!=', TripBookingStatus::CANCELLED->value)
                ->sum('weight');

            $availableCapacity = $trip->available_weight - $alreadyBookedWeight;

            if ($requestedWeight > $availableCapacity) {
                return $this->error("Not enough weight capacity available. Only {$availableCapacity} kg remaining.", 422, [
                    'available_capacity' => $availableCapacity,
                    'requested_weight'   => $requestedWeight
                ]);
            }

            // Get current prices from price_manages table
            $priceManage = PriceManage::first();

            if (!$priceManage) {
                return $this->error('Price configuration not found. Please contact admin.', 500);
            }

            $weight           = (int) $request->weight;
            $weightPerKgPrice = $priceManage->weight_per_kg_price;
            $serviceFee       = $priceManage->service_fee;
            $pickupFee        = $request->pickup_service_status ? $priceManage->pickup_fee : 0;
            $pickupAddress    = $request->pickup_service_status ? $request->pickup_address : null;

            // Calculate total
            $weightCost = $weight * $weightPerKgPrice;

            // Minimum weightCost is 10 euro
            if ($weightCost < 10) {
                $weightCost = 10;
            }

            $total      = $weightCost + $serviceFee + $pickupFee;

            return DB::transaction(function () use ($request, $trip, $weight, $weightPerKgPrice, $serviceFee, $pickupFee, $pickupAddress, $total) {
                // Save booking with unpaid status
                $booking = TripBooking::create([
                    'trip_id'               => $request->trip_id,
                    'date'                  => $request->date,
                    'time'                  => $request->time,
                    'full_name'             => $request->full_name,
                    'email'                 => $request->email,
                    'phone_number'          => $request->phone_number,
                    'address'               => $request->address,
                    'pickup_address'        => $pickupAddress,
                    'weight'                => $weight,
                    'weight_per_kg_price'   => $weightPerKgPrice,
                    'service_fee'           => $serviceFee,
                    'pickup_fee'            => $pickupFee,
                    'total'                 => $total,
                    'pickup_service_status' => $request->pickup_service_status,
                    'is_paid'               => false,
                ]);

                // Attach user if authenticated
                if (auth('sanctum')->check()) {
                    $booking->users()->attach(auth('sanctum')->id());
                }

                // Create Stripe Checkout Session
                $stripe = new StripeClient(config('services.stripe.secret'));

                $session = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'customer_email'       => $request->email,
                    'line_items'           => [
                        [
                            'price_data' => [
                                'currency'     => 'eur',
                                'unit_amount'  => (int) round($total * 100),
                                'product_data' => [
                                    'name'        => 'Trip Booking #' . $booking->id,
                                    'description' => 'Booking for trip from ' . $trip->departure_city . ' to ' . $trip->arrival_city,
                                ],
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'mode'        => 'payment',
                    'success_url' => route('booking.payment-confirmation') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url'  => config('app.frontend_url', config('app.url')) . '/booking/cancel?booking_id=' . $booking->id,
                    'metadata'    => [
                        'booking_id' => $booking->id,
                    ],
                ]);

                // Store the Stripe session ID as transaction_id
                $booking->update(['transaction_id' => $session->id]);

                return $this->success('Booking created. Redirect to payment URL to complete.', [
                    'booking'     => $booking,
                    'payment_url' => $session->url,
                ], 201);
            });

        } catch (\Exception $exception) {
            return $this->error('Something went wrong: ' . $exception->getMessage(), 500);
        }
    }

    /**
     * Stripe redirects here after successful payment.
     * Verifies payment via session_id and marks booking as paid.
     * Redirects to frontend confirmation page.
     */
    public function paymentConfirmation(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $sessionId = $request->query('session_id');

            if (!$sessionId) {
                return $this->error('Session ID is required.', 422);
            }

            return DB::transaction(function () use ($sessionId) {
                // Retrieve Checkout Session from Stripe
                $stripe  = new StripeClient(config('services.stripe.secret'));
                $session = $stripe->checkout->sessions->retrieve($sessionId);

                if ($session->payment_status !== 'paid') {
                    return $this->error('Payment not completed. Status: ' . $session->payment_status, 400);
                }

                // Find booking by transaction_id (session ID)
                $booking = TripBooking::where('transaction_id', $sessionId)->first();

                if (!$booking) {
                    return $this->error('Booking not found for this payment.', 404);
                }

                if (!$booking->is_paid) {
                    $booking->update([
                        'is_paid' => true,
                    ]);
                }

                $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
                // $frontendUrl = 'http://localhost:3000';
                $redirectUrl = $frontendUrl . '/booking/payment-confirmation';

                return redirect($redirectUrl);
            });

        } catch (\Exception $exception) {
            return $this->error('Something went wrong: ' . $exception->getMessage(), 500);
        }
    }
}
