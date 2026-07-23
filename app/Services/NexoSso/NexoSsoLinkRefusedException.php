<?php

declare(strict_types=1);

namespace App\Services\NexoSso;

// Thrown when a local account exists for the claimed email but the provider
// did not assert email_verified — linking would allow account takeover. (AC-LINK-2)
class NexoSsoLinkRefusedException extends NexoSsoException {}
