# Nexo Agenda — Wireframes

Mobile-first ASCII wireframes of the core screens. Desktop is the same content
with more air (booking flow stays a single centered column; dashboard gains a
sidebar). Reference, not pixel spec.

## 1. Public business page — `/{slug}`

```
┌──────────────────────────────┐
│ [logo] Barbería Central      │
│ Peluquería · Palermo, CABA   │
│ ★ 4.8 (36)      [ⓘ Info]    │
├──────────────────────────────┤
│ Reserva tu turno             │
│                              │
│ ┌──────────────────────────┐ │
│ │ Corte clásico       30m  │ │
│ │ $8.000        [Reservar] │ │
│ ├──────────────────────────┤ │
│ │ Corte + barba       45m  │ │
│ │ $11.000       [Reservar] │ │
│ ├──────────────────────────┤ │
│ │ Asesoría online 🖥  30m  │ │
│ │ $6.000        [Reservar] │ │
│ └──────────────────────────┘ │
│                              │
│ Horarios · Lun–Sáb 9–19      │
│ ⌂ Av. Santa Fe 1234          │
│ ✆ WhatsApp                   │
│ ── powered by alvarocdev ──  │
└──────────────────────────────┘
```

## 2. Booking flow — service → professional → slot → data

```
Paso 2/4: ¿Con quién?          Paso 3/4: ¿Cuándo?
┌──────────────────────────────┐ ┌──────────────────────────────┐
│ ← Corte clásico · 30m        │ │ ← Corte clásico · Ana        │
├──────────────────────────────┤ ├──────────────────────────────┤
│ (○) Cualquiera disponible    │ │ ‹ Julio 2026 ›               │
│ ( ) ⬤ Ana                    │ │ L  M  M  J  V  S             │
│ ( ) ⬤ Luis                   │ │ 20 21 22 23 24 25            │
│                              │ │       ▲ hoy                  │
│            [Continuar]       │ ├──────────────────────────────┤
└──────────────────────────────┘ │ Mañana    [09:00] [09:30]    │
                                 │           [10:00] [11:30]    │
Paso 4/4: Tus datos              │ Tarde     [15:00] [16:30]    │
┌──────────────────────────────┐ └──────────────────────────────┘
│ ← Mar 22 jul · 10:00 · Ana   │
├──────────────────────────────┤  Confirmación
│ Nombre    [____________]     │ ┌──────────────────────────────┐
│ Email     [____________]     │ │ ✓ ¡Turno confirmado!         │
│ Teléfono  [____________]     │ │ Corte clásico                │
│ Nota opc. [____________]     │ │ Mar 22 jul · 10:00 · Ana     │
│                              │ │ [QR de tu turno]             │
│ Sin cuenta: te enviamos un   │ │ Te enviamos un email con el  │
│ link para gestionar tu turno │ │ link de gestión y el .ics    │
│        [Confirmar turno]     │ │ [＋ Agregar al calendario]   │
└──────────────────────────────┘ └──────────────────────────────┘
```

## 3. Client magic-link page — `/t/{token}`

```
┌──────────────────────────────┐
│ Tu turno en Barbería Central │
│ Corte clásico · $8.000       │
│ Mar 22 jul · 10:00 · Ana     │
│ Estado: ● Confirmado         │
│ [QR de check-in]             │
├──────────────────────────────┤
│ [Reprogramar] [Cancelar]     │
│ Cancelación gratis hasta     │
│ 12 h antes.                  │
└──────────────────────────────┘
```

## 4. Owner dashboard — day view (default on mobile)

```
┌──────────────────────────────┐
│ ☰ Hoy · Mar 22 jul     ‹ ›   │
│ [Día] [Semana]   ⊕ Turno     │
├──────────────────────────────┤
│ Ana          Luis            │
│ ┌──────────┐                 │
│ │09:00     │ ┌──────────┐    │
│ │J. Pérez  │ │09:30     │    │
│ │Corte ✓QR │ │M. Gómez  │    │
│ └──────────┘ └──────────┘    │
│ 10:00 libre  ┌──────────┐    │
│ ┌──────────┐ │10:30     │    │
│ │11:30     │ │C. Ruiz ⚠ │    │
│ │S. López  │ │no-show×2 │    │
│ └──────────┘ └──────────┘    │
├──────────────────────────────┤
│ [Agenda][Servicios][Equipo]  │
│ [Clientes][Ajustes]          │
└──────────────────────────────┘
```

## 5. Professional weekly schedule editor

```
┌──────────────────────────────┐
│ ← Ana · Horarios             │
├──────────────────────────────┤
│ Lun  [09:00–13:00] [14–19] ✕ │
│ Mar  [09:00–19:00]         ✕ │
│ Mié  — descansa      [+ franja]│
│ ...                          │
├──────────────────────────────┤
│ Ausencias                    │
│ 28 jul – 3 ago · Vacaciones ✕│
│ [+ Agregar ausencia]         │
└──────────────────────────────┘
```

## 6. Directory — `/explorar` (Stage 2)

```
┌──────────────────────────────┐
│ Nexo Agenda      [es ▾] [☾] │
│ Encuentra dónde reservar     │
│ [🔍 corte de pelo________ ]  │
│ [Peluquería ▾] [Palermo ▾]   │
├──────────────────────────────┤
│ ⬤ Barbería Central  ★ 4.8   │
│   Peluquería · Palermo       │
│   próximo turno: hoy 15:00   │
├──────────────────────────────┤
│ ⬤ Studio Mika       ★ 4.6   │
│   Peluquería · Colegiales    │
│   próximo turno: mañana      │
└──────────────────────────────┘
```

## 7. Check-in scan (owner, Stage 2)

```
┌──────────────────────────────┐
│ ← Check-in                   │
│ ┌──────────────────────────┐ │
│ │      [cámara / QR]       │ │
│ └──────────────────────────┘ │
│ ✓ J. Pérez · 09:00 · Ana     │
│   marcado como Asistió       │
└──────────────────────────────┘
```
