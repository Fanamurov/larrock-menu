<?php

namespace Larrock\ComponentMenu\Models;

use LarrockMenu;
use Larrock\Core\Component;
use Larrock\Core\Traits\GetLink;
use Illuminate\Database\Eloquent\Model;

/**
 * Larrock\ComponentMenu\Models\Menu.
 *
 * @property int $id
 * @property string $title
 * @property int $category
 * @property string $type
 * @property int $parent
 * @property string $url
 * @property string $connect
 * @property int $position
 * @property int $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property mixed $parent_tree
 * @property mixed $getParent
 * @property mixed $getParentActive
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereCategory($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereParent($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereConnect($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentMenu\Models\Menu find($value)
 * @mixin \Eloquent
 */
class Menu extends Model
{
    /**
     * @var Component
     */
    protected $config;

    use GetLink;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockMenu::addFillableUserRows([]));
        $this->table = LarrockMenu::getTable();
        $this->config = LarrockMenu::getConfig();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getChild()
    {
        return $this->hasMany(LarrockMenu::getModelName(), 'parent', 'id')->orderBy('position', 'DESC');
    }

    public function getChildActive()
    {
        return $this->hasMany(LarrockMenu::getModelName(), 'parent', 'id')->whereActive(1)->orderBy('position', 'DESC');
    }

    public function getParentTreeAttribute()
    {
        $key = 'tree_menu'.$this->id;
        $list = \Cache::remember($key, 1440, function () {
            $list[] = $this;

            return $this->iterateTree($this, $list);
        });

        return $list;
    }

    protected function iterateTree($category, $list = [])
    {
        if ($get_data = $category->get_parent()->first()) {
            $list[] = $get_data;

            return $this->iterate_tree($get_data, $list);
        }

        return array_reverse($list);
    }

    public function getParent()
    {
        return $this->hasOne(LarrockMenu::getModelName(), 'id', 'parent');
    }

    public function getParentActive()
    {
        return $this->hasOne(LarrockMenu::getModelName(), 'id', 'parent')->whereActive(1);
    }
}
