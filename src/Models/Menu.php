<?php

namespace Larrock\ComponentMenu\Models;

use Illuminate\Database\Eloquent\Model;
use Larrock\ComponentMenu\Facades\LarrockMenu;

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
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockMenu::addFillableUserRows(['title', 'type', 'parent', 'url', 'connect', 'position', 'active']));
        $this->table = LarrockMenu::getConfig()->table;
    }

    public function get_child()
    {
        return $this->hasMany(LarrockMenu::getModelName(), 'parent', 'id')->orderBy('position', 'DESC');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function getParentTreeAttribute()
    {
        $key = 'tree_menu'. $this->id;
        $list = \Cache::remember($key, 1440, function() {
            $list[] = $this;
            return $this->iterate_tree($this, $list);
        });
        return $list;
    }

    protected function iterate_tree($category, $list = [])
    {
        if($get_data = $category->get_parent()->first()){
            $list[] = $get_data;
            return $this->iterate_tree($get_data, $list);
        }
        return array_reverse($list);
    }

    public function get_parent()
    {
        return $this->hasOne(LarrockMenu::getModelName(), 'id', 'parent');
    }
}