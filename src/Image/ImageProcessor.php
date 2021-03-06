<?php
namespace Utils\Image;

/**
 * ImageProcessor class
 *
 * @author   Robert Curran <robert.d.curran@googlemail.com>
 * @license  MIT
 * @license  http://www.opensource.org/licenses/mit-license.php
 */
class ImageProcessor
{
    /**
     * @var resource Image identifier referencing the image
     */
    private $image;

    /**
     * @var int The width of the image
     */
    private $width;

    /**
     * @var int The height of the image
     */
    private $height;

    /**
     * Class constructor loads source image in to memory and gets its size
     *
     * @param string $imagePath File path to the image to process
     *
     * @throws Exception if provided path does not exist of is a type other than png or jpg/jpeg
     */
    public function __construct($imagePath)
    {
        if (!file_exists($imagePath)) {
            throw new \Exception(
                "Unable in instantiate ImageProcessor {$imagePath} does not exist."
            );
        }

        $imageInfo = pathinfo($imagePath);
        $imageType = $imageInfo['extension'];

        switch (strtolower($imageType)) {
            case "jpeg":
            case "jpg":
                $this->image = @imagecreatefromjpeg($imagePath);
                if ($this->image === false) {
                    throw new \Exception("{$imagePath} is not a valid jpeg file");
                }
                break;
            case "png":
                $this->image = @imagecreatefrompng($imagePath);
                if ($this->image === false) {
                    throw new \Exception("{$imagePath} is not a valid png file");
                }
                imagealphablending($this->image, true);
                imagesavealpha($this->image, true);
                break;
            default:
                throw new \Exception("Unsupported file type {$imageType} supplied.");
        }

        list($this->width, $this->height) = getimagesize($imagePath);
    }

    /**
     * Class destructor clears image from memory
     */
    public function __destruct()
    {
        imagedestroy($this->image);
    }

    /**
     * Scales the image by a given scale factor
     *
     * @param int $scale The factor to scale the image by where 1 = 100%
     *
     * @return void
     */
    public function scaleImage($scale)
    {
        if ($scale == 1) {
            return;
        }

        $scaledSize = $this->getScaledImageSize($scale);

        $image = $this->createNewTransparentImage($scaledSize->width, $scaledSize->height);

        imagecopyresampled(
            $image,
            $this->image,
            0,
            0,
            0,
            0,
            $scaledSize->width,
            $scaledSize->height,
            $this->width,
            $this->height
        );

        imagedestroy($this->image);
        $this->image = $image;
        $this->width = $scaledSize->width;
        $this->height = $scaledSize->height;
    }

    /**
     * Crops the image to a given width and height, uses top left as the origin
     *
     * @param int $targetWidth  The width to crop to in pixels
     * @param int $targetHeight The height to crop to in pixels
     *
     * @return void
     */
    public function cropImage($targetWidth, $targetHeight)
    {
        $croppedSize = $this->getCroppedIamgeSize($targetWidth, $targetHeight);

        // Do nothing if the target size is >= the original size
        if ($croppedSize->width >= $this->width && $croppedSize->height >= $this->height) {
            return;
        }

        $croppedSize->width = min($croppedSize->width, $targetWidth);
        $croppedSize->height = min($croppedSize->height, $targetHeight);

        $image = $this->createNewTransparentImage($croppedSize->width, $croppedSize->height);

        imagecopyresampled(
            $image,
            $this->image,
            0,
            0,
            0,
            0,
            $croppedSize->width,
            $croppedSize->height,
            $croppedSize->width,
            $croppedSize->height
        );

        imagedestroy($this->image);
        $this->image = $image;
        $this->width = $croppedSize->width;
        $this->height = $croppedSize->height;
    }

    /**
     * Centers the image in a canvas of the given width and height
     *
     * @param int $targetWidth  The width of the canvas to pixels
     * @param int $targetHeight The height of the canvas pixels
     *
     * @return void
     */
    public function centerImage($targetWidth, $targetHeight)
    {
        $position = $this->getCenteredImagePostion($targetWidth, $targetHeight);

        // Do nothing if the top left of the image in the canvas is x = 0, y = 0
        if ($position->x == 0 && $position->y == 0) {
            return;
        }

        $image = $this->createNewTransparentImage($targetWidth, $targetHeight);

        imagecopyresampled(
            $image,
            $this->image,
            $position->x,
            $position->y,
            0,
            0,
            $this->width,
            $this->height,
            $this->width,
            $this->height
        );

        imagedestroy($this->image);
        $this->image = $image;
    }

    /**
     * Saves the current image to a given path as a png
     *
     * @param string $path        The path on the file system to save the image
     * @param int    $compression The compression level to use from 0-9
     *
     * @return void
     */
    public function savePngImageToFile($path, $compression = 5)
    {
        imagepng($this->image, $path, $compression);
    }

    /**
     * Saves the current image to a given path as a jpg
     *
     * @param string $path    The path on the file system to save the image
     * @param int    $quality The qulaity of the image being saved 0 - 100
     *
     * @return void
     */
    public function saveJpgImageToFile($path, $quality = 75)
    {
        imagejpeg($this->image, $path, $quality);
    }

    /**
    * Creates a new true color image that is transparent
    *
    * @param int $width  The width in pixels of the image to create
    * @param int $height The height in pixels of the image to create
    *
    * @return resource Image identifier representing a transparent image
    */
    private function createNewTransparentImage($width, $height)
    {
        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));

        return $image;
    }

    /**
     * Calulates the new width and height for the image based on a given scale factor
     *
     * @param int $scale The scale factor where 1 = 100%
     *
     * @return stdClass Object with the scaled width and height
     */
    private function getScaledImageSize($scale)
    {
        $size = new \stdClass();

        $size->width = $this->width * $scale;
        $size->height = $this->height * $scale;

        return $size;
    }

    /**
     * Calulates the width and height to crop the image to based on the target width and height
     *
     * @param int $targetWidth  The width to crop to in pixels
     * @param int $targetHeight The height to crop to in pixels
     *
     * @return stdClass Object with the width and height to crop to
     */
    private function getCroppedIamgeSize($targetWidth, $targetHeight)
    {
        $result = new \stdClass();

        if ($this->width > $targetWidth) {
            $result->width = $targetWidth;
        } else {
            $result->width = $this->width;
        }

        if ($this->height > $targetHeight) {
            $result->height = $targetHeight;
        } else {
            $result->height = $this->height;
        }

        return $result;
    }

    /**
     * Calulates the x and y coordinates at which to place $this->image so that it is centered on a
     * canvas of given width and heght
     *
     * @param int $targetWidth  The width of the canvas to pixels
     * @param int $targetHeight The height of the canvas pixels
     *
     * @return stdClass Object with the x and y coordinates for centering $this->image
     */
    private function getCenteredImagePostion($targetWidth, $targetHeight)
    {
        $result = new \stdClass();

        if ($targetWidth > $this->width) {
            $result->x = ($targetWidth - $this->width) / 2;
        } else {
            $result->x = 0;
        }

        if ($targetHeight > $this->height) {
            $result->y = ($targetHeight - $this->height) / 2;
        } else {
            $result->y = 0;
        }

        return $result;
    }
}
