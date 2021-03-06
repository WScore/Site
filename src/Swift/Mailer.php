<?php
namespace WScore\Site\Swift;

use Swift_Mailer;
use Swift_Message;

class Mailer
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var MessageDefault
     */
    private $default_call;

    /**
     * @param Swift_Mailer $mailer
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param MessageDefault $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default_call = $default;
        return $this;
    }

    /**
     * @return MessageDefault
     */
    public function getDefault()
    {
        return clone($this->default_call);
    }

    /**
     * @return Swift_Message
     */
    public function message()
    {
        return Swift_Message::newInstance();
    }

    /**
     * @return Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * sends email in text (UTF-8).
     *
     * @param string   $text
     * @param callable $callable
     * @return int
     */
    public function sendText($text, $callable)
    {
        return $this->send($text, null, $callable);
    }

    /**
     * sends email in html format.
     *
     * @param string   $html
     * @param callable $callable
     * @return int
     */
    public function sendHtml($html, $callable)
    {
        return $this->send($html, 'text/html', $callable);
    }

    /**
     * sends email in ISO2022 encoding (Japanese mail encoding).
     * must run MailerFactory::goJapaneseIso2022() in prior to
     * using this method.
     *
     * @param string   $text
     * @param callable $callable
     * @return int
     */
    public function sendJIS($text, $callable)
    {
        return $this->send($text, null, $callable, function($message) {
            /** @var Swift_Message $message */
            $message
                ->setCharset('iso-2022-jp')
                ->setEncoder( new \Swift_Mime_ContentEncoder_PlainContentEncoder( '7bit' ) )
                ->setMaxLineLength(0);
        });
    }

    /**
     * sends an email.
     *
     * @param string      $text
     * @param null|string $type
     * @param callable    $callable
     * @param callable    $preCallable
     * @return int
     */
    private function send($text, $type, $callable, $preCallable=null)
    {
        $message = $this->message();
        if($preCallable) {
            $preCallable($message);
        }
        $message->setBody($text, $type);
        if($this->default_call) {
            $default = $this->default_call;
            $default($message);
        }
        $callable($message);
        return $this->mailer->send($message);
    }
}
