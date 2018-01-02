<?php

/*
 * This file is part of the bqrd openapi component package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi\Middleware;

use Bqrd\OpenApi\Response\Response;
use Closure;
use Log;

class Render
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $content = $response->getOriginalContent();
        $headers = $response->headers->all();
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        } elseif ($response->headers->get('x-api-proxy') == 'wxa') {
            return $response;
        } elseif ($response->status() == 200) {
            return new Response($content, $response->status(), $headers);
        } elseif ($response->exception) {
            $exception = $response->exception;
            $code = $exception->getCode() ?: $response->status();

            return new Response('', $response->status(), $headers, $code, $exception->getMessage());
        } elseif (in_array($response->status(),[401, 419])) {
            return new Response('', 401, $headers, $response->status(), $content);
        } else {
            $data = $response->getData(true);
            $statusText = Response :: $statusTexts[$response->status()];
            if ($response->status() == 422) {
                $statusText = current(current($data));
                $data = null;
            }

            return new Response($data, $response->status(), $headers, $response->status(), $statusText);
        }
    }

    /**
     * terminate.
     *
     * @param mixed $request
     * @param mixed $response
     *
     * @return mixed
     */
    public function terminate($request, $response)
    {
        if (config('log.default.level') == 'debug') {
            log :: debug($request . ' '. $response);
        } else {
            $message = $response->getContent();

            if (!method_exists($response, 'getCode') || $response->getCode() == 0) {
                $message = mb_strlen($message, 'utf-8') > 256 ? (mb_substr($message, 0, 240, 'utf-8').'...') : $message;
                Log :: notice($message);
            } elseif (in_array($response->getCode(), [422])) {
                Log :: warning($message);
            } else {
                Log :: error($message);
            }
        }
    }
}
