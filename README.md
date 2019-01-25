# vcn/symfony-writer

vcn/symfony-writer is a library that provides an alternative to the StreamedResponse-class of Symfony. In contrast to StreamedResponse, WriterResponse allows you to attach listeners and capture what is being responded.

## Installation

Install using composer in your symfony-project:

```php
composer require vcn/symfony-writer
```

## Usage

Usage is similar to usage of the StreamedResponse, with a few small changes:
- the callback now takes an instance of `\Vcn\Symfony\HttpFoundation\Writer` as first and only argument
- instead of echo-ing, you should call `Writer::write` with the data to output as string parameter
-  before the response is sent, you can attach a listener to the response using `Writer::attachListener`

Example:

```php
<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;
use Vcn\Symfony\HttpFoundation\Writer\Writer;
use Vcn\Symfony\HttpFoundation\Writer\WriterResponse;

class Controller
{
    public function count(): Response
    {
        $response = new WriterResponse(
            function (Writer $writer) {
                for ($i = 0; $i < 50; $i++) {
                    $writer->write("{$i} ");
                }
            }
        );

        $tmpFile       = tempnam(sys_get_temp_dir(), 'writer-response-');
        $tmpFileHandle = fopen($tmpFile, 'w');
        register_shutdown_function(
            function () use ($tmpFileHandle) {
                @fclose($tmpFileHandle);
            }
        );

        error_log("Copy of response is sent to {$tmpFile}");

        $response->attachListener(
            function (string $data) use ($tmpFileHandle) {
                fwrite($tmpFileHandle, $data);
            }
        );

        return $response;
    }
}



```
