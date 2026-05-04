<?php
use Aws\S3\S3Client;

class ModelToolS3 extends Model {
    private $product_bucket = 'cdnimages.net.staging';
    private $keyname = '';
    private $s3 = '';
    public $s3_host = 'https://s3.ap-south-1.amazonaws.com/';

    public function __construct($registry)
    {
        parent::__construct($registry);
        // Instantiate the client.
        $this->s3 = new S3Client([
            'profile'=>'default',
            'version' => 'latest',
            'region'  => 'ap-south-1'
        ]);

    }

    /**
     * Check if file exist on S3 bucket
     * @param $filepath
     * @return bool
     */
    public function checkExistence($filepath){
        $filepath = 'image/'.$filepath;
        if(S3_ENABLED == 1) {
            $object =  $filepath;
            echo $chkFileExist = $this->s3->doesObjectExist($this->product_bucket, $object);
            return $chkFileExist;
        }else{
            return false;
        }

    }

    /**
     * Upload file on S3 bucket
     * @param $keyname
     * @return mixed
     */
    public function createObject($filepath){

        $this->keyname = $filepath;
        $filename = basename($this->keyname);


        $filepath = DIR_IMAGE.$filepath;
        // Upload data.
        $result = $this->s3->putObject(array(
            'Bucket' => $this->product_bucket,
            'Key'    => $this->keyname,
            'SourceFile' => $filepath, // file path which is putting on AWS S3, Path should be absolute path like $filepath = "/var/www/html/for_testing_aws/assets/img/avtar.png";
            'ContentType' => mime_content_type($filepath),
        ));

       return $result['ObjectURL'];
    }

    /**
     * Get Complete URL of S3 object
     * @param $filepath
     * @return \Aws\Result
     */
    public function getObject($filepath){
        $result = $this->s3->getObject(array(
            'Bucket' => $this->bucket,
            'Key'    => $filepath
        ));

        return $result;
    }
}