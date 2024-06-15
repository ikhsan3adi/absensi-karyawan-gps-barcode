<?php

namespace App;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\RoundBlockSizeMode;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeGenerator
{

    /**
     * Create a new class instance.
     */
    public function __construct(
        protected $barcodeGenerator = new BarcodeGeneratorPNG(),
        protected $qrGenerator = new QrCode(''),
        protected $writer = new PngWriter(),
        protected $manager = new ImageManager(Driver::class),
        protected $barcodeHeight = 75,
        protected $width = 720,
        protected $height = 720,
    ) {
        //
    }

    /**
     * @deprecated
     */
    public function generateBarcode($value)
    {
        $barcodeFile = $this->barcodeGenerator->getBarcode(
            $value,
            type: $this->barcodeGenerator::TYPE_CODE_128,
            height: $this->barcodeHeight,
        );

        return $this->manager->create($this->width, $this->height)
            ->fill('#fff')
            ->place($this->manager->read($barcodeFile)->scale($this->width * .9), 'center')
            ->toPng();
    }

    /**
     * @deprecated
     * @param array<string, string> $values name => value
     */
    public function generateBarcodesZip(array $values)
    {
        $zip = new \ZipArchive();
        $dir = public_path('temp');
        if (!file_exists($dir)) {
            mkdir($dir, recursive: true);
        }
        $zipFile = public_path('temp/barcodes.zip');
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($values as $name => $value) {
            $barcodeFile = $this->generateBarcode($value);
            $zip->addFromString(($name ?? $value) . '.png', $barcodeFile->toString());
        }
        $zip->close();
        return $zipFile;
    }

    public function generateQrCode($value)
    {
        $qrCode = $this->qrGenerator
            ->create($value)
            ->setSize(300)
            ->setMargin(10)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $file = $this->writer->write(qrCode: $qrCode)->getString();

        return $this->manager->create($this->width, $this->height)
            ->fill('#fff')
            ->place($this->manager->read($file)->scale($this->width * .9), 'center')
            ->toPng();
    }

    /**
     * @param array<string, string> $values name => value
     */
    public function generateQrCodesZip(array $values)
    {
        $zip = new \ZipArchive();
        $dir = public_path('temp');
        if (!file_exists($dir)) {
            mkdir($dir, recursive: true);
        }
        $zipFile = public_path('temp/barcodes.zip');
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($values as $name => $value) {
            $barcodeFile = $this->generateQrCode($value);
            $zip->addFromString(($name ?? $value) . '.png', $barcodeFile->toString());
        }
        $zip->close();
        return $zipFile;
    }
}
