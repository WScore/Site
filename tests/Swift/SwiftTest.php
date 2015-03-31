<?php
namespace tests\Swift;

use Swift_Message;
use WScore\Site\Swift\DumbSpool;
use WScore\Site\Swift\MailerFactory;
use WScore\Site\Swift\MessageDefault;

require_once(dirname(__DIR__) . '/autoloader.php');

class SwiftTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        /** @var DumbSpool $spool */
        $mailer = MailerFactory::forgeDumb($spool);
        $this->assertEquals('WScore\Site\Swift\Mailer', get_class($mailer));
        
        $mailer->sendText('test mail', function($message) {
            /** @var Swift_Message $message */
            $message->setTo('test@example.com');
        });
        $msg = $spool->getMessage();
        $this->assertEquals(['test@example.com'=> ''], $msg->getTo());
    }
    
    function test1()
    {
        /** @var DumbSpool $spool */
        $mailer = MailerFactory::forgeDumb($spool);
        $this->assertEquals('WScore\Site\Swift\Mailer', get_class($mailer));

        $mailer->sendHtml('html mail', function($message) {
            /** @var Swift_Message $message */
            $message->setTo('html@example.com');
        });
        $msg = $spool->getMessage();
        $this->assertEquals(['html@example.com'=> ''], $msg->getTo());
    }

    /**
     * @test
     */
    function default_sets_messages()
    {
        $default = new MessageDefault();
        $default->setFrom('from@test.com', 'from');
        $default->setReplyTo('reply@test.com', 'reply');
        $default->setReturnPath('return@test.com');
        /** @var DumbSpool $spool */
        $mailer = MailerFactory::forgeDumb($spool);
        $mailer->setDefault($default);
        $mailer->sendText('test mail', function($message) {
            /** @var Swift_Message $message */
            $message->setTo('test@example.com');
        });
        $msg = $spool->getMessage();
        $this->assertEquals(['test@example.com'=> ''], $msg->getTo());
        $this->assertEquals(['from@test.com'=> 'from'], $msg->getFrom());
        $this->assertEquals(['reply@test.com'=> 'reply'], $msg->getReplyTo());
        $this->assertEquals('return@test.com', $msg->getReturnPath());
    }
}
