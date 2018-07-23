<?php

namespace Guard\Handlers;

/**
 * Interface IHandler
 *
 * @package Guard\Handlers
 * @author Laurent Morel
 */
interface IHandler
{

    public function __construct(\Psr\Http\Message\ServerRequestInterface $request);

    /**
     * Returns the name of handled reverse proxy
     * @return string
     */
    public function getName(): string;

    /**
     * Parse requests from the proxy
     * @return string
     */
    public function getRedirectUrl(): string;

}