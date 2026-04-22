<?php

namespace App\Services;

class StatusTransitionService
{
    const ALLOWED = [
        'draft'     => ['submitted'],
        'submitted' => ['approved', 'sent_back', 'rejected'],
        'sent_back' => ['submitted'],
        'approved'  => [],
        'rejected'  => [],
    ];

    public static function assertAllowed(string $from, string $to): void
    {
        if (!in_array($to, self::ALLOWED[$from] ?? [])) {
            abort(422, "Cannot transition from '{$from}' to '{$to}'.");
        }
    }
}