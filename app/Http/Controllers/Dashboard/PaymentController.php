<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Resources\Dashboard\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    //payment create
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accountName' => 'required|string|max:255',
            'accountNumber' => 'required',
            'accountType' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $payment = Payment::create([
                'account_name' => $request->accountName,
                'account_number' => $request->accountNumber,
                'account_type' => $request->accountType
            ]);

            return $this->successResponse('Payment created sucessfully', new PaymentResource($payment), 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Payment creation failed:' . $e->getMessage(), 500);
        }
    }

    //payment list
    public function index()
    {
        $payment = Payment::all();

        return $this->successResponse('Payment retrieved sucessfully', PaymentResource::collection($payment), 200);
    }

    //payment delete
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        $payment->delete();

        return $this->successResponse('Payment deleted successfully', '', 204);
    }

    //payment update
    public function update(Request $request, $id)
    {
        $paymentData = Payment::findOrFail($id);

        if (!$paymentData) {
            return $this->errorResponse('Payment not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'accountName' => 'required|string|max:255',
            'accountNumber' => 'required',
            'accountType' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $payment = Payment::update([
                'account_name' => $request->accountName,
                'account_number' => $request->accountNumber,
                'account_type' => $request->accountType
            ]);

            return $this->successResponse('Payment updated sucessfully', new PaymentResource($payment), 200);

        } catch (\Exception $e) {
            return $this->errorResponse('Payment updated failed:' . $e->getMessage(), 500);
        }
    }
}
