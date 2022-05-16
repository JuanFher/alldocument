<?php
namespace App\Documents\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use Notifiable;
    use HasFactory;
    use Sortable;

    protected $table = "tipos_documentos";

    protected $guarded = [
        'id',
        'name',
    ];

    protected $fillable = [
        'name',
        'fields',
    ];

    public $sortable = [
        'id',
        'name',
        'created_at',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestaps = true;

    public function meta_data(): array | null
    {
        return \DB::table('files_meta')->where('file_id', $this->id)->get()->toArray(); 
    }

}
