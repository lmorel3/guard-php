<?php

namespace Guard\Views;

use Slim\Http\Response;

/**
 * Class View
 *
 * @author Laurent Morel
 * @package Guard\Views
 */

class View
{

    /**
     * Renders a page
     *
     * @param Response $response
     * @param string $name
     * @param array $params
     * @return Response
     */
    public static function render(Response $response, string $name, array $params = []): Response {
        $content = file_get_contents('../views/' . $name . '.html');

        // Fill the "template"
        foreach ($params as $k=>$v) {
            $content = str_replace("{%$k%}", $v, $content);
        }

        $response->getBody()->write($content);

        return $response;
    }

}