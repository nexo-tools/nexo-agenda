<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $type
 * @property string $message
 * @property string|null $email
 * @property string|null $page_url
 */
#[Fillable(['type', 'message', 'email', 'page_url'])]
class FeedbackReport extends Model
{
    /** Feedback categories a visitor can pick from. */
    public const TYPES = ['bug', 'idea', 'negocio', 'otro'];
}
