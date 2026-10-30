<?php

namespace App\DataAccess\Models;

use Database\Factories\DeportistaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeportistaModel extends Model
{
    use HasFactory;

    protected $table = 'deportistas';

    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'foto',
        'modalidad_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    protected static function newFactory(): DeportistaFactory
    {
        return DeportistaFactory::new();
    }

    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(ModalidadModel::class, 'modalidad_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
