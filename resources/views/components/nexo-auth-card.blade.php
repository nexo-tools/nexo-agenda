{{-- Canonical auth card: the box every sign-in, sign-up, password-reset and
     verify screen of the ecosystem sits in. Copy to
     resources/views/components/nexo-auth-card.blade.php.

     Why it is a component and not a class in each layout: it already drifted
     four ways (audit 2026-08-02) — border + surface-raised in nexotools and
     nexoshort, no border and plain surface in nexoagenda and nexoevents,
     max-w-sm without shadow in nexoid, Breeze's rounded-lg + shadow-md in
     nexolinks. The same flow looked like four products, and no check could see
     it because a Tailwind class list is not a contract.

     `title` is optional: a tool whose page already renders its own h1 passes
     only the slot. Either way the heading is text-xl font-semibold — the
     smaller, quieter weight, because on an auth screen the form is the subject.

     data-nexo-auth-card is the marker AuthChromeTest looks for. --}}
@props(['title' => null])

<div
    {{ $attributes->merge(['class' => 'w-full max-w-md rounded-2xl border border-line bg-surface-raised p-6 shadow-sm sm:p-8']) }}
    data-nexo-auth-card
>
    @if ($title)
        <h1 class="mb-6 text-xl font-semibold">{{ $title }}</h1>
    @endif

    {{ $slot }}
</div>
