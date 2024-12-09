<x-mail::message>
# {{ $message }}

Dear {{ $order->first_name }} {{ $order->last_name }},

We are writing to inform you that the  status for your order **{{ $order->code }}** has been updated.

**<strong>{{ $message }}</strong>**

Please click the button below to view the details of your order:

<x-mail::button :url="config('app.frontend_url').'/order/checkout/?code='.$order->code">View Order Details</x-mail::button>

If you have any questions or need further assistance, feel free to contact our support team.

Thanks for shopping with us!<br>
{{ config('app.name') }}
</x-mail::message>
