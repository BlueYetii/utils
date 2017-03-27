<?php

namespace Utils\Image\Tests;

use \Utils\Image\ImageProcessor;

class ImageProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $testPngSrc = __DIR__ . "/../data/test_src.png";
    private $testJpgSrc = __DIR__ . "/../data/test_src.jpg";

    private function getProcessorPng()
    {
        return new ImageProcessor($this->testPngSrc);
    }

    private function getProcessorJpg()
    {
        return new ImageProcessor($this->testJpgSrc);
    }

    private function getFileMD5($path)
    {
        return md5_file($path);
    }

    /**
     * @expectedException Exception
     */
    public function testFileNotExists()
    {
        $processor = new ImageProcessor(__DIR__ . "/../file_that_does_not_exist.jpg");
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidFileType()
    {
        $processor = new ImageProcessor(__DIR__ . "/../data/test.txt");
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidJpegFile()
    {
        $processor = new ImageProcessor(__DIR__ . "/../data/invalid.jpg");
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidPngFile()
    {
        $processor = new ImageProcessor(__DIR__ . "/../data/invalid.png");
    }

    public function testScaleImage_scale1()
    {
        $outPath = __DIR__ . "/../data/test_scale_1.png";
        $expPath = __DIR__ . "/../data/test_scale_1_expected.png";

        $processor = $this->getProcessorPng();

        $processor->scaleImage(1);
        $processor->savePngImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($this->testPngSrc),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testScaleImage_scale2()
    {
        $outPath = __DIR__ . "/../data/test_scale_2.png";
        $expPath = __DIR__ . "/../data/test_scale_2_expected.png";

        $processor = $this->getProcessorPng();

        $processor->scaleImage(2);
        $processor->savePngImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCropImage_smallerTarget()
    {
        $outPath = __DIR__ . "/../data/test_crop_smaller.jpg";
        $expPath = __DIR__ . "/../data/test_crop_smaller_expected.jpg";

        $processor = $this->getProcessorJpg();

        $processor->cropImage(100, 100);
        $processor->saveJpgImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCropImage_biggerTarget()
    {
        $outPath = __DIR__ . "/../data/test_crop_bigger.jpg";
        $expPath = __DIR__ . "/../data/test_crop_bigger_expected.jpg";

        $processor = $this->getProcessorJpg();

        $processor->cropImage(300, 300);
        $processor->saveJpgImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCropImage_equalTarget()
    {
        $outPath = __DIR__ . "/../data/test_crop_equal.jpg";
        $expPath = __DIR__ . "/../data/test_crop_equal_expected.jpg";

        $processor = $this->getProcessorJpg();

        $processor->cropImage(200, 200);
        $processor->saveJpgImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCropImage_narrow()
    {
        $outPath = __DIR__ . "/../data/test_crop_narrow.jpg";
        $expPath = __DIR__ . "/../data/test_crop_narrow_expected.jpg";

        $processor = $this->getProcessorJpg();

        $processor->cropImage(100, 300);
        $processor->saveJpgImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCropImage_short()
    {
        $outPath = __DIR__ . "/../data/test_crop_short.jpg";
        $expPath = __DIR__ . "/../data/test_crop_short_expected.jpg";

        $processor = $this->getProcessorJpg();

        $processor->cropImage(200, 157);
        $processor->saveJpgImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCenterImage_smaller()
    {
        $outPath = __DIR__ . "/../data/test_center_smaller.png";
        $expPath = __DIR__ . "/../data/test_center_smaller_expected.png";

        $processor = $this->getProcessorPng();

        $processor->centerImage(150, 150);
        $processor->savePngImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCenterImage_bigger()
    {
        $outPath = __DIR__ . "/../data/test_center_bigger.png";
        $expPath = __DIR__ . "/../data/test_center_bigger_expected.png";

        $processor = $this->getProcessorPng();

        $processor->centerImage(300, 300);
        $processor->savePngImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCenterImage_equal()
    {
        $outPath = __DIR__ . "/../data/test_center_equal.png";
        $expPath = __DIR__ . "/../data/test_center_equal_expected.png";

        $processor = $this->getProcessorPng();

        $processor->centerImage(200, 200);
        $processor->savePngImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCenterImage_narrow()
    {
        $outPath = __DIR__ . "/../data/test_center_narrow.png";
        $expPath = __DIR__ . "/../data/test_center_narrow_expected.png";

        $processor = $this->getProcessorPng();

        $processor->centerImage(300, 200);
        $processor->savePngImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testCenterImage_short()
    {
        $outPath = __DIR__ . "/../data/test_center_short.png";
        $expPath = __DIR__ . "/../data/test_center_short_expected.png";

        $processor = $this->getProcessorPng();

        $processor->centerImage(200, 300);
        $processor->savePngImageToFile($outPath);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }

    public function testNonDestructiveSave()
    {
        $outPath = __DIR__ . "/../data/test_scale_0_5.png";
        $expPath = __DIR__ . "/../data/test_scale_0_5_expected.png";

        $processor = $this->getProcessorPng();

        $processor->scaleImage(0.5);
        $processor->savePngImageToFile($outPath, 6, false);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);

        $outPath = __DIR__ . "/../data/test_scale_0_25.png";
        $expPath = __DIR__ . "/../data/test_scale_0_25_expected.png";

        $processor->scaleImage(0.5);
        $processor->savePngImageToFile($outPath, 6, true);

        $this->assertEquals(
            $this->getFileMD5($expPath),
            $this->getFileMD5($outPath)
        );

        unlink($outPath);
    }
}
