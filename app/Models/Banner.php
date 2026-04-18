<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Banner extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'image_path',
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'link',
        'button_text_en',
        'button_text_ar',
        'sort_order',
        'is_active',
    ];
 
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
