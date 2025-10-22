<?php

namespace App\Imports;

use App\Models\Note;
use App\Models\Topic;
use App\Enums\NoteStatusEnum;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class NotesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private array $summary = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors'  => [],
    ];

    public function collection(Collection $rows): void
    {
        $seenTitles = [];
        $validTopicIds = Topic::pluck('id')->toArray();
        $validStatuses = NoteStatusEnum::getValues();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // dòng 1 là header

            $title    = trim((string)($row['title'] ?? ''));
            $topicId  = $row['topic_id'] ?? null;
            $status   = $row['status'] ?? null;
            $content  = $row['content'] ?? null;

            if ($title === '') {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: Thiếu cột 'title'";
                continue;
            }

            if (isset($seenTitles[$title])) {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: Trùng 'title' trong file: {$title}";
                continue;
            }
            $seenTitles[$title] = true;

            if (strlen($title) > 256) {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: 'title' vượt quá 256 ký tự";
                continue;
            }

            if ($content === null || trim((string)$content) === '') {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: Thiếu cột 'content'";
                continue;
            }

            if (!is_null($topicId) && !in_array((int)$topicId, $validTopicIds)) {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: 'topic_id' không hợp lệ ({$topicId})";
                continue;
            }

            if ($status === null || !in_array((int)$status, $validStatuses)) {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: 'status' không hợp lệ ({$status})";
                continue;
            }

            // --- xử lý dữ liệu ---
            $note = Note::query()->where('title', $title)->first();

            if ($note) {
                $dirty = false;
                if ($note->content !== $content) {
                    $note->content = $content;
                    $dirty = true;
                }
                if ($note->status !== (int)$status) {
                    $note->status = (int)$status;
                    $dirty = true;
                }
                if ($note->topic_id !== (is_null($topicId) ? null : (int)$topicId)) {
                    $note->topic_id = $topicId ? (int)$topicId : null;
                    $dirty = true;
                }

                if ($dirty) {
                    $note->save();
                    $this->summary['updated']++;
                } else {
                    $this->summary['skipped']++;
                }
            } else {
                Note::create([
                    'title'    => $title,
                    'topic_id' => $topicId ? (int)$topicId : null,
                    'status'   => (int)$status,
                    'content'  => $content,
                ]);
                $this->summary['created']++;
            }
        }
    }

    public function getData(): array
    {
        return $this->summary;
    }
}
