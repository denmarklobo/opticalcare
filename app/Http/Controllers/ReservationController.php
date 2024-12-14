<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExampleEmail;


class ReservationController extends Controller
{

    public function index()
    {
        try {
            $totalReservations = Reservation::count(); 
            return response()->json($totalReservations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching reservations: ' . $e->getMessage()], 500);
        }
    }

    public function accepted()
    {
        $reservations = Reservation::with('patient', 'product')
            ->where('status', 'accepted')
            ->get();

        return response()->json($reservations);
    }

    public function pending()
    {
        $reservations = Reservation::with('patient', 'product')
            ->where('status', 'pending')
            ->get();

        return response()->json($reservations);
    }

    public function pickedUp()
    {
        $reservations = Reservation::with('patient', 'product')
            ->where('status', 'picked_up')
            ->get();

        return response()->json($reservations);
    }

    public function reserve(Request $request, $userId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $existingReservation = Reservation::where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        if ($existingReservation) {
            return response()->json(['error' => 'User already has a pending reservation'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'status' => 'pending',
        ]);

        return response()->json($reservation, 201);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data, including the color
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'color' => 'nullable|string',  // Add validation for color
            'quantity' => 'required|integer|min:1'

        ]);

        // Create a new reservation with the validated data
        $reservation = Reservation::create([
            'user_id' => $validatedData['user_id'],
            'product_id' => $validatedData['product_id'],
            'product_name' => $validatedData['product_name'],
            'color' => $validatedData['color'] ?? null,  // Store the color
            'quantity' => $validatedData['quantity'],
            'status' => 'pending',
        ]);

        // Return a JSON response with the created reservation
        return response()->json($reservation, 201);
    }

    public function adminReserve(Request $request)
    {
        // Validate the incoming request data, including the color
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'color' => 'nullable|string',  // Add validation for color
            'quantity' => 'required|integer|min:1'

        ]);

        // Create a new reservation with the validated data
        $reservation = Reservation::create([
            'user_id' => $validatedData['user_id'],
            'product_id' => $validatedData['product_id'],
            'color' => $validatedData['color'] ?? null,  // Store the color
            'quantity' => $validatedData['quantity'],
            'status' => 'accepted',
        ]);

        // Return a JSON response with the created reservation
        return response()->json($reservation, 201);
    }

    /**
     * Accept a reservation.
     *
     * @param  int  
     * @return \Illuminate\Http\Response
     */
    public function accept($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->status = 'accepted';
            $reservation->save();

            // Send the email with the reservation details
            Mail::to($reservation->patient->email)->send(new ExampleEmail($reservation));

            return response()->json([
                'reservation' => $reservation,
                'message' => 'Reservation accepted and email sent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to accept reservation: ' . $e->getMessage()], 500);
        }
    }



    
    /**
     * Decline a reservation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function decline($id)
    {

        $reservation = Reservation::findOrFail($id);

        $reservation->status = 'declined';
        $reservation->save();

        return response()->json($reservation);
    }

    public function reservationStatusCounts()
    {
        try {
            $pending = Reservation::where('status', 'pending')->count();
            $accepted = Reservation::where('status', 'accepted')->count();
            $pickedUp = Reservation::where('status', 'picked_up')->count();
            $declined = Reservation::where('status', 'declined')->count();

            return response()->json([
                'pending' => $pending,
                'accepted' => $accepted,
                'picked_up' => $pickedUp,
                'declined' => $declined,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching reservation status counts: ' . $e->getMessage()], 500);
        }
    }

    public function accountsCreatedPerMonth(Request $request)
    {
        // Validate the year parameter
         $request->validate([
            'year' => 'required|integer|min:2000|max:2030',
        ]);
        // Get the year from the request
        $year = $request->query('year');

        // Fetch accounts created per month for the specified year
        $accountsPerMonth = Patient::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year) // Filter by year
            ->groupBy('month')
            ->pluck('count', 'month');

        // Define month names
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        // Prepare the response data
        $data = [];
        foreach ($months as $num => $name) {
            $data[strtolower($name)] = $accountsPerMonth->get($num, 0); // Default to 0 if no accounts
        }

        return response()->json($data);
    }


      public function getUserReservations($userId)
    {
        $reservations = Reservation::with('product', 'patient')
        ->where('user_id', $userId)
        ->get();
        
        return response()->json($reservations);
    }

    public function cancelReservation($reservationId)
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        $reservation->delete();
        return response()->json(['message' => 'Reservation cancelled successfully']);
    }

    public function pickUp($id)
    {
        Log::info("Attempting to pick up reservation with ID: $id");

        $reservation = Reservation::findOrFail($id);

        if ($reservation->status === 'picked_up') {
            return response()->json(['message' => 'Reservation is already picked up'], 400);
        }

        // Update reservation status and picked up date
        $reservation->status = 'picked_up';
        $reservation->picked_up_date = now(); 
        $reservation->save();

        // Fetch the associated product
        $product = Product::findOrFail($reservation->product_id);

        // Check if the total product quantity is greater than or equal to the reserved quantity
        if ($product->quantity >= $reservation->quantity) {
            // Deduct the reserved quantity from the total product quantity
            $product->quantity -= $reservation->quantity;

            // Decode the color_stock JSON field
            $colorStocks = json_decode($product->color_stock, true);

            // Flag to check if the color stock was updated
            $colorUpdated = false;

            // Loop through the color stock and update the specific color
            foreach ($colorStocks as &$colorStock) {
                if ($colorStock['color'] === $reservation->color) {
                    // Deduct the reserved quantity from the color-specific stock
                    $colorStock['stock'] -= $reservation->quantity;
                    if ($colorStock['stock'] < 0) {
                        $colorStock['stock'] = 0; // Ensure stock does not go below zero
                    }
                    $colorUpdated = true;
                    break;
                }
            }

            // If the color stock was updated, save the changes
            if ($colorUpdated) {
                // Encode the updated color_stock back to JSON
                $product->color_stock = json_encode($colorStocks);
                $product->save();
            } else {
                return response()->json(['message' => 'Color stock not found'], 400);
            }
        } else {
            return response()->json(['message' => 'Not enough stock available for the reserved quantity'], 400);
        }

        return response()->json(['message' => 'Reservation marked as picked up and product stock updated', 'reservation' => $reservation], 200);
    }

    public function sendEmail($id)
    {
        try {
            // Fetch the reservation by ID
            $reservation = Reservation::findOrFail($id);

            // Send the email using the ReservationEmail mailable
            Mail::to($reservation->patient->email)->send(new ReservationEmail($reservation));

            return response()->json([
                'message' => 'Email sent successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkReservation($productId) {
        $hasPendingReservation = Reservation::where('product_id', $productId)
                                            ->where('status', 'pending')
                                            ->exists();

        return response()->json(['hasPendingReservation' => $hasPendingReservation]);
    }

}
