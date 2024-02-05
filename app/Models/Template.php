<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content',
        'signature_image',

        'is_draft',
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'template_document_mapping',
            'template_id', 'document_id')
            ->as('pivot');
    }

    public function populate($array)
    {
        $content = $this->content;

        foreach($array as $key => $value)
        {
            $string = "[".$key."]";
            $content = str_replace($string, $value, $content);
        }

        return $content;
    }
}
