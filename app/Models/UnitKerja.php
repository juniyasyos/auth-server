<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Juniyasyos\ManageUnitKerja\Models\UnitKerja as ModelsUnitKerja;

class UnitKerja extends ModelsUnitKerja
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_unit_kerja', 'unit_kerja_id', 'user_id')->withTimestamps();
    }
}
