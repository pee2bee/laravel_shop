<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;


/**
 * Class ensureEmailVerified
 * @package App\Http\Middleware
 * 重写邮箱验证中间件，改提示语为中文
 */
class ensureEmailVerified {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle( $request, Closure $next, $redirectToRoute = null ) {
        if ( ! $request->user() ||
             ( $request->user() instanceof MustVerifyEmail &&
               ! $request->user()->hasVerifiedEmail() ) ) {
            return $request->expectsJson()
                ? abort( 403, '你的邮箱还没验证，请先验证' )
                : \Redirect::route( $redirectToRoute ?: 'verification.notice' );
        }

        return $next( $request );
    }
}
