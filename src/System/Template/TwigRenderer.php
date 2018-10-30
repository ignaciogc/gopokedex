<?php
/**
 * TwigRenderer
 *
 * @author Ignacio Garcia <igcemail@gmail.com>
 */

namespace GoPokedex\System\Template;

use Twig_Environment;
use GoPokedex\System\Template;
use Http\Response;

class TwigRenderer implements Template
{
    private $template;

    public function __construct(Twig_Environment $template, Response $response)
    {
        $this->template = $template;
        $this->response = $response;
    }

    public function render($template, $data = [], $layout = 'default')
    {
        if (isset($data['app_layout_name'])) {
            exit;
        }

        $data['app_layout_name'] = "$layout.html";

        return $this->template->render("$template.html", $data);
    }

    public function output($template, $data = [], $layout = 'default')
    {
        $this->response->setContent(
            $this->render($template, $data, $layout)
        );
    }
}
