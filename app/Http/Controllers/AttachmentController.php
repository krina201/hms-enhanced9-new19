<?php
/**
 * Controller that streams and downloads private attachments securely.
 *
 * @package HMS
 */
namespace App\Http\Controllers;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController
{
    public function show(int $id)
    {
        $att = Attachment::findOrFail($id);
        // authorization: allow owner-module editors or a generic attachments.view
        abort_unless(auth()->check(), 403);
        $disk = 'local'; // private
        if (!Storage::disk($disk)->exists($att->path)) abort(404);
        $mime = $att->mime ?: 'application/octet-stream';
        return new StreamedResponse(function() use ($disk,$att){
            $stream = Storage::disk($disk)->readStream($att->path);
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="'.$att->original_name.'"'
        ]);
    }

    public function download(int $id)
    {
        $att = Attachment::findOrFail($id);
        abort_unless(auth()->check(), 403);
        $disk = 'local';
        if (!Storage::disk($disk)->exists($att->path)) abort(404);
        return Storage::disk($disk)->download($att->path, $att->original_name, [
            'X-Content-Type-Options' => 'nosniff'
        ]);
    }
}