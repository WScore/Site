<?php
namespace WScore\Site\Swift;

use Swift_Message;

class MessageDefault
{
    /**
     * @var array
     */
    private $from = [];

    /**
     * @var string
     */
    private $return_path = null;

    /**
     * @var
     */
    public $reply_to = [];

    /**
     * @param Swift_Message $message
     */
    public function __invoke($message)
    {
        foreach($this->from as $from) {
            if (is_array($from)) {
                $message->setFrom($from[0], $from[1]);
            } else {
                $message->setFrom($from);
            }
        }
        if($this->return_path) {
            $message->setReturnPath($this->return_path);
        }
        if($this->reply_to) {
            $message->setReplyTo($this->reply_to[0], $this->reply_to[1]);
        }
    }

    /**
     * @param string $from_mail
     * @param string $name
     * @return $this
     */
    public function setFrom($from_mail, $name=null)
    {
        $this->from[] = [$from_mail, $name];
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setReturnPath($path)
    {
        $this->return_path = $path;
        return $this;
    }

    /**
     * @param string $reply_mail
     * @param string $name
     * @return $this
     */
    public function setReplyTo($reply_mail, $name=null)
    {
        $this->reply_to = [$reply_mail, $name];
        return $this;
    }
}