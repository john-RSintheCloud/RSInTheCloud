<?php

/**
 * Handle S3 buckets
 *
 * @author John
 */
class s3_model_bucket
{
//    protected $_bucket = 'bioquell-backup-bucket';
//    protected $_accessKeyId = 'AKIAJ5L3L3T4RQGLAKQQ';
//    protected $_secret = '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';
    protected $_bucket = 'sportarchive-rs-global';
    protected $_accessKeyId = 'AKIAIDRJ4NLMBZRJZ6SQ';
    protected $_secret = '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';

    protected $_runtimes = 'html5,flash';
    protected $_pluploadPath = '/library/Plupload/';


    protected function getBucket()
    {
        return $this->_bucket;
    }

    protected function getAccessKeyId()
    {
        return $this->_accessKeyId;
    }

    protected function getSecret()
    {
        return $this->_secret;
    }
    protected function getRuntimes()
    {
        return $this->_runtimes;
    }
    protected function getPluploadPath()
    {
        return $this->_pluploadPath;
    }


    public function setBucket($bucket)
    {
        $this->_bucket = $bucket;
        return $this;
    }

    public function setAccessKeyId($accessKeyId)
    {
        $this->_accessKeyId = $accessKeyId;
        return $this;
    }

    public function setSecret($secret)
    {
        $this->_secret = $secret;
        return $this;
    }
    public function setRuntimes($runtimes)
    {
        $this->_runtimes = $runtimes;
        return $this;
    }


    protected function getPolicy()
    {
        return base64_encode(json_encode(array(
            // ISO 8601 - date('c'); generates uncompatible date, so better do it manually
            'expiration' => date('Y-m-d\TH:i:s.000\Z', strtotime('+1 day')),
            'conditions' => array(
                array('bucket' => $this->getBucket()),
                array('acl' => 'public-read'),
                array('starts-with', '$key', ''),
                array('starts-with', '$Content-Type', ''), // accept all files
                // Plupload internally adds name field, so we need to mention it here
                array('starts-with', '$name', ''),
                // One more field to take into account: Filename - gets silently sent by FileReference.upload() in Flash
                // http://docs.amazonwebservices.com/AmazonS3/latest/dev/HTTPPOSTFlash.html
                array('starts-with', '$Filename', ''),
            )
        )));
    }
    protected function getSignature()
    {
        return base64_encode(hash_hmac('sha1', $this->getPolicy(), $this->getSecret(), true));
    }

    /**
     * Create the function to run plupload
     *
     * @return string - jquery function
     */
    public function getPlUpload($uploaderId = 'uploader')
    {
        return
<<<HeredocBlock
\$("#{$uploaderId}").plupload({
    // General settings
    runtimes : '{$this->getRuntimes()}',
    flash_swf_url : '{$this->getPluploadPath()}js/Moxie.swf',

    // S3 specific settings
    url : "https://{$this->getBucket() }.s3.amazonaws.com:443/",

    multipart_params: {
        'key': '\${filename}', // use filename as a key
        'Filename': '\${filename}', // adding this to keep consistency across the runtimes
        'acl': 'public-read',
        'Content-Type': '',
        'AWSAccessKeyId' : '{$this->getAccessKeyId()}',
        'policy': '{$this->getPolicy()}',
        'signature': '{$this->getSignature()}'
    },

    chunk_size: '20mb',

    file_data_name: 'file',

    filters : {
        // Maximum file size
        max_file_size : '10gb',
        // Specify what files to browse for
        mime_types: [
            {title : "Image files", extensions : "jpg,jpeg,png"},
            {title : "Documents", extensions : "doc,docx,rtf,xls,xlsx,csv,pdf"}
        ]
    },
    uploaded: function(event, args) {
        alert('bob');

        // stuff ...
    }

});
// \$('#{$uploaderId}').plupload('notify', 'info', "Do not close this window till the uploads are complete");

// Handle the case when form was submitted before uploading has finished
	\$('#form').submit(function(e) {
		// Files in queue upload them first
		if (\$('#uploader').plupload('getFiles').length > 0) {

			// When all files are uploaded submit form
			\$('#uploader').on('complete', function() {
				\$('#form')[0].submit();
			});

			\$('#uploader').plupload('start');
		} else {
			alert("You must have at least one file in the queue.");
		}
		return false; // Keep the form from submitting
	});
HeredocBlock;
        }

    /**
     * Load plupload and all it's runtimes and finally the UI widget
     *
     * @return string - jquery function
     */
    public function getPlUploadJs()
    {
        $ret = '<link rel="stylesheet" href="/library/Plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" />
    ' ;

        // <!-- production -->
        $ret .= '<script type="text/javascript" src="/library/Plupload/js/plupload.full.min.js"></script>
<script type="text/javascript" src="/library/Plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>';

        // <!-- debug
//        $ret .= '<script type="text/javascript" src="../../js/moxie.js"></script>
//<script type="text/javascript" src="../../js/plupload.dev.js"></script>
//<script type="text/javascript" src="../../js/jquery.ui.plupload/jquery.ui.plupload.js"></script>';

        return $ret;
    }

}

