<?php

use App\Http\Middleware\FileUploadSecurity;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

test('allows valid file uploads', function () {
    $middleware = new FileUploadSecurity();

    // Create a mock valid file
    $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

    $request = Request::create('/upload', 'POST');
    $request->files->set('file', $file);

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(200);
});

test('blocks dangerous file extensions', function () {
    $middleware = new FileUploadSecurity();

    // Create a file with dangerous extension
    $file = UploadedFile::fake()->create('malware.exe', 1000, 'application/octet-stream');

    $request = Request::create('/upload', 'POST');
    $request->files->set('file', $file);

    Log::shouldReceive('warning')
        ->once()
        ->with('Dangerous file extension blocked', \Mockery::any());

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(403);
});

test('blocks oversized files', function () {
    $middleware = new FileUploadSecurity();

    // Create a file that's too large for documents (over 10MB)
    $file = UploadedFile::fake()->create('large.pdf', 15000000, 'application/pdf');

    $request = Request::create('/upload', 'POST');
    $request->files->set('file', $file);

    Log::shouldReceive('warning')
        ->once()
        ->with('File size exceeds limit', \Mockery::any());

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(403);
});

test('blocks disallowed MIME types', function () {
    $middleware = new FileUploadSecurity();

    // Create a file with disallowed MIME type
    $file = UploadedFile::fake()->create('script.js', 1000, 'application/javascript');

    $request = Request::create('/upload', 'POST');
    $request->files->set('file', $file);

    Log::shouldReceive('warning')
        ->once()
        ->with('Disallowed MIME type blocked', \Mockery::any());

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(403);
});

test('blocks files with malicious content', function () {
    $middleware = new FileUploadSecurity();

    // Create a text file with PHP content
    $maliciousContent = '<?php echo "malicious"; ?>';
    $file = UploadedFile::fake()->create('script.txt', strlen($maliciousContent))
        ->setContent($maliciousContent);

    $request = Request::create('/upload', 'POST');
    $request->files->set('file', $file);

    Log::shouldReceive('warning')
        ->once()
        ->with('File failed security checks', \Mockery::any());

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(403);
});

test('blocks files with null bytes in filename', function () {
    $middleware = new FileUploadSecurity();

    // Create a file with null bytes in filename
    $file = UploadedFile::fake()->create("malicious\x00file.pdf", 1000, 'application/pdf');

    $request = Request::create('/upload', 'POST');
    $request->files->set('file', $file);

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(403);
});

test('allows multiple file uploads', function () {
    $middleware = new FileUploadSecurity();

    // Create multiple valid files
    $file1 = UploadedFile::fake()->create('doc1.pdf', 1000, 'application/pdf');
    $file2 = UploadedFile::fake()->create('doc2.pdf', 1000, 'application/pdf');

    $request = Request::create('/upload', 'POST');
    $request->files->set('files', [$file1, $file2]);

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(200);
});

test('blocks requests without files', function () {
    $middleware = new FileUploadSecurity();

    $request = Request::create('/upload', 'POST');

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(200);
});

test('logs security violations with context', function () {
    $middleware = new FileUploadSecurity();

    $file = UploadedFile::fake()->create('dangerous.exe', 1000, 'application/octet-stream');

    $request = Request::create('/upload', 'POST', [], [], ['file' => $file]);
    $request->server->set('REMOTE_ADDR', '192.168.1.100');
    $request->server->set('HTTP_USER_AGENT', 'Test Browser');

    Log::shouldReceive('warning')
        ->once()
        ->with('Dangerous file extension blocked', \Mockery::on(function ($context) {
            return isset($context['extension']) &&
                   isset($context['filename']) &&
                   isset($context['client_ip']);
        }));

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getStatusCode())->toBe(403);
});
