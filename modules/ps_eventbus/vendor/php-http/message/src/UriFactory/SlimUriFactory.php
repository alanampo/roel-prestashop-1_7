<?php

namespace ps_eventbus_v3_0_7\Http\Message\UriFactory;

use ps_eventbus_v3_0_7\Http\Message\UriFactory;
use Psr\Http\Message\UriInterface;
use ps_eventbus_v3_0_7\Slim\Http\Uri;
/**
 * Creates Slim 3 URI.
 *
 * @author Mika Tuupola <tuupola@appelsiini.net>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Slim PSR-17 factory
 */
final class SlimUriFactory implements UriFactory
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri)
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        }
        if (\is_string($uri)) {
            return Uri::createFromString($uri);
        }
        throw new \InvalidArgumentException('URI must be a string or UriInterface');
    }
}
