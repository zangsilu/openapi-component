<?php

/*
 * This file is part of the bqrd openapi component package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi\Response;

use Illuminate\Http\Response as BaseResponse;

class Response extends BaseResponse
{
    /**
     * responseStatus.
     *
     * public @int
     */
    public $responseStatus = 0;
    /**
     * responseMsg.
     *
     * @public string
     */
    public $responseMsg = '';

    /**
     * __construct.
     *
     * @param string $content
     * @param int    $status
     * @param array  $headers
     *
     * @return mixed
     */
    public function __construct(
        $content = '',
        $status = 200,
        $headers = array(),
        $responseStatus = 0,
        $responseMsg = ''
    ) {
        $this->responseStatus = $responseStatus;
        $this->responseMsg = $responseMsg;
        parent :: __construct($content, $status, $headers);
    }

    /**
     * setContent.
     *
     * @param mixed $content
     *
     * @return mixed
     */
    public function setContent($content)
    {
        parent :: setContent([
                       'ResponseStatus' => $this->responseStatus,
                       'ResponseMsg' => $this->responseMsg,
                       'ResponseData' => $content,
                       ]);
    }

    /**
     * Morph the given content into JSON.
     *
     * @param mixed $content
     *
     * @return string
     */
    protected function morphToJson($content)
    {
        if ($content instanceof Jsonable) {
            return $content->toJson();
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    /**
     * getCode.
     *
     *
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->responseStatus;
    }
}
