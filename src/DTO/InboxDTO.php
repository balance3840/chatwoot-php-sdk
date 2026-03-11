<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class InboxDTO extends BaseDTO
{
    public ?int    $id                     = null;
    public ?int    $account_id             = null;
    public ?string $name                   = null;
    public ?string $channel_type           = null;
    public ?string $email                  = null;
    public ?string $avatar_url             = null;
    public ?string $widget_color           = null;
    public ?string $website_url            = null;
    public ?string $welcome_title          = null;
    public ?string $welcome_tagline        = null;
    public ?bool   $email_enabled          = null;
    public ?bool   $enable_auto_assignment = null;
    public ?bool   $enable_email_collect   = null;
    public ?bool   $csat_survey_enabled    = null;
    public ?bool   $show_response_time     = null;
    public ?string $out_of_office_message  = null;  // string message, not bool
    public ?string $timezone               = null;
    public ?bool   $working_hours_enabled  = null;
    public ?array  $working_hours          = null;
    public ?array  $custom_attributes      = null;
}
