<x-mail::message>
# Order Confirmation

Hello {{ $order->first_name }} {{ $order->last_name }},

Thank you for your order! Your order code is **{{ $order->code }}**.

<x-mail::table>
| Item | Quantity | Price |
|------|----------|-------|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | {{ number_format($item->price, 2, '.', ',') }} |
@endforeach
</x-mail::table>

<x-mail::button :url="config('app.frontend_url').'/order/checkout/?code='.$order->code">Visit Our Site</x-mail::button>

Thanks,<br>{{ config('app.name') }}
</x-mail::message>
