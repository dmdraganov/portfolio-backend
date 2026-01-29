<?php
require_once __DIR__ . '/../../core/BaseController.php';

class DraftsController extends BaseController
{
    public function __construct()
    {
        parent::__construct('drafts.json', 'Draft', 'draft-');
    }
}
