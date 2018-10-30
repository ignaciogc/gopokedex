<?php
/**
 * Template Interface
 *
 * @author Ignacio Garcia <igcemail@gmail.com>
 */
namespace GoPokedex\System;

interface Template
{
    public function render($template, $data = [], $layout = 'default');
}
