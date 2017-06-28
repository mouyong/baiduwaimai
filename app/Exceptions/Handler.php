<?php

namespace App\Exceptions;

use App\Mail\BaiduMail;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use function Psy\debug;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);

        // 非线上环境
        if (config('app.debug')) {
            return;
        }

        // 不需要发送邮件的错误
        if ($this->shouldntReport($exception)){
            return;
        }

        BaiduMail::trance($exception)->sendTo();

        $data = [
            'first' => '系统异常',
            'keyword1' => get_class($this),
            'keyword2' => url()->current(),
            'keyword3' => $_SERVER['REMOTE_ADDR'],
            'remark' => '请及时处理'
        ];

        $wechat = app('wechat');
        $wechat->notice->to('oExW-vgbrMqersRSI4LarFHElnNY')
            ->uses('hSv7tkI6iYvdeoZhUlPt9wcpi2ECFk3X1ly4UnNBK2M')
            ->andUrl(url()->current())
            ->data($data)
            ->send();
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
