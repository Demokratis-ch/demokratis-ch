<?php

namespace App\Config;

enum ConsultationStatus: string
{
    case Planned = 'planned';
    case Ongoing = 'ongoing';
    case PendingStatementsReport = 'pending_statements_report';
    case PendingReport = 'pending_report';
    case Done = 'done';
    case Unknown = 'unknown';
}
