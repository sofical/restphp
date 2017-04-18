<?php
namespace restphp\http;

/**
 * Class RestHttpMethod
 * @author sofical
 * @date 2017-03-17
 * @package restphp\http
 */
final class RestHttpMethod{
    public static function FULL_HTTP_METHODS() {
        return array(
            'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'COPY', 'HEAD', 'OPTIONS', 'LINK', 'UNLINK', 'PURGE', 'LOCK', 'UNLOCK', 'PROPFIND', 'VIEW'
        );
    }

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const HEAD = 'HEAD';
    const OPTIONS = 'OPTIONS';
    const LINK = 'LINK';
    const UNLINK = 'UNLINK';
    const PURGE = 'PURGE';
    const LOCK = 'LOCK';
    const UNLOCK = 'UNLOCK';
    const PROPFIND = 'PROPFIND';
    const VIEW = 'VIEW';
}