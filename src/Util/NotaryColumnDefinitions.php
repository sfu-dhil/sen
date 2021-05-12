<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Util;

class NotaryColumnDefinitions {
    public const notary_name = 0;

    public const ledger_volume = 1;

    public const ledger_page = 2;

    public const transaction_date = 3;

    public const first_party_last_name = 4;

    public const first_party_first_name = 5;

    public const first_party_race = 6;

    public const first_party_sex = 7;

    public const first_party_status = 8;

    public const first_party_spouse = 9;

    public const first_party_notes = 10;

    public const transaction_conjunction = 11;

    public const second_party_last_name = 12;

    public const second_party_first_name = 13;

    public const second_party_race = 14;

    public const second_party_sex = 15;

    public const second_party_status = 16;

    public const second_party_spouse = 17;

    public const second_party_notes = 18;

    public const transaction_category = 19;

    public const transaction_notes = 20;
}
