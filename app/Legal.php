<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Legal
 *
 * @package App
 * @property int $id
 * @property int $image_id
 * @property Image $image
 * @property string $right_of_personality
 * @property string $originator_type
 * @property string $licence
 * @property string $originator
 * @property string|null $stock_url
 * @property bool $shared
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Legal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereLicence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereOriginator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereOriginatorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereRightOfPersonality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereShared($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereStockUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Legal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Legal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Legal withoutTrashed()
 * @mixin \Eloquent
 */
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

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
