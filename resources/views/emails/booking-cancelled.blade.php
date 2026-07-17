@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('Turno cancelado') }}</h1>
    <p style="margin:0;">{{ __('Hola :name, tu turno fue cancelado.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('Si quieres, puedes reservar un nuevo turno cuando lo necesites:') }}
        <a href="{{ route('public.business', $booking->business) }}" style="color:#0f766e;">{{ url('/'.$booking->business->slug) }}</a>
    </p>
@endcomponent
