<?php

namespace Bets\Services\Admin;


use Bets\Models\Payment;
use Bets\Services\BaseService;

class PaymentsService extends BaseService
{
    protected $modelClass = Payment::class;
}