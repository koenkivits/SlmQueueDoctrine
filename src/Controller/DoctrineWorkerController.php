<?php

namespace SlmQueueDoctrine\Controller;

use SlmQueue\Controller\AbstractWorkerController;
use SlmQueue\Controller\Exception\WorkerProcessException;
use SlmQueue\Exception\ExceptionInterface;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueueDoctrine\Queue\DoctrineQueueInterface;

/**
 * Worker controller
 */
class DoctrineWorkerController extends AbstractWorkerController
{
    /**
     * Recover long running jobs
     */
    public function recoverAction(): string
    {
        $queueName     = $this->params('queue');
        $executionTime = $this->params('executionTime', 0);
        $queue         = $this->queuePluginManager->get($queueName);

        if (! $queue instanceof DoctrineQueueInterface) {
            return sprintf("\nQueue % does not support the recovering of job\n\n", $queueName);
        }

        try {
            $count = $queue->recover($executionTime);
        } catch (ExceptionInterface $exception) {
            throw new WorkerProcessException("An error occurred", $exception->getCode(), $exception);
        }

        return sprintf(
            "\nWork for queue %s is done, %s jobs were recovered\n\n",
            $queueName,
            $count
        );
    }
}
