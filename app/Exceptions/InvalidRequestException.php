<?php

namespace App\Exceptions;

use Exception;

class InvalidRequestException extends Exception {
    //
    public function __construct( string $message = '', int $code = 400 ) {
        //调用父类的构造方法，覆盖错误信息和错误代码
        parent::__construct( $message, $code );
    }

    public function render( $request ) {
        //如果前端需要json 数据
        if ( $request->expectsJson() ) {
            return response()->json( [ 'msg' => $this->message ], $this->code );
        }

        //如果是页面ajax
        return view( 'pages.error', [ 'msg' => $this->message ] );
    }
}
