<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

use RamiroEstrella\ChatwootPhpSdk\Enums\CustomAttributeModel;

class CustomAttributeDTO extends BaseDTO
{
    public ?int                  $id                       = null;
    public ?int                  $account_id               = null;
    public ?string               $attribute_display_name   = null;
    public ?string               $attribute_key            = null;
    public ?CustomAttributeModel $attribute_model          = null;
    public ?int                  $attribute_display_type   = null;
    public ?string               $created_at               = null;
    public ?string               $updated_at               = null;
    public ?string               $regex_pattern            = null;
    public ?string               $regex_cue                = null;
    public ?array                $default_value            = null;
}
