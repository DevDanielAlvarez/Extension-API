<?php

namespace App\DTO\Patient;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\DocumentTypeEnum;
use Carbon\Carbon;

class UpdatePatientDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly DocumentTypeEnum $document_type,
        public readonly string $document_number,
        public readonly Carbon $admission_date,
        public readonly Carbon $birthday,
        public readonly ?string $phone,
        public readonly ?array $nursing_report
    ) {
    }
}
