<?php

namespace RPillz\FeatureAccess\Tests;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use RPillz\FeatureAccess\Traits\HasFeatureAccess;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use HasFeatureAccess;
    use Authorizable;
    use Authenticatable;

    protected $fillable = ['email'];

    public $timestamps = false;

    protected $table = 'users';

    public function getFeatureSubscriptionLevel(string $feature_name = null): ?string
    {
        return 'pro';
    }
}
