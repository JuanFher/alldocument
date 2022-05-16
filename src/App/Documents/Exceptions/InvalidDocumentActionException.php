<?php
namespace App\Documents\Exceptions;

use Exception;

class InvalidDocumentActionException extends Exception
{
    public $message = 'This user action is not allowed.';

    public function __construct()
    {
        parent::__construct();

        $this->message = __t('user_action_not_allowed');
    }
}
