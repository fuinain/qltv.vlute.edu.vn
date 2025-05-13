<?php

namespace App\Http\Controllers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SSOController extends Controller
{
    public function dangNhap() {
        return Socialite::driver('keycloak')->redirect();
    }

    public function logout(){
        session()->invalidate();
        return redirect('/');
    }

    public function thayDoiMatKhau(){
        session()->invalidate();
        $KEYCLOAK_BASE_URL = env('KEYCLOAK_BASE_URL');
        $REALM = env('KEYCLOAK_REALM');
        return redirect($KEYCLOAK_BASE_URL . "realms/" . $REALM . "/account/#/security/signingin");
    }

    public function callback(Request $request)
    {
    try {
        $user = Socialite::driver('keycloak')
            ->setHttpClient(new \GuzzleHttp\Client([
                'verify' => false,
            ]))
            ->stateless()
            ->user();
        $token = $user->token;
        $tttk = $this->decodeJWTPayloadOnly($token);
        
        if (isset($tttk->email)) {
            // Lấy email từ payload
            $email = $tttk->email;
            
            // Kiểm tra nếu email có đuôi @st.vlute.edu.vn hoặc @student.vlute.edu.vn
            if (preg_match('/@(st\.vlute\.edu\.vn|student\.vlute\.edu\.vn)$/', $email)) {
                // Gán session
                $request->session()->put('IsLogin', true);
                $request->session()->put('Email', $email);
                $request->session()->put('Username', $tttk->preferred_username);
                $request->session()->put('Quyen', 'docgia');
                $request->session()->put('HoTen', $tttk->name ?? 'Sinh viên');

                // Điều hướng đến view sinh viên
                return redirect()->to('/docgia/');
            }

            // Nếu không phải email có đuôi trên, kiểm tra trong cơ sở dữ liệu
            $taiKhoan = DB::table('tai_khoan')
                ->where('email', $email)
                ->select('email', 'quyen', 'ho_ten')
                ->first();
            if ($taiKhoan) {
                // Gán session
                $request->session()->put('IsLogin', true);
                $request->session()->put('Email', $email);
                $request->session()->put('Username', $tttk->preferred_username);
                $request->session()->put('Quyen', $taiKhoan->quyen);
                $request->session()->put('HoTen', $taiKhoan->ho_ten);

                // Chuyển hướng dựa trên quyền
                switch ($taiKhoan->quyen) {
                    case 'admin':
                        return redirect()->to('/admin/');
                    default:
                        // Nếu có quyền khác trong tương lai, xử lý tại đây
                        return redirect('/')->with('error', 'Không xác định được quyền truy cập.');
                }
            } else {
                return redirect('/')->with('error', 'Email không tồn tại trong hệ thống.');
            }
        }
        
        return redirect('/')->with('error', 'Không thể lấy email từ thông tin đăng nhập.');
    } catch (\Exception $e) {
        return redirect('/')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    }
}


    function decodeJWTPayloadOnly($token)
    {
        $tks = explode('.', $token);
        if (count($tks) != 3) {
            return null;
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        $input = $bodyb64;
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        $input = (base64_decode(strtr($input, '-_', '+/')));

        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length = strlen((string)PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{' . $max_int_length . ',})/', ': "$1"', $input);
            $obj = json_decode($json_without_bigints);
        }
        return $obj;
    }
}