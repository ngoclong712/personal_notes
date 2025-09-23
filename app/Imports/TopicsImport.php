<?php

namespace App\Imports;

use App\Models\Topic;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TopicsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private array $summary = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => [],
    ];

    /**
     * Định dạng cột tiêu đề mong muốn: name, description
     */
    public function collection(Collection $rows): void
    {
        $seenNames = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 vì có header (dòng 1)

            $name = trim((string)($row['name'] ?? ''));
            $description = $row['description'] ?? null;
            $description = is_null($description) ? null : trim((string)$description);

            if ($name === '') {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: Thiếu cột 'name'";
                continue;
            }

            if (isset($seenNames[$name])) {
                $this->summary['skipped']++;
                $this->summary['errors'][] = "Dòng {$rowNumber}: Trùng 'name' trong file: {$name}";
                continue;
            }

            $seenNames[$name] = true;

            $topic = Topic::query()->where('name', $name)->first();
            if ($topic) {
                // Chỉ update khi có thay đổi để thống kê chính xác
                $dirty = false;
                if ($description !== $topic->description) {
                    $topic->description = $description;
                    $dirty = true;
                }
                if ($dirty) {
                    $topic->save();
                    $this->summary['updated']++;
                } else {
                    $this->summary['skipped']++;
                }
            } else {
                Topic::create([
                    'name' => $name,
                    'description' => $description,
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
