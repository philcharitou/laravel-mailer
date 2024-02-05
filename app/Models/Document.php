<?php

namespace App\Models;

use Illuminate\Contracts\Mail\Attachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Mail\Attachment;

class Document extends Model implements Attachable
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attachable representation of the model.
     */
    public function toMailAttachment(): Attachment
    {
        return Attachment::fromStorageDisk('s3', $this->file)
            ->as($this->title)
            ->withMime($this->format);
    }

    public $sortable = [
        'title',
        'author',
        'type',
        'category',
        'format',
    ];

    protected $fillable = [
        // Identification Field(s)
        'version_number',
        'title',
        'author',
        'type',
        'category',
        'format',
        // File(s)
        'file',
        // Optional Field(s)
        'description',
        'notes',
    ];

    public function templates()
    {
        return $this->belongsToMany(Template::class, 'template_document_mapping');
    }
}
