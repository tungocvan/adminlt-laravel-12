<?php

namespace App\Models\Traits;

use DateTime;
use DateTimeZone;

trait AutoParseDates
{
    /**
     * Parse string date/datetime sang Y-m-d hoặc Y-m-d H:i:s
     *
     * @param string|null $value
     * @param string $castType 'date' hoặc 'datetime'
     * @return string|null
     */
    protected function parseDateField(?string $value, string $castType = 'date'): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value, "\"' \t\n\r\0\x0B");

        $date = null;
        $tz = new DateTimeZone('Asia/Ho_Chi_Minh');

        // --- dd/mm/yyyy hoặc dd/mm/yyyy H:i:s ---
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}:\d{2}(?::\d{2})?))?$/', $value, $matches)) {
            $format = isset($matches[3]) && !empty($matches[3]) ? 'd/m/Y H:i:s' : 'd/m/Y';
            $date = DateTime::createFromFormat($format, $value, $tz);

            if ($date && $castType === 'datetime' && !strpos($value, ':')) {
                $date = DateTime::createFromFormat('d/m/Y H:i:s', $date->format('d/m/Y') . ' 00:00:00', $tz);
            }
        }

        // --- yyyy-mm-dd hoặc yyyy-mm-dd H:i:s ---
        if (!$date && preg_match('/^\d{4}-\d{1,2}-\d{1,2}(?:\s+\d{1,2}:\d{2}(?::\d{2})?)?$/', $value)) {
            $format = strpos($value, ' ') !== false ? 'Y-m-d H:i:s' : 'Y-m-d';
            $date = DateTime::createFromFormat($format, $value, $tz);

            if ($date && $castType === 'datetime' && !strpos($value, ':')) {
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' 00:00:00', $tz);
            }
        }

        if (!$date) {
            return null;
        }

        return $castType === 'datetime' ? $date->format('Y-m-d H:i:s') : $date->format('Y-m-d');
    }

    /**
     * Override setAttribute để tự động parse tất cả field kiểu date/datetime trong $casts
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $casts = $this->getCasts();

        if (isset($casts[$key])) {
            $castType = strtolower($casts[$key]);

            if (in_array($castType, ['date', 'datetime']) && is_string($value)) {
                $parsed = $this->parseDateField($value, $castType);
                if ($parsed !== null) {
                    $value = $parsed;
                }
            }
        }

        return parent::setAttribute($key, $value);
    }
}
