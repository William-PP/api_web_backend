<?php

namespace App\DataAccess\Models;

use Database\Factories\ModalidadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModalidadModel extends Model
{
    use HasFactory;

    protected $table = 'modalidades';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected static function newFactory(): ModalidadFactory
    {
        return ModalidadFactory::new();
    }

    public function deportistas(): HasMany
    {
        return $this->hasMany(DeportistaModel::class, 'modalidad_id');
    }
}
