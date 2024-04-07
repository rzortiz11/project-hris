<?php

namespace App;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum ApplicationStatusTypeEnum: string
{
    use IsKanbanStatus;

    case NEW_APPLICANT = 'New Applicant';
    case APPLICATION_INITIAL_INTERVIEW = 'Application for Initial Interview';
    case APPLICATION_FINAL_INTERVIEW = 'Application for Final Inteview';
    case APPLICATION_WITHDRAWN = 'Application Withdrawn';
    case BACKGROUND_CHECK = 'Background Check';
    case OFFER_EXTENDED = 'Offer Extended';
    case OFFER_ACCEPTED = 'Offer Accepted';
    case OFFER_DECLINED = 'Offer Declined';
}
