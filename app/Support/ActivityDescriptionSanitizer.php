<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class ActivityDescriptionSanitizer
{
    private const IMAGE_SOURCE_PATTERN = '#^/description-images/(?:users|admins)/\d+/[0-9a-f-]+\.(?:jpg|png|webp)$#i';

    public function sanitize(?string $description): string
    {
        if ($description === null || trim($description) === '') {
            return '';
        }

        return $this->removeUnsupportedImages((string) Purifier::clean($description, 'activity_description'));
    }

    public function excerpt(?string $description, int $limit = 180): string
    {
        $plainText = html_entity_decode(strip_tags($this->sanitize($description)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = preg_replace('/\s+/u', ' ', trim($plainText)) ?? '';

        return Str::limit($plainText, $limit);
    }

    public function firstImageUrl(?string $description): ?string
    {
        $document = $this->parse($this->sanitize($description));
        $body = $document?->getElementsByTagName('body')->item(0);

        if (! $body instanceof DOMElement) {
            return null;
        }

        foreach ($body->getElementsByTagName('img') as $image) {
            $source = $image->getAttribute('src');

            if ($this->isSupportedImageSource($source)) {
                return $source;
            }
        }

        return null;
    }

    private function removeUnsupportedImages(string $html): string
    {
        $document = $this->parse($html);
        $body = $document?->getElementsByTagName('body')->item(0);

        if (! $body instanceof DOMElement) {
            return $html;
        }

        $images = [];
        foreach ($body->getElementsByTagName('img') as $image) {
            $images[] = $image;
        }

        foreach ($images as $image) {
            if (! $this->isSupportedImageSource($image->getAttribute('src'))) {
                $image->parentNode?->removeChild($image);
            }
        }

        $content = '';
        foreach ($body->childNodes as $child) {
            $content .= $document->saveHTML($child);
        }

        return $content;
    }

    private function isSupportedImageSource(string $source): bool
    {
        if (! preg_match(self::IMAGE_SOURCE_PATTERN, $source)) {
            return false;
        }

        return Storage::disk('local')->exists(ltrim($source, '/'));
    }

    private function parse(string $html): ?DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrorMode = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<!DOCTYPE html><html><body>'.$html.'</body></html>',
            LIBXML_NONET | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorMode);

        return $loaded ? $document : null;
    }
}
