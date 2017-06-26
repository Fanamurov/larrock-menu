<?php

namespace Larrock\ComponentMenu\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Menu
 *
 * @property integer $id
 * @property string $title
 * @property integer $category
 * @property string $type
 * @property integer $parent
 * @property string $url
 * @property string $connect
 * @property integer $position
 * @property integer $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereCategory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereParent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereConnect($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu find($value)
 * @mixin \Eloquent
 */
class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = ['title', 'type', 'parent', 'url', 'connect', 'position', 'active'];
}
