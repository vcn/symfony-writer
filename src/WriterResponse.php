<?php

namespace Vcn\Symfony\HttpFoundation\Writer;

use LogicException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WriterResponse extends StreamedResponse
{
    /**
     * @var callable[]
     */
    private $listeners = [];

    /**
     * @param callable $callback callback that takes an instance of Writer as sole argument, and performs all its writes
     *                           to Writer::write()
     * @param int      $status
     * @param array    $headers
     */
    public function __construct(callable $callback, int $status = 200, array $headers = array())
    {
        parent::__construct($callback, $status, $headers);
    }

    /**
     * {@inheritdoc}
     *
     * This method only sends the content once.
     *
     * @return $this
     */
    public function sendContent()
    {
        if ($this->streamed) {
            return $this;
        }

        $this->streamed = true;

        if (null === $this->callback) {
            throw new LogicException('The Response callback must not be null.');
        }

        $writer = new Writer(
            function (string $data) {
                echo $data;
            },
            ...$this->listeners
        );

        ($this->callback)($writer);

        return $this;
    }

    /**
     * @param callable $listener a callable that will receive whatever buffer is being written as sole argument
     *
     * @return $this
     */
    public function attachListener(callable $listener): self
    {
        if ($this->streamed) {
            throw new CannotAttachException('Cannot attach listener after sending the response');
        }

        $this->listeners[] = $listener;

        return $this;
    }
}
