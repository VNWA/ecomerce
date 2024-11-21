<x-mail::message>
    # Your OTP Code


<x-mail::panel><strong>{{ $otp }}</strong></x-mail::panel>



<x-mail::button :url="config('app.frontend_url')"> Visit Our Site </x-mail::button>



Thanks,<br>{{ config('app.name') }}

</x-mail::message>
