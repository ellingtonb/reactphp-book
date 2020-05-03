<?php

use Psr\Http\Message\ServerRequestInterface;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Http\Response;

return [
    '/' => function (ServerRequestInterface $request, LoopInterface $loop) {
        $childProcess = new Process('cat src/view/index.html', __DIR__);
        $childProcess->start($loop);
        
        return new Response(
            200,
            [ 'Content-Type' => 'text/html' ],
            $childProcess->stdout
        );
    },
    '/upload' => function (ServerRequestInterface $request, LoopInterface $loop) {
        /** @var \Psr\Http\Message\UploadedFileInterface $file */
       $file = $request->getUploadedFiles()['file'];
       
       $process = new Process(
           "cat > uploads/{$file->getClientFilename()}",
           __DIR__
       );
       $process->start($loop);
       $process->stdin->write($file->getStream()->getContents());
       $process->stdin->end();
       
       $loop->addPeriodicTimer(1, function () use ($process) {
           echo "Child Process ";
           echo $process->isRunning() ? 'is running...' : 'has stopped!';
           echo PHP_EOL;
       });
       
       return new Response(
           200,
           ['Content-Type' => 'text/plain'],
           'File Uploaded!'
       );
    },
    ''
];
