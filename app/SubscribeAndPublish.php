<?php

declare(strict_types=1);

namespace unreal4u\mqttMonolog;

use unreal4u\MQTT\DataTypes\Message;
use unreal4u\MQTT\DataTypes\TopicFilter;
use unreal4u\rpiCommonLibrary\Base;
use unreal4u\rpiCommonLibrary\Communications\MQTT\Operations;
use unreal4u\rpiCommonLibrary\JobContract;

class SubscribeAndPublish extends Base {
    /**
     * @var Operations
     */
    private $mqtt;

    /**
     * Will be executed once before running the actual job
     *
     * @return JobContract
     * @throws \Exception
     */
    public function setUp(): JobContract
    {
        $this->mqtt = $this->communicationsFactory('MQTT');
        return $this;
    }

    public function configure()
    {
        $this
            ->setName('main-server:mqtt-to-monolog')
            ->setDescription('Reads out all topics and prints them out to monolog')
        ;
    }

    /**
     * Runs the actual job that needs to be executed
     *
     * @return bool Returns true if job was successful, false otherwise
     * @throws \unreal4u\MQTT\Exceptions\ServerClosedConnection
     */
    public function runJob(): bool
    {
        $this->mqtt->subscribeToTopic(new TopicFilter('#'), function(Message $message) {
            $this->logger->info('Event', ['topic' => $message->getTopicName(), 'payload' => $message->getPayload()]);
        });
        return true;
    }

    /**
     * If method runJob returns false, this will return an array with errors that may have happened during execution
     *
     * @return \Generator
     */
    public function retrieveErrors(): \Generator
    {
        yield '';
    }

    /**
     * The number of seconds after which this script should kill itself
     *
     * @return int
     */
    public function forceKillAfterSeconds(): int
    {
        return 3600;
    }

    /**
     * The loop should run after this amount of microseconds (1 second === 1000000 microseconds)
     *
     * @return int
     */
    public function executeEveryMicroseconds(): int
    {
        return 0;
    }
}
