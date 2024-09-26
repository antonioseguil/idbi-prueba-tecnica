<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VouchersCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $vouchers, public User $user)
    {
        $this->vouchers = $vouchers;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->view(view: 'emails.comprobante')
            ->with(['vouchers' => $this->vouchers, 'user' => $this->user]);
    }
}
