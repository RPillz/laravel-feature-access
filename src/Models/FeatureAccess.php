<?php

namespace RPillz\FeatureAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureAccess extends Model
{
    use HasFactory;

    protected $guarded = ['id','created_at','updated_at'];

    protected $casts = [
        'create' => 'boolean',
        'read' => 'boolean',
        'update' => 'boolean',
        'destroy' => 'boolean',
        'limit' => 'integer',
        'expires_at' => 'timestamp',
    ];

    protected $table = 'feature_access';

    public function owner()
    {
        return $this->morphTo();
    }

    public function permission()
    {
        $permissions = ['name', 'create', 'read', 'update', 'destroy', 'limit', 'level'];

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
