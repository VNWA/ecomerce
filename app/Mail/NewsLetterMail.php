<?php

namespace App\Mail;

use App\Models\Appearance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $origin; // Dữ liệu đơn hàng
    public function __construct($origin)
    {
        $this->origin = $origin;
    }

    public function envelope(): Envelope
    {
        $appName = env('APP_NAME', 'Vinawebapp.com');
        return new Envelope(
            subject: 'Grab Your Exclusive Offer Inside! -'.$appName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $appearanceProfile = Appearance::where('type', 'profile')->first();
        $profile = $appearanceProfile ? $appearanceProfile->value : [];

        $appearanceLogo = Appearance::where('type', 'logo')->first();
        $logo = $appearanceLogo ? $appearanceLogo->value : [];

        return new Content(
            view: 'emails.news_letter',
            with: [
                'url' => $this->origin,
                'logo' => $logo['logo_full'] ?? 'https://file.vinawebapp.com/uploads/images/Company/vnwaLogoFull.png',
                'company_name' => env('APP_NAME', 'Vinawebapp.com'),
                'profile' => $profile,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
