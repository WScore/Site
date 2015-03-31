<?php
namespace WScore\Site\Swift;

use Swift;
use Swift_DependencyContainer;
use Swift_FileSpool;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_NullTransport;
use Swift_Plugins_AntiFloodPlugin;
use Swift_Plugins_ThrottlerPlugin;
use Swift_Preferences;
use Swift_SmtpTransport;
use Swift_Spool;
use Swift_SpoolTransport;
use Swift_Transport;

class MailerFactory
{
    /**
     * @param Swift_Spool $spool
     * @return Mailer
     */
    private static function mailerBySpool($spool)
    {
        $transport = Swift_SpoolTransport::newInstance($spool);
        return static::mailerByTransport($transport);
    }

    /**
     * @param Swift_Transport $transport
     * @return Mailer
     */
    private static function mailerByTransport($transport)
    {
        $mailer    = Swift_Mailer::newInstance($transport);
        return new Mailer($mailer);
    }

    /**
     * creates a mailer instance that will NOT send.
     *
     * @return static
     */
    public static function forgeNull()
    {
        $transport = Swift_NullTransport::newInstance();
        return static::mailerByTransport($transport);
    }

    /**
     * creates a mailer instance that will spool to memory.
     *
     * @param DumbSpool $spool
     * @return Mailer
     */    
    public static function forgeDumb(&$spool)
    {
        $spool     = new DumbSpool();
        return static::mailerBySpool($spool);
    }

    /**
     * creates a mailer instance that will save mail to a file.
     *
     * @param string $path
     * @return Mailer
     */
    public static function forgeFileSpool($path)
    {
        $spool     = new Swift_FileSpool($path);
        return static::mailerBySpool($spool);
    }

    /**
     * creates a mailer instance that will send mail using PHP's mail() function.
     *
     * @return Mailer
     */
    public static function forgePhpMailer()
    {
        $transport = Swift_MailTransport::newInstance();
        return static::mailerByTransport($transport);
    }

    /**
     * creates a mailer instance that will send mail via SMTP.
     * $security maybe 'ssl', 'tls' ?
     *
     * @param string $host
     * @param int    $port
     * @param string $security
     * @param string $user
     * @param string $pass
     * @return Mailer
     */
    public static function forgeSmtp($host='localhost', $port=25, $security = null, $user=null, $pass=null)
    {
        $transport = Swift_SmtpTransport::newInstance($host, $port, $security);
        if($user) {
            $transport->setUsername($user);
            $transport->setPassword($pass);
            $transport->start();
            if(!$transport->isStarted()) {
                throw new \RuntimeException('cannot start SMPT transport.');
            }
        }
        return static::mailerByTransport($transport);
    }

    /**
     * call this method to use mails in ISO2022
     * (this was the Japanese traditional mail encoding).
     */
    public static function goJapaneseIso2022()
    {
        Swift::init(function () {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asAliasOf('mime.base64headerencoder');
            Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
        });
    }

    /**
     * @param Mailer $mailer
     * @param int    $threshold
     * @param int    $sleep
     */
    public static function antiFlood($mailer, $threshold=99, $sleep=0)
    {
        $plugIn = new Swift_Plugins_AntiFloodPlugin($threshold, $sleep);
        $mailer->getMailer()->registerPlugin($plugIn);
    }

    /**
     * @param Mailer $mailer
     * @param int    $rate
     * @param int    $mode
     */
    public static function throttle($mailer, $rate=10, $mode=Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE)
    {
        $plugIn = new Swift_Plugins_ThrottlerPlugin($rate, $mode);
        $mailer->getMailer()->registerPlugin($plugIn);
    }
}
