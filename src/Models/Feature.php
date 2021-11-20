<?php

namespace RPillz\FeatureAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $guarded = ['id','created_at','updated_at'];

    protected $casts = [
        'read' => 'boolean',
        'edit' => 'boolean',
        'create' => 'boolean',
        'destroy' => 'boolean',
        'limit' => 'integer',
        'expires_at' => 'timestamp',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function permission()
    {
        $permissions = ['name', 'read', 'edit', 'create', 'destroy', 'limit', 'level'];

        $access = [];

        foreach ($permissions as $permission) {
            if (! is_null($this->$permission)) {
                $access[$permission] = $this->$permission;
            } elseif (! is_null($this->level) && $value = config('feature-access.'.$this->feature.'.levels.'.$this->level.'.'.$permission)) {
                $access[$permission] = $value;
            } else {
                $access[$permission] = config('feature-access.'.$this->feature.'.'.$permission);
            }
        }

        return $access;
    }
}
