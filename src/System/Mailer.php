<?php

namespace GoPokedex\System;

use Mailgun\Mailgun;

class Mailer
{
    private $mailer;
    private $domain;

    public function __construct()
    {
        $this->mailer = new Mailgun(CONFIG['mailgun']['key'], new \Http\Adapter\Guzzle6\Client());
        $this->domain = CONFIG['mailgun']['domain'];
        /*$smtpConfig = CONFIG['smtp'];
        // Create the Transport
        $transport = (new Swift_SmtpTransport($smtpConfig['host'], $smtpConfig['port'], $smtpConfig['encryption']))
            ->setUsername($smtpConfig['username'])
            ->setPassword($smtpConfig['password']);

        // Create the Mailer using your created Transport
        $this->mailer = new Swift_Mailer($transport);*/
    }

    public function send($message)
    {
        // Set up message
        $message['from'] = CONFIG['email'];

        // Send the message
        return $this->mailer->sendMessage($this->domain, $message);
    }
}
