<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;

use App\Mail\OtpMail;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Mail;

class CustomerController extends Controller
{
    public function customerOrders(Request $request)
    {
        $customer = $request->user();
        try {
            $orders = Order::where('email', $customer->email)->latest()->paginate(4)->setPath('');
            $orders->each(function ($order) {
                $order->create_time = Carbon::parse($order->created_at)->format('H:i , d/m/Y ');
                // $order->phoneFormat  = $this->formatPhone($order->phone);
                // $order->emailFormat  = $this->formatEmail($order->email);

                $order->makeHidden(['id', 'coupon_id', 'created_at', 'updated_at']);
            });
            return response()->json([
                'message' => 'Profile updated successfully.',
                'data' => $orders // Trả về thông tin đã cập nhật
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'message' => 'Failed to update profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {

        $customer = $request->user();

        // Xác thực dữ liệu
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string',
            'post_code' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'address_number' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Đăng ký không thành công', // Thông điệp lỗi
                'errors' => $validator->errors(), // Các lỗi xác thực
            ], 422);
        }

        try {
            // Cập nhật thông tin khách hàng
            $customer->update($request->only([
                'first_name',
                'last_name',
                'email',
                'phone',
                'company',
                'post_code',
                'city',
                'address',
                'address_number',
            ]));

            return response()->json([
                'message' => 'Profile updated successfully.',
                'customer' => $customer // Trả về thông tin đã cập nhật
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'message' => 'Failed to update profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updatePassword(Request $request)
    {

        $customer = $request->user();

        // Xác thực dữ liệu
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Đăng ký không thành công', // Thông điệp lỗi
                'errors' => $validator->errors(), // Các lỗi xác thực
            ], 422);
        }

        try {
            // Cập nhật thông tin khách hàng
            $customer->update(['password' => Hash::make($request->password)]);

            return response()->json([
                'message' => 'Password updated successfully.',
                'customer' => $customer // Trả về thông tin đã cập nhật
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'message' => 'Failed to update password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'required|string|unique:customers',
            'password' => 'required|string|min:6|confirmed',
            'post_code' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'address_number' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Đăng ký không thành công', // Thông điệp lỗi
                'errors' => $validator->errors(), // Các lỗi xác thực
            ], 422);
        }

        // Tạo người dùng mới
        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'post_code' => $request->post_code,
            'city' => $request->city,
            'address' => $request->address,
            'address_number' => $request->address_number,
            'company' => $request->company,
        ]);

        // Tạo OTP và lưu vào cơ sở dữ liệu

        // Tạo token
        $token = $customer->createToken('vnwa_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công, vui lòng kiểm tra email để xác thực OTP.',
            'token' => $token,
        ], 200);
    }
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Email không hợp lệ hoặc không tồn tại.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $customer = Customer::where('email', $request->email)->first();

            $otp = Str::random(6);
            $expiresAt = now()->addMinutes(10);

            $customer->otp = $otp;
            $customer->otp_expires_at = $expiresAt;
            $customer->save();

            Mail::to($customer->email)->send(new OtpMail($otp));

            return response()->json(['message' => 'OTP đã được gửi đến email của bạn.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể gửi OTP. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function verifyOtp(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:customers,email',
            'otp' => 'required|string|size:6', // Giả sử OTP là 6 ký tự
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Email hoặc OTP không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Lấy thông tin khách hàng từ email
            $customer = Customer::where('email', $request->email)->first();

            // Kiểm tra OTP và thời gian hết hạn
            if ($customer->otp !== $request->otp) {
                return response()->json(['message' => 'OTP không chính xác.'], 401);
            }

            if ($customer->otp_expires_at < now()) {
                return response()->json(['message' => 'OTP đã hết hạn.'], 401);
            }
            $customer->email_verified_at = now();
            $customer->otp = null;
            $customer->otp_expires_at = null;
            $customer->save();

            return response()->json(['message' => 'Xác thực OTP thành công.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xác thực OTP.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // Iniciar sesión
    public function login(Request $request)
    {
        // 1. Kiểm tra và validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6', // Kiểm tra độ dài mật khẩu tối thiểu
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }

        // 2. Tìm khách hàng theo email
        $customer = Customer::where('email', $request->email)->first();

        // 3. Kiểm tra mật khẩu hoặc email không đúng
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // 4. Tạo token cho phiên đăng nhập
        $token = $customer->createToken('vnwa_auth_token')->plainTextToken;
        return response()->json([
            'message' => "Đăng nhập thành công",
            'token' => $token,
        ], 200);
    }

    // Olvidé mi contraseña
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $response = Password::sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Se ha enviado un enlace para restablecer la contraseña.'])
            : response()->json(['message' => 'Ocurrió un error.'], 500);
    }

    // Restablecer la contraseña
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $resetStatus = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer) use ($request) {
                $customer->password = Hash::make($request->password);
                $customer->save();
            }
        );

        return $resetStatus == Password::PASSWORD_RESET
            ? response()->json(['message' => 'La contraseña ha sido restablecida.'])
            : response()->json(['message' => 'Ocurrió un error.'], 500);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
