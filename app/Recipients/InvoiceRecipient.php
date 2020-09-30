<?php

//app/Recipients/AdminRecipient.php

namespace App\Recipients;

class InvoiceRecipient extends Recipient{

    public function __construct($email)
    {
        $this->email = $email;
    }

}