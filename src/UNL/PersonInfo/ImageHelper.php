<?php

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

    protected $original_key = 'original';

    public function __construct(string $path_to_image, array $options = array())
    {
        $this->random_id = uniqid();
        $this->tmp_path = dirname(dirname(dirname(__DIR__))) . '/www/person_images/tmp/' . $this->random_id . '/';

        if (!file_exists($this->tmp_path)) {
            mkdir($this->tmp_path);
        }

        if (!file_exists($path_to_image)) {
            throw new UNL_PersonInfo_Exceptions_InvalidImage('Image does not exist');
        }

        // Load the image
        $tmp_image = new Imagick();
        $loaded_image = $tmp_image->readImage($path_to_image);
        if ($loaded_image === false) {
            throw new UNL_PersonInfo_Exceptions_InvalidImage('Error opening image');
        }

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
        foreach ($this->images as $image_data) {
            $image_data->clear();
        }

        if ($this->keep_files !== true) {
            $tmp_files = array_diff(scandir($this->tmp_path), array('.','..'));
            foreach ($tmp_files as $file) {
                unlink($this->tmp_path . '/' . $file);
            }
            rmdir($this->tmp_path);
        }
    }

    public function rename_original(string $new_name)
    {
        $tmp_image = $this->images[$this->original_key];
        unset($this->images[$this->original_key]);
        $this->original_key = $new_name;
        $this->images[$this->original_key] = $tmp_image;
    }

    public function crop_image($x_pos, $y_pos, $width, $height)
    {
        /** @var Imagick $tmp_image */
        $tmp_image = clone $this->images[$this->original_key];

        $cropped_image = $tmp_image->cropImage($width, $height, $x_pos, $y_pos);

        if ($cropped_image === false) {
            throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Cropping Image');
        }

        $this->images['cropped'] = $tmp_image;
    }

    public function resize_image(array $sizes, array $resolutions = array(72))
    {
        $images_to_resize = array($this->original_key => $this->images[$this->original_key]);
        if (isset($this->images['cropped'])) {
            $images_to_resize['cropped'] = $this->images['cropped'];
        }

        foreach ($sizes as $width) {
            foreach ($resolutions as $dpi) {
                foreach ($images_to_resize as $image_name => $current_image) {
                    /** @var Imagick $current_image */
                    $tmp_image = clone $current_image;
    
                    $tmp_image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
                    $tmp_image->setImageResolution($dpi, $dpi);

                    $tmp_image->resizeImage($width, null, Imagick::FILTER_LANCZOS, 1);

                    $this->images[$image_name . '_' . $width . '_' . $dpi] = $tmp_image;
                }
            }
        }
    }

    public function save_to_formats(array $formats)
    {
        // These are their own functions just so we can have different configs depending on the file type
        foreach ($formats as $format) {
            if (preg_match('/jpg|jpeg/i', $format)) {
                $this->save_jpeg();
            } elseif (preg_match('/png/i', $format)) {
                $this->save_png();
            } elseif (preg_match('/gif/i', $format)) {
                $this->save_gif();
            } elseif (preg_match('/bmp/i', $format)) {
                $this->save_bmp();
            } elseif (preg_match('/avif/i', $format)) {
                $this->save_avif();
            } elseif (preg_match('/webp/i', $format)) {
                $this->save_webp();
            }
        }
    }

    public function write_to_user(UNL_PersonInfo_Record $record, bool $clear_previous_record = true)
    {
        if ($clear_previous_record) {
            $record->clear_images();
        }
        foreach ($this->files as $path) {
            $record->save_image($path, basename($path));
        }
    }

    public function save_jpeg(): void
    {
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.jpeg';
            $image_data->setFormat('JPEG');
            $saved_image = $image_data->writeImage($path);

            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving JPEG Image');
            }
            $this->files[] = $path;
        }
    }

    public function save_png(): void
    {
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.png';
            $image_data->setFormat('PNG');
            $saved_image = $image_data->writeImage($path);

            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving PNG Image');
            }
            $this->files[] = $path;
        }
    }

    public function save_gif(): void
    {
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.gif';
            $image_data->setFormat('GIF');
            $saved_image = $image_data->writeImage($path);

            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving GIF Image');
            }
            $this->files[] = $path;
        }
    }

    public function save_bmp(): void
    {
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.bmp';
            $image_data->setFormat('BMP');
            $saved_image = $image_data->writeImage($path);

            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving BMP Image');
            }
            $this->files[] = $path;
        }
    }

    public function save_avif(): void
    {
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.avif';
            $image_data->setFormat('AVIF');
            $saved_image = $image_data->writeImage($path);

            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving AVIF Image');
            }
            $this->files[] = $path;
        }
    }

    public function save_webp(): void
    {
        foreach ($this->images as $file_name => $image_data) {
            /** @var Imagick $image_data */
            /** @var string $file_name */
            $path = $this->tmp_path . '/' . $file_name . '.webp';
            $image_data->setFormat('WEBP');
            $saved_image = $image_data->writeImage($path);

            if ($saved_image === false) {
                throw new UNL_PersonInfo_Exceptions_ImageProcessing('Error Saving WEBP Image');
            }
            $this->files[] = $path;
        }
    }
}