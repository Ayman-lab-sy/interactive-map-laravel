<?php

namespace App\Services\Pdf;

use Illuminate\Support\Facades\Http;

class GotenbergPdfService
{
    protected string $endpoint;

    public function __construct()
    {
        $this->endpoint = rtrim(config('services.gotenberg.url'), '/') . '/forms/chromium/convert/html';
    }

    protected function footerHtml(): string
    {
        return <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body {
                font-family: "Times New Roman", Georgia, serif;
                font-size: 12px;
                color: #000;
                margin: 0;
                padding: 0;
                text-align: center;
            }
            .page {
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="page">
            Page <span class="pageNumber"></span> of <span class="totalPages"></span>
        </div>
    </body>
    </html>
    HTML;
    }


    /**
     * @param string $html
     * @param array $options
     * @return string  PDF binary
     */
    public function generateFromHtml(string $html, array $options = []): string
    {
        $response = Http::asMultipart()
            ->timeout(60)

            // الملف الأساسي
            ->attach('files', $html, 'index.html')

            // Footer للترقيم
            ->attach('files', $this->footerHtml(), 'footer.html')

            // ✅ الشعار
            ->attach(
                'files',
                file_get_contents(public_path('assets/logo.png')),
                'logo.png'
            )

            ->post($this->endpoint, array_merge([
                'paperWidth'  => '8.27', // A4
                'paperHeight' => '11.69',

                // هوامش منطقية (تقلل الفراغات)
                'marginTop'    => '0.6',
                'marginBottom' => '1.2', // أكبر شوي للـ footer
                'marginLeft'   => '0.6',
                'marginRight'  => '0.6',

                'printBackground' => 'true',

                // 🔴 تفعيل footer
                'footer' => 'footer.html',

            ], $options));

        if (!$response->successful()) {
            throw new \RuntimeException('Gotenberg PDF generation failed');
        }

        return $response->body();
    }

}
