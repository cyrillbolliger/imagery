<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\GroupLogo
 *
 * @property int $id
 * @property int $logo_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo whereLogoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupLogo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupLogo extends Pivot
{
    //
}
