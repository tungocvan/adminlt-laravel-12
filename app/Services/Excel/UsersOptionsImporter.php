<?php

namespace App\Services\Excel;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;

class UsersOptionsImporter
{
    protected $sheets;
    protected $errors = [];

    /**
     * Load Excel file trực tiếp từ đường dẫn
     */
    public function loadFile(string $filePath): static
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        // Load workbook
        $spreadsheet = IOFactory::load($filePath);
        $sheetNames = $spreadsheet->getSheetNames();

        // Load tất cả sheet vào Collection
        $sheets = Excel::toCollection(null, $filePath);
        $sheets['sheetNames'] = $sheetNames;

        return $this->load($sheets);
    }

    /**
     * Load sheets trực tiếp (nếu đã có collection)
     */
    public function load($sheets): static
    {
        $this->sheets = $sheets;
        return $this;
    }

    /**
     * Thực hiện import
     */
    public function import(): bool
    {
        DB::beginTransaction();

        try {
            $this->importUsersSheet();
            $this->importOptionSheets();

            if (!empty($this->errors)) {
                foreach ($this->errors as $err) {
                    // Log::channel('import')->error($err);
                }
                throw new \Exception('Import failed — check import.log');
            }

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            // Log::channel('import')->error($e->getMessage());
            return false;
        }
    }

    private function importUsersSheet()
    {
        $sheet = $this->sheets->first();
        if (!$sheet) {
            $this->errors[] = 'No sheets found in Excel file';
            return;
        }

        $rows = $sheet->skip(1); // bỏ header
        // Log::channel('import')->info('Sheet 1 total rows: ' . $rows->count());

        foreach ($rows as $row) {
            if (!$row[0]) continue;

            $id = $row[0];
            $name = $row[1] ?? null;
            $email = $row[2] ?? null;
            $username = $row[3] ?? null;

            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Invalid email for user ID $id: $email";
                continue;
            }

            $user = User::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $name,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make('123456'),
                ]
            );

            // Log::channel('import')->info("Imported user ID {$user->id}: {$name}");
        }
    }

    private function importOptionSheets()
    {
        $sheetNames = $this->sheets['sheetNames'] ?? [];
        $firstSheetKey = 0; // users sheet

        foreach ($this->sheets as $sheetKey => $rows) {
            if ($sheetKey === 'sheetNames') continue;

            if ($sheetKey === $firstSheetKey) {
                // Chỉ log user
                foreach ($rows as $rowIndex => $row) {
                    $rowArray = $row instanceof \Illuminate\Support\Collection ? $row->toArray() : $row;
                    $userId = $rowArray[0] ?? null;
                    $username = $rowArray[1] ?? null;
                    if (!$userId) continue;
                    // Log::channel('import')->info("Imported user ID {$userId}: {$username}");
                }
                continue;
            }

            $titleName = $sheetNames[$sheetKey] ?? "sheet_{$sheetKey}";
            // Log::channel('import')->info("Processing option sheet: {$titleName}");

            $rowsArray = $rows instanceof \Illuminate\Support\Collection ? $rows->toArray() : $rows;
            $header = $rowsArray[0] ?? [];
            $dataRows = array_slice($rowsArray, 1);

            foreach ($dataRows as $rowIndex => $row) {
                $rowArray = $row instanceof \Illuminate\Support\Collection ? $row->toArray() : $row;
                $userId = $rowArray[0] ?? null;
                if (!$userId) continue;

                $user = User::find($userId);
                if (!$user) {
                    $this->errors[] = "User ID {$userId} not found in sheet {$titleName}";
                    continue;
                }

                $optionData = [];
                foreach ($header as $colIndex => $colName) {
                    if ($colIndex === 0) continue; // bỏ cột user_id
                    $optionData[$colName] = $rowArray[$colIndex] ?? null;
                }

                $user->setOption($titleName, $optionData);

                // Log::channel('import')->info("Saved options for user {$userId} under key '{$titleName}'");
            }
        }

        if (!empty($this->errors)) {
            // Log::channel('import')->error("Errors during option import: " . implode("; ", $this->errors));
        }
    }
}
