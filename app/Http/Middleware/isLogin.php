<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        if ($request->is('logout')) {
            return $next($request);
        }

        // Kiểm tra nếu chưa đăng nhập
        if (!$request->session()->exists('IsLogin')) {
            return redirect()->action('App\Http\Controllers\SSOController@dangNhap');
        }

        // Nếu có danh sách các vai trò được phép, kiểm tra xem người dùng có vai trò hợp lệ không
        if (!empty($allowedRoles)) {
            $userRole = $request->session()->get('Quyen');
            
            // Nếu vai trò của người dùng không nằm trong danh sách được phép
            if (!in_array($userRole, $allowedRoles)) {
                // Chuyển hướng dựa trên vai trò hiện tại của người dùng
                if ($userRole === 'admin') {
                    return redirect('/admin');
                } elseif ($userRole === 'docgia') {
                    return redirect('/docgia');
                } else {
                    // Trường hợp vai trò không rõ ràng
                    return redirect('/')->with('error', 'Bạn không có quyền truy cập vào trang này.');
                }
            }
        }

        return $next($request);
    }

}