<?php

/**
 * Helper class for processing person's avatars
 *
 * PHP version 7.4
 *
 * @category  Services
 * @package   UNL_PersonInfo_ImageHelper
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 University Communications & Marketing
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 */
class UNL_PersonInfo_ImageHelper
{
    /** @var string $original_image_path */
    protected $original_image_path;

    /** @var Array[Imagick] $images */
    protected $images = array();

    /** @var Array[string] $images */
    protected $files = array();

    /** @var bool $keep_files */
    protected $keep_files = false;

    /** @var string $random_id */
    protected $random_id;

    /** @var string $tmp_path */
    protected $tmp_path;

    /**
     * The file prefix and key in images array
     */
    protected $original_key = 'original';

    public function __construct(string $path_to_image, array $options = array())
    {
        // Creates a tmp directory
        $this->random_id = uniqid();
        $this->tmp_path = dirname(dirname(dirname(__DIR__))) . '/www/person_images/tmp/' . $this->random_id;
        if (!file_exists($this->tmp_path)) {
            mkdir($this->tmp_path, 0775);
        }

        // Validates the image exists
        if (!file_exists($path_to_image)) {
            throw new UNL_PersonInfo_Exceptions_InvalidImage('Image does not exist or has exceeded max upload size');
        }

        // Load the image
        $tmp_image = new Imagick();
        $loaded_image = $tmp_image->readImage($path_to_image);
        if ($loaded_image === false) {
            throw new UNL_PersonInfo_Exceptions_InvalidImage('Error opening image');
        }

        // set the background to white
        $tmp_image->setImageBackgroundColor(new ImagickPixel('rgb(227, 227, 226)'));

        // flattens multiple layers
        $tmp_image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        $tmp_image = $tmp_image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

        // Fix the orientation of the image
        switch ($tmp_image->getImageOrientation()) {
            case 3:
                $tmp_image->rotateImage('#000000', 180);
                break;
            case 6:
                $tmp_image->rotateImage('#000000', 90);
                break;
            case 8:
                $tmp_image->rotateImage('#000000', -90);
                break;
            default:
        }
        $tmp_image->setImageOrientation(0);

        // Add it to the list of images
        $this->images[$this->original_key] = $tmp_image;

        // Set option
        if (isset($options['keep_files']) && $options['keep_files'] === true) {
            $this->keep_files = true;
        }
    }

    public function __destruct()
    {
        // Clears the imagemagick images from memory
        foreach ($this->images as $image_data) {
            $image_data->clear();
        }

        // Check if the directory exists and we want to remove the files
        if ($this->keep_files !== true && file_exists($this->tmp_path)) {
            // Get all the files and delete them all
            $tmp_files = array_diff(scandir($this->tmp_path), array('.','..'));
            foreach ($tmp_files as $file) {
                unlink($this->tmp_path . '/' . $file);
            }

            // Delete the directory once we removed all the files
            rmdir($this->tmp_path);
        }
    }

    /**
     * This method is not recommended to be used unless multiple version of the image are being saved
     *
     * @param string $new_name New key for the original image
     * @return void
     */
    public function rename_original(string $new_name): void
    {
        // Deleted the original key's image and re-save it as the new key
        $tmp_image = $this->images[$this->original_key];
        unset($this->images[$this->original_key]);
        $this->original_key = $new_name;
        $this->images[$this->original_key] = $tmp_image;
    }

    /**
     * Creates a new instance of the original image and crops it
     *
     * @param int $x_pos X position of the cropped rectangle
     * @param int $y_pos Y position of the cropped rectangle
     * @param int $width Width of the cropped rectangle
     * @param int $height Height of the cropped rectangle
     * @return void
     *
     * @throws UNL_PersonInfo_Exceptions_ImageProcessing Error cropping image
     */
    public function crop_image($x_pos, $y_pos, $width, $height): void
    {
        /** @var Imagick $tmp_image */
        $tmp_image = clone $this->images[$this->original_key];

        // Tries to crop the image and throw error if it has one
        $cropped_image = $tmp_image->cropImage($width, $height, $x_pos, $y_pos);
        if ($cropped_image === false) {
            throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Cropping Image');
        }

        // Saves cropped image
        $this->images['cropped'] = $tmp_image;
    }

    /**
     * Creates new instance of original(and cropped if set) and resizes/sets resolution to all combos of the values
     *
     * @param array $sizes Array of integer values corresponding to sizes
     * @param array $resolutions Array of integer values corresponding to DPI resolutions
     * @return void
     */
    public function resize_image(array $sizes, array $resolutions = array(72)): void
    {
        // Gets the original image and the cropped if its set
        $images_to_resize = array($this->original_key => $this->images[$this->original_key]);
        if (isset($this->images['cropped'])) {
            $images_to_resize['cropped'] = $this->images['cropped'];
        }

        // Loops through all the sizes and resolutions and images to crop
        foreach ($sizes as $width) {
            foreach ($resolutions as $dpi) {
                foreach ($images_to_resize as $image_name => $current_image) {
                    /** @var Imagick $current_image */
                    $tmp_image = clone $current_image;

                    $tmp_image_width = $tmp_image->getImageWidth();
                    $tmp_image_height = $tmp_image->getImageHeight();
                    $temp_image_aspect_ratio = $tmp_image_height / $tmp_image_width;

                    $newHeight = $temp_image_aspect_ratio * $width;

                    // Sets the resolution
                    $tmp_image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
                    $tmp_image->setImageResolution($dpi, $dpi);

                    // Resizes and this will resample the image at the new resolution
                    $tmp_image->resizeImage($width, $newHeight, Imagick::FILTER_LANCZOS, 1, true);

                    // Saves the new image
                    $this->images[$image_name . '_' . $width . '_' . $dpi] = $tmp_image;
                }
            }
        }
    }

    /**
     * Saves all the different images to the formats inputted
     *
     * @param array $formats Formats to save to
     * @return void
     *
     * @throws UNL_PersonInfo_Exceptions_ImageProcessing On saving image
     */
    public function save_to_formats(array $formats): void
    {
        // These are their own functions just so we can have different configs depending on the file type
        foreach ($formats as $format) {
            if (preg_match('/jpg|jpeg/i', $format)) {
                $this->save_jpeg();
            } elseif (preg_match('/png/i', $format)) {
                $this->save_png();
            } elseif (preg_match('/gif/i', $format)) {
                $this->save_gif();
            } elseif (preg_match('/webp/i', $format)) {
                $this->save_webp();
            } elseif (preg_match('/avif/i', $format)) {
                $this->save_avif();
            }
        }
    }

    /**
     * Writes the files that were saved to the user's record
     *
     * @param UNL_PersonInfo_Record $record Person's record to save the images to
     * @param bool $clear_previous_record false to keep the person's previous images
     * @return void
     */
    public function write_to_user(UNL_PersonInfo_Record $record, bool $clear_previous_record = true): void
    {
        // Clears the images if we can
        if ($clear_previous_record) {
            $record->clear_images();
        }

        // Loops through all the files generated and saves them
        foreach ($this->files as $path) {
            $record->save_image($path, basename($path));
        }
    }

    /**
     * Saves the images as JPEG
     * @return void
     *
     * @throws UNL_PersonInfo_Exceptions_ImageProcessing On saving image
     */
    public function save_jpeg(): void
    {
        // Loop through the images
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.jpeg';

            // Sets the format to JPEG
            $image_data->setFormat('JPEG');

            // Tries to save the image and throws error if we have one
            $saved_image = $image_data->writeImage($path);
            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving JPEG Image');
            }

            // Add the file to the files array
            $this->files[] = $path;
        }
    }

    /**
     * Saves the images as PNG
     * @return void
     *
     * @throws UNL_PersonInfo_Exceptions_ImageProcessing On saving image
     */
    public function save_png(): void
    {
        // Loop through the images
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.png';

            // Sets the format to PNG
            $image_data->setFormat('PNG');

            // Tries to save the image and throws error if we have one
            $saved_image = $image_data->writeImage($path);
            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving PNG Image');
            }

            // Add the file to the files array
            $this->files[] = $path;
        }
    }

    /**
     * Saves the images as GIF
     * @return void
     *
     * @throws UNL_PersonInfo_Exceptions_ImageProcessing On saving image
     */
    public function save_gif(): void
    {
        // Loop through the images
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.gif';

            // Sets the format to GIF
            $image_data->setFormat('GIF');

            // Tries to save the image and throws error if we have one
            $saved_image = $image_data->writeImage($path);
            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving GIF Image');
            }

            // Add the file to the files array
            $this->files[] = $path;
        }
    }

    /**
     * Saves the images as AVIF
     * @return void
     *
     * @throws UNL_PersonInfo_Exceptions_ImageProcessing On saving image
     */
    public function save_avif(): void
    {
        // Loop through the images
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.avif';

            // Sets the format to AVIF
            $image_data->setFormat('AVIF');

            // Tries to save the image and throws error if we have one
            $saved_image = $image_data->writeImage($path);
            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving AVIF Image');
            }

            // Add the file to the files array
            $this->files[] = $path;
        }
    }

    /**
     * Saves the images as WEBP
     * @return void
     *
     * @throws UNL_PersonInfo_Exceptions_ImageProcessing On saving image
     */
    public function save_webp(): void
    {
        // Loop through the images
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.webp';

            // Sets the format to WEBP
            $image_data->setFormat('WEBP');

            // Tries to save the image and throws error if we have one
            $saved_image = $image_data->writeImage($path);
            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving WEBP Image');
            }

            // Add the file to the files array
            $this->files[] = $path;
        }
    }
}
