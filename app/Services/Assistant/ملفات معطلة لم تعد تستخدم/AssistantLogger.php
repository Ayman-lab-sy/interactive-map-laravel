<?php

namespace App\Services\Assistant;

class AssistantLogger
{
    protected string $logFile;

    public function __construct()
    {
        $this->logFile = public_path('assistant-log.json');
    }

    public function log(array $payload): void
    {
        $logs = [];

        if (file_exists($this->logFile)) {
            $decoded = json_decode(file_get_contents($this->logFile), true);
            if (is_array($decoded)) {
                $logs = $decoded;
            }
        }

        $logs[] = array_merge($payload, [
            'timestamp' => date('c'),
        ]);

        file_put_contents(
            $this->logFile,
            json_encode($logs, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}
