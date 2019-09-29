<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Legal extends Model
{
    use SoftDeletes;

    public const PERSONALITY_NOT_APPLICABLE = 'not_applicable';
    public const PERSONALITY_AGREEMENT = 'agreement';
    public const PERSONALITY_PUBLIC_INTEREST = 'public_interest';
    public const PERSONALITY_UNKNOWN = 'unknown';
    public const PERSONALITY_NO_AGREEMENT = 'no_agreement';

    public const ORIGINATOR_USER = 'user';
    public const ORIGINATOR_STOCK = 'stock';
    public const ORIGINATOR_AGENCY = 'agency';
    public const ORIGINATOR_FRIEND = 'friend';
    public const ORIGINATOR_UNKNOWN = 'unknown';

    public const LICENCE_CC = 'creative_commons';
    public const LICENCE_CC_ATTRIBUTION = 'creative_commons_attribution';
    public const LICENCE_OTHER = 'other';

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
