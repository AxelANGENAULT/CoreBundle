<?php

namespace Arii\CoreBundle\Service;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class AriiException {
    protected $log;
    
    public function __construct(AriiLog $log) {
        $this->log = $log;
    }
    
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = sprintf(
                'Some Error Occured: %s with code: %s at %s line %s "\n" %s',
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
        );
        $response = new Response();
        $response->setContent($message);
        $Message = $exception->getMessage();
        $Code = $exception->getCode();
        $File = $exception->getFile();
        $Line = $exception->getLine();
        $Trace = $exception->getTraceAsString();
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $this->log->createLog($Message,$Code,$File,$Line,$Trace,$ip);
        
        if ($exception instanceof HttpExceptionInterface)
        {
            
         /*   print $response->setStatusCode($exception->getStatusCode());
            exit();
         */
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else
        {
            $response->setStatusCode(500);
        }
        
        $event->setResponse($response);
    }
}

?>
