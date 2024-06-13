<?php

namespace App;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeGenerator
{

    /**
     * Create a new class instance.
     */
    public function __construct(
        protected $generator = new BarcodeGeneratorPNG(),
        protected $manager = new ImageManager(Driver::class),
        protected $barcodeHeight = 75,
        protected $width = 720,
        protected $height = 360,
    ) {
        //
    }

    public function generateBarcode($value)
    {
        $barcodeFile = $this->generator->getBarcode(
            $value,
            type: $this->generator::TYPE_CODE_128,
            height: $this->barcodeHeight,
        );

        return $this->manager->create($this->width, $this->height)
            ->fill('#fff')
            ->place($this->manager->read($barcodeFile)->scale($this->width * .9), 'center')
            ->toPng();
    }

    /**
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
}
