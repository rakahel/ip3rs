<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Models\Log;
use Illuminate\Support\Facades\Session;

// use Tymon\JWTAuth\Exceptions\InvalidClaimException;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Tymon\JWTAuth\Exceptions\PayloadException;
// use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
// use Tymon\JWTAuth\Exceptions\UserNotDefinedException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $session_id=trim(Session::get('cilog.session_id'));
        if(empty($session_id)) {
            $session_id = date('YmdHis').rand(1000,9999);
            session()->put('cilog.session_id', $session_id);
        }
        // print('<pre>'.print_r($session_id,true).'</pre>');exit;

        $this->reportable(function (Throwable $e) {
            // Laravel melakukan looping untuk menyimpan log ke dalam laravel.log disini
            // Hentikan proses simpan log
            return false;
        });

        // Sama saja kaya Throwable
        $this->renderable(function (NotFoundHttpException $e, $request) use ($session_id) {
            // $methods = get_class_methods($e);
            // cilog()->exit($methods);
            // cilog()->exit($e->getStatusCode());
            // return $this->log($e, $request, $session_id);
        });

        $this->renderable(function (TokenInvalidException $e, $request) {
            return \Response::json(['error'=>'Invalid token'],401);
        });

        $this->renderable(function (TokenExpiredException $e, $request) {
            return \Response::json(['error'=>'Token has Expired'],401);
        });

        $this->renderable(function (JWTException $e, $request) {
            return \Response::json(['error'=>'Token not parsed'],401);
        });

        $this->renderable(function (Throwable $e, $request) use ($session_id) {

            $namespace = get_class($e);
            $className = (new \ReflectionClass($e))->getShortName();
            // cilog()->exit(gettype($e->getStatusCode()));

            return $this->log($e, $request, $session_id);
        });
    }

    protected function log($e, $request, $session_id)
    {
        $log_path = storage_path('logs/laravel.log');
        cilog()->toDb(json_encode([
            'message' => 'An internal system error occurred',
            'noref' => $session_id
        ]), Log::TYPE_LARAVEL_ERROR, [
            'path' => $log_path,
            'toFile' => false
        ]);
        $message = empty($e->getMessage()) ? $this->getHttpMessage($e->getStatusCode()) : $e->getMessage();
        $logs = implode(PHP_EOL, [
            'uid:'.$session_id,
            '[message] '.PHP_EOL.trim(preg_replace('/\n\s+/','',str_replace('|',PHP_EOL,preg_replace('/(\n|\t|\r|\r\n|\s{2})+/','|',$message)))),
            '[file] '.PHP_EOL.$e->getFile().':'.$e->getLine(),
            '[stacktrace] '.PHP_EOL.$e->getTraceAsString()
        ]);
        cilog()->toFile($logs.PHP_EOL, Log::TYPE_LARAVEL_ERROR, $log_path);

        if ($request->is('api/*')) {
            return response()->json([
                'code' => 'R0003',
                // 'message' => 'An error occurred, Please try again',
                'message' => $message,
                'noref' => $session_id
            ], 500);
        }
    }

    protected function getHttpMessage($code):String
    {
        $message = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing', // WebDAV; RFC 2518
            103 => 'Early Hints', // RFC 8297
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information', // since HTTP/1.1
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content', // RFC 7233
            207 => 'Multi-Status', // WebDAV; RFC 4918
            208 => 'Already Reported', // WebDAV; RFC 5842
            226 => 'IM Used', // RFC 3229
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found', // Previously "Moved temporarily"
            303 => 'See Other', // since HTTP/1.1
            304 => 'Not Modified', // RFC 7232
            305 => 'Use Proxy', // since HTTP/1.1
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect', // since HTTP/1.1
            308 => 'Permanent Redirect', // RFC 7538
            400 => 'Bad Request',
            401 => 'Unauthorized', // RFC 7235
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required', // RFC 7235
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed', // RFC 7232
            413 => 'Payload Too Large', // RFC 7231
            414 => 'URI Too Long', // RFC 7231
            415 => 'Unsupported Media Type', // RFC 7231
            416 => 'Range Not Satisfiable', // RFC 7233
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot', // RFC 2324, RFC 7168
            421 => 'Misdirected Request', // RFC 7540
            422 => 'Unprocessable Entity', // WebDAV; RFC 4918
            423 => 'Locked', // WebDAV; RFC 4918
            424 => 'Failed Dependency', // WebDAV; RFC 4918
            425 => 'Too Early', // RFC 8470
            426 => 'Upgrade Required',
            428 => 'Precondition Required', // RFC 6585
            429 => 'Too Many Requests', // RFC 6585
            431 => 'Request Header Fields Too Large', // RFC 6585
            451 => 'Unavailable For Legal Reasons', // RFC 7725
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates', // RFC 2295
            507 => 'Insufficient Storage', // WebDAV; RFC 4918
            508 => 'Loop Detected', // WebDAV; RFC 5842
            510 => 'Not Extended', // RFC 2774
            511 => 'Network Authentication Required', // RFC 6585
        ];

        return isset($message[$code]) ? $message[$code] : "Unknown Error : {$code}";
    }
}
