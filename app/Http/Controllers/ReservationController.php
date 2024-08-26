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
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $reservation = Reservation::create([
            'user_id' => $validatedData['user_id'],
            'product_id' => $validatedData['product_id'],
            'product_name' => $validatedData['product_name'],
            'status' => 'pending',
        ]);

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

        $reservation = Reservation::findOrFail($id);

        $reservation->status = 'accepted';
        $reservation->save();

        return response()->json($reservation);
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

    public function accountsCreatedPerMonth()
   {
        $accountsPerMonth = Patient::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $data = [];
        foreach ($months as $num => $name) {
            $data[strtolower($name)] = $accountsPerMonth->get($num, 0);
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

        $reservation->status = 'picked_up';
        $reservation->picked_up_date = now(); 
        $reservation->save();

        $product = Product::findOrFail($reservation->product_id);
        if ($product->quantity > 0) {
            $product->quantity -= 1;
            $product->save();
        } else {
            return response()->json(['message' => 'Product is out of stock'], 400);
        }

        return response()->json(['message' => 'Reservation marked as picked up and product stock updated', 'reservation' => $reservation], 200);
    }
}
