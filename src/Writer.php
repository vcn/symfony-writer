<?php

namespace Vcn\Symfony\HttpFoundation\Writer;

class Writer
{
    /**
     * @var callable
     */
    private $writer;

    /**
     * @var callable[]
     */
    private $listeners;

    /**
     * @param callable   $writer
     * @param callable[] $listeners
     */
    public function __construct(callable $writer, callable ...$listeners)
    {
        $this->listeners = $listeners;
        $this->writer    = $writer;
    }

    /**
     * @param string $data
     */
    public function write(string $data)
    {
        ($this->writer)($data);

        foreach ($this->listeners as $listener) {
            // It is the responsibility of the listener to make sure it never crashes
            $listener($data);
        }
    }
}
