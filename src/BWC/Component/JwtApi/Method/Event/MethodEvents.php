<?php

namespace BWC\Component\JwtApi\Method\Event;

final class MethodEvents
{
    const BEFORE_HANDLE = 'bwc_component_jwt_api.method.before_handle';

    const AFTER_HANDLE = 'bwc_component_jwt_api.method.after_handle';

    const ERROR = 'bwc_component_jwt_api.method.error';

    private function __construct() { }
} 