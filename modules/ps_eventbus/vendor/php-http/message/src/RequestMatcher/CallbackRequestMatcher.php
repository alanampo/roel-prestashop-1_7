<?php

namespace ps_eventbus_v3_0_7\Http\Message\RequestMatcher;

use ps_eventbus_v3_0_7\Http\Message\RequestMatcher;
use Psr\Http\Message\RequestInterface;
/**
 * Match a request with a callback.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class CallbackRequestMatcher implements RequestMatcher
{
    /**
     * @var callable
     */
    private $callback;
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    /**
     * {@inheritdoc}
     */
    public function matches(RequestInterface $request)
    {
        return (bool) \call_user_func($this->callback, $request);
    }
}
