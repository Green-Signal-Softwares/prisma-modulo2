<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'solicitation_id',
        'user_id',
        'text',
        'file_path',
        'file_name',
        'parent_id',
        'reactions',
        'read_at',
        'type',
        'metadata',
    ];

    protected $casts = [
        'reactions' => 'array',
        'read_at' => 'datetime',
        'metadata' => 'array',
        'file_path' => 'array',
        'file_name' => 'array',
    ];

    /**
     * Get list of attachments for the message.
     */
    public function getFilesAttribute(): array
    {
        if (!$this->file_path) {
            return [];
        }

        $paths = $this->file_path;
        if (is_string($paths)) {
            $decoded = json_decode($paths, true);
            $paths = is_array($decoded) ? $decoded : [$paths];
        }

        $names = $this->file_name;
        if (is_string($names)) {
            $decoded = json_decode($names, true);
            $names = is_array($decoded) ? $decoded : [$names];
        }

        $files = [];
        if (is_array($paths)) {
            foreach ($paths as $index => $path) {
                if (!$path) continue;
                $name = (is_array($names) && isset($names[$index])) ? $names[$index] : basename($path);
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $type = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'document';
                $files[] = [
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                    'name' => $name,
                    'type' => $type,
                ];
            }
        }
        return $files;
    }


    public function solicitation()
    {
        return $this->belongsTo(Solicitation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }
}
