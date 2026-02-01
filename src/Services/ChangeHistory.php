<?php

namespace MarceliTo\Aicms\Services;

use MarceliTo\Aicms\Models\Change;
use Illuminate\Support\Facades\Auth;

class ChangeHistory
{
    public function record(string $filePath, string $before, string $after, ?string $summary = null): Change
    {
        return Change::create([
            'file_path' => $filePath,
            'content_before' => $before,
            'content_after' => $after,
            'summary' => $summary,
            'user_id' => Auth::id(),
        ]);
    }

    public function getHistory(string $filePath = null, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        $query = Change::orderBy('created_at', 'desc')->limit($limit);

        if ($filePath) {
            $query->where('file_path', $filePath);
        }

        return $query->get();
    }

    public function getLatest(string $filePath): ?Change
    {
        return Change::where('file_path', $filePath)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function revert(int $changeId): ?array
    {
        $change = Change::find($changeId);

        if (!$change) {
            return null;
        }

        return [
            'file_path' => $change->file_path,
            'content' => $change->content_before,
        ];
    }
}
