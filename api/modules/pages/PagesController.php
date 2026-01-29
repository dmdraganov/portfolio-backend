<?php
require_once __DIR__ . '/../../core/BaseController.php';

class PagesController extends BaseController
{
    public function __construct()
    {
        parent::__construct('practical-pages.json', 'Page', 'page-');
    }
}
