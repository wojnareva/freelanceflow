<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attachable_type',
        'attachable_id',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'disk',
        'path',
        'description',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the owning attachable model (expense, invoice, etc.)
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the attachment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file URL
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get human readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a PDF
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if file is a document
     */
    public function isDocument(): bool
    {
        $documentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ];

        return in_array($this->mime_type, $documentMimes);
    }

    /**
     * Get file icon based on mime type
     */
    public function getIconAttribute(): string
    {
        if ($this->isImage()) {
            return 'photo';
        }

        if ($this->isPdf()) {
            return 'document-text';
        }

        if (str_starts_with($this->mime_type, 'video/')) {
            return 'film';
        }

        if (str_starts_with($this->mime_type, 'audio/')) {
            return 'musical-note';
        }

        return 'document';
    }

    /**
     * Delete the file from storage when the model is deleted
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        });
    }

    /**
     * Create attachment from uploaded file
     */
    public static function createFromUpload($file, Model $attachable, ?string $description = null): self
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('attachments', $filename, 'public');

        return self::create([
            'user_id' => auth()->id(),
            'attachable_type' => get_class($attachable),
            'attachable_id' => $attachable->id,
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => 'public',
            'path' => $path,
            'description' => $description,
        ]);
    }

    /**
     * Scope to filter by attachable type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('attachable_type', $type);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by mime type
     */
    public function scopeByMimeType($query, string $mimeType)
    {
        return $query->where('mime_type', 'like', $mimeType . '%');
    }

    /**
     * Get allowed file types for upload
     */
    public static function getAllowedMimeTypes(): array
    {
        return [
            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
            
            // Archives
            'application/zip',
            'application/x-rar-compressed',
            
            // Other
            'text/plain',
        ];
    }

    /**
     * Get max file size in bytes (10MB)
     */
    public static function getMaxFileSize(): int
    {
        return 10 * 1024 * 1024; // 10MB
    }
}