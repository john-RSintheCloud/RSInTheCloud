<?php
/*
 * QE Upload Handler - apply our variations on the default handler.
 *
 */

class Uploader_QeUploader extends Uploader_UploadHandler
{


    /**
     * Set QE specific options - run from constructor
     */
    protected function initialize()
    {
//                echo realpath(APPLICATION_PATH . '/../' ) . '/data/uploads/';

//        Qe\Debug::dump($this->options, 1);
        //  overwrite default options
        $this->options['upload_dir'] = realpath( 
            APPLICATION_PATH . '/../' ) . '/data/uploads/' ;

        //  we cannot see this location so do not use upload_url
        $this->options['upload_url'] = '';
            // all download requests must go through PHP:
        $this->options['download_via_php'] = true;
            //  and specify where the script is
        $this->options['script_url'] = $this->get_full_url().'/image/upload/';

            //  Use different upload areas for each session?
        $this->options['user_dirs'] = false;
        
            // Defines which files can be displayed when downloaded
            // others are downloaded as files:
        $this->options['inline_file_types'] = '/\.(gif|jpe?g|png)$/i';
            // Defines which files (based on their names) are accepted for upload:
        $this->options['accept_file_types'] = '/.+$/i';
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
        $this->options['max_file_size'] = 10000000;
        $this->options['min_file_size'] = 1;
            // The maximum number of files for the upload directory:
        $this->options['max_number_of_files'] = 250;
            // Image resolution restrictions:
        $this->options['max_width'] = null;
        $this->options['max_height'] = null;
        $this->options['min_width'] = 20;
        $this->options['min_height'] = 20;
            // Set to true to rotate images based on EXIF meta data, if available:
        $this->options['orient_image'] = false;

            //  image versions are resized images used as thumbs.
            //  Each version has a separate subfolder
        $this->options['image_versions'] = array(
                // Uncomment the following version to restrict the size of
                // uploaded images:
                /*
                '' => array(
                    'max_width' => 1920,
                    'max_height' => 1200,
                    'jpeg_quality' => 95
                ),
                */
                // Uncomment the following to create medium sized thumbs:
                /*
                'medium' => array(
                    'max_width' => 800,
                    'max_height' => 600,
                    'jpeg_quality' => 80
                ),
                */
                'thumbnail' => array(
                    // Uncomment the following to force the max
                    // dimensions and e.g. create square thumbnails:
                    //'crop' => true,
                    'max_width' => 80,
                    'max_height' => 80
                )
        );
        parent::initialize();

    }

    protected function get_file_object($file_name) {
        
        $file = parent::get_file_object($file_name);
        if ($file){
            $file->cropUrl = $this->get_edit_url($file->name);
            
        }
        return $file;
    }

    protected function get_edit_url($file_name, $version = null) {

        $url = 'http://local.admin.qe/image/edit/p/up/f/'
 //       $url = $this->options['script_url']
 //           .$this->get_query_separator($this->options['script_url'])
//            .'file='
            .rawurlencode($file_name);
//        if ($version) {
//            $url .= '&version='.rawurlencode($version);
//        }
        return $url  ; // .'&download=1';
    }




        protected function trim_file_name($name,
            $type = null, $index = null, $content_range = null) {
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        // Use a timestamp for empty filenames:
        // Add missing file extension for known image types:
        //  and force lower case
        return strtolower(parent::trim_file_name($name, $type, $index, $content_range));
    }

        protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
            $index = null, $content_range = null) {
        $file = new stdClass();
        $file->name = $this->get_file_name($name, $type, $index, $content_range);
        $file->size = $this->fix_integer_overflow(intval($size));
        $file->type = $type;

        try {

            if ($this->validate($uploaded_file, $file, $error, $index)) {
                $this->handle_form_data($file, $index);
                $upload_dir = $this->get_upload_path();
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, $this->options['mkdir_mode'], true);
                }
                if (!is_dir($upload_dir)) {
                    throw new Exception ('Cannot create directory:  ' . $upload_dir);
                }
                $file_path = $this->get_upload_path($file->name);
                $append_file = $content_range && is_file($file_path) &&
                    $file->size > $this->get_file_size($file_path);
                if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                    // multipart/formdata uploads (POST method uploads)
                    if ($append_file) {
                        file_put_contents(
                            $file_path,
                            fopen($uploaded_file, 'r'),
                            FILE_APPEND
                        );
                    } else {
                        move_uploaded_file($uploaded_file, $file_path);
                    }
                } else {
                    // Non-multipart uploads (PUT method support)
                    file_put_contents(
                        $file_path,
                        fopen('php://input', 'r'),
                        $append_file ? FILE_APPEND : 0
                    );
                }
                $file_size = $this->get_file_size($file_path, $append_file);
                if ($file_size === $file->size) {
                    $file->url = $this->get_download_url($file->name);
                    list($img_width, $img_height) = @getimagesize($file_path);
                    if (is_int($img_width)) {
                        $this->handle_image_file($file_path, $file);
                    }
                } else {
                    $file->size = $file_size;
                    if (!$content_range && $this->options['discard_aborted_uploads']) {
                        unlink($file_path);
                        $file->error .= 'Upload aborted - could not create file' . ' (QU156)';
                    }
                }
                $this->set_additional_file_properties($file);
            }
        } catch (Exception $exc) {
            $file->error .= 'Upload aborted -  '
                . $exc->getMessage() . ' (QU162)';
        }

        return $file;
    }

    protected function get_upload_path($file_name = '', $version = null) {
        $version_path = empty($version) ? '' : $version.'/';
        return $this->options['upload_dir'] . $this->get_user_path()
            .$version_path.$file_name;
    }


}
