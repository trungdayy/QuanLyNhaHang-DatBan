<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DatBan;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $datBan; // Thông tin booking

    /**
     * Create a new message instance.
     */
    public function __construct(DatBan $datBan)
    {
        $this->datBan = $datBan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Hóa đơn đặt bàn: {$this->datBan->ma_dat_ban}")
            ->view('restaurants.emails.emai-out')
            ->with([
                'datBan' => $this->datBan,
                'combo' => $this->datBan->combo, // nếu có quan hệ combo
                'ban' => $this->datBan->ban,     // nếu có quan hệ bàn
            ]);
    }
}
