<?php

namespace Realtor\Queue\Storage;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Realtor\Queue\StorageInterface;

/**
 * Class RabbitMQ
 * @package Realtor\Queue
 */
class RabbitMQ implements StorageInterface
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $channel;

    /**
     * @var string
     */
    private $queueKey;

    /**
     * Init logic
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->queueKey = $parameters['queue_key'];

        $this->connection = new AMQPStreamConnection(
            $parameters['host'],
            $parameters['port'],
            $parameters['login'],
            $parameters['password']
        );
        $this->channel = $this->connection->channel();

        $this->channel->queue_declare($this->queueKey, false, false, false, false);
    }

    /**
     * Closes connection with queue
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Adds new message to the queue
     *
     * @param mixed $message
     */
    public function enqueue($message)
    {
        $messageObj = new AMQPMessage($message);
        $this->channel->basic_publish($messageObj, '', $this->queueKey);
    }

    /**
     * Returns next message from the queue
     *
     * @param callable $callback
     */
    public function listen(callable $callback)
    {
        $this->channel->basic_consume($this->queueKey, '', false, true, false, false, $callback);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

    }
}
